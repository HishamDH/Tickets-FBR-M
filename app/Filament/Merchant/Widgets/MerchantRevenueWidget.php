<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class MerchantRevenueWidget extends ChartWidget
{
    protected static ?string $heading = 'Revenue Overview';

    protected static ?string $description = 'Daily revenue for the last 30 days';

    protected int|string|array $columnSpan = 2;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $userId = Auth::id();
        $days = collect();
        $revenues = collect();

        // Get last 30 days revenue
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days->push($date->format('M j'));

            $dayRevenue = Booking::whereHas('offering', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
                ->where('status', 'completed')
                ->whereDate('created_at', $date->toDateString())
                ->sum('total_amount');

            $revenues->push($dayRevenue);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Daily Revenue',
                    'data' => $revenues->toArray(),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $days->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "function(value) { return '$' + value; }",
                    ],
                ],
            ],
            'interaction' => [
                'intersect' => false,
            ],
            'maintainAspectRatio' => false,
        ];
    }
}
