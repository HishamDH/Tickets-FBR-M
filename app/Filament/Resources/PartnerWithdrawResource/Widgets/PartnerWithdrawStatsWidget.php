<?php

namespace App\Filament\Resources\PartnerWithdrawResource\Widgets;

use App\Models\PartnerWithdraw;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class PartnerWithdrawStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalWithdraws = PartnerWithdraw::count();
        $pendingWithdraws = PartnerWithdraw::where('status', PartnerWithdraw::STATUS_PENDING)->count();
        $approvedWithdraws = PartnerWithdraw::where('status', PartnerWithdraw::STATUS_APPROVED)->count();
        $completedWithdraws = PartnerWithdraw::where('status', PartnerWithdraw::STATUS_COMPLETED)->count();
        
        $totalAmount = PartnerWithdraw::sum('amount');
        $pendingAmount = PartnerWithdraw::where('status', PartnerWithdraw::STATUS_PENDING)->sum('amount');
        $approvedAmount = PartnerWithdraw::where('status', PartnerWithdraw::STATUS_APPROVED)->sum('amount');
        $completedAmount = PartnerWithdraw::where('status', PartnerWithdraw::STATUS_COMPLETED)->sum('amount');
        
        // الطلبات المتأخرة (أكثر من 7 أيام)
        $overdueWithdraws = PartnerWithdraw::where('status', PartnerWithdraw::STATUS_PENDING)
            ->where('requested_at', '<', now()->subDays(7))
            ->count();

        // متوسط مبلغ السحب
        $averageAmount = $totalWithdraws > 0 ? $totalAmount / $totalWithdraws : 0;

        return [
            Stat::make('إجمالي الطلبات', Number::format($totalWithdraws))
                ->description('جميع طلبات السحب')
                ->descriptionIcon('heroicon-m-list-bullet')
                ->color('primary'),

            Stat::make('الطلبات المعلقة', Number::format($pendingWithdraws))
                ->description('تحتاج للمراجعة')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingWithdraws > 0 ? 'warning' : 'success'),

            Stat::make('الطلبات الموافق عليها', Number::format($approvedWithdraws))
                ->description('جاهزة للتحويل')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($approvedWithdraws > 0 ? 'info' : 'gray'),

            Stat::make('الطلبات المكتملة', Number::format($completedWithdraws))
                ->description('تم التحويل بنجاح')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('إجمالي المبالغ', Number::format($totalAmount, 2) . ' ريال')
                ->description('مجموع جميع الطلبات')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),

            Stat::make('المبالغ المعلقة', Number::format($pendingAmount, 2) . ' ريال')
                ->description('تحتاج للموافقة')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($pendingAmount > 0 ? 'warning' : 'success'),

            Stat::make('المبالغ المحولة', Number::format($completedAmount, 2) . ' ريال')
                ->description('تم التحويل فعلياً')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('success'),

            Stat::make('الطلبات المتأخرة', Number::format($overdueWithdraws))
                ->description('أكثر من 7 أيام')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color($overdueWithdraws > 0 ? 'danger' : 'success'),

            Stat::make('متوسط المبلغ', Number::format($averageAmount, 2) . ' ريال')
                ->description('متوسط مبلغ السحب')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}
