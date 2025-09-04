<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $venue_layout_id
 * @property string $seat_number
 * @property string|null $section
 * @property string $seat_type
 * @property int $capacity
 * @property float $price
 * @property float $x_position
 * @property float $y_position
 * @property float $width
 * @property float $height
 * @property int $rotation
 * @property string $status
 * @property bool $is_accessible
 * @property array|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\VenueLayout $venueLayout
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SeatReservation> $reservations
 * @property-read int|null $reservations_count
 */
class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_layout_id',
        'seat_number',
        'section',
        'seat_type',
        'capacity',
        'price',
        'x_position',
        'y_position',
        'width',
        'height',
        'rotation',
        'status',
        'is_accessible',
        'metadata',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'price' => 'decimal:2',
        'x_position' => 'decimal:2',
        'y_position' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'rotation' => 'integer',
        'is_accessible' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the venue layout that owns the seat.
     */
    public function venueLayout(): BelongsTo
    {
        return $this->belongsTo(VenueLayout::class);
    }

    /**
     * Get all reservations for this seat.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(SeatReservation::class);
    }

    /**
     * Check if seat is available for booking.
     */
    public function isAvailable(\DateTime $datetime = null): bool
    {
        if ($this->status !== 'available') {
            return false;
        }

        if ($datetime) {
            // Check if there are any active reservations for this datetime
            return !$this->reservations()
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($datetime) {
                    $query->whereNull('expires_at')
                          ->orWhere('expires_at', '>', $datetime);
                })
                ->exists();
        }

        return true;
    }

    /**
     * Reserve this seat temporarily.
     */
    public function reserveTemporarily(int $bookingId, int $minutes = 15): SeatReservation
    {
        return SeatReservation::create([
            'booking_id' => $bookingId,
            'seat_id' => $this->id,
            'status' => 'reserved',
            'amount_paid' => $this->price,
            'reserved_at' => now(),
            'expires_at' => now()->addMinutes($minutes),
        ]);
    }

    /**
     * Confirm seat reservation.
     */
    public function confirmReservation(int $bookingId): void
    {
        $reservation = $this->reservations()
            ->where('booking_id', $bookingId)
            ->where('status', 'reserved')
            ->first();

        if ($reservation) {
            $reservation->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'expires_at' => null,
            ]);

            $this->update(['status' => 'reserved']);
        }
    }

    /**
     * Cancel seat reservation.
     */
    public function cancelReservation(int $bookingId): void
    {
        $this->reservations()
            ->where('booking_id', $bookingId)
            ->update(['status' => 'cancelled']);

        $this->update(['status' => 'available']);
    }

    /**
     * Mark seat as occupied (customer has arrived).
     */
    public function markAsOccupied(int $bookingId): void
    {
        $reservation = $this->reservations()
            ->where('booking_id', $bookingId)
            ->where('status', 'confirmed')
            ->first();

        if ($reservation) {
            $this->update(['status' => 'occupied']);
        }
    }

    /**
     * Get seat pricing with any applicable modifiers.
     */
    public function getPriceWithModifiers(array $modifiers = []): float
    {
        $basePrice = $this->price;

        foreach ($modifiers as $modifier) {
            switch ($modifier['type']) {
                case 'percentage':
                    $basePrice += ($basePrice * $modifier['value'] / 100);
                    break;
                case 'fixed':
                    $basePrice += $modifier['value'];
                    break;
                case 'discount':
                    $basePrice -= ($basePrice * $modifier['value'] / 100);
                    break;
            }
        }

        return max(0, $basePrice);
    }

    /**
     * Get seat display name for UI.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->section) {
            return $this->section . ' - ' . $this->seat_number;
        }

        return $this->seat_number;
    }

    /**
     * Get seat color based on status.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'available' => '#10B981',    // green
            'reserved' => '#F59E0B',     // amber
            'occupied' => '#EF4444',     // red
            'maintenance' => '#6B7280',  // gray
            'blocked' => '#374151',      // dark gray
            default => '#6B7280',
        };
    }

    /**
     * Get seat icon based on type.
     */
    public function getTypeIconAttribute(): string
    {
        return match ($this->seat_type) {
            'individual' => '🪑',
            'table' => '🪑',
            'sofa' => '🛋️',
            'vip' => '👑',
            'wheelchair' => '♿',
            default => '🪑',
        };
    }

    /**
     * Scope for available seats.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope for seats by section.
     */
    public function scopeInSection($query, string $section)
    {
        return $query->where('section', $section);
    }

    /**
     * Scope for seats by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('seat_type', $type);
    }

    /**
     * Scope for accessible seats.
     */
    public function scopeAccessible($query)
    {
        return $query->where('is_accessible', true);
    }

    /**
     * Get seats within a price range.
     */
    public function scopeInPriceRange($query, float $min, float $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }
}