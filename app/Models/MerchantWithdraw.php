<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperMerchantWithdraw
 * @property int $id
 * @property int $merchant_id
 * @property string $amount
 * @property string $status
 * @property array $bank_details
 * @property string|null $transaction_id
 * @property string|null $notes
 * @property string|null $admin_notes
 * @property \Illuminate\Support\Carbon $requested_at
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $account_holder_name
 * @property-read string|null $account_number
 * @property-read string|null $bank_name
 * @property-read int $days_ago
 * @property-read string $formatted_amount
 * @property-read string|null $iban
 * @property-read string $status_color
 * @property-read string $status_label
 * @property-read string|null $swift_code
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WithdrawLog> $logs
 * @property-read int|null $logs_count
 * @property-read \App\Models\User $merchant
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw approved()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw completed()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw forMerchant($merchantId)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw pending()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw query()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw rejected()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw thisMonth()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw thisYear()
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereAdminNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereBankDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereRequestedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MerchantWithdraw whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MerchantWithdraw extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'amount',
        'status',
        'bank_details',
        'transaction_id',
        'notes',
        'admin_notes',
        'requested_at',
        'processed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'bank_details' => 'array',
        'amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_PROCESSING = 'processing';

    const STATUS_COMPLETED = 'completed';

    const STATUS_REJECTED = 'rejected';

    const STATUS_CANCELLED = 'cancelled';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    // Relationships
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WithdrawLog::class, 'merchant_withdraw_id');
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'info',
            self::STATUS_PROCESSING => 'primary',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_CANCELLED => 'secondary',
            default => 'secondary',
        };
    }

    public function getBankNameAttribute(): ?string
    {
        return $this->bank_details['bank_name'] ?? null;
    }

    public function getAccountNumberAttribute(): ?string
    {
        return $this->bank_details['account_number'] ?? null;
    }

    public function getAccountHolderNameAttribute(): ?string
    {
        return $this->bank_details['account_holder_name'] ?? null;
    }

    public function getIbanAttribute(): ?string
    {
        return $this->bank_details['iban'] ?? null;
    }

    public function getSwiftCodeAttribute(): ?string
    {
        return $this->bank_details['swift_code'] ?? null;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeForMerchant($query, $merchantId)
    {
        return $query->where('merchant_id', $merchantId);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    public function scopeThisYear($query)
    {
        return $query->whereYear('created_at', now()->year);
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function canBeCancelled(): bool
    {
        return $this->isPending();
    }

    public function canBeApproved(): bool
    {
        return $this->isPending();
    }

    public function canBeRejected(): bool
    {
        return $this->isPending() || $this->isApproved();
    }

    public function canBeProcessed(): bool
    {
        return $this->isApproved();
    }

    public function canBeCompleted(): bool
    {
        return $this->isProcessing();
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2).' '.config('app.currency', 'USD');
    }

    public function getDaysAgoAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }
}
