<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperAnalyticsAlert
 * @property int $id
 * @property string $alert_type
 * @property string $severity
 * @property string $title
 * @property string $message
 * @property array|null $metadata
 * @property bool $is_active
 * @property bool $is_dismissed
 * @property \Illuminate\Support\Carbon $triggered_at
 * @property \Illuminate\Support\Carbon|null $dismissed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert active()
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert bySeverity($severity)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert byType($type)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert query()
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereAlertType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereDismissedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereIsDismissed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereSeverity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereTriggeredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AnalyticsAlert whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
