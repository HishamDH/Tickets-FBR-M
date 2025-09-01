<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Service;
use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class UserGrowthChart extends ChartWidget
{
    protected static ?string $heading = 'User Growth (Last 12 Months)';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = User::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as users')
        )
        ->whereYear('created_at', now()->year)
        ->groupBy('month')
        ->pluck('users', 'month')
        ->toArray();

        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];

        $chartData = [];
        foreach ($months as $monthNum => $monthName) {
            $chartData[] = $data[$monthNum] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'New Users',
                    'data' => $chartData,
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'borderColor' => 'rgb(99, 102, 241)',
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
