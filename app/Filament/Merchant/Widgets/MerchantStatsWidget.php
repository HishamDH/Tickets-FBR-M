<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Booking;
use App\Models\Offering;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class MerchantStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();
        
        // Services/Offerings stats
        $totalOfferings = Offering::where('user_id', $userId)->count();
        $activeOfferings = Offering::where('user_id', $userId)
            ->where('status', 'active')
            ->count();
        
        // Bookings stats
        $totalBookings = Booking::whereHas('offering', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();
        
        $pendingBookings = Booking::whereHas('offering', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->where('booking_status', 'pending')->count();
        
        $todayBookings = Booking::whereHas('offering', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->whereDate('booking_date', today())->count();
        
        // Revenue calculations
        $monthlyRevenue = Booking::whereHas('offering', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('booking_status', 'completed')
        ->whereMonth('created_at', now()->month)
        ->sum('total_amount');
        
        $totalRevenue = Booking::whereHas('offering', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('booking_status', 'completed')
        ->sum('total_amount');

        // Growth calculations
        $lastMonthRevenue = Booking::whereHas('offering', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->where('booking_status', 'completed')
        ->whereMonth('created_at', now()->subMonth()->month)
        ->sum('total_amount');
        
        $revenueGrowth = $lastMonthRevenue > 0 
            ? (($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 0;

        return [
            Stat::make('Total Services', number_format($totalOfferings))
                ->description($activeOfferings . ' active • ' . ($totalOfferings - $activeOfferings) . ' inactive')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->chartColor('primary'),
                
            Stat::make('Total Bookings', number_format($totalBookings))
                ->description($pendingBookings . ' pending • ' . $todayBookings . ' today')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('info')
                ->chart([2, 4, 6, 8, 10, 12, 14])
                ->chartColor('info'),
                
            Stat::make('Monthly Revenue', '$' . number_format($monthlyRevenue, 2))
                ->description(($revenueGrowth >= 0 ? '+' : '') . number_format($revenueGrowth, 1) . '% from last month')
                ->descriptionIcon($revenueGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueGrowth >= 0 ? 'success' : 'danger')
                ->chart([5, 8, 12, 15, 18, 22, 25])
                ->chartColor($revenueGrowth >= 0 ? 'success' : 'danger'),
                
            Stat::make('Total Earnings', '$' . number_format($totalRevenue, 2))
                ->description('All time revenue')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning')
                ->chart([10, 15, 20, 25, 30, 35, 40])
                ->chartColor('warning'),
        ];
    }
}
