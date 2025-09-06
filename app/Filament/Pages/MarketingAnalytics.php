<?php

namespace App\Filament\Pages;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\LoyaltyProgram;
use App\Models\LoyaltyTransaction;
use App\Models\ReferralProgram;
use App\Models\Referral;
use App\Models\ReferralReward;
use Filament\Pages\Page;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class MarketingAnalytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.marketing-analytics';

    protected static ?string $navigationLabel = 'تحليلات التسويق';

    protected static ?string $title = 'تحليلات التسويق الشاملة';

    protected static ?string $navigationGroup = 'نظام التسويق';

    protected static ?int $navigationSort = 4;

    public $dateRange = 30;

    protected function getHeaderWidgets(): array
    {
        return [
            MarketingAnalyticsWidget::class,
        ];
    }

    public function getMarketingStats()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);

        return [
            'coupons' => [
                'total' => Coupon::count(),
                'active' => Coupon::where('is_active', true)->count(),
                'expired' => Coupon::where('expires_at', '<', now())->count(),
                'usage_count' => CouponUsage::where('created_at', '>=', $startDate)->count(),
                'total_savings' => CouponUsage::where('created_at', '>=', $startDate)->sum('discount_amount'),
                'top_coupons' => Coupon::withCount('usages')
                                     ->orderBy('usages_count', 'desc')
                                     ->limit(5)
                                     ->get(),
            ],
            'loyalty' => [
                'programs' => LoyaltyProgram::count(),
                'active_programs' => LoyaltyProgram::where('is_active', true)->count(),
                'points_awarded' => LoyaltyTransaction::where('type', 'earn')
                                                    ->where('created_at', '>=', $startDate)
                                                    ->sum('points'),
                'points_redeemed' => abs(LoyaltyTransaction::where('type', 'redeem')
                                                         ->where('created_at', '>=', $startDate)
                                                         ->sum('points')),
                'active_users' => LoyaltyTransaction::where('created_at', '>=', $startDate)
                                                   ->distinct('user_id')
                                                   ->count('user_id'),
            ],
            'referrals' => [
                'programs' => ReferralProgram::count(),
                'active_programs' => ReferralProgram::where('is_active', true)->count(),
                'total_referrals' => Referral::where('created_at', '>=', $startDate)->count(),
                'successful_referrals' => Referral::where('is_successful', true)
                                                 ->where('created_at', '>=', $startDate)
                                                 ->count(),
                'total_rewards' => ReferralReward::whereHas('referral', function($query) use ($startDate) {
                                                    $query->where('created_at', '>=', $startDate);
                                                 })
                                                 ->sum('reward_value'),
                'conversion_rate' => $this->calculateConversionRate($startDate),
            ],
        ];
    }

    public function getTrendData()
    {
        $days = min($this->dateRange, 30);
        $trends = [];

        for ($i = $days; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->format('Y-m-d');

            $trends[] = [
                'date' => $date->format('d/m'),
                'coupons' => CouponUsage::whereDate('created_at', $dateString)->count(),
                'loyalty' => LoyaltyTransaction::whereDate('created_at', $dateString)->count(),
                'referrals' => Referral::whereDate('created_at', $dateString)->count(),
            ];
        }

        return $trends;
    }

    private function calculateConversionRate($startDate)
    {
        $total = Referral::where('created_at', '>=', $startDate)->count();
        $successful = Referral::where('is_successful', true)
                             ->where('created_at', '>=', $startDate)
                             ->count();

        return $total > 0 ? round(($successful / $total) * 100, 1) : 0;
    }

    public function updatedDateRange()
    {
        // This will trigger a re-render when date range changes
    }
}

class MarketingAnalyticsWidget extends \Filament\Widgets\StatsOverviewWidget
{
    public function getStats(): array
    {
        $page = $this->getPage();
        $stats = $page->getMarketingStats();

        return [
            Stat::make('الكوبونات النشطة', $stats['coupons']['active'])
                ->description('من أصل ' . $stats['coupons']['total'] . ' كوبون')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('success'),

            Stat::make('إجمالي الوفورات', number_format($stats['coupons']['total_savings'], 2) . ' ر.س')
                ->description('وفورات آخر ' . $page->dateRange . ' يوم')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('برامج الولاء النشطة', $stats['loyalty']['active_programs'])
                ->description('من أصل ' . $stats['loyalty']['programs'] . ' برنامج')
                ->descriptionIcon('heroicon-m-heart')
                ->color('info'),

            Stat::make('النقاط المستردة', number_format($stats['loyalty']['points_redeemed']))
                ->description('آخر ' . $page->dateRange . ' يوم')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('معدل نجاح الإحالات', $stats['referrals']['conversion_rate'] . '%')
                ->description($stats['referrals']['successful_referrals'] . ' من ' . $stats['referrals']['total_referrals'])
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($stats['referrals']['conversion_rate'] >= 50 ? 'success' : 'warning'),

            Stat::make('مكافآت الإحالة', number_format($stats['referrals']['total_rewards'], 2) . ' ر.س')
                ->description('آخر ' . $page->dateRange . ' يوم')
                ->descriptionIcon('heroicon-m-gift')
                ->color('primary'),
        ];
    }
}
