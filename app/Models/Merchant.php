<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
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
