<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\PaidReservation;
use App\Models\WalletTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BranchAnalyticsWidget extends BaseWidget
{
    public ?Branch $record = null;

    protected function getStats(): array
    {
        if (!$this->record) {
            return [];
        }

        $branch = $this->record;
        
        // إجمالي الحجوزات
        $totalBookings = PaidReservation::where('branch_id', $branch->id)->count();
        
        // الحجوزات هذا الشهر
        $monthlyBookings = PaidReservation::where('branch_id', $branch->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // إجمالي الإيرادات
        $totalRevenue = WalletTransaction::whereHas('wallet', function($query) use ($branch) {
            $query->where('user_id', $branch->user_id);
        })
        ->where('type', 'credit')
        ->where('category', 'booking')
        ->where('metadata->branch_id', $branch->id)
        ->sum('amount');
        
        // الإيرادات هذا الشهر
        $monthlyRevenue = WalletTransaction::whereHas('wallet', function($query) use ($branch) {
            $query->where('user_id', $branch->user_id);
        })
        ->where('type', 'credit')
        ->where('category', 'booking')
        ->where('metadata->branch_id', $branch->id)
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('amount');
        
        // متوسط قيمة الحجز
        $averageBookingValue = $totalBookings > 0 ? $totalRevenue / $totalBookings : 0;
        
        // معدل الإشغال (افتراضي بناءً على السعة)
        $occupancyRate = $branch->capacity > 0 && $monthlyBookings > 0 
            ? min(($monthlyBookings / ($branch->capacity * 30)) * 100, 100) 
            : 0;

        // حساب النمو الشهري للحجوزات
        $lastMonthBookings = PaidReservation::where('branch_id', $branch->id)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
            
        $bookingsGrowth = $lastMonthBookings > 0 
            ? (($monthlyBookings - $lastMonthBookings) / $lastMonthBookings) * 100
            : 0;

        // حساب النمو الشهري للإيرادات
        $lastMonthRevenue = WalletTransaction::whereHas('wallet', function($query) use ($branch) {
            $query->where('user_id', $branch->user_id);
        })
        ->where('type', 'credit')
        ->where('category', 'booking')
        ->where('metadata->branch_id', $branch->id)
        ->whereMonth('created_at', now()->subMonth()->month)
        ->whereYear('created_at', now()->subMonth()->year)
        ->sum('amount');
        
        $revenueGrowth = $lastMonthRevenue > 0 
            ? (($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : 0;

        return [
            Stat::make('إجمالي الحجوزات', number_format($totalBookings))
                ->description($monthlyBookings . ' حجز هذا الشهر')
                ->descriptionIcon($bookingsGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($bookingsGrowth >= 0 ? 'success' : 'danger')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
                
            Stat::make('إجمالي الإيرادات', '$' . number_format($totalRevenue, 2))
                ->description('$' . number_format($monthlyRevenue, 2) . ' هذا الشهر')
                ->descriptionIcon($revenueGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueGrowth >= 0 ? 'success' : 'danger')
                ->chart([15, 4, 10, 2, 12, 4, 12]),
                
            Stat::make('متوسط قيمة الحجز', '$' . number_format($averageBookingValue, 2))
                ->description('متوسط الإيراد لكل حجز')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),
                
            Stat::make('معدل الإشغال', number_format($occupancyRate, 1) . '%')
                ->description('بناءً على السعة المتاحة')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($occupancyRate >= 70 ? 'success' : ($occupancyRate >= 40 ? 'warning' : 'danger')),
                
            Stat::make('سعة الفرع', number_format($branch->capacity ?? 0))
                ->description('العدد الأقصى للضيوف')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
                
            Stat::make('حالة الفرع', $branch->is_active ? 'نشط' : 'معطل')
                ->description($branch->is_active ? 'يقبل حجوزات جديدة' : 'لا يقبل حجوزات')
                ->descriptionIcon($branch->is_active ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                ->color($branch->is_active ? 'success' : 'danger'),
        ];
    }
}
