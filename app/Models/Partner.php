<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->merchants()
            ->join('bookings', 'merchants.id', '=', 'bookings.merchant_id')
            ->where('bookings.payment_status', 'paid')
            ->sum('bookings.commission_amount');
    }
}
