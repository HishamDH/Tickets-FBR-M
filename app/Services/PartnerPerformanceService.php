<?php

namespace App\Services;

use App\Models\Partner;
use App\Models\PartnerPerformanceMetric;
use App\Models\PartnerGoal;
use App\Models\PartnerAchievement;
use App\Models\Merchant;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class PartnerPerformanceService
{
    protected PartnerAnalyticsService $analyticsService;

    public function __construct(PartnerAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * حساب وتحديث مقاييس الأداء للشريك
     */
    public function calculateAndStoreMetrics(Partner $partner, Carbon $date = null): PartnerPerformanceMetric
    {
        $date = $date ?? now();
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        // حساب المقاييس
        $metrics = $this->calculatePerformanceMetrics($partner, $startOfMonth, $endOfMonth);

        // حفظ أو تحديث المقاييس
        return PartnerPerformanceMetric::updateOrCreate([
            'partner_id' => $partner->id,
            'year' => $date->year,
            'month' => $date->month,
        ], $metrics);
    }

    /**
     * حساب مقاييس الأداء للشريك
     */
    public function calculatePerformanceMetrics(Partner $partner, Carbon $startDate, Carbon $endDate): array
    {
        $wallet = $partner->wallet;
        
        // العمولات المكتسبة
        $commissionEarned = $wallet ? $wallet->transactions()
            ->where('type', 'commission')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount') : 0;

        // التجار الجدد
        $newMerchantsCount = $partner->merchants()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // التجار النشطون
        $activeMerchantsCount = $partner->merchants()
            ->whereHas('bookings', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
            })
            ->count();

        // إجمالي الحجوزات من التجار المحالين
        $totalBookings = Booking::whereHas('merchant', function($q) use ($partner) {
            $q->where('partner_id', $partner->id);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->where('payment_status', 'paid')
        ->count();

        // إجمالي الإيرادات المحققة
        $totalRevenue = Booking::whereHas('merchant', function($q) use ($partner) {
            $q->where('partner_id', $partner->id);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->where('payment_status', 'paid')
        ->sum('total_amount');

        // معدل تحويل الدعوات
        $invitationsSent = $partner->invitations()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $invitationsAccepted = $partner->invitations()
            ->where('status', 'accepted')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $conversionRate = $invitationsSent > 0 ? ($invitationsAccepted / $invitationsSent) * 100 : 0;

        // متوسط الإيرادات لكل تاجر
        $avgRevenuePerMerchant = $activeMerchantsCount > 0 ? $totalRevenue / $activeMerchantsCount : 0;

        // معدل الاحتفاظ بالتجار
        $retentionRate = $this->calculateMerchantRetentionRate($partner, $startDate, $endDate);

        // نقاط الجودة
        $qualityScore = $this->calculateQualityScore($partner, $startDate, $endDate);

        return [
            'commission_earned' => $commissionEarned,
            'new_merchants_count' => $newMerchantsCount,
            'active_merchants_count' => $activeMerchantsCount,
            'total_bookings' => $totalBookings,
            'total_revenue' => $totalRevenue,
            'conversion_rate' => round($conversionRate, 2),
            'avg_revenue_per_merchant' => round($avgRevenuePerMerchant, 2),
            'retention_rate' => round($retentionRate, 2),
            'quality_score' => round($qualityScore, 2),
            'performance_score' => $this->calculateOverallPerformanceScore([
                'commission_earned' => $commissionEarned,
                'new_merchants_count' => $newMerchantsCount,
                'active_merchants_count' => $activeMerchantsCount,
                'conversion_rate' => $conversionRate,
                'quality_score' => $qualityScore,
            ]),
        ];
    }

    /**
     * تعيين أهداف للشريك
     */
    public function setGoals(Partner $partner, array $goals, Carbon $targetDate = null): array
    {
        $targetDate = $targetDate ?? now()->addMonth();
        $createdGoals = [];

        foreach ($goals as $goal) {
            $partnerGoal = PartnerGoal::create([
                'partner_id' => $partner->id,
                'goal_type' => $goal['type'],
                'target_value' => $goal['target'],
                'current_value' => 0,
                'target_date' => $targetDate,
                'description' => $goal['description'] ?? null,
                'reward_amount' => $goal['reward'] ?? 0,
                'status' => 'active',
            ]);

            $createdGoals[] = $partnerGoal;
        }

        return $createdGoals;
    }

    /**
     * تحديث تقدم الأهداف
     */
    public function updateGoalsProgress(Partner $partner): array
    {
        $activeGoals = $partner->goals()->where('status', 'active')->get();
        $completedGoals = [];

        foreach ($activeGoals as $goal) {
            $currentValue = $this->calculateGoalCurrentValue($partner, $goal);
            $goal->update(['current_value' => $currentValue]);

            // التحقق من إكمال الهدف
            if ($currentValue >= $goal->target_value) {
                $goal->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                // إنشاء إنجاز
                $achievement = $this->createAchievement($partner, $goal);
                $completedGoals[] = ['goal' => $goal, 'achievement' => $achievement];

                // منح المكافأة إذا كانت محددة
                if ($goal->reward_amount > 0) {
                    $this->rewardPartner($partner, $goal->reward_amount, "مكافأة إنجاز الهدف: {$goal->goal_type}");
                }
            }
        }

        return $completedGoals;
    }

    /**
     * الحصول على لوحة أداء الشريك
     */
    public function getPerformanceDashboard(Partner $partner, int $months = 6): array
    {
        $cacheKey = "partner_dashboard_{$partner->id}_{$months}";
        
        return Cache::remember($cacheKey, 300, function() use ($partner, $months) {
            // المقاييس الحالية
            $currentMetrics = $this->calculatePerformanceMetrics(
                $partner, 
                now()->startOfMonth(), 
                now()->endOfMonth()
            );

            // الاتجاهات التاريخية
            $historicalMetrics = PartnerPerformanceMetric::where('partner_id', $partner->id)
                ->where('created_at', '>=', now()->subMonths($months))
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            // الأهداف النشطة
            $activeGoals = $partner->goals()
                ->where('status', 'active')
                ->where('target_date', '>=', now())
                ->get();

            // الإنجازات الأخيرة
            $recentAchievements = $partner->achievements()
                ->with('goal')
                ->latest()
                ->limit(5)
                ->get();

            // مقارنة مع المتوسط
            $industryBenchmark = $this->getIndustryBenchmark();

            // التوصيات
            $recommendations = $this->generatePerformanceRecommendations($partner, $currentMetrics);

            return [
                'current_metrics' => $currentMetrics,
                'historical_metrics' => $historicalMetrics,
                'active_goals' => $activeGoals,
                'recent_achievements' => $recentAchievements,
                'industry_benchmark' => $industryBenchmark,
                'recommendations' => $recommendations,
                'performance_tier' => $this->getPartnerTier($partner),
                'next_tier_requirements' => $this->getNextTierRequirements($partner),
            ];
        });
    }

    /**
     * إنشاء تقرير مقارنة الأداء
     */
    public function generateBenchmarkReport(Partner $partner): array
    {
        $partnerMetrics = $this->calculatePerformanceMetrics(
            $partner, 
            now()->startOfMonth(), 
            now()->endOfMonth()
        );

        // مقارنة مع نفس المستوى
        $sameTierPartners = Partner::where('id', '!=', $partner->id)
            ->whereHas('metrics', function($q) use ($partnerMetrics) {
                $q->where('performance_score', '>=', $partnerMetrics['performance_score'] - 10)
                  ->where('performance_score', '<=', $partnerMetrics['performance_score'] + 10);
            })
            ->with('metrics')
            ->get();

        $tierAverages = $this->calculateTierAverages($sameTierPartners);

        // مقارنة مع أفضل الأداء
        $topPerformers = Partner::whereHas('metrics', function($q) {
            $q->orderByDesc('performance_score')->limit(10);
        })->with('metrics')->get();

        $topPerformerAverages = $this->calculateTierAverages($topPerformers);

        return [
            'partner_metrics' => $partnerMetrics,
            'tier_comparison' => [
                'averages' => $tierAverages,
                'partner_rank' => $this->calculateRankInTier($partner, $sameTierPartners),
                'total_in_tier' => $sameTierPartners->count(),
            ],
            'top_performers_comparison' => [
                'averages' => $topPerformerAverages,
                'gaps' => $this->calculatePerformanceGaps($partnerMetrics, $topPerformerAverages),
            ],
        ];
    }

    // Helper Methods

    private function calculateMerchantRetentionRate(Partner $partner, Carbon $startDate, Carbon $endDate): float
    {
        $previousPeriodStart = $startDate->copy()->subMonth();
        $previousPeriodEnd = $startDate->copy()->subDay();

        $previousPeriodMerchants = $partner->merchants()
            ->where('created_at', '<=', $previousPeriodEnd)
            ->pluck('id');

        if ($previousPeriodMerchants->isEmpty()) {
            return 0;
        }

        $retainedMerchants = $partner->merchants()
            ->whereIn('id', $previousPeriodMerchants)
            ->whereHas('bookings', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
            })
            ->count();

        return ($retainedMerchants / $previousPeriodMerchants->count()) * 100;
    }

    private function calculateQualityScore(Partner $partner, Carbon $startDate, Carbon $endDate): float
    {
        $merchants = $partner->merchants()->get();
        $totalScore = 0;
        $totalMerchants = $merchants->count();

        if ($totalMerchants === 0) {
            return 0;
        }

        foreach ($merchants as $merchant) {
            $merchantScore = 0;
            
            // معدل الحجوزات الناجحة
            $totalBookings = $merchant->bookings()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            
            $successfulBookings = $merchant->bookings()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('payment_status', 'paid')
                ->count();

            if ($totalBookings > 0) {
                $merchantScore += ($successfulBookings / $totalBookings) * 40; // 40% من النقاط
            }

            // متوسط التقييمات من جدول المراجعات
            $serviceIds = $merchant->services->pluck('id');
            $avgRating = \App\Models\Review::whereIn('service_id', $serviceIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('is_approved', true)
                ->avg('rating') ?? 0;

            $merchantScore += ($avgRating / 5) * 30; // 30% من النقاط

            // نشاط الخدمات
            $activeServices = $merchant->services()->where('is_active', true)->count();
            $merchantScore += min($activeServices * 5, 30); // 30% من النقاط، حد أقصى

            $totalScore += $merchantScore;
        }

        return $totalScore / $totalMerchants;
    }

    private function calculateOverallPerformanceScore(array $metrics): float
    {
        $weights = [
            'commission_earned' => 0.3,
            'new_merchants_count' => 0.2,
            'active_merchants_count' => 0.2,
            'conversion_rate' => 0.15,
            'quality_score' => 0.15,
        ];

        $score = 0;
        $maxScores = [
            'commission_earned' => 5000, // 5000 ريال
            'new_merchants_count' => 10, // 10 تجار جدد
            'active_merchants_count' => 20, // 20 تاجر نشط
            'conversion_rate' => 50, // 50% معدل تحويل
            'quality_score' => 100, // 100 نقطة جودة
        ];

        foreach ($weights as $metric => $weight) {
            $value = $metrics[$metric] ?? 0;
            $maxValue = $maxScores[$metric];
            $normalizedValue = min($value / $maxValue, 1) * 100;
            $score += $normalizedValue * $weight;
        }

        return round($score, 2);
    }

    private function calculateGoalCurrentValue(Partner $partner, PartnerGoal $goal): float
    {
        $targetDate = $goal->target_date;
        $startDate = $goal->created_at;

        switch ($goal->goal_type) {
            case 'new_merchants':
                return $partner->merchants()
                    ->whereBetween('created_at', [$startDate, $targetDate])
                    ->count();

            case 'commission_earned':
                return $partner->wallet ? $partner->wallet->transactions()
                    ->where('type', 'commission')
                    ->where('status', 'completed')
                    ->whereBetween('created_at', [$startDate, $targetDate])
                    ->sum('amount') : 0;

            case 'total_revenue':
                return Booking::whereHas('merchant', function($q) use ($partner) {
                    $q->where('partner_id', $partner->id);
                })
                ->whereBetween('created_at', [$startDate, $targetDate])
                ->where('payment_status', 'paid')
                ->sum('total_amount');

            case 'active_merchants':
                return $partner->merchants()
                    ->whereHas('bookings', function($q) use ($startDate, $targetDate) {
                        $q->whereBetween('created_at', [$startDate, $targetDate])
                          ->where('payment_status', 'paid');
                    })
                    ->count();

            default:
                return 0;
        }
    }

    private function createAchievement(Partner $partner, PartnerGoal $goal): PartnerAchievement
    {
        return PartnerAchievement::create([
            'partner_id' => $partner->id,
            'goal_id' => $goal->id,
            'achievement_type' => 'goal_completion',
            'title' => "إنجاز الهدف: {$goal->goal_type}",
            'description' => "تم إنجاز هدف {$goal->description} بقيمة {$goal->target_value}",
            'reward_amount' => $goal->reward_amount,
            'achieved_at' => now(),
        ]);
    }

    private function rewardPartner(Partner $partner, float $amount, string $description): void
    {
        if (!$partner->wallet) {
            return;
        }

        $partner->wallet->transactions()->create([
            'transaction_reference' => 'REW_' . strtoupper(uniqid()),
            'type' => 'commission',
            'category' => 'performance_bonus',
            'amount' => $amount,
            'balance_after' => $partner->wallet->balance + $amount,
            'description' => $description,
            'status' => 'completed',
            'processed_at' => now(),
        ]);

        $partner->wallet->increment('balance', $amount);
        $partner->wallet->increment('total_earned', $amount);
    }

    private function getIndustryBenchmark(): array
    {
        return Cache::remember('industry_benchmark', 3600, function() {
            return [
                'avg_commission_earned' => PartnerPerformanceMetric::avg('commission_earned') ?? 0,
                'avg_new_merchants' => PartnerPerformanceMetric::avg('new_merchants_count') ?? 0,
                'avg_conversion_rate' => PartnerPerformanceMetric::avg('conversion_rate') ?? 0,
                'avg_quality_score' => PartnerPerformanceMetric::avg('quality_score') ?? 0,
                'avg_performance_score' => PartnerPerformanceMetric::avg('performance_score') ?? 0,
            ];
        });
    }

    private function generatePerformanceRecommendations(Partner $partner, array $metrics): array
    {
        $recommendations = [];
        $industryBenchmark = $this->getIndustryBenchmark();

        // توصيات بناءً على مقارنة مع المتوسط
        if ($metrics['conversion_rate'] < $industryBenchmark['avg_conversion_rate']) {
            $recommendations[] = [
                'type' => 'conversion',
                'priority' => 'high',
                'title' => 'تحسين معدل التحويل',
                'description' => 'معدل تحويل الدعوات أقل من المتوسط. ركز على جودة الدعوات والمتابعة.',
            ];
        }

        if ($metrics['quality_score'] < $industryBenchmark['avg_quality_score']) {
            $recommendations[] = [
                'type' => 'quality',
                'priority' => 'medium',
                'title' => 'رفع نقاط الجودة',
                'description' => 'نقاط الجودة تحتاج تحسين. ادعم التجار لتحسين خدماتهم.',
            ];
        }

        if ($metrics['new_merchants_count'] < $industryBenchmark['avg_new_merchants']) {
            $recommendations[] = [
                'type' => 'acquisition',
                'priority' => 'high',
                'title' => 'زيادة إحالة التجار',
                'description' => 'عدد التجار الجدد أقل من المتوسط. ركز على أنشطة التسويق.',
            ];
        }

        return $recommendations;
    }

    private function getPartnerTier(Partner $partner): string
    {
        $metrics = $this->calculatePerformanceMetrics(
            $partner, 
            now()->startOfMonth(), 
            now()->endOfMonth()
        );

        $score = $metrics['performance_score'];
        $merchantsCount = $partner->merchants()->count();

        if ($score >= 80 && $merchantsCount >= 30) return 'platinum';
        if ($score >= 60 && $merchantsCount >= 15) return 'gold';
        if ($score >= 40 && $merchantsCount >= 5) return 'silver';
        
        return 'bronze';
    }

    private function getNextTierRequirements(Partner $partner): array
    {
        $currentTier = $this->getPartnerTier($partner);
        $merchantsCount = $partner->merchants()->count();
        
        $requirements = config('partner.partner_tiers');
        
        foreach (['silver', 'gold', 'platinum'] as $tier) {
            if ($currentTier !== $tier) {
                return [
                    'tier' => $tier,
                    'merchants_needed' => max(0, $requirements[$tier]['min_merchants'] - $merchantsCount),
                    'commission_rate' => $requirements[$tier]['commission_rate'],
                ];
            }
        }

        return ['tier' => 'platinum', 'merchants_needed' => 0, 'commission_rate' => 6.0];
    }

    private function calculateTierAverages($partners): array
    {
        if ($partners->isEmpty()) {
            return [];
        }

        return [
            'avg_commission_earned' => $partners->avg('metrics.commission_earned') ?? 0,
            'avg_new_merchants' => $partners->avg('metrics.new_merchants_count') ?? 0,
            'avg_conversion_rate' => $partners->avg('metrics.conversion_rate') ?? 0,
            'avg_quality_score' => $partners->avg('metrics.quality_score') ?? 0,
            'avg_performance_score' => $partners->avg('metrics.performance_score') ?? 0,
        ];
    }

    private function calculateRankInTier(Partner $partner, $tiersPartners): int
    {
        $partnerScore = $partner->metrics()->latest()->first()?->performance_score ?? 0;
        
        return $tiersPartners->filter(function($p) use ($partnerScore) {
            $score = $p->metrics()->latest()->first()?->performance_score ?? 0;
            return $score > $partnerScore;
        })->count() + 1;
    }

    private function calculatePerformanceGaps(array $partnerMetrics, array $topPerformerAverages): array
    {
        $gaps = [];
        
        foreach ($topPerformerAverages as $metric => $topValue) {
            $partnerValue = $partnerMetrics[str_replace('avg_', '', $metric)] ?? 0;
            $gap = $topValue - $partnerValue;
            $gapPercentage = $topValue > 0 ? ($gap / $topValue) * 100 : 0;
            
            $gaps[str_replace('avg_', '', $metric)] = [
                'gap' => round($gap, 2),
                'gap_percentage' => round($gapPercentage, 2),
                'improvement_needed' => $gap > 0,
            ];
        }
        
        return $gaps;
    }
}
