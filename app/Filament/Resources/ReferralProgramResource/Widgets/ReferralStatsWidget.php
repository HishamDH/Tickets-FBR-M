<?php

namespace App\Filament\Resources\ReferralProgramResource\Widgets;

use App\Models\ReferralProgram;
use App\Models\Referral;
use App\Models\ReferralReward;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReferralStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPrograms = ReferralProgram::count();
        $activePrograms = ReferralProgram::where('is_active', true)->count();
        
        $totalReferrals = Referral::count();
        $successfulReferrals = Referral::where('is_successful', true)->count();
        $pendingReferrals = Referral::where('is_successful', false)->whereNull('referee_id')->count();
        
        $totalRewards = ReferralReward::sum('reward_value');
        $processedRewards = ReferralReward::where('status', 'processed')->sum('reward_value');
        
        $conversionRate = $totalReferrals > 0 ? ($successfulReferrals / $totalReferrals) * 100 : 0;

        return [
            Stat::make('إجمالي برامج الإحالة', $totalPrograms)
                ->description('العدد الكلي للبرامج')
                ->descriptionIcon('heroicon-m-share')
                ->color('primary'),

            Stat::make('البرامج النشطة', $activePrograms)
                ->description('البرامج القابلة للاستخدام')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('إجمالي الإحالات', number_format($totalReferrals))
                ->description('جميع الإحالات المُرسلة')
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color('info'),

            Stat::make('الإحالات الناجحة', number_format($successfulReferrals))
                ->description('الإحالات المكتملة')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('معدل التحويل', number_format($conversionRate, 1) . '%')
                ->description('نسبة نجاح الإحالات')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($conversionRate >= 50 ? 'success' : ($conversionRate >= 25 ? 'warning' : 'danger')),

            Stat::make('إجمالي المكافآت', number_format($totalRewards, 2) . ' ر.س')
                ->description('قيمة جميع المكافآت')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),
        ];
    }
}
