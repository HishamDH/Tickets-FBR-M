<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Merchant;
use App\Models\User;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $totalMerchants = Merchant::count();
        $pendingMerchants = Merchant::where('verification_status', 'pending')->count();
        $totalBookings = Booking::count();
        $totalServices = Service::count();
        $totalRevenue = Booking::where('status', 'completed')->sum('total_amount');

        return [
            Stat::make('Total Users', number_format($totalUsers))
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
                
            Stat::make('Total Merchants', number_format($totalMerchants))
                ->description($pendingMerchants . ' pending approval')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('success'),
                
            Stat::make('Total Bookings', number_format($totalBookings))
                ->description('All time bookings')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('info'),
                
            Stat::make('Total Services', number_format($totalServices))
                ->description('Available services')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('warning'),
                
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('From completed bookings')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
}
