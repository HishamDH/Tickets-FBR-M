<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'referral_id',
        'user_id',
        'type',
        'reward_type',
        'reward_value',
        'status',
        'processed_at',
        'metadata',
    ];

    protected $casts = [
        'reward_value' => 'decimal:2',
        'processed_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Relations
    public function referral()
    {
        return $this->belongsTo(Referral::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'processed');
    }

    public function scopeReferrer($query)
    {
        return $query->where('type', 'referrer');
    }

    public function scopeReferee($query)
    {
        return $query->where('type', 'referee');
    }

    // Methods
    public function markAsProcessed()
    {
        $this->update([
            'status' => 'processed',
            'processed_at' => now(),
        ]);
    }

    public function getTypeLabel()
    {
        return $this->type === 'referrer' ? 'المُحيل' : 'المُحال إليه';
    }

    public function getRewardTypeLabel()
    {
        switch ($this->reward_type) {
            case 'fixed': return 'مبلغ ثابت';
            case 'percentage': return 'نسبة مئوية';
            case 'points': return 'نقاط';
            default: return 'غير محدد';
        }
    }

    public function getStatusLabel()
    {
        switch ($this->status) {
            case 'pending': return 'في الانتظار';
            case 'processed': return 'تم التنفيذ';
            case 'cancelled': return 'ملغي';
            default: return 'غير محدد';
        }
    }

    public function getFormattedRewardValue()
    {
        switch ($this->reward_type) {
            case 'fixed':
                return number_format($this->reward_value, 2) . ' ر.س';
            case 'percentage':
                return number_format($this->reward_value, 2) . '%';
            case 'points':
                return number_format($this->reward_value) . ' نقطة';
            default:
                return $this->reward_value;
        }
    }
}
