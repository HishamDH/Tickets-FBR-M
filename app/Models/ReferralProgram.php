<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ReferralProgram extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'referrer_reward_type',
        'referrer_reward_value',
        'referee_reward_type',
        'referee_reward_value',
        'minimum_order_amount',
        'maximum_uses_per_referrer',
        'maximum_total_uses',
        'validity_days',
        'is_active',
        'merchant_id',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'referrer_reward_value' => 'decimal:2',
        'referee_reward_value' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
    ];

    // Relations
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Methods
    public function generateReferralCode($userId)
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Referral::where('code', $code)->exists());

        return Referral::create([
            'referrer_id' => $userId,
            'referral_program_id' => $this->id,
            'code' => $code,
            'expires_at' => $this->validity_days ? now()->addDays($this->validity_days) : null,
        ]);
    }

    public function processReferral($referralCode, $refereeId, $orderId = null, $orderAmount = null)
    {
        $referral = Referral::where('code', $referralCode)
                           ->where('referral_program_id', $this->id)
                           ->first();

        if (!$referral || !$referral->isValid()) {
            return false;
        }

        // Check minimum order amount
        if ($orderAmount && $this->minimum_order_amount && $orderAmount < $this->minimum_order_amount) {
            return false;
        }

        // Check maximum uses
        if ($this->maximum_uses_per_referrer && $referral->successful_referrals >= $this->maximum_uses_per_referrer) {
            return false;
        }

        if ($this->maximum_total_uses && $this->referrals()->where('is_successful', true)->count() >= $this->maximum_total_uses) {
            return false;
        }

        // Update referral
        $referral->update([
            'referee_id' => $refereeId,
            'is_successful' => true,
            'completed_at' => now(),
            'order_id' => $orderId,
            'order_amount' => $orderAmount,
        ]);

        // Process rewards
        $this->processReferrerReward($referral);
        $this->processRefereeReward($referral);

        return $referral;
    }

    protected function processReferrerReward($referral)
    {
        $reward = $this->calculateReward($this->referrer_reward_type, $this->referrer_reward_value, $referral->order_amount);
        
        if ($reward > 0) {
            ReferralReward::create([
                'referral_id' => $referral->id,
                'user_id' => $referral->referrer_id,
                'type' => 'referrer',
                'reward_type' => $this->referrer_reward_type,
                'reward_value' => $reward,
                'status' => 'pending',
            ]);
        }
    }

    protected function processRefereeReward($referral)
    {
        $reward = $this->calculateReward($this->referee_reward_type, $this->referee_reward_value, $referral->order_amount);
        
        if ($reward > 0) {
            ReferralReward::create([
                'referral_id' => $referral->id,
                'user_id' => $referral->referee_id,
                'type' => 'referee',
                'reward_type' => $this->referee_reward_type,
                'reward_value' => $reward,
                'status' => 'pending',
            ]);
        }
    }

    protected function calculateReward($type, $value, $orderAmount = null)
    {
        switch ($type) {
            case 'fixed':
                return $value;
            case 'percentage':
                return $orderAmount ? ($orderAmount * $value / 100) : 0;
            case 'points':
                return $value;
            default:
                return 0;
        }
    }

    public function getTotalReferrals()
    {
        return $this->referrals()->where('is_successful', true)->count();
    }

    public function getTotalRewards()
    {
        return ReferralReward::whereIn('referral_id', $this->referrals()->pluck('id'))->sum('reward_value');
    }

    public function getConversionRate()
    {
        $total = $this->referrals()->count();
        $successful = $this->referrals()->where('is_successful', true)->count();
        
        return $total > 0 ? ($successful / $total) * 100 : 0;
    }
}
