<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'loyalty_program_id',
        'type',
        'points',
        'description',
        'order_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
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

    public function order()
    {
        return $this->belongsTo(Booking::class, 'order_id');
    }

    // Scopes
    public function scopeEarned($query)
    {
        return $query->where('type', 'earn');
    }

    public function scopeRedeemed($query)
    {
        return $query->where('type', 'redeem');
    }

    // Methods
    public function isEarned()
    {
        return $this->type === 'earn';
    }

    public function isRedeemed()
    {
        return $this->type === 'redeem';
    }

    public function getTypeLabel()
    {
        return $this->type === 'earn' ? 'كسب' : 'استرداد';
    }

    public function getPointsDisplayAttribute()
    {
        $prefix = $this->isEarned() ? '+' : '';
        return $prefix . number_format($this->points);
    }
}
