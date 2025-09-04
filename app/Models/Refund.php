<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperRefund
 * @property int $id
 * @property string $refund_reference
 * @property int $payment_id
 * @property int $booking_id
 * @property int $user_id
 * @property string $amount
 * @property string $fee
 * @property string $net_amount
 * @property string $status
 * @property string $type
 * @property string|null $reason
 * @property string|null $admin_notes
 * @property array|null $gateway_response
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Booking $booking
 * @property-read string $status_color
 * @property-read string $status_label
 * @property-read string $type_label
 * @property-read \App\Models\Payment $payment
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Refund newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Refund newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Refund query()
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereGatewayResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereNetAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereRefundReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Refund whereUserId($value)
 * @mixin \Eloquent
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
