<?php

namespace App\Filament\Customer\Pages;

use App\Filament\Customer\Widgets\CustomerStatsWidget;
use App\Filament\Customer\Widgets\RecentBookingsWidget;
use App\Filament\Customer\Widgets\RecommendedServicesWidget;
use App\Filament\Customer\Widgets\UpcomingEventsWidget;
use App\Models\Booking;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament-panels::pages.dashboard';

    public function getTitle(): string
    {
        return 'Customer Dashboard';
    }

    public function getHeading(): string
    {
        $user = Auth::user();
        $greeting = $this->getTimeBasedGreeting();

        return "{$greeting}, ".($user->name ?? 'Valued Customer').'!';
    }

    public function getSubheading(): ?string
    {
        $upcomingBookings = Booking::where('customer_id', Auth::id())
            ->where('booking_date', '>=', now())
            ->count();

        return $upcomingBookings > 0
            ? "You have {$upcomingBookings} upcoming booking".($upcomingBookings > 1 ? 's' : '')
            : 'Discover amazing services and make new bookings';
    }

    public function getWidgets(): array
    {
        return [
            CustomerStatsWidget::class,
            [RecentBookingsWidget::class, ['limit' => 5]],
            [UpcomingEventsWidget::class, ['limit' => 6]],
            [RecommendedServicesWidget::class, ['limit' => 4]],
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

    protected function getTimeBasedGreeting(): string
    {
        $hour = now()->hour;

        if ($hour < 12) {
            return 'Good Morning';
        } elseif ($hour < 17) {
            return 'Good Afternoon';
        } else {
            return 'Good Evening';
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('quickBook')
                ->label('Quick Book Service')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->url('/services')
                ->openUrlInNewTab(false),

            \Filament\Actions\Action::make('viewCart')
                ->label('View Cart')
                ->icon('heroicon-o-shopping-cart')
                ->color('success')
                ->badge(function () {
                    try {
                        return \App\Models\Cart::where('user_id', Auth::id())->count() ?: 0;
                    } catch (\Exception $e) {
                        return 0;
                    }
                })
                ->url('/cart')
                ->visible(function () {
                    try {
                        return \App\Models\Cart::where('user_id', Auth::id())->exists();
                    } catch (\Exception $e) {
                        return false;
                    }
                }),
        ];
    }
}
