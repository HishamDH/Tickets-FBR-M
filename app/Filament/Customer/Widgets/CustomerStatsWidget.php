<?php

namespace App\Filament\Customer\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class CustomerStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        
        $totalBookings = Booking::where('customer_id', $user->id)->count();
        $upcomingBookings = Booking::where('customer_id', $user->id)
            ->where('booking_status', 'confirmed')
            ->where('booking_date', '>=', now())
            ->count();
        
        $completedBookings = Booking::where('customer_id', $user->id)
            ->where('booking_status', 'completed')
            ->count();
            
        $totalSpent = Booking::where('customer_id', $user->id)
            ->where('booking_status', 'completed')
            ->sum('total_amount');

        return [
            Stat::make('Total Bookings', number_format($totalBookings))
                ->description('All your bookings')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),
                
            Stat::make('Upcoming Events', number_format($upcomingBookings))
                ->description('Confirmed bookings')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),
                
            Stat::make('Completed', number_format($completedBookings))
                ->description('Finished events')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Total Spent', '$' . number_format($totalSpent, 2))
                ->description('All time spending')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),
        ];
    }
}
