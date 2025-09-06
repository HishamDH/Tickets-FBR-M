<?php

namespace App\Services;

use App\Models\Partner;
use App\Models\PartnerWallet;
use App\Models\PartnerWalletTransaction;
use App\Models\PartnerWithdraw;
use App\Models\PartnerInvitation;
use App\Models\Merchant;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PartnerAnalyticsService
{
    /**
     * تقرير الأداء الشامل للشريك
     */
    public function getPartnerPerformanceReport(Partner $partner, $startDate = null, $endDate = null): array
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now()->endOfMonth();

        $wallet = $partner->wallet;

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'days_count' => $startDate->diffInDays($endDate) + 1,
            ],
            
            // الإحصائيات المالية
            'financial' => [
                'current_balance' => $wallet ? $wallet->balance : 0,
                'total_earned' => $wallet ? $wallet->total_earned : 0,
                'total_withdrawn' => $wallet ? $wallet->total_withdrawn : 0,
                'period_earnings' => $this->getPeriodEarnings($partner, $startDate, $endDate),
                'period_withdrawals' => $this->getPeriodWithdrawals($partner, $startDate, $endDate),
                'commission_breakdown' => $this->getCommissionBreakdown($partner, $startDate, $endDate),
            ],
            
            // إحصائيات الإحالات
            'referrals' => [
                'total_merchants' => $partner->merchants()->count(),
                'active_merchants' => $partner->merchants()->where('verification_status', 'approved')->count(),
                'period_new_merchants' => $this->getPeriodNewMerchants($partner, $startDate, $endDate),
                'pending_invitations' => $partner->invitations()->where('status', 'pending')->count(),
                'acceptance_rate' => $this->getInvitationAcceptanceRate($partner),
            ],
            
            // إحصائيات الأداء
            'performance' => [
                'total_bookings' => $this->getTotalBookingsFromPartner($partner),
                'period_bookings' => $this->getPeriodBookingsFromPartner($partner, $startDate, $endDate),
                'total_revenue_generated' => $this->getTotalRevenueFromPartner($partner),
                'period_revenue_generated' => $this->getPeriodRevenueFromPartner($partner, $startDate, $endDate),
                'average_booking_value' => $this->getAverageBookingValue($partner),
                'top_performing_merchants' => $this->getTopPerformingMerchants($partner, 5),
            ],
            
            // الاتجاهات والتنبؤات
            'trends' => [
                'monthly_earnings_trend' => $this->getMonthlyEarningsTrend($partner, 6),
                'monthly_merchants_growth' => $this->getMonthlyMerchantsGrowth($partner, 6),
                'projected_monthly_earnings' => $this->getProjectedEarnings($partner),
            ],
        ];
    }

    /**
     * تحليل الأداء المقارن لجميع الشركاء
     */
    public function getPartnersComparativeAnalysis($period = 'month'): array
    {
        $partners = Partner::with(['wallet', 'merchants', 'invitations'])->get();
        
        $analysis = [];
        
        foreach ($partners as $partner) {
            $wallet = $partner->wallet;
            
            $analysis[] = [
                'partner_id' => $partner->id,
                'partner_name' => $partner->name,
                'tier' => $this->getPartnerTier($partner),
                'commission_rate' => $partner->commission_rate,
                'current_balance' => $wallet ? $wallet->balance : 0,
                'total_earned' => $wallet ? $wallet->total_earned : 0,
                'merchants_count' => $partner->merchants()->count(),
                'active_merchants' => $partner->merchants()->where('verification_status', 'approved')->count(),
                'monthly_earnings' => $this->getMonthlyEarnings($partner),
                'invitation_acceptance_rate' => $this->getInvitationAcceptanceRate($partner),
                'performance_score' => $this->calculatePerformanceScore($partner),
            ];
        }
        
        // ترتيب حسب نقاط الأداء
        usort($analysis, function($a, $b) {
            return $b['performance_score'] <=> $a['performance_score'];
        });
        
        return [
            'total_partners' => count($analysis),
            'partners' => $analysis,
            'summary' => [
                'total_balance' => array_sum(array_column($analysis, 'current_balance')),
                'total_earned' => array_sum(array_column($analysis, 'total_earned')),
                'total_merchants' => array_sum(array_column($analysis, 'merchants_count')),
                'average_performance_score' => array_sum(array_column($analysis, 'performance_score')) / count($analysis),
            ]
        ];
    }

    /**
     * تقرير الاتجاهات والتوقعات
     */
    public function getTrendsAndForecasting(): array
    {
        return [
            'partner_growth' => $this->getPartnerGrowthTrend(),
            'revenue_trends' => $this->getRevenueTrends(),
            'merchant_acquisition' => $this->getMerchantAcquisitionTrend(),
            'commission_efficiency' => $this->getCommissionEfficiencyTrend(),
            'forecasts' => [
                'next_month_revenue' => $this->forecastNextMonthRevenue(),
                'partner_growth_projection' => $this->forecastPartnerGrowth(),
                'market_penetration' => $this->calculateMarketPenetration(),
            ]
        ];
    }

    /**
     * تحليل ROI للشركاء
     */
    public function getPartnerROIAnalysis(): array
    {
        $partners = Partner::with('wallet')->get();
        $analysis = [];

        foreach ($partners as $partner) {
            $totalPaid = PartnerWalletTransaction::whereHas('partnerWallet', function($q) use ($partner) {
                $q->where('partner_id', $partner->id);
            })->where('type', 'commission')->sum('amount');

            $revenueGenerated = $this->getTotalRevenueFromPartner($partner);
            $roi = $revenueGenerated > 0 ? ($revenueGenerated / ($totalPaid ?: 1)) * 100 : 0;

            $analysis[] = [
                'partner_id' => $partner->id,
                'partner_name' => $partner->name,
                'total_commission_paid' => $totalPaid,
                'total_revenue_generated' => $revenueGenerated,
                'roi_percentage' => round($roi, 2),
                'efficiency_rating' => $this->getEfficiencyRating($roi),
            ];
        }

        usort($analysis, function($a, $b) {
            return $b['roi_percentage'] <=> $a['roi_percentage'];
        });

        return $analysis;
    }

    // Helper Methods

    private function getPeriodEarnings(Partner $partner, Carbon $start, Carbon $end): float
    {
        if (!$partner->wallet) return 0;

        return $partner->wallet->transactions()
            ->where('type', 'commission')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');
    }

    private function getPeriodWithdrawals(Partner $partner, Carbon $start, Carbon $end): float
    {
        if (!$partner->wallet) return 0;

        return $partner->wallet->withdrawals()
            ->where('status', 'completed')
            ->whereBetween('requested_at', [$start, $end])
            ->sum('amount');
    }

    private function getCommissionBreakdown(Partner $partner, Carbon $start, Carbon $end): array
    {
        if (!$partner->wallet) {
            return [
                'booking_commissions' => 0,
                'referral_bonuses' => 0,
                'performance_bonuses' => 0,
                'other' => 0,
            ];
        }

        $transactions = $partner->wallet->transactions()
            ->where('type', 'commission')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->get();

        return [
            'booking_commissions' => $transactions->where('category', 'booking_commission')->sum('amount'),
            'referral_bonuses' => $transactions->where('category', 'merchant_referral')->sum('amount'),
            'performance_bonuses' => $transactions->where('category', 'performance_bonus')->sum('amount'),
            'other' => $transactions->whereNotIn('category', ['booking_commission', 'merchant_referral', 'performance_bonus'])->sum('amount'),
        ];
    }

    private function getPeriodNewMerchants(Partner $partner, Carbon $start, Carbon $end): int
    {
        return $partner->merchants()
            ->whereBetween('created_at', [$start, $end])
            ->count();
    }

    private function getInvitationAcceptanceRate(Partner $partner): float
    {
        $totalInvitations = $partner->invitations()->count();
        if ($totalInvitations === 0) return 0;

        $acceptedInvitations = $partner->invitations()->where('status', 'accepted')->count();
        return round(($acceptedInvitations / $totalInvitations) * 100, 2);
    }

    private function getTotalBookingsFromPartner(Partner $partner): int
    {
        return Booking::whereHas('merchant', function($q) use ($partner) {
            $q->where('partner_id', $partner->id);
        })->count();
    }

    private function getPeriodBookingsFromPartner(Partner $partner, Carbon $start, Carbon $end): int
    {
        return Booking::whereHas('merchant', function($q) use ($partner) {
            $q->where('partner_id', $partner->id);
        })->whereBetween('created_at', [$start, $end])->count();
    }

    private function getTotalRevenueFromPartner(Partner $partner): float
    {
        return Booking::whereHas('merchant', function($q) use ($partner) {
            $q->where('partner_id', $partner->id);
        })->where('payment_status', 'paid')->sum('total_amount');
    }

    private function getPeriodRevenueFromPartner(Partner $partner, Carbon $start, Carbon $end): float
    {
        return Booking::whereHas('merchant', function($q) use ($partner) {
            $q->where('partner_id', $partner->id);
        })
        ->where('payment_status', 'paid')
        ->whereBetween('created_at', [$start, $end])
        ->sum('total_amount');
    }

    private function getAverageBookingValue(Partner $partner): float
    {
        $totalRevenue = $this->getTotalRevenueFromPartner($partner);
        $totalBookings = $this->getTotalBookingsFromPartner($partner);
        
        return $totalBookings > 0 ? $totalRevenue / $totalBookings : 0;
    }

    private function getTopPerformingMerchants(Partner $partner, int $limit = 5): array
    {
        return $partner->merchants()
            ->select('merchants.*')
            ->selectRaw('COALESCE(SUM(bookings.total_amount), 0) as total_revenue')
            ->selectRaw('COUNT(bookings.id) as bookings_count')
            ->leftJoin('bookings', 'merchants.id', '=', 'bookings.merchant_id')
            ->where('bookings.payment_status', 'paid')
            ->groupBy('merchants.id')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get()
            ->map(function($merchant) {
                return [
                    'merchant_id' => $merchant->id,
                    'business_name' => $merchant->business_name,
                    'total_revenue' => $merchant->total_revenue,
                    'bookings_count' => $merchant->bookings_count,
                ];
            })
            ->toArray();
    }

    private function getMonthlyEarningsTrend(Partner $partner, int $months = 6): array
    {
        if (!$partner->wallet) return [];

        $trend = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $earnings = $partner->wallet->transactions()
                ->where('type', 'commission')
                ->where('status', 'completed')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            $trend[] = [
                'month' => $date->format('Y-m'),
                'earnings' => $earnings,
            ];
        }

        return $trend;
    }

    private function getMonthlyMerchantsGrowth(Partner $partner, int $months = 6): array
    {
        $growth = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $newMerchants = $partner->merchants()
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $growth[] = [
                'month' => $date->format('Y-m'),
                'new_merchants' => $newMerchants,
            ];
        }

        return $growth;
    }

    private function getProjectedEarnings(Partner $partner): float
    {
        $lastThreeMonths = $this->getMonthlyEarningsTrend($partner, 3);
        if (count($lastThreeMonths) < 3) return 0;

        $earnings = array_column($lastThreeMonths, 'earnings');
        $averageGrowth = (end($earnings) - reset($earnings)) / 2;
        
        return max(0, end($earnings) + $averageGrowth);
    }

    private function getPartnerTier(Partner $partner): string
    {
        $merchantsCount = $partner->merchants()->count();
        $config = config('partner.partner_tiers');

        foreach (['platinum', 'gold', 'silver', 'bronze'] as $tier) {
            if ($merchantsCount >= $config[$tier]['min_merchants']) {
                return $tier;
            }
        }

        return 'bronze';
    }

    private function getMonthlyEarnings(Partner $partner): float
    {
        if (!$partner->wallet) return 0;

        return $partner->wallet->transactions()
            ->where('type', 'commission')
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
    }

    private function calculatePerformanceScore(Partner $partner): float
    {
        $metrics = [
            'merchants_score' => min(100, $partner->merchants()->count() * 10),
            'earnings_score' => min(100, $this->getMonthlyEarnings($partner) / 100),
            'acceptance_rate_score' => $this->getInvitationAcceptanceRate($partner),
            'activity_score' => min(100, $this->getTotalBookingsFromPartner($partner) / 10),
        ];

        return array_sum($metrics) / count($metrics);
    }

    private function getPartnerGrowthTrend(): array
    {
        return Partner::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();
    }

    private function getRevenueTrends(): array
    {
        return PartnerWalletTransaction::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
            ->where('type', 'commission')
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();
    }

    private function getMerchantAcquisitionTrend(): array
    {
        return Merchant::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->whereNotNull('partner_id')
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();
    }

    private function getCommissionEfficiencyTrend(): array
    {
        return DB::select("
            SELECT 
                DATE_FORMAT(pwt.created_at, '%Y-%m') as month,
                SUM(pwt.amount) as commissions_paid,
                SUM(b.total_amount) as revenue_generated,
                ROUND((SUM(b.total_amount) / SUM(pwt.amount)) * 100, 2) as efficiency_ratio
            FROM partner_wallet_transactions pwt
            JOIN partner_wallets pw ON pwt.partner_wallet_id = pw.id
            JOIN partners p ON pw.partner_id = p.id
            JOIN merchants m ON m.partner_id = p.id
            JOIN bookings b ON b.merchant_id = m.id
            WHERE pwt.type = 'commission' 
            AND pwt.status = 'completed'
            AND b.payment_status = 'paid'
            AND pwt.created_at >= ?
            GROUP BY month
            ORDER BY month
        ", [Carbon::now()->subMonths(12)]);
    }

    private function forecastNextMonthRevenue(): float
    {
        $lastSixMonths = PartnerWalletTransaction::selectRaw('SUM(amount) as total')
            ->where('type', 'commission')
            ->where('status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->pluck('total')
            ->toArray();

        if (count($lastSixMonths) < 3) return 0;

        // حساب متوسط النمو
        $totalGrowth = 0;
        for ($i = 1; $i < count($lastSixMonths); $i++) {
            $growth = ($lastSixMonths[$i] - $lastSixMonths[$i-1]) / $lastSixMonths[$i-1];
            $totalGrowth += $growth;
        }

        $averageGrowthRate = $totalGrowth / (count($lastSixMonths) - 1);
        $lastMonthRevenue = end($lastSixMonths);

        return $lastMonthRevenue * (1 + $averageGrowthRate);
    }

    private function forecastPartnerGrowth(): array
    {
        $monthlyGrowth = $this->getPartnerGrowthTrend();
        $recentGrowth = array_slice($monthlyGrowth, -3);
        
        if (count($recentGrowth) < 3) {
            return ['next_month' => 0, 'confidence' => 'low'];
        }

        $averageMonthlyGrowth = array_sum(array_column($recentGrowth, 'count')) / 3;
        
        return [
            'next_month' => round($averageMonthlyGrowth),
            'confidence' => $averageMonthlyGrowth > 0 ? 'high' : 'medium'
        ];
    }

    private function calculateMarketPenetration(): array
    {
        $totalMerchants = Merchant::count();
        $partnerMerchants = Merchant::whereNotNull('partner_id')->count();
        
        return [
            'total_merchants' => $totalMerchants,
            'partner_merchants' => $partnerMerchants,
            'penetration_rate' => $totalMerchants > 0 ? round(($partnerMerchants / $totalMerchants) * 100, 2) : 0,
        ];
    }

    private function getEfficiencyRating(float $roi): string
    {
        if ($roi >= 500) return 'excellent';
        if ($roi >= 300) return 'good';
        if ($roi >= 150) return 'average';
        if ($roi >= 100) return 'below_average';
        return 'poor';
    }
}
