<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnerGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'goal_type',
        'target_value',
        'current_value',
        'target_date',
        'description',
        'reward_amount',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'reward_amount' => 'decimal:2',
        'target_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Partner relationship
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Achievements relationship
     */
    public function achievements(): HasMany
    {
        return $this->hasMany(PartnerAchievement::class, 'goal_id');
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_value <= 0) return 0;
        
        return round(min(($this->current_value / $this->target_value) * 100, 100), 2);
    }

    /**
     * Get goal type display name
     */
    public function getGoalTypeNameAttribute(): string
    {
        return match($this->goal_type) {
            'new_merchants' => 'التجار الجدد',
            'commission_earned' => 'العمولة المكتسبة',
            'total_revenue' => 'إجمالي الإيرادات',
            'active_merchants' => 'التجار النشطون',
            'conversion_rate' => 'معدل التحويل',
            'quality_score' => 'نقاط الجودة',
            default => 'هدف عام',
        };
    }

    /**
     * Get status display name
     */
    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'expired' => 'منتهي الصلاحية',
            'cancelled' => 'ملغي',
            default => 'غير محدد',
        };
    }

    /**
     * Check if goal is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if goal is expired
     */
    public function isExpired(): bool
    {
        return $this->target_date && $this->target_date->isPast() && $this->status !== 'completed';
    }

    /**
     * Get remaining days to target
     */
    public function getRemainingDaysAttribute(): int
    {
        if (!$this->target_date || $this->isCompleted()) return 0;
        
        return max(0, now()->diffInDays($this->target_date, false));
    }

    /**
     * Scope for active goals
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for completed goals
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for expired goals
     */
    public function scopeExpired($query)
    {
        return $query->where('target_date', '<', now())
                    ->where('status', '!=', 'completed');
    }
}
