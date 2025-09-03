<?php

namespace App\Filament\Merchant\Pages;

use App\Filament\Merchant\Widgets\MerchantAnalyticsWidget;
use App\Filament\Merchant\Widgets\MerchantRevenueWidget;
use App\Filament\Merchant\Widgets\MerchantStatsWidget;
use App\Filament\Merchant\Widgets\RecentBookingsChart;
use App\Filament\Merchant\Widgets\ServicePerformanceWidget;
use App\Models\Booking;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament-panels::pages.dashboard';

    public function getTitle(): string
    {
        return 'Merchant Dashboard';
    }

    public function getHeading(): string
    {
        $user = Auth::user();
        $merchant = $user->merchant ?? $user;
        $businessName = $merchant->business_name ?? $user->name ?? 'Your Business';

        return "Welcome back, {$businessName}!";
    }

    public function getSubheading(): ?string
    {
        $todayBookings = Booking::whereHas('offering', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->whereDate('booking_date', today())
            ->count();

        $pendingBookings = Booking::whereHas('offering', function ($query) {
            $query->where('user_id', Auth::id());
        })
            ->where('booking_status', 'pending')
            ->count();

        $messages = [];
        if ($todayBookings > 0) {
            $messages[] = "{$todayBookings} booking".($todayBookings > 1 ? 's' : '').' today';
        }
        if ($pendingBookings > 0) {
            $messages[] = "{$pendingBookings} pending approval".($pendingBookings > 1 ? 's' : '');
        }

        return ! empty($messages)
            ? implode(' • ', $messages)
            : 'Manage your services, bookings, and business analytics';
    }

    public function getWidgets(): array
    {
        return [
            MerchantStatsWidget::class,
            [MerchantRevenueWidget::class, ['columnSpan' => 2]],
            [RecentBookingsChart::class, ['columnSpan' => 2]],
            [ServicePerformanceWidget::class, ['columnSpan' => 'full']],
            [MerchantAnalyticsWidget::class, ['columnSpan' => 'full']],
        ];
    }

    public function getColumns(): int|string|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'lg' => 3,
            'xl' => 4,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('createService')
                ->label('New Service')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->url('/merchant/offerings/create')
                ->openUrlInNewTab(false),

            \Filament\Actions\Action::make('viewBookings')
                ->label('View Bookings')
                ->icon('heroicon-o-calendar-days')
                ->color('success')
                ->badge(function () {
                    try {
                        return Booking::whereHas('offering', function ($query) {
                            $query->where('user_id', Auth::id());
                        })->where('booking_status', 'pending')->count();
                    } catch (\Exception $e) {
                        return 0;
                    }
                })
                ->url('/merchant/bookings')
                ->visible(function () {
                    try {
                        return Booking::whereHas('offering', function ($query) {
                            $query->where('user_id', Auth::id());
                        })->exists();
                    } catch (\Exception $e) {
                        return false;
                    }
                }),

            \Filament\Actions\Action::make('analytics')
                ->label('Analytics')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->url('/analytics')
                ->openUrlInNewTab(false),
        ];
    }
}
