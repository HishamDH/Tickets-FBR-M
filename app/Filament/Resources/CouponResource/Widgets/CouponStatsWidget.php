<?php

namespace App\Filament\Resources\CouponResource\Widgets;

use App\Models\Coupon;
use App\Models\CouponUsage;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class CouponStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCoupons = Coupon::count();
        $activeCoupons = Coupon::where('is_active', true)->count();
        $expiredCoupons = Coupon::where('expires_at', '<', now())->count();
        
        $totalUsages = CouponUsage::count();
        $monthlyUsages = CouponUsage::whereMonth('created_at', now()->month)
                                   ->whereYear('created_at', now()->year)
                                   ->count();
        
        $totalSavings = CouponUsage::sum('discount_amount');
        $monthlySavings = CouponUsage::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->sum('discount_amount');

        return [
            Stat::make('إجمالي الكوبونات', $totalCoupons)
                ->description('العدد الكلي للكوبونات')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),

            Stat::make('الكوبونات النشطة', $activeCoupons)
                ->description('الكوبونات القابلة للاستخدام')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('الكوبونات المنتهية', $expiredCoupons)
                ->description('انتهت صلاحيتها')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),

            Stat::make('إجمالي الاستخدامات', number_format($totalUsages))
                ->description('جميع استخدامات الكوبونات')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),

            Stat::make('استخدامات الشهر', number_format($monthlyUsages))
                ->description('استخدامات الشهر الحالي')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),

            Stat::make('إجمالي الوفورات', number_format($totalSavings, 2) . ' ر.س')
                ->description('المبلغ الكلي للوفورات')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
}
