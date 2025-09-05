<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payment_number' => $this->payment_number,
            
            // Related entities
            'booking_id' => $this->booking_id,
            'merchant_id' => $this->merchant_id,
            'customer_id' => $this->customer_id,
            'payment_gateway_id' => $this->payment_gateway_id,
            
            // Payment amounts
            'amount' => (float) $this->amount,
            'gateway_fee' => (float) $this->gateway_fee,
            'platform_fee' => (float) $this->platform_fee,
            'total_amount' => (float) $this->total_amount,
            'currency' => $this->currency,
            
            // Formatted amounts
            'amount_formatted' => number_format($this->amount, 2) . ' ' . ($this->currency ?? 'ريال'),
            'gateway_fee_formatted' => number_format($this->gateway_fee, 2) . ' ' . ($this->currency ?? 'ريال'),
            'platform_fee_formatted' => number_format($this->platform_fee, 2) . ' ' . ($this->currency ?? 'ريال'),
            'total_amount_formatted' => number_format($this->total_amount, 2) . ' ' . ($this->currency ?? 'ريال'),
            
            // Payment status and method
            'status' => $this->status,
            'status_display' => $this->status_arabic,
            'payment_method' => $this->payment_method,
            'payment_method_display' => $this->getPaymentMethodDisplay(),
            
            // Gateway information
            'gateway_transaction_id' => $this->gateway_transaction_id,
            'gateway_reference' => $this->gateway_reference,
            'gateway_response' => $this->when($request->user()?->isAdmin(), $this->gateway_response),
            'gateway_metadata' => $this->when($request->user()?->isAdmin(), $this->gateway_metadata),
            
            // Timestamps
            'initiated_at' => $this->initiated_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'failed_at' => $this->failed_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Failure information
            'failure_reason' => $this->failure_reason,
            'customer_ip' => $this->when($request->user()?->isAdmin(), $this->customer_ip),
            'notes' => $this->notes,
            
            // Relationships (when loaded)
            'booking' => $this->when($this->relationLoaded('booking'), [
                'id' => $this->booking->id,
                'booking_number' => $this->booking->booking_number,
                'status' => $this->booking->status,
                'booking_date' => $this->booking->booking_date?->format('Y-m-d'),
            ]),
            'customer' => $this->when($this->relationLoaded('customer'), [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
                'email' => $this->customer->email,
            ]),
            'merchant' => $this->when($this->relationLoaded('merchant'), [
                'id' => $this->merchant->id,
                'name' => $this->merchant->name,
                'business_name' => $this->merchant->business_name,
            ]),
            'payment_gateway' => $this->when($this->relationLoaded('paymentGateway'), [
                'id' => $this->paymentGateway->id,
                'name' => $this->paymentGateway->name,
                'display_name' => $this->paymentGateway->display_name,
                'type' => $this->paymentGateway->type,
                'logo' => $this->paymentGateway->logo ? url('storage/' . $this->paymentGateway->logo) : null,
            ]),
            
            // Payment state checks
            'is_pending' => $this->status === 'pending',
            'is_processing' => $this->status === 'processing',
            'is_completed' => $this->status === 'completed',
            'is_failed' => $this->status === 'failed',
            'is_cancelled' => $this->status === 'cancelled',
            'is_refunded' => $this->status === 'refunded',
            'can_be_refunded' => $this->canBeRefunded(),
            
            // Processing time
            'processing_time_seconds' => $this->getProcessingTimeInSeconds(),
            'processing_time_display' => $this->getProcessingTimeDisplay(),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'status_color' => match ($this->status) {
                    'pending' => '#FFA500',
                    'processing' => '#007BFF',
                    'completed' => '#28A745',
                    'failed' => '#DC3545',
                    'cancelled' => '#6C757D',
                    'refunded' => '#17A2B8',
                    default => '#6C757D'
                },
                'status_icon' => match ($this->status) {
                    'pending' => '�',
                    'processing' => '�',
                    'completed' => '',
                    'failed' => 'L',
                    'cancelled' => 'N',
                    'refunded' => '�',
                    default => 'S'
                },
                'currency_symbol' => match ($this->currency) {
                    'SAR', 'ريال' => 'ر.س',
                    'USD' => '$',
                    'EUR' => '€',
                    default => $this->currency ?? 'ر.س'
                },
                'refund_policy' => [
                    'eligible' => $this->canBeRefunded(),
                    'days_remaining' => $this->getRemainingRefundDays(),
                    'policy_text' => 'Refunds are available within 30 days of successful payment'
                ],
                'receipt_url' => $this->status === 'completed' ? route('payments.receipt', $this->id) : null,
            ]
        ];
    }

    /**
     * Get payment method display name
     */
    protected function getPaymentMethodDisplay(): string
    {
        return match ($this->payment_method) {
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'stc_pay' => 'STC Pay',
            'mada' => 'Mada',
            'apple_pay' => 'Apple Pay',
            'google_pay' => 'Google Pay',
            'bank_transfer' => 'Bank Transfer',
            'cash' => 'Cash',
            'wallet' => 'Wallet',
            default => ucfirst(str_replace('_', ' ', $this->payment_method ?? 'Unknown'))
        };
    }

    /**
     * Get processing time in seconds
     */
    protected function getProcessingTimeInSeconds(): ?int
    {
        if (!$this->initiated_at) {
            return null;
        }
        
        $endTime = $this->completed_at ?? $this->failed_at ?? now();
        return $this->initiated_at->diffInSeconds($endTime);
    }

    /**
     * Get processing time display
     */
    protected function getProcessingTimeDisplay(): ?string
    {
        $seconds = $this->getProcessingTimeInSeconds();
        
        if ($seconds === null) {
            return null;
        }
        
        if ($seconds < 60) {
            return $seconds . ' seconds';
        } elseif ($seconds < 3600) {
            return round($seconds / 60) . ' minutes';
        } else {
            return round($seconds / 3600, 1) . ' hours';
        }
    }

    /**
     * Get remaining refund days
     */
    protected function getRemainingRefundDays(): ?int
    {
        if (!$this->completed_at) {
            return null;
        }
        
        $daysSinceCompletion = $this->completed_at->diffInDays(now());
        return max(0, 30 - $daysSinceCompletion);
    }
}