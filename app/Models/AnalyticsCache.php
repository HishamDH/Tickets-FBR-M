<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsCache extends Model
{
    use HasFactory;

    protected $table = 'analytics_cache';

    protected $fillable = [
        'cache_key',
        'cache_data',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * التحقق من صلاحية الكاش
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * الحصول على البيانات المخزنة
     */
    public function getData()
    {
        if ($this->isExpired()) {
            return null;
        }

        return json_decode($this->cache_data, true);
    }

    /**
     * حفظ البيانات في الكاش
     */
    public static function store($key, $data, $ttl = 3600)
    {
        return static::updateOrCreate(
            ['cache_key' => $key],
            [
                'cache_data' => json_encode($data),
                'expires_at' => now()->addSeconds($ttl),
            ]
        );
    }

    /**
     * الحصول على البيانات من الكاش
     */
    public static function get($key)
    {
        $cache = static::where('cache_key', $key)->first();

        if (! $cache || $cache->isExpired()) {
            return null;
        }

        return $cache->getData();
    }

    /**
     * مسح الكاش المنتهي الصلاحية
     */
    public static function clearExpired()
    {
        return static::where('expires_at', '<', now())->delete();
    }

    /**
     * مسح كاش معين
     */
    public static function forget($key)
    {
        return static::where('cache_key', $key)->delete();
    }
}
