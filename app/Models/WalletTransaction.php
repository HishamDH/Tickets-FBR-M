<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $transaction_reference
 * @property int $merchant_wallet_id
 * @property int|null $booking_id
 * @property int|null $payment_id
 * @property string $type
 * @property string $category
 * @property string $amount
 * @property string $balance_after
 * @property string|null $description
 * @property array|null $metadata
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Booking|null $booking
 * @property-read string $category_label
 * @property-read string $formatted_amount
 * @property-read string $status_color
 * @property-read string $type_color
 * @property-read \App\Models\MerchantWallet $merchantWallet
 * @property-read \App\Models\Payment|null $payment
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereBalanceAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereMerchantWalletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereTransactionReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletTransaction whereUpdatedAt($value)
 * @mixin \Eloquent
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
