<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $user_id
 * @property string $partner_code
 * @property string $business_name
 * @property string $contact_person
 * @property string $commission_rate
 * @property string $status
 * @property array|null $settings
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Merchant> $merchants
 * @property-read int|null $merchants_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Partner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Partner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Partner query()
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereBusinessName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereCommissionRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereContactPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner wherePartnerCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereUserId($value)
 * @mixin \Eloquent
 * @mixin IdeHelperPartner
 */
class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'partner_code',
        'business_name',
        'contact_person',
        'commission_rate',
        'status',
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
     * Merchants referred by this partner
     */
    public function merchants(): HasMany
    {
        return $this->hasMany(Merchant::class);
    }

    /**
     * Partner wallet
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(PartnerWallet::class);
    }

    /**
     * Partner referral invitations
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(PartnerInvitation::class);
    }

    /**
     * Partner performance metrics
     */
    public function metrics(): HasMany
    {
        return $this->hasMany(PartnerPerformanceMetric::class);
    }

    /**
     * Partner goals
     */
    public function goals(): HasMany
    {
        return $this->hasMany(PartnerGoal::class);
    }

    /**
     * Partner achievements
     */
    public function achievements(): HasMany
    {
        return $this->hasMany(PartnerAchievement::class);
    }

    /**
     * Scheduled reports
     */
    public function scheduledReports(): HasMany
    {
        return $this->hasMany(ScheduledReport::class);
    }

    /**
     * Check if partner is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get total commission earned
     */
    public function getTotalCommission(): float
    {
        return $this->wallet ? $this->wallet->total_earned : 0;
    }

    /**
     * Get available balance
     */
    public function getAvailableBalance(): float
    {
        return $this->wallet ? $this->wallet->available_balance : 0;
    }

    /**
     * Create wallet if not exists
     */
    public function getOrCreateWallet(): PartnerWallet
    {
        if (!$this->wallet) {
            $this->wallet()->create([
                'commission_rate' => $this->commission_rate,
            ]);
            $this->load('wallet');
        }
        
        return $this->wallet;
    }

    /**
     * Generate unique referral code
     */
    public static function generateReferralCode(): string
    {
        do {
            $code = 'REF_' . strtoupper(substr(uniqid(), -8));
        } while (self::where('partner_code', $code)->exists());
        
        return $code;
    }

    /**
     * Get referral link
     */
    public function getReferralLink(): string
    {
        return url('/register/merchant?ref=' . $this->partner_code);
    }

    /**
     * Calculate commission for a booking
     */
    public function calculateCommission(float $bookingAmount): float
    {
        return ($bookingAmount * $this->commission_rate) / 100;
    }
}
