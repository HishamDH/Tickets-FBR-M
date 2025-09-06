<?php

namespace App\Filament\Resources\LoyaltyProgramResource\Widgets;

use App\Models\LoyaltyProgram;
use App\Models\LoyaltyPoint;
use App\Models\LoyaltyTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LoyaltyStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPrograms = LoyaltyProgram::count();
        $activePrograms = LoyaltyProgram::where('is_active', true)->count();
        
        $totalPointsAwarded = LoyaltyTransaction::where('type', 'earn')->sum('points');
        $totalPointsRedeemed = abs(LoyaltyTransaction::where('type', 'redeem')->sum('points'));
        $activePoints = LoyaltyPoint::where('points', '>', 0)
                                   ->whereNull('used_at')
                                   ->where(function($query) {
                                       $query->whereNull('expires_at')
                                             ->orWhere('expires_at', '>', now());
                                   })
                                   ->sum('points');
        
        $totalUsers = LoyaltyPoint::distinct('user_id')->count('user_id');

        return [
            Stat::make('إجمالي برامج الولاء', $totalPrograms)
                ->description('العدد الكلي للبرامج')
                ->descriptionIcon('heroicon-m-heart')
                ->color('primary'),

            Stat::make('البرامج النشطة', $activePrograms)
                ->description('البرامج القابلة للاستخدام')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('النقاط الممنوحة', number_format($totalPointsAwarded))
                ->description('إجمالي النقاط الممنوحة')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('info'),

            Stat::make('النقاط المستردة', number_format($totalPointsRedeemed))
                ->description('إجمالي النقاط المستردة')
                ->descriptionIcon('heroicon-m-minus-circle')
                ->color('warning'),

            Stat::make('النقاط النشطة', number_format($activePoints))
                ->description('النقاط القابلة للاستخدام')
                ->descriptionIcon('heroicon-m-star')
                ->color('success'),

            Stat::make('المستخدمون المشاركون', number_format($totalUsers))
                ->description('إجمالي المستخدمين في برامج الولاء')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}
