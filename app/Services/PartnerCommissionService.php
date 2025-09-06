<?php

namespace App\Services;

use App\Models\Partner;
use App\Models\PartnerWallet;
use App\Models\PartnerWalletTransaction;
use App\Models\Merchant;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PartnerCommissionService
{
    /**
     * حساب وإضافة عمولة للشريك من حجز
     */
    public function processBookingCommission(Booking $booking): bool
    {
        try {
            // التحقق من وجود تاجر وشريك
            $merchant = $booking->merchant;
            if (!$merchant || !$merchant->partner_id) {
                return false;
            }

            $partner = $merchant->partner;
            if (!$partner || !$partner->isActive()) {
                return false;
            }

            // التحقق من أن الحجز مدفوع
            if ($booking->payment_status !== 'paid') {
                return false;
            }

            // حساب العمولة
            $commissionAmount = $this->calculateBookingCommission($booking, $partner);
            
            if ($commissionAmount <= 0) {
                return false;
            }

            // إضافة العمولة للمحفظة
            return $this->addCommissionToWallet(
                $partner,
                $commissionAmount,
                "عمولة من حجز #{$booking->id} - {$merchant->business_name}",
                [
                    'booking_id' => $booking->id,
                    'merchant_id' => $merchant->id,
                    'booking_amount' => $booking->total_amount,
                    'commission_rate' => $partner->commission_rate,
                    'type' => 'booking_commission'
                ]
            );

        } catch (\Exception $e) {
            Log::error('خطأ في معالجة عمولة الشريك', [
                'booking_id' => $booking->id,
                'partner_id' => $partner->id ?? null,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * حساب عمولة الحجز
     */
    private function calculateBookingCommission(Booking $booking, Partner $partner): float
    {
        $baseAmount = $booking->total_amount;
        $commissionRate = $partner->commission_rate;
        
        return ($baseAmount * $commissionRate) / 100;
    }

    /**
     * إضافة عمولة إحالة تاجر جديد
     */
    public function processNewMerchantReferral(Partner $partner, Merchant $merchant): bool
    {
        try {
            // مكافأة إحالة التاجر (مبلغ ثابت)
            $referralBonus = config('partner.new_merchant_bonus', 100.00);
            
            return $this->addCommissionToWallet(
                $partner,
                $referralBonus,
                "مكافأة إحالة التاجر: {$merchant->business_name}",
                [
                    'merchant_id' => $merchant->id,
                    'type' => 'merchant_referral',
                    'bonus_type' => 'new_merchant'
                ]
            );

        } catch (\Exception $e) {
            Log::error('خطأ في معالجة مكافأة إحالة التاجر', [
                'partner_id' => $partner->id,
                'merchant_id' => $merchant->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * إضافة عمولة لمحفظة الشريك
     */
    private function addCommissionToWallet(Partner $partner, float $amount, string $description, array $metadata = []): bool
    {
        return DB::transaction(function () use ($partner, $amount, $description, $metadata) {
            $wallet = $partner->getOrCreateWallet();
            
            // إضافة العمولة
            $wallet->addCommission($amount, $description, $metadata);
            
            return true;
        });
    }

    /**
     * حساب إجمالي العمولات للشريك في فترة محددة
     */
    public function getTotalCommissions(Partner $partner, $startDate = null, $endDate = null): float
    {
        $wallet = $partner->wallet;
        if (!$wallet) {
            return 0;
        }

        $query = $wallet->transactions()
            ->where('type', PartnerWalletTransaction::TYPE_COMMISSION)
            ->where('status', PartnerWalletTransaction::STATUS_COMPLETED);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query->sum('amount');
    }

    /**
     * حساب عمولات شهرية للشريك
     */
    public function getMonthlyCommissions(Partner $partner, int $year = null, int $month = null): array
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $wallet = $partner->wallet;
        if (!$wallet) {
            return [
                'total_commission' => 0,
                'booking_commissions' => 0,
                'referral_bonuses' => 0,
                'transactions_count' => 0
            ];
        }

        $transactions = $wallet->transactions()
            ->where('type', PartnerWalletTransaction::TYPE_COMMISSION)
            ->where('status', PartnerWalletTransaction::STATUS_COMPLETED)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        return [
            'total_commission' => $transactions->sum('amount'),
            'booking_commissions' => $transactions->where('category', PartnerWalletTransaction::CATEGORY_BOOKING_COMMISSION)->sum('amount'),
            'referral_bonuses' => $transactions->where('category', PartnerWalletTransaction::CATEGORY_MERCHANT_REFERRAL)->sum('amount'),
            'transactions_count' => $transactions->count()
        ];
    }

    /**
     * إحصائيات شاملة للشريك
     */
    public function getPartnerStats(Partner $partner): array
    {
        $wallet = $partner->wallet;
        
        return [
            // الأرصدة
            'current_balance' => $wallet ? $wallet->balance : 0,
            'available_balance' => $wallet ? $wallet->available_balance : 0,
            'pending_balance' => $wallet ? $wallet->pending_balance : 0,
            'total_earned' => $wallet ? $wallet->total_earned : 0,
            'total_withdrawn' => $wallet ? $wallet->total_withdrawn : 0,

            // الإحالات
            'total_merchants' => $partner->merchants()->count(),
            'active_merchants' => $partner->merchants()->where('verification_status', 'approved')->count(),
            'pending_merchants' => $partner->merchants()->where('verification_status', 'pending')->count(),

            // الحجوزات
            'total_bookings' => $this->getTotalBookings($partner),
            'monthly_bookings' => $this->getMonthlyBookings($partner),
            'total_revenue' => $this->getTotalRevenue($partner),
            'monthly_revenue' => $this->getMonthlyRevenue($partner),

            // العمولات
            'monthly_commission' => $this->getMonthlyCommissions($partner)['total_commission'],
            'commission_rate' => $partner->commission_rate,
        ];
    }

    /**
     * إجمالي الحجوزات من التجار المُحالين
     */
    private function getTotalBookings(Partner $partner): int
    {
        return Booking::whereHas('merchant', function ($query) use ($partner) {
            $query->where('partner_id', $partner->id);
        })->count();
    }

    /**
     * الحجوزات الشهرية من التجار المُحالين
     */
    private function getMonthlyBookings(Partner $partner): int
    {
        return Booking::whereHas('merchant', function ($query) use ($partner) {
            $query->where('partner_id', $partner->id);
        })
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();
    }

    /**
     * إجمالي الإيرادات من التجار المُحالين
     */
    private function getTotalRevenue(Partner $partner): float
    {
        return Booking::whereHas('merchant', function ($query) use ($partner) {
            $query->where('partner_id', $partner->id);
        })
        ->where('payment_status', 'paid')
        ->sum('total_amount');
    }

    /**
     * الإيرادات الشهرية من التجار المُحالين
     */
    private function getMonthlyRevenue(Partner $partner): float
    {
        return Booking::whereHas('merchant', function ($query) use ($partner) {
            $query->where('partner_id', $partner->id);
        })
        ->where('payment_status', 'paid')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('total_amount');
    }

    /**
     * معالجة السحب التلقائي للشريك
     */
    public function processAutoWithdrawal(PartnerWallet $wallet): bool
    {
        if (!$wallet->auto_withdraw || !$wallet->auto_withdraw_threshold) {
            return false;
        }

        if ($wallet->available_balance < $wallet->auto_withdraw_threshold) {
            return false;
        }

        // إنشاء طلب سحب تلقائي
        $withdrawal = $wallet->withdrawals()->create([
            'amount' => $wallet->available_balance,
            'status' => 'pending',
            'bank_details' => [
                'bank_name' => $wallet->bank_name,
                'account_number' => $wallet->bank_account_number,
                'account_holder_name' => $wallet->account_holder_name,
                'routing_number' => $wallet->bank_routing_number,
                'swift_code' => $wallet->swift_code,
            ],
            'notes' => 'طلب سحب تلقائي',
            'requested_at' => now(),
        ]);

        // تحديث الرصيد المعلق
        $wallet->increment('pending_balance', $withdrawal->amount);
        $wallet->decrement('balance', $withdrawal->amount);

        Log::info('تم إنشاء طلب سحب تلقائي', [
            'partner_id' => $wallet->partner_id,
            'withdrawal_id' => $withdrawal->id,
            'amount' => $withdrawal->amount
        ]);

        return true;
    }
}
