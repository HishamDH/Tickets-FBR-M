<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'display_name_ar',
        'display_name_en',
        'description',
        'icon',
        'provider',
        'settings',
        'transaction_fee',
        'fee_type',
        'is_active',
        'supports_refund',
        'sort_order',
    ];

    protected $casts = [
        'settings' => 'array',
        'transaction_fee' => 'decimal:2',
        'is_active' => 'boolean',
        'supports_refund' => 'boolean',
    ];

    /**
     * العلاقة مع إعدادات التجار
     */
    public function merchantSettings(): HasMany
    {
        return $this->hasMany(MerchantPaymentSetting::class);
    }

    /**
     * العلاقة مع المدفوعات
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * النطاق - البوابات النشطة فقط
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * النطاق - ترتيب العرض
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * الحصول على الاسم المحلي
     */
    public function getLocalizedNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->display_name_ar : $this->display_name_en;
    }

    /**
     * التحقق من دعم الاسترداد
     */
    public function supportsRefunds(): bool
    {
        return $this->supports_refund;
    }

    /**
     * حساب رسوم المعاملة
     */
    public function calculateFee(float $amount): float
    {
        if ($this->fee_type === 'fixed') {
            return $this->transaction_fee;
        }

        return $amount * ($this->transaction_fee / 100);
    }
}
