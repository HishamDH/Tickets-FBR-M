<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerWalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_wallet_id',
        'transaction_reference',
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

    // أنواع المعاملات
    const TYPE_COMMISSION = 'commission';
    const TYPE_WITHDRAWAL = 'withdrawal';
    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_REFERRAL_BONUS = 'referral_bonus';
    const TYPE_DEDUCTION = 'deduction';

    // فئات المعاملات
    const CATEGORY_MERCHANT_REFERRAL = 'merchant_referral';
    const CATEGORY_BOOKING_COMMISSION = 'booking_commission';
    const CATEGORY_BONUS = 'bonus';
    const CATEGORY_PENALTY = 'penalty';
    const CATEGORY_WITHDRAWAL = 'withdrawal';
    const CATEGORY_ADJUSTMENT = 'adjustment';

    // حالات المعاملات
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    public function partnerWallet(): BelongsTo
    {
        return $this->belongsTo(PartnerWallet::class);
    }

    public static function generateReference(): string
    {
        return 'PWT_' . strtoupper(uniqid());
    }

    // دوال مساعدة للحصول على الخيارات
    public static function getTypes(): array
    {
        return [
            self::TYPE_COMMISSION => 'عمولة',
            self::TYPE_WITHDRAWAL => 'سحب',
            self::TYPE_ADJUSTMENT => 'تعديل',
            self::TYPE_REFERRAL_BONUS => 'مكافأة إحالة',
            self::TYPE_DEDUCTION => 'خصم',
        ];
    }

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_MERCHANT_REFERRAL => 'إحالة تاجر',
            self::CATEGORY_BOOKING_COMMISSION => 'عمولة حجز',
            self::CATEGORY_BONUS => 'مكافأة',
            self::CATEGORY_PENALTY => 'غرامة',
            self::CATEGORY_WITHDRAWAL => 'سحب',
            self::CATEGORY_ADJUSTMENT => 'تعديل',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'في الانتظار',
            self::STATUS_COMPLETED => 'مكتمل',
            self::STATUS_FAILED => 'فاشل',
            self::STATUS_CANCELLED => 'ملغي',
        ];
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }
}
