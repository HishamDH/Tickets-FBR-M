<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $booking_id
 * @property int $seat_id
 * @property string $status
 * @property float $amount_paid
 * @property string|null $special_requirements
 * @property \Illuminate\Support\Carbon $reserved_at
 * @property \Illuminate\Support\Carbon|null $confirmed_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Booking $booking
 * @property-read \App\Models\Seat $seat
 */
class SeatReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'seat_id',
        'status',
        'amount_paid',
        'special_requirements',
        'reserved_at',
        'confirmed_at',
        'expires_at',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'reserved_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the booking that owns the reservation.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the seat that is reserved.
     */
    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    /**
     * Check if reservation has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at < now();
    }

    /**
     * Check if reservation is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if reservation is active (not cancelled or expired).
     */
    public function isActive(): bool
    {
        return $this->status !== 'cancelled' && !$this->isExpired();
    }

    /**
     * Extend reservation expiry time.
     */
    public function extend(int $minutes = 15): void
    {
        if ($this->expires_at) {
            $this->update([
                'expires_at' => max($this->expires_at, now())->addMinutes($minutes),
            ]);
        }
    }

    /**
     * Confirm the reservation.
     */
    public function confirm(): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'expires_at' => null,
        ]);
    }

    /**
     * Cancel the reservation.
     */
    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
        
        // Make seat available again
        $this->seat->update(['status' => 'available']);
    }

    /**
     * Mark customer as no-show.
     */
    public function markNoShow(): void
    {
        $this->update(['status' => 'no_show']);
        
        // Make seat available again
        $this->seat->update(['status' => 'available']);
    }

    /**
     * Scope for active reservations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope for expired reservations.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
            ->where('status', 'reserved');
    }

    /**
     * Scope for confirmed reservations.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Clean up expired temporary reservations.
     */
    public static function cleanupExpired(): int
    {
        $expiredReservations = static::expired()->get();
        $count = $expiredReservations->count();

        foreach ($expiredReservations as $reservation) {
            $reservation->cancel();
        }

        return $count;
    }

    /**
     * Get reservation duration in minutes.
     */
    public function getDurationMinutes(): ?int
    {
        if (!$this->reserved_at || !$this->expires_at) {
            return null;
        }

        return $this->reserved_at->diffInMinutes($this->expires_at);
    }

    /**
     * Get remaining time until expiry in minutes.
     */
    public function getRemainingMinutes(): ?int
    {
        if (!$this->expires_at || $this->isExpired()) {
            return null;
        }

        return now()->diffInMinutes($this->expires_at);
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reservation) {
            $reservation->reserved_at = $reservation->reserved_at ?: now();
        });
    }
}