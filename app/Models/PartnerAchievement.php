<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerAchievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'goal_id',
        'achievement_type',
        'title',
        'description',
        'reward_amount',
        'achieved_at',
        'badge_icon',
        'badge_color',
    ];

    protected $casts = [
        'reward_amount' => 'decimal:2',
        'achieved_at' => 'datetime',
    ];

    /**
     * Partner relationship
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Goal relationship
     */
    public function goal(): BelongsTo
    {
        return $this->belongsTo(PartnerGoal::class, 'goal_id');
    }

    /**
     * Get achievement type display name
     */
    public function getAchievementTypeNameAttribute(): string
    {
        return match($this->achievement_type) {
            'goal_completion' => 'إنجاز الهدف',
            'milestone' => 'إنجاز معلم',
            'streak' => 'سلسلة إنجازات',
            'special_event' => 'حدث خاص',
            'performance_bonus' => 'مكافأة أداء',
            default => 'إنجاز عام',
        };
    }

    /**
     * Get badge configuration
     */
    public function getBadgeConfigAttribute(): array
    {
        return [
            'icon' => $this->badge_icon ?? 'heroicon-o-trophy',
            'color' => $this->badge_color ?? 'yellow',
            'css_class' => match($this->badge_color ?? 'yellow') {
                'gold' => 'text-yellow-500',
                'silver' => 'text-gray-400',
                'bronze' => 'text-orange-600',
                'blue' => 'text-blue-500',
                'green' => 'text-green-500',
                'purple' => 'text-purple-500',
                default => 'text-yellow-500',
            }
        ];
    }

    /**
     * Scope for recent achievements
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('achieved_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for specific achievement type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('achievement_type', $type);
    }
}
