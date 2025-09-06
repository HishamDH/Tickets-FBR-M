<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'used_count',
        'user_limit',
        'starts_at',
        'expires_at',
        'is_active',
        'merchant_id',
        'service_id',
        'applicable_to',
        'created_by',
        'settings'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'settings' => 'array',
        'value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
    ];

    // Relations
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('starts_at', '<=', now())
                    ->where('expires_at', '>=', now());
    }

    public function scopeForMerchant($query, $merchantId)
    {
        return $query->where('merchant_id', $merchantId);
    }

    public function scopeForService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function scopePublic($query)
    {
        return $query->whereNull('merchant_id')->whereNull('service_id');
    }

    // Accessors & Mutators
    public function getFormattedValueAttribute()
    {
        if ($this->type === 'percentage') {
            return $this->value . '%';
        }
        return number_format($this->value, 2) . ' ر.س';
    }

    public function getIsValidAttribute()
    {
        return $this->is_active 
            && $this->starts_at <= now() 
            && $this->expires_at >= now()
            && ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    public function getUsagePercentageAttribute()
    {
        if (!$this->usage_limit) return 0;
        return round(($this->used_count / $this->usage_limit) * 100, 1);
    }

    public function getRemainingUsesAttribute()
    {
        if (!$this->usage_limit) return 'غير محدود';
        return max(0, $this->usage_limit - $this->used_count);
    }

    // Methods
    public function isValidForUser($userId = null)
    {
        if (!$this->is_valid) return false;

        if ($userId && $this->user_limit) {
            $userUsages = $this->usages()->where('user_id', $userId)->count();
            if ($userUsages >= $this->user_limit) return false;
        }

        return true;
    }

    public function canBeUsedBy($userId, $amount = 0, $serviceId = null)
    {
        // Check if coupon is valid
        if (!$this->isValidForUser($userId)) return false;

        // Check minimum amount
        if ($this->minimum_amount && $amount < $this->minimum_amount) return false;

        // Check service restriction
        if ($this->service_id && $serviceId !== $this->service_id) return false;

        return true;
    }

    public function calculateDiscount($amount)
    {
        if ($this->type === 'percentage') {
            $discount = ($amount * $this->value) / 100;
            
            // Apply maximum discount limit
            if ($this->maximum_discount) {
                $discount = min($discount, $this->maximum_discount);
            }
        } else {
            // Fixed amount discount
            $discount = min($this->value, $amount);
        }

        return round($discount, 2);
    }

    public function use($userId, $amount = null, $orderId = null)
    {
        // Record usage
        $this->usages()->create([
            'user_id' => $userId,
            'amount' => $amount,
            'order_id' => $orderId,
            'discount_amount' => $amount ? $this->calculateDiscount($amount) : null,
            'used_at' => now(),
        ]);

        // Increment used count
        $this->increment('used_count');

        return $this;
    }

    public function generateCode()
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    // Static methods
    public static function findByCode($code)
    {
        return static::where('code', strtoupper($code))->first();
    }

    public static function getActiveForService($serviceId)
    {
        return static::active()
                     ->where(function($query) use ($serviceId) {
                         $query->where('service_id', $serviceId)
                               ->orWhere('applicable_to', 'all')
                               ->orWhereNull('service_id');
                     })
                     ->get();
    }

    public static function getPublicCoupons()
    {
        return static::active()
                     ->whereNull('merchant_id')
                     ->whereNull('service_id')
                     ->get();
    }
}
