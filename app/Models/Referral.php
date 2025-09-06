<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'referee_id',
        'referral_program_id',
        'code',
        'is_successful',
        'completed_at',
        'order_id',
        'order_amount',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'is_successful' => 'boolean',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'order_amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    // Relations
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referee()
    {
        return $this->belongsTo(User::class, 'referee_id');
    }

    public function referralProgram()
    {
        return $this->belongsTo(ReferralProgram::class);
    }

    public function order()
    {
        return $this->belongsTo(Booking::class, 'order_id');
    }

    public function rewards()
    {
        return $this->hasMany(ReferralReward::class);
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('is_successful', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_successful', false)
                    ->whereNull('referee_id');
    }

    public function scopeActive($query)
    {
        return $query->where(function($query) {
            $query->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
        });
    }

    // Methods
    public function isValid()
    {
        return !$this->is_successful && 
               (!$this->expires_at || $this->expires_at->isFuture()) &&
               $this->referralProgram->is_active;
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getStatusAttribute()
    {
        if ($this->is_successful) return 'successful';
        if ($this->isExpired()) return 'expired';
        if ($this->referee_id) return 'used';
        return 'pending';
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 'successful': return 'ناجحة';
            case 'expired': return 'منتهية الصلاحية';
            case 'used': return 'مستخدمة';
            case 'pending': return 'في الانتظار';
            default: return 'غير محدد';
        }
    }

    public function getSuccessfulReferralsAttribute()
    {
        return static::where('referrer_id', $this->referrer_id)
                    ->where('referral_program_id', $this->referral_program_id)
                    ->where('is_successful', true)
                    ->count();
    }

    public function getReferrerReward()
    {
        return $this->rewards()->where('type', 'referrer')->first();
    }

    public function getRefereeReward()
    {
        return $this->rewards()->where('type', 'referee')->first();
    }

    public function getTotalRewardValue()
    {
        return $this->rewards()->sum('reward_value');
    }
}
