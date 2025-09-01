<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Booking;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class MerchantStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $merchant = $user->merchant;
        
        if (!$merchant) {
            return [];
        }

        $totalServices = Service::where('merchant_id', $merchant->id)->count();
        $activeServices = Service::where('merchant_id', $merchant->id)
            ->where('is_available', true)
            ->count();
        
        $totalBookings = Booking::whereHas('service', function($query) use ($merchant) {
            $query->where('merchant_id', $merchant->id);
        })->count();
        
        $pendingBookings = Booking::whereHas('service', function($query) use ($merchant) {
            $query->where('merchant_id', $merchant->id);
        })->where('status', 'pending')->count();
        
        $monthlyRevenue = Booking::whereHas('service', function($query) use ($merchant) {
            $query->where('merchant_id', $merchant->id);
        })
        ->where('status', 'completed')
        ->whereMonth('created_at', now()->month)
        ->sum('total_amount');

        return [
            Stat::make('Total Services', number_format($totalServices))
                ->description($activeServices . ' active services')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('primary'),
                
            Stat::make('Total Bookings', number_format($totalBookings))
                ->description($pendingBookings . ' pending')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('info'),
                
            Stat::make('Monthly Revenue', '$' . number_format($monthlyRevenue, 2))
                ->description('This month earnings')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
}
