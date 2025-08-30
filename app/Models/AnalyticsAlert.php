<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'alert_type',
        'severity',
        'title',
        'message',
        'metadata',
        'is_active',
        'is_dismissed',
        'triggered_at',
        'dismissed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'is_dismissed' => 'boolean',
        'triggered_at' => 'datetime',
        'dismissed_at' => 'datetime',
    ];

    /**
     * Scope للتنبيهات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_dismissed', false);
    }

    /**
     * Scope للتنبيهات حسب النوع
     */
    public function scopeByType($query, $type)
    {
        return $query->where('alert_type', $type);
    }

    /**
     * Scope للتنبيهات حسب الخطورة
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * إنشاء تنبيه جديد
     */
    public static function createAlert($type, $severity, $title, $message, $metadata = null)
    {
        return static::create([
            'alert_type' => $type,
            'severity' => $severity,
            'title' => $title,
            'message' => $message,
            'metadata' => $metadata,
            'is_active' => true,
            'is_dismissed' => false,
            'triggered_at' => now(),
        ]);
    }

    /**
     * إيقاف التنبيه
     */
    public function dismiss()
    {
        $this->update([
            'is_dismissed' => true,
            'dismissed_at' => now(),
        ]);
    }

    /**
     * إلغاء تفعيل التنبيه
     */
    public function deactivate()
    {
        $this->update([
            'is_active' => false,
        ]);
    }

    /**
     * الحصول على التنبيهات الحرجة
     */
    public static function getCriticalAlerts()
    {
        return static::active()->bySeverity('critical')->latest('triggered_at')->get();
    }

    /**
     * الحصول على عدد التنبيهات النشطة
     */
    public static function getActiveCount()
    {
        return static::active()->count();
    }

    /**
     * تنظيف التنبيهات القديمة
     */
    public static function cleanupOldAlerts($days = 30)
    {
        return static::where('triggered_at', '<', now()->subDays($days))
                    ->where('is_dismissed', true)
                    ->delete();
    }
}
