<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $balance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet query()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWallet whereUserId($value)
 * @mixin \Eloquent
 * @mixin IdeHelperMerchantWallet
 */
class MerchantWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'pending_balance',
        'total_earned',
        'total_withdrawn',
        'last_transaction_at',
        'is_active',
        'additional_data',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'last_transaction_at' => 'datetime',
        'is_active' => 'boolean',
        'additional_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function withdrawRequests()
    {
        return $this->hasMany(MerchantWithdraw::class, 'merchant_id', 'user_id');
    }

    // Helper methods
    public function getAvailableBalanceAttribute(): float
    {
        return max(0, $this->balance - $this->pending_balance);
    }

    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance, 2) . ' ' . config('app.currency', 'USD');
    }

    public function getFormattedAvailableBalanceAttribute(): string
    {
        return number_format($this->available_balance, 2) . ' ' . config('app.currency', 'USD');
    }

    public function canWithdraw(float $amount): bool
    {
        return $this->is_active && $this->available_balance >= $amount && $amount > 0;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithBalance($query)
    {
        return $query->where('balance', '>', 0);
    }
}
