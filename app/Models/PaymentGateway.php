<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $display_name_ar
 * @property string $display_name_en
 * @property string|null $description
 * @property string|null $icon
 * @property string|null $provider
 * @property array|null $settings
 * @property string $transaction_fee
 * @property string $fee_type
 * @property bool $is_active
 * @property bool $supports_refund
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $localized_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MerchantPaymentSetting> $merchantSettings
 * @property-read int|null $merchant_settings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway active()
 * @method static \Database\Factories\PaymentGatewayFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway ordered()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereDisplayNameAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereDisplayNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereFeeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereSupportsRefund($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereTransactionFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGateway whereUpdatedAt($value)
 * @mixin \Eloquent
 * @mixin IdeHelperPaymentGateway
 */
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
