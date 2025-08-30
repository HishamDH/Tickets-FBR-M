<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ChartDataService
{
    /**
     * Get dashboard charts data
     */
    public function getDashboardCharts($period = 30)
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays($period);
        
        return [
            'revenue_chart' => $this->getRevenueChart($startDate, $endDate, 'day'),
            'bookings_chart' => $this->getBookingsChart($startDate, $endDate, 'day'),
            'services_pie_chart' => $this->getTopServicesPieChart($startDate, $endDate),
            'merchants_bar_chart' => $this->getTopMerchantsBarChart($startDate, $endDate),
            'satisfaction_gauge' => $this->getCustomerSatisfactionGauge($startDate, $endDate),
            'conversion_funnel' => $this->getConversionFunnel($startDate, $endDate),
        ];
    }

    /**
     * Get revenue chart data
     */
    public function getRevenueChart($startDate, $endDate, $groupBy = 'day')
    {
        $groupByClause = $this->getGroupByClause($groupBy);
        $dateFormat = $this->getDateFormat($groupBy);
        
        $data = Booking::selectRaw("{$groupByClause} as period, SUM(total_amount) as revenue, COUNT(*) as bookings")
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->groupBy('period')
            ->orderBy('period')
            ->get();
            
        return [
            'type' => 'line',
            'data' => [
                'labels' => $data->pluck('period')->map(function($period) use ($dateFormat) {
                    return Carbon::parse($period)->format($dateFormat);
                })->toArray(),
                'datasets' => [
                    [
                        'label' => 'الإيرادات (ريال)',
                        'data' => $data->pluck('revenue')->toArray(),
                        'borderColor' => 'rgb(59, 130, 246)',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'display' => true,
                        'position' => 'top',
                    ],
                    'tooltip' => [
                        'mode' => 'index',
                        'intersect' => false,
                    ]
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'ticks' => [
                            'callback' => 'function(value) { return value.toLocaleString() + " ريال"; }'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get bookings chart data
     */
    public function getBookingsChart($startDate, $endDate, $groupBy = 'day')
    {
        $groupByClause = $this->getGroupByClause($groupBy);
        $dateFormat = $this->getDateFormat($groupBy);
        
        $data = Booking::selectRaw("{$groupByClause} as period")
            ->selectRaw('COUNT(*) as total_bookings')
            ->selectRaw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_bookings')
            ->selectRaw('SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled_bookings')
            ->selectRaw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_bookings')
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->groupBy('period')
            ->orderBy('period')
            ->get();
            
        return [
            'type' => 'bar',
            'data' => [
                'labels' => $data->pluck('period')->map(function($period) use ($dateFormat) {
                    return Carbon::parse($period)->format($dateFormat);
                })->toArray(),
                'datasets' => [
                    [
                        'label' => 'مكتملة',
                        'data' => $data->pluck('completed_bookings')->toArray(),
                        'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                        'borderColor' => 'rgb(34, 197, 94)',
                        'borderWidth' => 1,
                    ],
                    [
                        'label' => 'قيد الانتظار',
                        'data' => $data->pluck('pending_bookings')->toArray(),
                        'backgroundColor' => 'rgba(251, 191, 36, 0.8)',
                        'borderColor' => 'rgb(251, 191, 36)',
                        'borderWidth' => 1,
                    ],
                    [
                        'label' => 'ملغية',
                        'data' => $data->pluck('cancelled_bookings')->toArray(),
                        'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                        'borderColor' => 'rgb(239, 68, 68)',
                        'borderWidth' => 1,
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'display' => true,
                        'position' => 'top',
                    ]
                ],
                'scales' => [
                    'x' => [
                        'stacked' => false,
                    ],
                    'y' => [
                        'beginAtZero' => true,
                        'stacked' => false,
                    ]
                ]
            ]
        ];
    }

    /**
     * Get top services pie chart
     */
    public function getTopServicesPieChart($startDate, $endDate, $limit = 8)
    {
        $data = Service::select('services.name')
            ->selectRaw('SUM(bookings.total_amount) as revenue')
            ->selectRaw('COUNT(bookings.id) as bookings_count')
            ->join('bookings', 'services.id', '=', 'bookings.service_id')
            ->whereBetween('bookings.booking_date', [$startDate, $endDate])
            ->where('bookings.status', 'completed')
            ->groupBy('services.id', 'services.name')
            ->orderBy('revenue', 'desc')
            ->limit($limit)
            ->get();
            
        $colors = [
            'rgba(59, 130, 246, 0.8)',   // Blue
            'rgba(34, 197, 94, 0.8)',    // Green
            'rgba(251, 191, 36, 0.8)',   // Yellow
            'rgba(239, 68, 68, 0.8)',    // Red
            'rgba(168, 85, 247, 0.8)',   // Purple
            'rgba(236, 72, 153, 0.8)',   // Pink
            'rgba(20, 184, 166, 0.8)',   // Teal
            'rgba(245, 101, 101, 0.8)',  // Orange
        ];
        
        return [
            'type' => 'doughnut',
            'data' => [
                'labels' => $data->pluck('name')->toArray(),
                'datasets' => [
                    [
                        'data' => $data->pluck('revenue')->toArray(),
                        'backgroundColor' => array_slice($colors, 0, $data->count()),
                        'borderWidth' => 2,
                        'borderColor' => '#ffffff',
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'display' => true,
                        'position' => 'bottom',
                    ],
                    'tooltip' => [
                        'callbacks' => [
                            'label' => 'function(context) { 
                                return context.label + ": " + context.parsed.toLocaleString() + " ريال"; 
                            }'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Get top merchants bar chart
     */
    public function getTopMerchantsBarChart($startDate, $endDate, $limit = 10)
    {
        $data = User::select('users.name')
            ->selectRaw('SUM(bookings.total_amount) as revenue')
            ->selectRaw('COUNT(bookings.id) as bookings_count')
            ->selectRaw('AVG(bookings.rating) as avg_rating')
            ->join('services', 'users.id', '=', 'services.merchant_id')
            ->join('bookings', 'services.id', '=', 'bookings.service_id')
            ->whereBetween('bookings.booking_date', [$startDate, $endDate])
            ->where('bookings.status', 'completed')
            ->where('users.user_type', 'merchant')
            ->groupBy('users.id', 'users.name')
            ->orderBy('revenue', 'desc')
            ->limit($limit)
            ->get();
            
        return [
            'type' => 'bar',
            'data' => [
                'labels' => $data->pluck('name')->toArray(),
                'datasets' => [
                    [
                        'label' => 'الإيرادات (ريال)',
                        'data' => $data->pluck('revenue')->toArray(),
                        'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                        'borderColor' => 'rgb(59, 130, 246)',
                        'borderWidth' => 1,
                        'yAxisID' => 'y',
                    ],
                    [
                        'label' => 'عدد الحجوزات',
                        'data' => $data->pluck('bookings_count')->toArray(),
                        'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                        'borderColor' => 'rgb(34, 197, 94)',
                        'borderWidth' => 1,
                        'yAxisID' => 'y1',
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'display' => true,
                        'position' => 'top',
                    ]
                ],
                'scales' => [
                    'y' => [
                        'type' => 'linear',
                        'display' => true,
                        'position' => 'left',
                        'beginAtZero' => true,
                    ],
                    'y1' => [
                        'type' => 'linear',
                        'display' => true,
                        'position' => 'right',
                        'beginAtZero' => true,
                        'grid' => [
                            'drawOnChartArea' => false,
                        ],
                    ]
                ]
            ]
        ];
    }

    /**
     * Get customer satisfaction gauge
     */
    public function getCustomerSatisfactionGauge($startDate, $endDate)
    {
        $avgRating = Booking::whereBetween('booking_date', [$startDate, $endDate])
            ->whereNotNull('rating')
            ->avg('rating') ?? 0;
            
        $ratingDistribution = Booking::selectRaw('rating, COUNT(*) as count')
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->whereNotNull('rating')
            ->groupBy('rating')
            ->orderBy('rating')
            ->get();
            
        return [
            'type' => 'doughnut',
            'data' => [
                'labels' => ['ممتاز (4.5-5)', 'جيد جداً (3.5-4.4)', 'جيد (2.5-3.4)', 'ضعيف (1-2.4)'],
                'datasets' => [
                    [
                        'data' => [
                            $ratingDistribution->whereBetween('rating', [4.5, 5])->sum('count'),
                            $ratingDistribution->whereBetween('rating', [3.5, 4.4])->sum('count'),
                            $ratingDistribution->whereBetween('rating', [2.5, 3.4])->sum('count'),
                            $ratingDistribution->whereBetween('rating', [1, 2.4])->sum('count'),
                        ],
                        'backgroundColor' => [
                            'rgba(34, 197, 94, 0.8)',    // Green for excellent
                            'rgba(59, 130, 246, 0.8)',   // Blue for very good
                            'rgba(251, 191, 36, 0.8)',   // Yellow for good
                            'rgba(239, 68, 68, 0.8)',    // Red for poor
                        ],
                        'borderWidth' => 2,
                        'borderColor' => '#ffffff',
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'display' => true,
                        'position' => 'bottom',
                    ]
                ]
            ],
            'average_rating' => round($avgRating, 1),
            'total_ratings' => $ratingDistribution->sum('count'),
        ];
    }

    /**
     * Get conversion funnel chart
     */
    public function getConversionFunnel($startDate, $endDate)
    {
        // This is a simplified funnel - in a real app you'd track page views, clicks, etc.
        $totalViews = Booking::whereBetween('created_at', [$startDate, $endDate])->count() * 10; // Simulated
        $totalBookings = Booking::whereBetween('booking_date', [$startDate, $endDate])->count();
        $completedBookings = Booking::whereBetween('booking_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();
        $ratedBookings = Booking::whereBetween('booking_date', [$startDate, $endDate])
            ->whereNotNull('rating')
            ->count();
            
        return [
            'type' => 'funnel',
            'data' => [
                'labels' => ['زيارات الموقع', 'طلبات الحجز', 'حجوزات مكتملة', 'تقييمات العملاء'],
                'datasets' => [
                    [
                        'data' => [$totalViews, $totalBookings, $completedBookings, $ratedBookings],
                        'backgroundColor' => [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(251, 191, 36, 0.8)',
                            'rgba(168, 85, 247, 0.8)',
                        ]
                    ]
                ]
            ],
            'conversion_rates' => [
                'view_to_booking' => $totalViews > 0 ? round(($totalBookings / $totalViews) * 100, 1) : 0,
                'booking_to_completion' => $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100, 1) : 0,
                'completion_to_rating' => $completedBookings > 0 ? round(($ratedBookings / $completedBookings) * 100, 1) : 0,
            ]
        ];
    }

    /**
     * Get specific chart data by type
     */
    public function getChartData($chartType, $period = 30, $filters = [])
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays($period);
        
        switch ($chartType) {
            case 'revenue_by_hour':
                return $this->getRevenueByHourChart($startDate, $endDate);
            case 'bookings_by_day_of_week':
                return $this->getBookingsByDayOfWeekChart($startDate, $endDate);
            case 'service_performance':
                return $this->getServicePerformanceChart($startDate, $endDate, $filters);
            case 'geographic_distribution':
                return $this->getGeographicDistributionChart($startDate, $endDate);
            case 'payment_methods':
                return $this->getPaymentMethodsChart($startDate, $endDate);
            default:
                return ['error' => 'Unknown chart type'];
        }
    }

    /**
     * Get revenue by hour chart
     */
    protected function getRevenueByHourChart($startDate, $endDate)
    {
        $data = Booking::selectRaw('HOUR(booking_date) as hour, SUM(total_amount) as revenue, COUNT(*) as bookings')
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
            
        // Fill missing hours with zero
        $hourlyData = [];
        for ($i = 0; $i < 24; $i++) {
            $hourData = $data->firstWhere('hour', $i);
            $hourlyData[] = [
                'hour' => $i,
                'revenue' => $hourData ? $hourData->revenue : 0,
                'bookings' => $hourData ? $hourData->bookings : 0,
            ];
        }
        
        return [
            'type' => 'line',
            'data' => [
                'labels' => array_map(function($h) { return $h['hour'] . ':00'; }, $hourlyData),
                'datasets' => [
                    [
                        'label' => 'الإيرادات (ريال)',
                        'data' => array_column($hourlyData, 'revenue'),
                        'borderColor' => 'rgb(59, 130, 246)',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'fill' => true,
                    ]
                ]
            ]
        ];
    }

    // Helper methods
    protected function getGroupByClause($groupBy)
    {
        switch ($groupBy) {
            case 'hour':
                return 'DATE_FORMAT(booking_date, "%Y-%m-%d %H:00:00")';
            case 'day':
                return 'DATE(booking_date)';
            case 'week':
                return 'DATE_FORMAT(booking_date, "%Y-%u")';
            case 'month':
                return 'DATE_FORMAT(booking_date, "%Y-%m")';
            case 'year':
                return 'YEAR(booking_date)';
            default:
                return 'DATE(booking_date)';
        }
    }

    protected function getDateFormat($groupBy)
    {
        switch ($groupBy) {
            case 'hour':
                return 'H:i';
            case 'day':
                return 'M j';
            case 'week':
                return 'M j';
            case 'month':
                return 'M Y';
            case 'year':
                return 'Y';
            default:
                return 'M j';
        }
    }

    // Placeholder methods for additional charts
    protected function getBookingsByDayOfWeekChart($startDate, $endDate) { /* Implementation */ }
    protected function getServicePerformanceChart($startDate, $endDate, $filters) { /* Implementation */ }
    protected function getGeographicDistributionChart($startDate, $endDate) { /* Implementation */ }
    protected function getPaymentMethodsChart($startDate, $endDate) { /* Implementation */ }
}
