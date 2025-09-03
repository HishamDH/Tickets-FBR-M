<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperWalletTransaction
 */
class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_reference',
        'merchant_wallet_id',
        'booking_id',
        'payment_id',
        'type',
        'category',
        'amount',
        'balance_after',
        'description',
        'metadata',
        'status',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    public function merchantWallet(): BelongsTo
    {
        return $this->belongsTo(MerchantWallet::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'credit' => 'success',
            'debit' => 'warning',
            default => 'secondary',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'commission' => 'Commission',
            'payout' => 'Payout',
            'refund' => 'Refund',
            'adjustment' => 'Adjustment',
            'penalty' => 'Penalty',
            'bonus' => 'Bonus',
            default => 'Unknown',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'completed' => 'success',
            'failed' => 'danger',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        $sign = $this->type === 'credit' ? '+' : '-';

        return $sign.'$'.number_format($this->amount, 2);
    }
}
