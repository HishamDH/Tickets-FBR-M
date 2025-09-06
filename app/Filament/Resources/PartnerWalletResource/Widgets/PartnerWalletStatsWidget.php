<?php

namespace App\Filament\Resources\PartnerWalletResource\Widgets;

use App\Models\PartnerWallet;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class PartnerWalletStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalWallets = PartnerWallet::count();
        $totalBalance = PartnerWallet::sum('balance');
        $totalPendingBalance = PartnerWallet::sum('pending_balance');
        $totalEarned = PartnerWallet::sum('total_earned');
        $totalWithdrawn = PartnerWallet::sum('total_withdrawn');
        $autoWithdrawEnabled = PartnerWallet::where('auto_withdraw', true)->count();
        
        // المحافظ الجاهزة للسحب التلقائي
        $readyForAutoWithdraw = PartnerWallet::where('auto_withdraw', true)
            ->whereNotNull('auto_withdraw_threshold')
            ->whereColumn('balance', '>=', 'auto_withdraw_threshold')
            ->count();

        return [
            Stat::make('إجمالي المحافظ', Number::format($totalWallets))
                ->description('عدد محافظ الشركاء')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('primary'),

            Stat::make('إجمالي الأرصدة', Number::format($totalBalance, 2) . ' ريال')
                ->description('مجموع أرصدة جميع الشركاء')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('الأرصدة المعلقة', Number::format($totalPendingBalance, 2) . ' ريال')
                ->description('الأرصدة تحت المراجعة')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('إجمالي الأرباح', Number::format($totalEarned, 2) . ' ريال')
                ->description('مجموع أرباح جميع الشركاء')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('إجمالي المسحوب', Number::format($totalWithdrawn, 2) . ' ريال')
                ->description('مجموع المبالغ المسحوبة')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('info'),

            Stat::make('السحب التلقائي', Number::format($autoWithdrawEnabled))
                ->description('المحافظ المفعلة للسحب التلقائي')
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color('primary'),

            Stat::make('جاهز للسحب', Number::format($readyForAutoWithdraw))
                ->description('محافظ تستحق السحب التلقائي')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($readyForAutoWithdraw > 0 ? 'warning' : 'gray'),

            Stat::make('متوسط الرصيد', $totalWallets > 0 ? Number::format($totalBalance / $totalWallets, 2) . ' ريال' : '0 ريال')
                ->description('متوسط رصيد المحفظة')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
