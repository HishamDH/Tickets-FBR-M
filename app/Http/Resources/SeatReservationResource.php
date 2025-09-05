<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeatReservationResource extends JsonResource
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
            'booking_id' => $this->booking_id,
            'seat_id' => $this->seat_id,
            'seat_number' => $this->seat?->seat_number,
            'seat_row' => $this->seat?->row,
            'seat_column' => $this->seat?->column,
            'seat_section' => $this->seat?->section,
            'seat_type' => $this->seat?->seat_type,
            'price' => (float) $this->price,
            'price_formatted' => number_format($this->price, 2) . ' ريال',
            'status' => $this->status,
            'reserved_at' => $this->reserved_at?->toISOString(),
            'expires_at' => $this->expires_at?->toISOString(),
            'confirmed_at' => $this->confirmed_at?->toISOString(),
            
            // Seat details (when loaded)
            'seat' => $this->when($this->relationLoaded('seat'), [
                'id' => $this->seat?->id,
                'seat_number' => $this->seat?->seat_number,
                'row' => $this->seat?->row,
                'column' => $this->seat?->column,
                'section' => $this->seat?->section,
                'seat_type' => $this->seat?->seat_type,
                'is_accessible' => $this->seat?->is_accessible ?? false,
                'coordinates' => [
                    'x' => $this->seat?->x_coordinate,
                    'y' => $this->seat?->y_coordinate,
                ],
            ]),
            
            // Reservation status
            'is_expired' => $this->expires_at && $this->expires_at->isPast(),
            'is_confirmed' => $this->status === 'confirmed',
            'is_cancelled' => $this->status === 'cancelled',
            'minutes_until_expiry' => $this->expires_at ? max(0, $this->expires_at->diffInMinutes(now())) : null,
            
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
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
                    'reserved' => '#FFA500', // Orange
                    'confirmed' => '#28A745', // Green
                    'cancelled' => '#DC3545', // Red
                    'expired' => '#6C757D', // Gray
                    default => '#6C757D'
                },
                'seat_display' => $this->getSeatDisplay(),
                'reservation_valid' => $this->expires_at && $this->expires_at->isFuture() && $this->status === 'reserved',
            ]
        ];
    }

    /**
     * Get seat display string
     */
    private function getSeatDisplay(): string
    {
        $parts = array_filter([
            $this->seat?->section ? "Section {$this->seat->section}" : null,
            $this->seat?->row ? "Row {$this->seat->row}" : null,
            $this->seat?->seat_number ? "Seat {$this->seat->seat_number}" : null,
        ]);

        return implode(', ', $parts) ?: "Seat #{$this->seat_id}";
    }
}