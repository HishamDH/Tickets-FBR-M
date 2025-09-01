<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\MerchantPaymentSetting;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * إنشاء دفعة جديدة
     */
    public function createPayment(Booking $booking, PaymentGateway $gateway, array $additionalData = []): Payment
    {
        try {
            DB::beginTransaction();

            // التحقق من إعدادات التاجر للبوابة
            $merchantSetting = MerchantPaymentSetting::where('merchant_id', $booking->merchant_id)
                ->where('payment_gateway_id', $gateway->id)
                ->where('is_enabled', true)
                ->first();

            if (! $merchantSetting) {
                throw new Exception('بوابة الدفع غير مفعلة لدى التاجر');
            }

            // حساب الرسوم
            $gatewayFee = $merchantSetting->calculateCustomFee($booking->total_amount);
            $platformFee = $this->calculatePlatformFee($booking->total_amount);
            $totalAmount = $booking->total_amount + $gatewayFee + $platformFee;

            // إنشاء سجل الدفع
            $payment = Payment::create([
                'payment_number' => Payment::generatePaymentNumber(),
                'booking_id' => $booking->id,
                'merchant_id' => $booking->merchant_id,
                'payment_gateway_id' => $gateway->id,
                'customer_id' => $booking->customer_id,
                'amount' => $booking->total_amount,
                'gateway_fee' => $gatewayFee,
                'platform_fee' => $platformFee,
                'total_amount' => $totalAmount,
                'currency' => 'SAR',
                'status' => 'pending',
                'payment_method' => $additionalData['payment_method'] ?? 'card',
                'customer_ip' => $additionalData['customer_ip'] ?? request()->ip(),
                'initiated_at' => now(),
            ]);

            DB::commit();

            Log::info('Payment created successfully', [
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
                'gateway' => $gateway->code,
                'amount' => $totalAmount,
            ]);

            return $payment;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Payment creation failed', [
                'booking_id' => $booking->id,
                'gateway' => $gateway->code,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * معالجة الدفع عبر البوابة المحددة
     */
    public function processPayment(Payment $payment, array $paymentData): array
    {
        try {
            $gateway = $payment->paymentGateway;

            switch ($gateway->provider) {
                case 'stripe':
                    return $this->processStripePayment($payment, $paymentData);

                case 'bank_integration':
                case 'stc_integration':
                    return $this->processBankIntegrationPayment($payment, $paymentData);

                case 'manual':
                    return $this->processManualPayment($payment, $paymentData);

                default:
                    throw new Exception('مقدم الخدمة غير مدعوم: '.$gateway->provider);
            }

        } catch (Exception $e) {
            $payment->updateStatus('failed', [
                'failure_reason' => $e->getMessage(),
            ]);

            Log::error('Payment processing failed', [
                'payment_id' => $payment->id,
                'gateway' => $payment->paymentGateway->code,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * معالجة الدفع عبر Stripe
     */
    protected function processStripePayment(Payment $payment, array $paymentData): array
    {
        // في الوقت الحالي نضع placeholder للتكامل المستقبلي مع Stripe
        // يمكن تفعيله عند الحاجة

        // محاكاة نجاح الدفع للاختبار
        if (config('app.env') === 'local' && isset($paymentData['simulate_success'])) {
            $payment->updateStatus('completed', [
                'gateway_transaction_id' => 'stripe_'.uniqid(),
                'gateway_response' => [
                    'status' => 'succeeded',
                    'payment_method' => $paymentData['payment_method'] ?? 'card',
                    'last4' => '4242',
                    'brand' => 'visa',
                ],
            ]);

            // تحديث حالة الحجز
            $payment->booking->update(['payment_status' => 'paid']);

            return [
                'success' => true,
                'message' => 'تم الدفع بنجاح',
                'transaction_id' => $payment->gateway_transaction_id,
            ];
        }

        return [
            'success' => false,
            'message' => 'تكامل Stripe سيتم تفعيله لاحقاً',
            'requires_setup' => true,
        ];
    }

    /**
     * معالجة الدفع عبر التكامل البنكي
     */
    protected function processBankIntegrationPayment(Payment $payment, array $paymentData): array
    {
        // placeholder للتكامل المستقبلي مع البنوك
        return [
            'success' => false,
            'message' => 'التكامل البنكي سيتم تفعيله لاحقاً',
            'requires_setup' => true,
        ];
    }

    /**
     * معالجة الدفع اليدوي
     */
    protected function processManualPayment(Payment $payment, array $paymentData): array
    {
        // للتحويل البنكي والدفع اليدوي
        $payment->updateStatus('processing', [
            'gateway_response' => [
                'type' => 'manual_payment',
                'reference' => $paymentData['reference'] ?? null,
                'notes' => $paymentData['notes'] ?? null,
            ],
        ]);

        return [
            'success' => true,
            'message' => 'تم استلام طلب الدفع، سيتم التحقق خلال 24 ساعة',
            'requires_verification' => true,
        ];
    }

    /**
     * التحقق من إتمام الدفع (webhook handler)
     */
    public function verifyPayment(Payment $payment, array $webhookData): bool
    {
        try {
            $gateway = $payment->paymentGateway;

            switch ($gateway->provider) {
                case 'stripe':
                    return $this->verifyStripePayment($payment, $webhookData);

                case 'bank_integration':
                case 'stc_integration':
                    return $this->verifyBankPayment($payment, $webhookData);

                default:
                    return false;
            }

        } catch (Exception $e) {
            Log::error('Payment verification failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * التحقق من دفع Stripe
     */
    protected function verifyStripePayment(Payment $payment, array $webhookData): bool
    {
        // placeholder للتحقق من Stripe webhooks
        return false;
    }

    /**
     * التحقق من الدفع البنكي
     */
    protected function verifyBankPayment(Payment $payment, array $webhookData): bool
    {
        // placeholder للتحقق من البنوك
        return false;
    }

    /**
     * حساب رسوم المنصة
     */
    protected function calculatePlatformFee(float $amount): float
    {
        $platformFeeRate = config('payment.platform_fee_rate', 1.0); // 1% افتراضي

        return $amount * ($platformFeeRate / 100);
    }

    /**
     * استرداد المبلغ
     */
    public function refundPayment(Payment $payment, ?float $amount = null, ?string $reason = null): bool
    {
        try {
            if (! $payment->canBeRefunded()) {
                throw new Exception('لا يمكن استرداد هذه الدفعة');
            }

            $refundAmount = $amount ?? $payment->total_amount;

            // معالجة الاسترداد حسب البوابة
            $gateway = $payment->paymentGateway;

            switch ($gateway->provider) {
                case 'stripe':
                    $result = $this->processStripeRefund($payment, $refundAmount, $reason);
                    break;

                case 'bank_integration':
                case 'stc_integration':
                    $result = $this->processBankRefund($payment, $refundAmount, $reason);
                    break;

                default:
                    throw new Exception('الاسترداد غير مدعوم لهذه البوابة');
            }

            if ($result) {
                $payment->updateStatus('refunded', [
                    'gateway_response' => [
                        'refund_amount' => $refundAmount,
                        'refund_reason' => $reason,
                        'refunded_at' => now()->toISOString(),
                    ],
                ]);

                // تحديث حالة الحجز
                $payment->booking->update(['payment_status' => 'refunded']);
            }

            return $result;

        } catch (Exception $e) {
            Log::error('Payment refund failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * استرداد عبر Stripe
     */
    protected function processStripeRefund(Payment $payment, float $amount, ?string $reason = null): bool
    {
        // placeholder لاسترداد Stripe
        return false;
    }

    /**
     * استرداد عبر البنك
     */
    protected function processBankRefund(Payment $payment, float $amount, ?string $reason = null): bool
    {
        // placeholder لاسترداد البنك
        return false;
    }

    /**
     * الحصول على بوابات الدفع المتاحة للتاجر
     */
    public function getAvailableGateways(int $merchantId): array
    {
        $settings = MerchantPaymentSetting::with('paymentGateway')
            ->where('merchant_id', $merchantId)
            ->where('is_enabled', true)
            ->whereHas('paymentGateway', function ($query) {
                $query->where('is_active', true);
            })
            ->ordered()
            ->get();

        return $settings->map(function ($setting) {
            return [
                'id' => $setting->paymentGateway->id,
                'code' => $setting->paymentGateway->code,
                'name' => $setting->paymentGateway->localized_name,
                'icon' => $setting->paymentGateway->icon,
                'fee' => $setting->calculateCustomFee(100), // رسوم لكل 100 ريال
                'supports_refund' => $setting->paymentGateway->supports_refund,
                'is_configured' => $setting->isConfigured(),
            ];
        })->toArray();
    }
}
