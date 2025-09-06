<?php

namespace App\Filament\Resources\PartnerInvitationResource\Widgets;

use App\Models\PartnerInvitation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class PartnerInvitationStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalInvitations = PartnerInvitation::count();
        $pendingInvitations = PartnerInvitation::where('status', PartnerInvitation::STATUS_PENDING)->count();
        $acceptedInvitations = PartnerInvitation::where('status', PartnerInvitation::STATUS_ACCEPTED)->count();
        $expiredInvitations = PartnerInvitation::where('status', PartnerInvitation::STATUS_EXPIRED)->count();
        $cancelledInvitations = PartnerInvitation::where('status', PartnerInvitation::STATUS_CANCELLED)->count();
        
        // الدعوات غير المرسلة
        $unsentInvitations = PartnerInvitation::whereNull('sent_at')
            ->where('status', PartnerInvitation::STATUS_PENDING)
            ->count();
        
        // الدعوات التي ستنتهي قريباً (خلال 3 أيام)
        $expiringSoon = PartnerInvitation::where('status', PartnerInvitation::STATUS_PENDING)
            ->whereBetween('expires_at', [now(), now()->addDays(3)])
            ->count();
        
        // معدل القبول
        $acceptanceRate = $totalInvitations > 0 ? ($acceptedInvitations / $totalInvitations) * 100 : 0;
        
        // الدعوات الشهرية
        $monthlyInvitations = PartnerInvitation::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            Stat::make('إجمالي الدعوات', Number::format($totalInvitations))
                ->description('جميع الدعوات المرسلة')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('primary'),

            Stat::make('الدعوات المعلقة', Number::format($pendingInvitations))
                ->description('في انتظار الاستجابة')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingInvitations > 0 ? 'warning' : 'success'),

            Stat::make('الدعوات المقبولة', Number::format($acceptedInvitations))
                ->description('تم قبولها بنجاح')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('معدل القبول', Number::format($acceptanceRate, 1) . '%')
                ->description('نسبة الدعوات المقبولة')
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color($acceptanceRate >= 50 ? 'success' : ($acceptanceRate >= 25 ? 'warning' : 'danger')),

            Stat::make('غير مرسلة', Number::format($unsentInvitations))
                ->description('لم يتم إرسالها بعد')
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color($unsentInvitations > 0 ? 'warning' : 'success'),

            Stat::make('تنتهي قريباً', Number::format($expiringSoon))
                ->description('خلال 3 أيام')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($expiringSoon > 0 ? 'danger' : 'success'),

            Stat::make('منتهية الصلاحية', Number::format($expiredInvitations))
                ->description('انتهت صلاحيتها')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($expiredInvitations > 0 ? 'danger' : 'gray'),

            Stat::make('هذا الشهر', Number::format($monthlyInvitations))
                ->description('الدعوات المرسلة')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('ملغاة', Number::format($cancelledInvitations))
                ->description('تم إلغاؤها')
                ->descriptionIcon('heroicon-m-minus-circle')
                ->color('gray'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}
