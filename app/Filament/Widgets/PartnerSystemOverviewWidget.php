<?php

namespace App\Filament\Widgets;

use App\Models\Partner;
use App\Models\PartnerWallet;
use App\Models\PartnerWithdraw;
use App\Models\PartnerInvitation;
use App\Models\PartnerWalletTransaction;
use App\Models\Merchant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class PartnerSystemOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // إحصائيات الشركاء
        $totalPartners = Partner::count();
        $activePartners = Partner::where('status', 'active')->count();
        $newPartnersThisMonth = Partner::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // إحصائيات المحافظ والأموال
        $totalBalance = PartnerWallet::sum('balance');
        $totalEarned = PartnerWallet::sum('total_earned');
        $totalWithdrawn = PartnerWallet::sum('total_withdrawn');
        $pendingBalance = PartnerWallet::sum('pending_balance');

        // إحصائيات طلبات السحب
        $pendingWithdrawals = PartnerWithdraw::where('status', 'pending')->count();
        $pendingWithdrawalsAmount = PartnerWithdraw::where('status', 'pending')->sum('amount');

        // إحصائيات الدعوات
        $totalInvitations = PartnerInvitation::count();
        $acceptedInvitations = PartnerInvitation::where('status', 'accepted')->count();
        $acceptanceRate = $totalInvitations > 0 ? ($acceptedInvitations / $totalInvitations) * 100 : 0;

        // إحصائيات التجار المُحالين
        $referredMerchants = Merchant::whereNotNull('partner_id')->count();
        $approvedReferredMerchants = Merchant::whereNotNull('partner_id')
            ->where('verification_status', 'approved')
            ->count();

        // العمولات الشهرية
        $monthlyCommissions = PartnerWalletTransaction::where('type', 'commission')
            ->where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        return [
            Stat::make('إجمالي الشركاء', Number::format($totalPartners))
                ->description("{$activePartners} نشط من أصل {$totalPartners}")
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5]),

            Stat::make('شركاء جدد هذا الشهر', Number::format($newPartnersThisMonth))
                ->description('منذ بداية الشهر')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color($newPartnersThisMonth > 0 ? 'success' : 'gray'),

            Stat::make('إجمالي الأرصدة', Number::format($totalBalance, 2) . ' ريال')
                ->description('أرصدة جميع الشركاء')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([2, 4, 3, 7, 5, 8, 6]),

            Stat::make('إجمالي الأرباح', Number::format($totalEarned, 2) . ' ريال')
                ->description('منذ بداية النظام')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('طلبات السحب المعلقة', Number::format($pendingWithdrawals))
                ->description(Number::format($pendingWithdrawalsAmount, 2) . ' ريال')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingWithdrawals > 0 ? 'warning' : 'success'),

            Stat::make('معدل قبول الدعوات', Number::format($acceptanceRate, 1) . '%')
                ->description("{$acceptedInvitations} من أصل {$totalInvitations}")
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color($acceptanceRate >= 50 ? 'success' : ($acceptanceRate >= 25 ? 'warning' : 'danger')),

            Stat::make('التجار المُحالين', Number::format($referredMerchants))
                ->description("{$approvedReferredMerchants} موافق عليه")
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('info'),

            Stat::make('عمولات هذا الشهر', Number::format($monthlyCommissions, 2) . ' ريال')
                ->description('إجمالي العمولات المدفوعة')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary')
                ->chart([1, 3, 2, 5, 4, 6, 5]),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
}
