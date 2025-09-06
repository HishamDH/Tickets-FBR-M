<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoyaltyProgram extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'points_per_amount',
        'redemption_rate',
        'minimum_points',
        'maximum_points_per_transaction',
        'expiry_months',
        'is_active',
        'merchant_id',
        'settings',
        'tier_benefits',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'tier_benefits' => 'array',
        'points_per_amount' => 'decimal:2',
        'redemption_rate' => 'decimal:2',
    ];

    // Relations
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function points()
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    public function transactions()
    {
        return $this->hasMany(LoyaltyTransaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Methods
    public function calculatePointsForAmount($amount)
    {
        if (!$this->is_active) return 0;
        
        $points = floor($amount * $this->points_per_amount);
        
        if ($this->maximum_points_per_transaction) {
            $points = min($points, $this->maximum_points_per_transaction);
        }
        
        return $points;
    }

    public function calculateDiscountForPoints($points)
    {
        if ($points < $this->minimum_points) return 0;
        
        return $points * $this->redemption_rate;
    }

    public function awardPoints($userId, $amount, $description = null, $orderId = null)
    {
        $points = $this->calculatePointsForAmount($amount);
        
        if ($points <= 0) return null;

        // Create points record
        $loyaltyPoint = LoyaltyPoint::create([
            'user_id' => $userId,
            'loyalty_program_id' => $this->id,
            'points' => $points,
            'expires_at' => $this->expiry_months ? now()->addMonths($this->expiry_months) : null,
        ]);

        // Create transaction record
        LoyaltyTransaction::create([
            'user_id' => $userId,
            'loyalty_program_id' => $this->id,
            'type' => 'earn',
            'points' => $points,
            'description' => $description ?: "نقاط من عملية شراء بقيمة {$amount} ر.س",
            'order_id' => $orderId,
        ]);

        return $loyaltyPoint;
    }

    public function redeemPoints($userId, $points, $description = null, $orderId = null)
    {
        $userTotalPoints = $this->getUserTotalPoints($userId);
        
        if ($userTotalPoints < $points || $points < $this->minimum_points) {
            return false;
        }

        // Deduct points (FIFO - first in, first out)
        $remainingPoints = $points;
        $userPoints = LoyaltyPoint::where('user_id', $userId)
                                  ->where('loyalty_program_id', $this->id)
                                  ->where('points', '>', 0)
                                  ->whereNull('used_at')
                                  ->where(function($query) {
                                      $query->whereNull('expires_at')
                                            ->orWhere('expires_at', '>', now());
                                  })
                                  ->orderBy('created_at')
                                  ->get();

        foreach ($userPoints as $pointRecord) {
            if ($remainingPoints <= 0) break;

            $deductAmount = min($remainingPoints, $pointRecord->points);
            $pointRecord->points -= $deductAmount;
            
            if ($pointRecord->points <= 0) {
                $pointRecord->used_at = now();
            }
            
            $pointRecord->save();
            $remainingPoints -= $deductAmount;
        }

        // Create transaction record
        LoyaltyTransaction::create([
            'user_id' => $userId,
            'loyalty_program_id' => $this->id,
            'type' => 'redeem',
            'points' => -$points,
            'description' => $description ?: "استرداد {$points} نقطة",
            'order_id' => $orderId,
        ]);

        return true;
    }

    public function getUserTotalPoints($userId)
    {
        return LoyaltyPoint::where('user_id', $userId)
                          ->where('loyalty_program_id', $this->id)
                          ->where('points', '>', 0)
                          ->whereNull('used_at')
                          ->where(function($query) {
                              $query->whereNull('expires_at')
                                    ->orWhere('expires_at', '>', now());
                          })
                          ->sum('points');
    }

    public function getUserTier($userId)
    {
        if (!$this->tier_benefits) return null;

        $totalPointsEarned = LoyaltyTransaction::where('user_id', $userId)
                                              ->where('loyalty_program_id', $this->id)
                                              ->where('type', 'earn')
                                              ->sum('points');

        $currentTier = null;
        foreach ($this->tier_benefits as $tier => $benefits) {
            if ($totalPointsEarned >= ($benefits['required_points'] ?? 0)) {
                $currentTier = $tier;
            }
        }

        return $currentTier;
    }
}
