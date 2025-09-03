<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperAnalytics
 */
class Analytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'metric_name',
        'metric_type',
        'metric_data',
        'period',
        'metric_date',
        'recorded_at',
    ];

    protected $casts = [
        'metric_data' => 'array',
        'metric_date' => 'date',
        'recorded_at' => 'datetime',
    ];

    /**
     * Scope للبحث حسب نوع المقياس
     */
    public function scopeByType($query, $type)
    {
        return $query->where('metric_type', $type);
    }

    /**
     * Scope للبحث حسب الفترة
     */
    public function scopeByPeriod($query, $period)
    {
        return $query->where('period', $period);
    }

    /**
     * Scope للبحث حسب اسم المقياس
     */
    public function scopeByMetric($query, $metricName)
    {
        return $query->where('metric_name', $metricName);
    }

    /**
     * Scope للبحث حسب التاريخ
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('metric_date', [$startDate, $endDate]);
    }

    /**
     * الحصول على أحدث البيانات لمقياس معين
     */
    public static function getLatestMetric($metricName, $period = 'daily')
    {
        return static::where('metric_name', $metricName)
            ->where('period', $period)
            ->latest('metric_date')
            ->first();
    }

    /**
     * الحصول على البيانات التاريخية لمقياس معين
     */
    public static function getHistoricalData($metricName, $period = 'daily', $days = 30)
    {
        $startDate = now()->subDays($days)->toDateString();
        $endDate = now()->toDateString();

        return static::where('metric_name', $metricName)
            ->where('period', $period)
            ->whereBetween('metric_date', [$startDate, $endDate])
            ->orderBy('metric_date')
            ->get();
    }

    /**
     * حفظ مقياس جديد
     */
    public static function recordMetric($metricName, $metricType, $data, $period = 'daily', $date = null)
    {
        return static::create([
            'metric_name' => $metricName,
            'metric_type' => $metricType,
            'metric_data' => $data,
            'period' => $period,
            'metric_date' => $date ?? now()->toDateString(),
            'recorded_at' => now(),
        ]);
    }
}
