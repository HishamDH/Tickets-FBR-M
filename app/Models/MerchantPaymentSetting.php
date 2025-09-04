<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperMerchantPaymentSetting
 * @property int $id
 * @property int $merchant_id
 * @property int $payment_gateway_id
 * @property bool $is_enabled
 * @property array|null $gateway_credentials
 * @property string|null $custom_fee
 * @property string|null $custom_fee_type
 * @property int $display_order
 * @property array|null $additional_settings
 * @property \Illuminate\Support\Carbon|null $last_tested_at
 * @property bool $test_passed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Merchant $merchant
 * @property-read \App\Models\PaymentGateway $paymentGateway
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting enabled()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting ordered()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereAdditionalSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereCustomFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereCustomFeeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereDisplayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereGatewayCredentials($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereIsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereLastTestedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting wherePaymentGatewayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereTestPassed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantPaymentSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MerchantPaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'payment_gateway_id',
        'is_enabled',
        'gateway_credentials',
        'custom_fee',
        'custom_fee_type',
        'display_order',
        'additional_settings',
        'last_tested_at',
        'test_passed',
    ];

    protected $casts = [
        'gateway_credentials' => 'array',
        'additional_settings' => 'array',
        'is_enabled' => 'boolean',
        'custom_fee' => 'decimal:2',
        'test_passed' => 'boolean',
        'last_tested_at' => 'datetime',
    ];

    /**
     * العلاقة مع التاجر
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * العلاقة مع بوابة الدفع
     */
    public function paymentGateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    /**
     * النطاق - الإعدادات المفعلة فقط
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * النطاق - ترتيب العرض
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('id');
    }

    /**
     * تشفير بيانات الاتصال قبل الحفظ
     */
    public function setGatewayCredentialsAttribute($value)
    {
        if (is_array($value) && ! empty($value)) {
            $this->attributes['gateway_credentials'] = json_encode($value);
        } else {
            $this->attributes['gateway_credentials'] = null;
        }
    }

    /**
     * فك تشفير بيانات الاتصال عند القراءة
     */
    public function getGatewayCredentialsAttribute($value)
    {
        if (empty($value)) {
            return [];
        }

        try {
            return json_decode($value, true) ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * حساب الرسوم المخصصة للتاجر
     */
    public function calculateCustomFee(float $amount): float
    {
        if (! $this->custom_fee) {
            return $this->paymentGateway->calculateFee($amount);
        }

        if ($this->custom_fee_type === 'fixed') {
            return $this->custom_fee;
        }

        return $amount * ($this->custom_fee / 100);
    }

    /**
     * التحقق من صحة الإعدادات
     */
    public function isConfigured(): bool
    {
        return $this->is_enabled &&
               ! empty($this->gateway_credentials) &&
               $this->test_passed;
    }
}
