<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerPerformanceMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'year',
        'month',
        'commission_earned',
        'new_merchants_count',
        'active_merchants_count',
        'total_bookings',
        'total_revenue',
        'conversion_rate',
        'avg_revenue_per_merchant',
        'retention_rate',
        'quality_score',
        'performance_score',
    ];

    protected $casts = [
        'commission_earned' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
        'avg_revenue_per_merchant' => 'decimal:2',
        'retention_rate' => 'decimal:2',
        'quality_score' => 'decimal:2',
        'performance_score' => 'decimal:2',
        'new_merchants_count' => 'integer',
        'active_merchants_count' => 'integer',
        'total_bookings' => 'integer',
        'year' => 'integer',
        'month' => 'integer',
    ];

    /**
     * Partner relationship
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Get the period display name
     */
    public function getPeriodNameAttribute(): string
    {
        $monthNames = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        return $monthNames[$this->month] . ' ' . $this->year;
    }

    /**
     * Get performance rating based on score
     */
    public function getPerformanceRatingAttribute(): string
    {
        if ($this->performance_score >= 80) return 'ممتاز';
        if ($this->performance_score >= 60) return 'جيد';
        if ($this->performance_score >= 40) return 'متوسط';
        if ($this->performance_score >= 20) return 'ضعيف';
        
        return 'ضعيف جداً';
    }

    /**
     * Scope for specific year
     */
    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope for specific month
     */
    public function scopeForMonth($query, int $month)
    {
        return $query->where('month', $month);
    }

    /**
     * Scope for date range
     */
    public function scopeForPeriod($query, int $fromYear, int $fromMonth, int $toYear, int $toMonth)
    {
        return $query->where(function($q) use ($fromYear, $fromMonth, $toYear, $toMonth) {
            $q->where('year', '>', $fromYear)
              ->orWhere(function($subQ) use ($fromYear, $fromMonth) {
                  $subQ->where('year', $fromYear)->where('month', '>=', $fromMonth);
              });
        })->where(function($q) use ($toYear, $toMonth) {
            $q->where('year', '<', $toYear)
              ->orWhere(function($subQ) use ($toYear, $toMonth) {
                  $subQ->where('year', $toYear)->where('month', '<=', $toMonth);
              });
        });
    }
}
