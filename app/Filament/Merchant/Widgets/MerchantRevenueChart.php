<?php

namespace App\Filament\Merchant\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MerchantRevenueChart extends ChartWidget
{
    protected static ?string $heading = 'My Monthly Revenue';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $user = Auth::user();
        $merchant = $user->merchant;

        if (! $merchant) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $data = Booking::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as revenue')
        )
            ->whereHas('service', function ($query) use ($merchant) {
                $query->where('merchant_id', $merchant->id);
            })
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
                    'label' => 'Revenue ($)',
                    'data' => $chartData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'borderColor' => 'rgb(16, 185, 129)',
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
