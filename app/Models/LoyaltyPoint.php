<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'loyalty_program_id',
        'points',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loyaltyProgram()
    {
        return $this->belongsTo(LoyaltyProgram::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('points', '>', 0)
                    ->whereNull('used_at')
                    ->where(function($query) {
                        $query->whereNull('expires_at')
                              ->orWhere('expires_at', '>', now());
                    });
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
                    ->where('expires_at', '<=', now());
    }

    public function scopeUsed($query)
    {
        return $query->whereNotNull('used_at')
                    ->orWhere('points', '<=', 0);
    }

    // Methods
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isUsed()
    {
        return $this->used_at || $this->points <= 0;
    }

    public function isActive()
    {
        return !$this->isExpired() && !$this->isUsed() && $this->points > 0;
    }

    public function getStatusAttribute()
    {
        if ($this->isUsed()) return 'used';
        if ($this->isExpired()) return 'expired';
        return 'active';
    }

    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 'used': return 'مستخدم';
            case 'expired': return 'منتهي الصلاحية';
            case 'active': return 'نشط';
            default: return 'غير محدد';
        }
    }
}
