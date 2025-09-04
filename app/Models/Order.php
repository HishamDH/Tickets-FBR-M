<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperOrder
 * @property int $id
 * @property int $user_id
 * @property string $order_number
 * @property string $status
 * @property string $payment_status
 * @property string $payment_method
 * @property string $subtotal
 * @property string $discount
 * @property string $total
 * @property string $currency
 * @property array $billing_details
 * @property array|null $shipping_details
 * @property array|null $payment_details
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $shipped_at
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $billing_address
 * @property-read string $billing_name
 * @property-read string $payment_method_label
 * @property-read string $payment_status_color
 * @property-read string $payment_status_label
 * @property-read string $status_color
 * @property-read string $status_label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Order confirmed()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order paid()
 * @method static \Illuminate\Database\Eloquent\Builder|Order pending()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order unpaid()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereBillingDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereOrderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShippedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereShippingDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'discount',
        'total',
        'currency',
        'billing_details',
        'shipping_details',
        'payment_details',
        'notes',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'billing_details' => 'array',
        'shipping_details' => 'array',
        'payment_details' => 'array',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';

    const STATUS_CONFIRMED = 'confirmed';

    const STATUS_PROCESSING = 'processing';

    const STATUS_SHIPPED = 'shipped';

    const STATUS_DELIVERED = 'delivered';

    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_STATUS_PENDING = 'pending';

    const PAYMENT_STATUS_COMPLETED = 'completed';

    const PAYMENT_STATUS_FAILED = 'failed';

    const PAYMENT_STATUS_REFUNDED = 'refunded';

    const PAYMENT_STATUS_PENDING_COD = 'pending_cod';

    const PAYMENT_METHOD_STRIPE = 'stripe';

    const PAYMENT_METHOD_PAYPAL = 'paypal';

    const PAYMENT_METHOD_BANK_TRANSFER = 'bank_transfer';

    const PAYMENT_METHOD_CASH_ON_DELIVERY = 'cash_on_delivery';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SHIPPED => 'Shipped',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Cancelled',
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        $labels = [
            self::PAYMENT_STATUS_PENDING => 'Pending Payment',
            self::PAYMENT_STATUS_COMPLETED => 'Paid',
            self::PAYMENT_STATUS_FAILED => 'Failed',
            self::PAYMENT_STATUS_REFUNDED => 'Refunded',
            self::PAYMENT_STATUS_PENDING_COD => 'Cash on Delivery',
        ];

        return $labels[$this->payment_status] ?? 'Unknown';
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        $labels = [
            self::PAYMENT_METHOD_STRIPE => 'Credit Card (Stripe)',
            self::PAYMENT_METHOD_PAYPAL => 'PayPal',
            self::PAYMENT_METHOD_BANK_TRANSFER => 'Bank Transfer',
            self::PAYMENT_METHOD_CASH_ON_DELIVERY => 'Cash on Delivery',
        ];

        return $labels[$this->payment_method] ?? 'Unknown';
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            self::STATUS_PENDING => 'yellow',
            self::STATUS_CONFIRMED => 'blue',
            self::STATUS_PROCESSING => 'purple',
            self::STATUS_SHIPPED => 'indigo',
            self::STATUS_DELIVERED => 'green',
            self::STATUS_CANCELLED => 'red',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getPaymentStatusColorAttribute(): string
    {
        $colors = [
            self::PAYMENT_STATUS_PENDING => 'yellow',
            self::PAYMENT_STATUS_COMPLETED => 'green',
            self::PAYMENT_STATUS_FAILED => 'red',
            self::PAYMENT_STATUS_REFUNDED => 'orange',
            self::PAYMENT_STATUS_PENDING_COD => 'blue',
        ];

        return $colors[$this->payment_status] ?? 'gray';
    }

    public function getBillingNameAttribute(): string
    {
        $billing = $this->billing_details ?? [];

        return trim(($billing['first_name'] ?? '').' '.($billing['last_name'] ?? ''));
    }

    public function getBillingAddressAttribute(): string
    {
        $billing = $this->billing_details ?? [];
        $parts = array_filter([
            $billing['address'] ?? '',
            $billing['city'] ?? '',
            $billing['state'] ?? '',
            $billing['postal_code'] ?? '',
        ]);

        return implode(', ', $parts);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_COMPLETED;
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_STATUS_COMPLETED);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', '!=', self::PAYMENT_STATUS_COMPLETED);
    }
}
