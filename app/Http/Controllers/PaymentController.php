<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * معالجة Webhook من بوابات الدفع
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            $provider = $request->header('X-Payment-Provider', 'unknown');
            $signature = $request->header('X-Signature');
            
            Log::info('Payment webhook received', [
                'provider' => $provider,
                'data' => $request->all(),
                'signature' => $signature,
            ]);

            // التحقق من التوقيع حسب البوابة
            if (!$this->verifyWebhookSignature($request, $provider, $signature)) {
                Log::warning('Invalid webhook signature', [
                    'provider' => $provider,
                    'signature' => $signature,
                ]);
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            // معالجة الـ webhook حسب البوابة
            switch ($provider) {
                case 'stripe':
                    return $this->handleStripeWebhook($request);
                
                case 'stc_pay':
                    return $this->handleStcPayWebhook($request);
                
                case 'bank_integration':
                    return $this->handleBankWebhook($request);
                
                default:
                    Log::warning('Unknown webhook provider', ['provider' => $provider]);
                    return response()->json(['error' => 'Unknown provider'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * معالجة webhook من Stripe
     */
    protected function handleStripeWebhook(Request $request): JsonResponse
    {
        $data = $request->all();
        $eventType = $data['type'] ?? null;

        switch ($eventType) {
            case 'payment_intent.succeeded':
                return $this->handleSuccessfulPayment($data);
            
            case 'payment_intent.payment_failed':
                return $this->handleFailedPayment($data);
            
            case 'charge.dispute.created':
                return $this->handleDispute($data);
            
            default:
                Log::info('Unhandled Stripe webhook event', ['type' => $eventType]);
                return response()->json(['status' => 'ignored']);
        }
    }

    /**
     * معالجة webhook من STC Pay
     */
    protected function handleStcPayWebhook(Request $request): JsonResponse
    {
        $data = $request->all();
        
        // معالجة أحداث STC Pay
        // هذا placeholder للتكامل المستقبلي
        
        return response()->json(['status' => 'received']);
    }

    /**
     * معالجة webhook من البنك
     */
    protected function handleBankWebhook(Request $request): JsonResponse
    {
        $data = $request->all();
        
        // معالجة أحداث البنوك
        // هذا placeholder للتكامل المستقبلي
        
        return response()->json(['status' => 'received']);
    }

    /**
     * معالجة دفع ناجح
     */
    protected function handleSuccessfulPayment(array $data): JsonResponse
    {
        $paymentIntentId = $data['data']['object']['id'] ?? null;
        
        if (!$paymentIntentId) {
            return response()->json(['error' => 'Missing payment intent ID'], 400);
        }

        $payment = Payment::where('gateway_transaction_id', $paymentIntentId)->first();
        
        if (!$payment) {
            Log::warning('Payment not found for webhook', ['payment_intent_id' => $paymentIntentId]);
            return response()->json(['error' => 'Payment not found'], 404);
        }

        if ($payment->status !== 'completed') {
            $payment->updateStatus('completed', [
                'gateway_response' => $data['data']['object'],
            ]);

            // تحديث حالة الحجز
            $payment->booking->update(['payment_status' => 'paid']);

            Log::info('Payment completed via webhook', [
                'payment_id' => $payment->id,
                'booking_id' => $payment->booking_id,
            ]);
        }

        return response()->json(['status' => 'processed']);
    }

    /**
     * معالجة دفع فاشل
     */
    protected function handleFailedPayment(array $data): JsonResponse
    {
        $paymentIntentId = $data['data']['object']['id'] ?? null;
        $failureReason = $data['data']['object']['last_payment_error']['message'] ?? 'Unknown error';
        
        if (!$paymentIntentId) {
            return response()->json(['error' => 'Missing payment intent ID'], 400);
        }

        $payment = Payment::where('gateway_transaction_id', $paymentIntentId)->first();
        
        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        $payment->updateStatus('failed', [
            'failure_reason' => $failureReason,
            'gateway_response' => $data['data']['object'],
        ]);

        Log::info('Payment failed via webhook', [
            'payment_id' => $payment->id,
            'reason' => $failureReason,
        ]);

        return response()->json(['status' => 'processed']);
    }

    /**
     * معالجة نزاع على الدفع
     */
    protected function handleDispute(array $data): JsonResponse
    {
        $chargeId = $data['data']['object']['charge'] ?? null;
        $reason = $data['data']['object']['reason'] ?? 'unknown';
        
        // البحث عن الدفع المرتبط والتعامل مع النزاع
        // هذا placeholder للتطوير المستقبلي
        
        Log::info('Payment dispute created', [
            'charge_id' => $chargeId,
            'reason' => $reason,
        ]);

        return response()->json(['status' => 'received']);
    }

    /**
     * التحقق من توقيع الـ webhook
     */
    protected function verifyWebhookSignature(Request $request, string $provider, string $signature = null): bool
    {
        switch ($provider) {
            case 'stripe':
                return $this->verifyStripeSignature($request, $signature);
            
            case 'stc_pay':
                return $this->verifyStcPaySignature($request, $signature);
            
            case 'bank_integration':
                return $this->verifyBankSignature($request, $signature);
            
            default:
                return false;
        }
    }

    /**
     * التحقق من توقيع Stripe
     */
    protected function verifyStripeSignature(Request $request, string $signature = null): bool
    {
        if (!$signature) {
            return false;
        }

        $webhookSecret = config('payment.stripe.webhook_secret');
        
        if (!$webhookSecret) {
            Log::warning('Stripe webhook secret not configured');
            return config('app.env') === 'local'; // السماح في البيئة المحلية فقط
        }

        // التحقق الفعلي من توقيع Stripe
        // هذا placeholder للتطوير المستقبلي
        return true;
    }

    /**
     * التحقق من توقيع STC Pay
     */
    protected function verifyStcPaySignature(Request $request, string $signature = null): bool
    {
        // placeholder للتحقق من STC Pay
        return config('app.env') === 'local';
    }

    /**
     * التحقق من توقيع البنك
     */
    protected function verifyBankSignature(Request $request, string $signature = null): bool
    {
        // placeholder للتحقق من البنوك
        return config('app.env') === 'local';
    }
}
