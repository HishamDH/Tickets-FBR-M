<?php

namespace App\Filament\Widgets;

use App\Models\Service;
use Filament\Widgets\ChartWidget;

class PopularServicesChart extends ChartWidget
{
    protected static ?string $heading = 'Most Popular Services';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Service::select('services.name')
            ->selectRaw('COUNT(bookings.id) as bookings_count')
            ->leftJoin('bookings', 'services.id', '=', 'bookings.service_id')
            ->groupBy('services.id', 'services.name')
            ->orderByDesc('bookings_count')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Bookings',
                    'data' => $data->pluck('bookings_count'),
                    'backgroundColor' => [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(6, 182, 212, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                    ],
                ],
            ],
            'labels' => $data->pluck('name'),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
