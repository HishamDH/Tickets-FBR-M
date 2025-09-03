<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class RecentBookingsChart extends ChartWidget
{
    protected static ?string $heading = 'Booking Trends';

    protected static ?string $description = 'Bookings by status over the last 7 days';

    protected int|string|array $columnSpan = 2;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $userId = Auth::id();
        $days = collect();
        $confirmedBookings = collect();
        $pendingBookings = collect();
        $cancelledBookings = collect();

        // Get last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days->push($date->format('M j'));

            $baseQuery = Booking::whereHas('offering', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->whereDate('created_at', $date->toDateString());

            $confirmedBookings->push($baseQuery->clone()->where('status', 'confirmed')->count());
            $pendingBookings->push($baseQuery->clone()->where('status', 'pending')->count());
            $cancelledBookings->push($baseQuery->clone()->where('status', 'cancelled')->count());
        }

        return [
            'datasets' => [
                [
                    'label' => 'Confirmed',
                    'data' => $confirmedBookings->toArray(),
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Pending',
                    'data' => $pendingBookings->toArray(),
                    'backgroundColor' => 'rgba(251, 191, 36, 0.8)',
                    'borderColor' => 'rgb(251, 191, 36)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Cancelled',
                    'data' => $cancelledBookings->toArray(),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $days->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
                'x' => [
                    'stacked' => false,
                ],
            ],
        ];
    }
}
