<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerWithdraw extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_wallet_id',
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
        'amount' => 'decimal:2',
        'bank_details' => 'array',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // حالات طلب السحب
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    public function partnerWallet(): BelongsTo
    {
        return $this->belongsTo(PartnerWallet::class);
    }

    public function partner()
    {
        return $this->hasOneThrough(Partner::class, PartnerWallet::class, 'id', 'id', 'partner_wallet_id', 'partner_id');
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'في الانتظار',
            self::STATUS_APPROVED => 'معتمد',
            self::STATUS_PROCESSING => 'قيد المعالجة',
            self::STATUS_COMPLETED => 'مكتمل',
            self::STATUS_REJECTED => 'مرفوض',
            self::STATUS_CANCELLED => 'ملغي',
        ];
    }

    // دوال فحص الحالة
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

    // دوال فحص إمكانية تغيير الحالة
    public function canBeApproved(): bool
    {
        return $this->isPending();
    }

    public function canBeRejected(): bool
    {
        return $this->isPending();
    }

    public function canBeProcessed(): bool
    {
        return $this->isApproved();
    }

    public function canBeCompleted(): bool
    {
        return $this->isProcessing();
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED]);
    }

    // دوال العمليات
    public function approve(string $adminNotes = null): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_APPROVED,
            'admin_notes' => $adminNotes,
            'processed_at' => now(),
        ]);

        return true;
    }

    public function reject(string $adminNotes): bool
    {
        if (!$this->canBeRejected()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_REJECTED,
            'admin_notes' => $adminNotes,
            'processed_at' => now(),
        ]);

        // إرجاع المبلغ للمحفظة
        $this->partnerWallet->increment('balance', $this->amount);
        $this->partnerWallet->decrement('pending_balance', $this->amount);

        return true;
    }

    public function markAsProcessing(string $transactionId): bool
    {
        if (!$this->canBeProcessed()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_PROCESSING,
            'transaction_id' => $transactionId,
        ]);

        return true;
    }

    public function markAsCompleted(): bool
    {
        if (!$this->canBeCompleted()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_COMPLETED,
            'processed_at' => now(),
        ]);

        // تحديث إحصائيات المحفظة
        $wallet = $this->partnerWallet;
        $wallet->decrement('pending_balance', $this->amount);
        $wallet->increment('total_withdrawn', $this->amount);
        $wallet->increment('withdrawal_count');
        $wallet->update(['last_withdrawal_at' => now()]);

        // إنشاء معاملة في المحفظة
        $wallet->transactions()->create([
            'transaction_reference' => PartnerWalletTransaction::generateReference(),
            'type' => PartnerWalletTransaction::TYPE_WITHDRAWAL,
            'category' => PartnerWalletTransaction::CATEGORY_WITHDRAWAL,
            'amount' => $this->amount,
            'balance_after' => $wallet->balance,
            'description' => "سحب مبلغ {$this->amount} دولار",
            'metadata' => [
                'withdrawal_id' => $this->id,
                'transaction_id' => $this->transaction_id,
            ],
            'status' => PartnerWalletTransaction::STATUS_COMPLETED,
            'processed_at' => now(),
        ]);

        return true;
    }

    public function cancel(string $reason = null): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_CANCELLED,
            'admin_notes' => $reason,
            'cancelled_at' => now(),
        ]);

        // إرجاع المبلغ للمحفظة
        $this->partnerWallet->increment('balance', $this->amount);
        $this->partnerWallet->decrement('pending_balance', $this->amount);

        return true;
    }

    // Accessors
    public function getDaysAgoAttribute(): int
    {
        return $this->requested_at->diffInDays(now());
    }
}
