<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MonthlyRevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Revenue';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Booking::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as revenue')
        )
            ->where('status', 'completed')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->pluck('revenue', 'month')
            ->toArray();

        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
        ];

        $chartData = [];
        foreach ($months as $monthNum => $monthName) {
            $chartData[] = $data[$monthNum] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $chartData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => array_values($months),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
