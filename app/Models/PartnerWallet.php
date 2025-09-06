<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnerWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'balance',
        'pending_balance',
        'total_earned',
        'total_withdrawn',
        'commission_rate',
        'withdrawal_limit_daily',
        'withdrawal_limit_monthly',
        'minimum_withdrawal',
        'is_active',
        'auto_withdraw',
        'auto_withdraw_threshold',
        'bank_name',
        'bank_account_number',
        'bank_routing_number',
        'account_holder_name',
        'swift_code',
        'require_verification',
        'verification_method',
        'last_transaction_at',
        'last_withdrawal_at',
        'transaction_count',
        'withdrawal_count',
        'referral_count',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'withdrawal_limit_daily' => 'decimal:2',
        'withdrawal_limit_monthly' => 'decimal:2',
        'minimum_withdrawal' => 'decimal:2',
        'auto_withdraw_threshold' => 'decimal:2',
        'is_active' => 'boolean',
        'auto_withdraw' => 'boolean',
        'require_verification' => 'boolean',
        'last_transaction_at' => 'datetime',
        'last_withdrawal_at' => 'datetime',
    ];

    // العلاقات
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PartnerWalletTransaction::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(PartnerWithdraw::class);
    }

    // الدوال المساعدة
    public function getAvailableBalanceAttribute(): float
    {
        return $this->balance - $this->pending_balance;
    }

    public function canWithdraw(float $amount): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($amount < $this->minimum_withdrawal) {
            return false;
        }

        if ($this->available_balance < $amount) {
            return false;
        }

        // فحص الحد اليومي
        $todayWithdrawals = $this->withdrawals()
            ->whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        if (($todayWithdrawals + $amount) > $this->withdrawal_limit_daily) {
            return false;
        }

        // فحص الحد الشهري
        $monthlyWithdrawals = $this->withdrawals()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', '!=', 'cancelled')
            ->sum('amount');

        if (($monthlyWithdrawals + $amount) > $this->withdrawal_limit_monthly) {
            return false;
        }

        return true;
    }

    public function addCommission(float $amount, string $description = null, array $metadata = []): void
    {
        $this->increment('balance', $amount);
        $this->increment('total_earned', $amount);
        $this->increment('transaction_count');
        $this->update(['last_transaction_at' => now()]);

        // إنشاء سجل المعاملة
        $this->transactions()->create([
            'type' => 'commission',
            'amount' => $amount,
            'balance_after' => $this->balance,
            'description' => $description ?? 'عمولة من إحالة تاجر',
            'metadata' => $metadata,
            'status' => 'completed',
        ]);

        // فحص السحب التلقائي
        if ($this->auto_withdraw && 
            $this->auto_withdraw_threshold && 
            $this->available_balance >= $this->auto_withdraw_threshold) {
            $this->triggerAutoWithdraw();
        }
    }

    public function deductAmount(float $amount, string $description = null): void
    {
        $this->decrement('balance', $amount);
        $this->increment('transaction_count');
        $this->update(['last_transaction_at' => now()]);

        $this->transactions()->create([
            'type' => 'deduction',
            'amount' => $amount,
            'balance_after' => $this->balance,
            'description' => $description ?? 'خصم من المحفظة',
            'status' => 'completed',
        ]);
    }

    private function triggerAutoWithdraw(): void
    {
        // منطق السحب التلقائي - سيتم تطويره لاحقاً
        // يمكن إضافة job للسحب التلقائي هنا
    }

    public function getMonthlyCommissions(int $year = null, int $month = null): float
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        return $this->transactions()
            ->where('type', 'commission')
            ->where('status', 'completed')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->sum('amount');
    }

    public function getTotalReferralCommissions(): float
    {
        return $this->transactions()
            ->where('type', 'commission')
            ->where('status', 'completed')
            ->sum('amount');
    }
}
