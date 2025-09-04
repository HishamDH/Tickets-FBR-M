<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $item_id
 * @property string $item_type
 * @property float $quantity
 * @property string $discount
 * @property string $code
 * @property string $status
 * @property int $user_id
 * @property string $booking_date
 * @property string|null $booking_time
 * @property int $guest_count
 * @property string $total_amount
 * @property string $payment_status
 * @property string $reservation_status
 * @property string|null $special_requests
 * @property string|null $qr_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $item
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereBookingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereBookingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereGuestCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereQrCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereReservationStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereSpecialRequests($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaidReservation whereUserId($value)
 * @mixin \Eloquent
 * @mixin IdeHelperPaidReservation
 */
class PaidReservation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'item_id',
        'item_type',
        'quantity',
        'price',
        'discount',
        'code',
        'status',
        'additional_data',
        'booking_date',
        'booking_time',
        'guest_count',
        'total_amount',
        'payment_status',
        'reservation_status',
        'special_requests',
        'qr_code',
        'pos_terminal_id',
        'pos_data',
        'printed_at',
        'print_count',
        'is_offline_transaction',
        'offline_transaction_id',
        'synced_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'additional_data' => 'array',
        'pos_data' => 'array',
        'booking_date' => 'date',
        'printed_at' => 'datetime',
        'synced_at' => 'datetime',
        'is_offline_transaction' => 'boolean',
        'print_count' => 'integer',
        'guest_count' => 'integer',
    ];

    /**
     * Get the user that owns the reservation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent item model (polymorphic).
     */
    public function item(): MorphTo
    {
        return $this->morphTo();
    }
}
