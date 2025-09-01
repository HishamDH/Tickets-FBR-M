<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return trim(($billing['first_name'] ?? '') . ' ' . ($billing['last_name'] ?? ''));
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