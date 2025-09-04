<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $merchant_withdraw_id
 * @property int $merchant_id
 * @property string $action
 * @property string $amount
 * @property string $status
 * @property int $performed_by
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon $action_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $action_color
 * @property-read string $action_label
 * @property-read string $formatted_amount
 * @property-read \App\Models\User $merchant
 * @property-read \App\Models\User $performedBy
 * @property-read \App\Models\MerchantWithdraw $withdrawal
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog byAction($action)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog forMerchant($merchantId)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog forWithdrawal($withdrawalId)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog thisMonth()
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereActionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereMerchantWithdrawId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog wherePerformedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawLog whereUpdatedAt($value)
 * @mixin \Eloquent
 * @mixin IdeHelperWithdrawLog
 */
class WithdrawLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_withdraw_id',
        'merchant_id',
        'action',
        'amount',
        'status',
        'performed_by',
        'notes',
        'action_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'action_date' => 'datetime',
    ];

    // Action constants
    const ACTION_REQUESTED = 'requested';

    const ACTION_APPROVED = 'approved';

    const ACTION_REJECTED = 'rejected';

    const ACTION_PROCESSING = 'processing';

    const ACTION_COMPLETED = 'completed';

    const ACTION_CANCELLED = 'cancelled';

    public static function getActions(): array
    {
        return [
            self::ACTION_REQUESTED => 'Requested',
            self::ACTION_APPROVED => 'Approved',
            self::ACTION_REJECTED => 'Rejected',
            self::ACTION_PROCESSING => 'Processing',
            self::ACTION_COMPLETED => 'Completed',
            self::ACTION_CANCELLED => 'Cancelled',
        ];
    }

    // Relationships
    public function withdrawal(): BelongsTo
    {
        return $this->belongsTo(MerchantWithdraw::class, 'merchant_withdraw_id');
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // Accessors
    public function getActionLabelAttribute(): string
    {
        return self::getActions()[$this->action] ?? ucfirst($this->action);
    }

    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            self::ACTION_REQUESTED => 'info',
            self::ACTION_APPROVED => 'success',
            self::ACTION_REJECTED => 'danger',
            self::ACTION_PROCESSING => 'warning',
            self::ACTION_COMPLETED => 'success',
            self::ACTION_CANCELLED => 'secondary',
            default => 'secondary',
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2).' '.config('app.currency', 'USD');
    }

    // Scopes
    public function scopeForWithdrawal($query, $withdrawalId)
    {
        return $query->where('merchant_withdraw_id', $withdrawalId);
    }

    public function scopeForMerchant($query, $merchantId)
    {
        return $query->where('merchant_id', $merchantId);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    // Helper methods
    public static function logAction($withdrawalId, $merchantId, $action, $amount, $status, $performedBy, $notes = null)
    {
        return self::create([
            'merchant_withdraw_id' => $withdrawalId,
            'merchant_id' => $merchantId,
            'action' => $action,
            'amount' => $amount,
            'status' => $status,
            'performed_by' => $performedBy,
            'notes' => $notes,
            'action_date' => now(),
        ]);
    }
}
