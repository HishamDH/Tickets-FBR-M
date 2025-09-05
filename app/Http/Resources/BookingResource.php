<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'booking_number' => $this->booking_number,
            'code' => $this->code,
            'qr_code' => $this->qr_code,
            
            // Customer information
            'customer_id' => $this->customer_id,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'customer_email' => $this->customer_email,
            'customer' => $this->when($this->relationLoaded('customer'), new UserResource($this->customer)),
            
            // Service/Bookable information
            'bookable_id' => $this->bookable_id,
            'bookable_type' => $this->bookable_type,
            'bookable' => $this->when($this->relationLoaded('bookable'), [
                'id' => $this->bookable?->id,
                'name' => $this->bookable?->name,
                'type' => class_basename($this->bookable_type),
                'location' => $this->bookable?->location ?? null,
                'image' => $this->bookable?->image ? url('storage/' . $this->bookable->image) : null,
            ]),
            
            // Merchant information
            'merchant_id' => $this->merchant_id,
            'merchant' => $this->when($this->relationLoaded('merchant'), new UserResource($this->merchant)),
            
            // Booking details
            'booking_date' => $this->booking_date?->format('Y-m-d'),
            'booking_time' => $this->booking_time,
            'guest_count' => $this->guest_count,
            'number_of_people' => $this->number_of_people,
            'number_of_tables' => $this->number_of_tables,
            'duration_hours' => $this->duration_hours,
            
            // Pricing
            'total_amount' => (float) $this->total_amount,
            'total_amount_formatted' => number_format($this->total_amount, 2) . ' ريال',
            'commission_amount' => (float) $this->commission_amount,
            'commission_rate' => (float) $this->commission_rate,
            'discount' => (float) $this->discount,
            'merchant_amount' => (float) $this->merchant_amount,
            
            // Status information
            'status' => $this->status,
            'status_display' => $this->status_arabic,
            'payment_status' => $this->payment_status,
            'payment_status_display' => $this->payment_status_arabic,
            'reservation_status' => $this->reservation_status,
            'booking_source' => $this->booking_source,
            
            // Additional information
            'special_requests' => $this->special_requests,
            'notes' => $this->notes,
            
            // Cancellation information
            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'cancelled_by' => $this->cancelled_by,
            'cancelled_by_user' => $this->when($this->relationLoaded('cancelledBy'), new UserResource($this->cancelledBy)),
            
            // POS and offline transaction data
            'pos_terminal_id' => $this->pos_terminal_id,
            'is_offline_transaction' => $this->is_offline_transaction,
            'offline_transaction_id' => $this->offline_transaction_id,
            'synced_at' => $this->synced_at?->toISOString(),
            
            // Printing information
            'printed_at' => $this->printed_at?->toISOString(),
            'print_count' => $this->print_count,
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Relationships (when loaded)
            'payments' => $this->when($this->relationLoaded('payments'), PaymentResource::collection($this->payments)),
            'latest_payment' => $this->when($this->relationLoaded('latestPayment'), new PaymentResource($this->latestPayment)),
            'seat_reservations' => $this->when($this->relationLoaded('seatReservations'), SeatReservationResource::collection($this->seatReservations)),
            'seat_reservations_count' => $this->when($this->relationLoaded('seatReservations'), $this->seatReservations->count()),
            
            // Booking state checks
            'is_confirmed' => $this->isConfirmed(),
            'is_paid' => $this->isPaid(),
            'is_cancelled' => $this->isCancelled(),
            'can_be_cancelled' => $this->canBeCancelled(),
            'can_be_modified' => $this->canBeModified(),
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
                'qr_code_data' => $this->generateQrCode(),
                'booking_url' => route('bookings.show', $this->id),
                'status_color' => match ($this->status) {
                    'pending' => '#FFA500',
                    'confirmed' => '#28A745',
                    'completed' => '#007BFF',
                    'cancelled' => '#DC3545',
                    'no_show' => '#6C757D',
                    default => '#6C757D'
                },
                'payment_status_color' => match ($this->payment_status) {
                    'pending' => '#FFA500',
                    'paid' => '#28A745',
                    'failed' => '#DC3545',
                    'refunded' => '#17A2B8',
                    default => '#6C757D'
                },
                'booking_type' => class_basename($this->bookable_type),
                'days_until_booking' => $this->booking_date ? $this->booking_date->diffInDays(now()) : null,
                'is_today' => $this->booking_date ? $this->booking_date->isToday() : false,
                'is_past' => $this->booking_date ? $this->booking_date->isPast() : false,
                'is_future' => $this->booking_date ? $this->booking_date->isFuture() : false,
            ]
        ];
    }

    /**
     * Check if booking can be cancelled
     */
    protected function canBeCancelled(): bool
    {
        if ($this->isCancelled() || $this->status === 'completed') {
            return false;
        }
        
        // Can cancel up to 24 hours before booking date
        if ($this->booking_date && $this->booking_date->subHours(24)->isPast()) {
            return false;
        }
        
        return true;
    }

    /**
     * Check if booking can be modified
     */
    protected function canBeModified(): bool
    {
        if ($this->isCancelled() || $this->status === 'completed') {
            return false;
        }
        
        // Can modify up to 48 hours before booking date
        if ($this->booking_date && $this->booking_date->subHours(48)->isPast()) {
            return false;
        }
        
        return true;
    }
}