<?php

namespace App\Services;

use App\Models\Refund;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\MerchantWallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class RefundService
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Request a refund for a booking
     */
    public function requestRefund(Booking $booking, string $reason, string $type = 'full', ?float $amount = null): Refund
    {
        // Validate booking can be refunded
        if (!$this->canRefund($booking)) {
            throw new Exception('This booking cannot be refunded');
        }

        $payment = $booking->payment;
        $refundAmount = $amount ?? $payment->amount;
        
        // Calculate refund fee based on gateway and timing
        $fee = $this->calculateRefundFee($payment, $refundAmount);
        
        DB::beginTransaction();
        try {
            // Create refund record
            $refund = Refund::create([
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
                'user_id' => $booking->customer_id,
                'amount' => $refundAmount,
                'fee' => $fee,
                'net_amount' => $refundAmount - $fee,
                'type' => $type,
                'reason' => $reason,
                'status' => 'pending',
            ]);

            // Update booking status
            $booking->update(['status' => 'refund_requested']);

            DB::commit();
            
            Log::info("Refund requested", [
                'refund_id' => $refund->id,
                'booking_id' => $booking->id,
                'amount' => $refundAmount
            ]);

            return $refund;
        } catch (Exception $e) {
            DB::rollback();
            Log::error("Refund request failed", [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Process a refund (admin action)
     */
    public function processRefund(Refund $refund, ?string $adminNotes = null): bool
    {
        if ($refund->status !== 'pending') {
            throw new Exception('Only pending refunds can be processed');
        }

        DB::beginTransaction();
        try {
            // Update refund status
            $refund->update([
                'status' => 'processing',
                'admin_notes' => $adminNotes,
            ]);

            // Process with payment gateway
            $gatewayResponse = $this->processGatewayRefund($refund);
            
            if ($gatewayResponse['success']) {
                $refund->update([
                    'status' => 'completed',
                    'gateway_response' => $gatewayResponse,
                    'processed_at' => now(),
                ]);

                // Update booking status
                $refund->booking->update([
                    'status' => 'refunded',
                    'payment_status' => 'refunded'
                ]);

                // Adjust merchant wallet if needed
                $this->adjustMerchantWallet($refund);
                
                DB::commit();
                return true;
            } else {
                $refund->update([
                    'status' => 'failed',
                    'gateway_response' => $gatewayResponse,
                ]);
                DB::commit();
                return false;
            }
        } catch (Exception $e) {
            DB::rollback();
            Log::error("Refund processing failed", [
                'refund_id' => $refund->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Check if booking can be refunded
     */
    public function canRefund(Booking $booking): bool
    {
        // Check if booking has a valid payment
        if (!$booking->payment || $booking->payment->status !== 'completed') {
            return false;
        }

        // Check if booking is not already refunded
        if (in_array($booking->status, ['refunded', 'refund_requested'])) {
            return false;
        }

        // Check if booking is not too old (e.g., 30 days)
        if ($booking->created_at->diffInDays(now()) > 30) {
            return false;
        }

        // Check if booking hasn't started yet (can be customized per service)
        if ($booking->booking_date && $booking->booking_date->isPast()) {
            // Allow refund only if service allows post-service refunds
            return $booking->service->allows_post_service_refund ?? false;
        }

        return true;
    }

    /**
     * Calculate refund fee based on various factors
     */
    protected function calculateRefundFee(Payment $payment, float $amount): float
    {
        $gateway = $payment->paymentGateway;
        
        if (!$gateway || !$gateway->supports_refund) {
            return $amount; // Full fee if gateway doesn't support refunds
        }

        // Base gateway refund fee
        $gatewayFee = $payment->amount * 0.03; // 3% gateway fee
        
        // Platform fee (can be customized)
        $platformFee = $amount * 0.05; // 5% platform cancellation fee
        
        return min($gatewayFee + $platformFee, $amount * 0.1); // Max 10% fee
    }

    /**
     * Process refund with payment gateway
     */
    protected function processGatewayRefund(Refund $refund): array
    {
        try {
            $payment = $refund->payment;
            $gateway = $payment->paymentGateway;

            // This would integrate with actual payment gateway APIs
            // For now, we'll simulate a successful refund
            
            if ($gateway->supports_refund) {
                // Simulate API call to payment gateway
                sleep(1); // Simulate processing time
                
                return [
                    'success' => true,
                    'transaction_id' => 'REF_' . uniqid(),
                    'gateway_reference' => $gateway->code . '_' . uniqid(),
                    'processed_amount' => $refund->net_amount,
                    'message' => 'Refund processed successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Gateway does not support refunds',
                    'message' => 'Manual refund required'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Refund processing failed'
            ];
        }
    }

    /**
     * Adjust merchant wallet after refund
     */
    protected function adjustMerchantWallet(Refund $refund): void
    {
        $booking = $refund->booking;
        $merchant = $booking->merchant;
        
        if ($merchant && $refund->status === 'completed') {
            $wallet = MerchantWallet::firstOrCreate(['user_id' => $merchant->id]);
            
            // Deduct refunded amount from merchant wallet
            $wallet->decrement('balance', $refund->net_amount);
            
            Log::info("Merchant wallet adjusted for refund", [
                'merchant_id' => $merchant->id,
                'refund_id' => $refund->id,
                'amount_deducted' => $refund->net_amount
            ]);
        }
    }

    /**
     * Get refund statistics
     */
    public function getRefundStats(int $merchantId = null): array
    {
        $query = Refund::query();
        
        if ($merchantId) {
            $query->whereHas('booking', function($q) use ($merchantId) {
                $q->where('merchant_id', $merchantId);
            });
        }

        return [
            'total_refunds' => $query->count(),
            'pending_refunds' => $query->where('status', 'pending')->count(),
            'completed_refunds' => $query->where('status', 'completed')->count(),
            'failed_refunds' => $query->where('status', 'failed')->count(),
            'total_refunded_amount' => $query->where('status', 'completed')->sum('amount'),
            'this_month_refunds' => $query->whereMonth('created_at', now()->month)->count(),
        ];
    }
}