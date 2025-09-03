<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperRefund
 */
class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'refund_reference',
        'payment_id',
        'booking_id',
        'user_id',
        'amount',
        'fee',
        'net_amount',
        'status',
        'type',
        'reason',
        'admin_notes',
        'gateway_response',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'gateway_response' => 'array',
        'processed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($refund) {
            if (! $refund->refund_reference) {
                $refund->refund_reference = 'REF-'.strtoupper(uniqid());
            }

            if (! $refund->net_amount) {
                $refund->net_amount = $refund->amount - $refund->fee;
            }
        });
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            default => 'Unknown',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'full' => 'Full Refund',
            'partial' => 'Partial Refund',
            'service_fee' => 'Service Fee Only',
            default => 'Unknown',
        };
    }
}
