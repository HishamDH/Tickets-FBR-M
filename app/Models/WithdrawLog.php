<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        return match($this->action) {
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
        return number_format($this->amount, 2) . ' ' . config('app.currency', 'USD');
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
