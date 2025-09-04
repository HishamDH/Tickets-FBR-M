<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $business_name
 * @property string $business_type
 * @property string $cr_number
 * @property string|null $business_address
 * @property string $city
 * @property string $verification_status
 * @property string $commission_rate
 * @property int|null $partner_id
 * @property int|null $account_manager_id
 * @property array|null $settings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $accountManager
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read mixed $slug
 * @property-read mixed $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Offering> $offerings
 * @property-read int|null $offerings_count
 * @property-read \App\Models\Partner|null $partner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MerchantPaymentSetting> $paymentSettings
 * @property-read int|null $payment_settings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Service> $services
 * @property-read int|null $services_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\MerchantFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereAccountManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereBusinessAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereBusinessName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereBusinessType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereCrNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant wherePartnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Merchant whereVerificationStatus($value)
 * @mixin \Eloquent
 * @mixin IdeHelperMerchant
 */
class Merchant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'business_type',
        'cr_number',
        'business_address',
        'city',
        'verification_status',
        'commission_rate',
        'partner_id',
        'account_manager_id',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'commission_rate' => 'decimal:2',
    ];

    /**
     * User relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Services relationship
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Offerings relationship (alias for services)
     */
    public function offerings(): HasMany
    {
        return $this->hasMany(Offering::class, 'user_id', 'user_id');
    }

    /**
     * Bookings relationship
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Partner relationship
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Account manager relationship
     */
    public function accountManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'account_manager_id');
    }

    /**
     * Payment settings relationship
     */
    public function paymentSettings(): HasMany
    {
        return $this->hasMany(MerchantPaymentSetting::class);
    }

    /**
     * Payments relationship
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Check if merchant is verified
     */
    public function isVerified(): bool
    {
        return $this->verification_status === 'approved';
    }

    /**
     * Status accessor - maps verification_status to status
     */
    public function getStatusAttribute()
    {
        return $this->verification_status === 'approved' ? 'active' : 'inactive';
    }

    /**
     * Slug accessor - simple slug generation
     */
    public function getSlugAttribute()
    {
        return $this->id; // For now, just use ID as slug
    }

    /**
     * Get merchant's total revenue
     */
    public function getTotalRevenue(): float
    {
        return $this->bookings()
            ->where('payment_status', 'paid')
            ->sum('total_amount');
    }
}
