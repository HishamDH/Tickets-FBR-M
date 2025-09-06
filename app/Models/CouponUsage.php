<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'user_id',
        'order_id',
        'amount',
        'discount_amount',
        'used_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'used_at' => 'datetime',
    ];

    // Relations
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Booking::class, 'order_id');
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' ر.س';
    }

    public function getFormattedDiscountAttribute()
    {
        return number_format($this->discount_amount, 2) . ' ر.س';
    }

    public function getSavingsPercentageAttribute()
    {
        if (!$this->amount || $this->amount == 0) return 0;
        return round(($this->discount_amount / $this->amount) * 100, 1);
    }
}
