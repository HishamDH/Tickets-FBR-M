<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Get Key Performance Indicators
     */
    public function getKPIs($period = 30, $comparison = 'previous', $merchantId = null)
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays($period);

        // Current period data
        $currentData = $this->getKPIData($startDate, $endDate, $merchantId);

        // Comparison period data
        if ($comparison === 'previous') {
            $comparisonStartDate = $startDate->copy()->subDays($period);
            $comparisonEndDate = $startDate->copy();
        } else { // year_ago
            $comparisonStartDate = $startDate->copy()->subYear();
            $comparisonEndDate = $endDate->copy()->subYear();
        }

        $comparisonData = $this->getKPIData($comparisonStartDate, $comparisonEndDate, $merchantId);

        return [
            'total_revenue' => [
                'current' => $currentData['total_revenue'],
                'previous' => $comparisonData['total_revenue'],
                'change' => $this->calculatePercentageChange(
                    $comparisonData['total_revenue'],
                    $currentData['total_revenue']
                ),
                'trend' => $this->calculateTrend($currentData['daily_revenue']),
            ],
            'total_bookings' => [
                'current' => $currentData['total_bookings'],
                'previous' => $comparisonData['total_bookings'],
                'change' => $this->calculatePercentageChange(
                    $comparisonData['total_bookings'],
                    $currentData['total_bookings']
                ),
                'trend' => $this->calculateTrend($currentData['daily_bookings']),
            ],
            'active_merchants' => [
                'current' => $currentData['active_merchants'],
                'previous' => $comparisonData['active_merchants'],
                'change' => $this->calculatePercentageChange(
                    $comparisonData['active_merchants'],
                    $currentData['active_merchants']
                ),
            ],
            'avg_booking_value' => [
                'current' => $currentData['avg_booking_value'],
                'previous' => $comparisonData['avg_booking_value'],
                'change' => $this->calculatePercentageChange(
                    $comparisonData['avg_booking_value'],
                    $currentData['avg_booking_value']
                ),
            ],
            'customer_satisfaction' => [
                'current' => $currentData['customer_satisfaction'],
                'previous' => $comparisonData['customer_satisfaction'],
                'change' => $this->calculatePercentageChange(
                    $comparisonData['customer_satisfaction'],
                    $currentData['customer_satisfaction']
                ),
            ],
            'conversion_rate' => [
                'current' => $currentData['conversion_rate'],
                'previous' => $comparisonData['conversion_rate'],
                'change' => $this->calculatePercentageChange(
                    $comparisonData['conversion_rate'],
                    $currentData['conversion_rate']
                ),
            ],
        ];
    }

    /**
     * Get KPI raw data for a specific period
     */
    protected function getKPIData($startDate, $endDate, $merchantId = null)
    {
        // Build base queries with merchant filtering if specified
        $revenueQuery = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed');
        $bookingsQuery = Booking::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($merchantId) {
            $revenueQuery->whereHas('service', function($q) use ($merchantId) {
                $q->where('merchant_id', $merchantId);
            });
            $bookingsQuery->whereHas('service', function($q) use ($merchantId) {
                $q->where('merchant_id', $merchantId);
            });
        }

        $totalRevenue = $revenueQuery->sum('total_amount');
        $totalBookings = $bookingsQuery->count();

        // For merchant-specific view, active merchants is always 1 (the current merchant) if they have bookings, 0 otherwise
        if ($merchantId) {
            $activeMerchants = $totalBookings > 0 ? 1 : 0;
        } else {
            $activeMerchants = User::whereHas('services.bookings', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
                ->where('user_type', 'merchant')
                ->count();
        }

        $avgBookingValue = $totalBookings > 0 ? $totalRevenue / $totalBookings : 0;

        // Get customer satisfaction from reviews, not bookings
        $reviewsQuery = \App\Models\Review::whereBetween('created_at', [$startDate, $endDate])
            ->where('is_approved', true);
        
        if ($merchantId) {
            $reviewsQuery->whereHas('service', function($q) use ($merchantId) {
                $q->where('merchant_id', $merchantId);
            });
        }
        
        $customerSatisfaction = $reviewsQuery->avg('rating') ?? 0;

        // Daily revenue for trend calculation
        $dailyRevenueQuery = Booking::selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed');
        
        if ($merchantId) {
            $dailyRevenueQuery->whereHas('service', function($q) use ($merchantId) {
                $q->where('merchant_id', $merchantId);
            });
        }
        
        $dailyRevenue = $dailyRevenueQuery->groupBy('date')
            ->orderBy('date')
            ->pluck('revenue')
            ->toArray();

        // Daily bookings for trend calculation
        $dailyBookingsQuery = Booking::selectRaw('DATE(created_at) as date, COUNT(*) as bookings')
            ->whereBetween('created_at', [$startDate, $endDate]);
        
        if ($merchantId) {
            $dailyBookingsQuery->whereHas('service', function($q) use ($merchantId) {
                $q->where('merchant_id', $merchantId);
            });
        }
        
        $dailyBookings = $dailyBookingsQuery->groupBy('date')
            ->orderBy('date')
            ->pluck('bookings')
            ->toArray();

        // Conversion rate calculation (simplified)
        $conversionRate = $this->calculateConversionRate($startDate, $endDate);

        return [
            'total_revenue' => $totalRevenue,
            'total_bookings' => $totalBookings,
            'active_merchants' => $activeMerchants,
            'avg_booking_value' => $avgBookingValue,
            'customer_satisfaction' => round($customerSatisfaction, 1),
            'conversion_rate' => $conversionRate,
            'daily_revenue' => $dailyRevenue,
            'daily_bookings' => $dailyBookings,
        ];
    }

    /**
     * Get trends analysis
     */
    public function getTrends($period = 30)
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays($period);

        return [
            'revenue_trend' => $this->getRevenueTrend($startDate, $endDate),
            'booking_trend' => $this->getBookingTrend($startDate, $endDate),
            'popular_services' => $this->getPopularServicesTrend($startDate, $endDate),
            'peak_hours' => $this->getPeakHoursTrend($startDate, $endDate),
            'geographic_trend' => $this->getGeographicTrend($startDate, $endDate),
        ];
    }

    /**
     * Get performance insights
     */
    public function getInsights($period = 30)
    {
        $insights = [];

        // Revenue insights
        $revenueGrowth = $this->analyzeRevenueGrowth($period);
        if ($revenueGrowth['significant']) {
            $insights[] = [
                'type' => 'revenue',
                'icon' => $revenueGrowth['positive'] ? '📈' : '📉',
                'title' => $revenueGrowth['positive'] ? 'نمو إيرادات ممتاز' : 'انخفاض في الإيرادات',
                'description' => $revenueGrowth['description'],
                'action' => $revenueGrowth['action'],
                'priority' => $revenueGrowth['positive'] ? 'success' : 'warning',
            ];
        }

        // Customer satisfaction insights
        $satisfactionAnalysis = $this->analyzeCustomerSatisfaction($period);
        if ($satisfactionAnalysis['noteworthy']) {
            $insights[] = [
                'type' => 'satisfaction',
                'icon' => '⭐',
                'title' => 'تحليل رضا العملاء',
                'description' => $satisfactionAnalysis['description'],
                'action' => $satisfactionAnalysis['action'],
                'priority' => $satisfactionAnalysis['priority'],
            ];
        }

        // Merchant performance insights
        $merchantInsights = $this->analyzeMerchantPerformance($period);
        $insights = array_merge($insights, $merchantInsights);

        // Seasonal insights
        $seasonalInsights = $this->analyzeSeasonalPatterns();
        $insights = array_merge($insights, $seasonalInsights);

        return $insights;
    }

    /**
     * Get performance alerts
     */
    public function getPerformanceAlerts()
    {
        $alerts = [];

        // Low performance merchants
        $lowPerformingMerchants = $this->getLowPerformingMerchants();
        if ($lowPerformingMerchants->count() > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => '⚠️',
                'title' => 'تجار بحاجة لدعم',
                'message' => "{$lowPerformingMerchants->count()} تاجر يحتاج للمساعدة لتحسين الأداء",
                'action_url' => route('analytics.merchants', ['filter' => 'low_performance']),
                'action_text' => 'عرض التفاصيل',
            ];
        }

        // Revenue drop alerts
        $revenueDropAlert = $this->checkRevenueDropAlert();
        if ($revenueDropAlert) {
            $alerts[] = $revenueDropAlert;
        }

        // System performance alerts
        $systemAlerts = $this->getSystemPerformanceAlerts();
        $alerts = array_merge($alerts, $systemAlerts);

        return $alerts;
    }

    /**
     * Get revenue by service
     */
    public function getRevenueByService($startDate, $endDate, $limit = 10)
    {
        return Service::select('services.*')
            ->selectRaw('SUM(bookings.total_amount) as total_revenue')
            ->selectRaw('COUNT(bookings.id) as total_bookings')
            ->join('bookings', 'services.id', '=', 'bookings.service_id')
            ->whereBetween('bookings.booking_date', [$startDate, $endDate])
            ->where('bookings.status', 'completed')
            ->groupBy('services.id')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->with('merchant.user')
            ->get();
    }

    /**
     * Get revenue by merchant
     */
    public function getRevenueByMerchant($startDate, $endDate, $limit = 10)
    {
        return User::select('users.*')
            ->selectRaw('SUM(bookings.total_amount) as total_revenue')
            ->selectRaw('COUNT(bookings.id) as total_bookings')
            ->selectRaw('0 as avg_rating')
            ->join('services', 'users.id', '=', 'services.merchant_id')
            ->join('bookings', 'services.id', '=', 'bookings.service_id')
            ->whereBetween('bookings.booking_date', [$startDate, $endDate])
            ->where('bookings.status', 'completed')
            ->where('users.user_type', 'merchant')
            ->groupBy('users.id')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get payment methods analysis
     */
    public function getPaymentMethodsAnalysis($startDate, $endDate)
    {
        $paymentMethods = Booking::selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as revenue')
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->groupBy('payment_method')
            ->orderBy('revenue', 'desc')
            ->get();

        $total = $paymentMethods->sum('revenue');

        return $paymentMethods->map(function ($method) use ($total) {
            return [
                'method' => $method->payment_method,
                'count' => $method->count,
                'revenue' => $method->revenue,
                'percentage' => $total > 0 ? round(($method->revenue / $total) * 100, 1) : 0,
            ];
        });
    }

    /**
     * Get revenue forecast using simple linear regression
     */
    public function getRevenueForecast($startDate, $endDate, $forecastDays = 30)
    {
        // Get historical daily revenue
        $historicalData = Booking::selectRaw('DATE(booking_date) as date, SUM(total_amount) as revenue')
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        if ($historicalData->count() < 7) {
            return ['error' => 'Insufficient data for forecasting'];
        }

        // Simple trend calculation
        $revenues = $historicalData->pluck('revenue')->toArray();
        $trend = $this->calculateLinearTrend($revenues);

        // Generate forecast
        $forecast = [];
        $lastValue = end($revenues);

        for ($i = 1; $i <= $forecastDays; $i++) {
            $forecastValue = $lastValue + ($trend * $i);
            $forecast[] = [
                'date' => Carbon::now()->addDays($i)->format('Y-m-d'),
                'forecasted_revenue' => max(0, $forecastValue), // Ensure non-negative
                'confidence' => max(0, 100 - ($i * 2)), // Decrease confidence over time
            ];
        }

        return [
            'historical' => $historicalData,
            'forecast' => $forecast,
            'trend' => $trend > 0 ? 'increasing' : ($trend < 0 ? 'decreasing' : 'stable'),
            'trend_value' => $trend,
        ];
    }

    // Helper methods
    protected function calculatePercentageChange($old, $new)
    {
        if ($old == 0) {
            return $new > 0 ? 100 : 0;
        }

        return round((($new - $old) / $old) * 100, 1);
    }

    protected function calculateTrend($data)
    {
        if (count($data) < 2) {
            return 'stable';
        }

        $trend = $this->calculateLinearTrend($data);

        if ($trend > 0.1) {
            return 'increasing';
        }
        if ($trend < -0.1) {
            return 'decreasing';
        }

        return 'stable';
    }

    protected function calculateLinearTrend($data)
    {
        $n = count($data);
        if ($n < 2) {
            return 0;
        }

        $sumX = $sumY = $sumXY = $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumX += $i;
            $sumY += $data[$i];
            $sumXY += $i * $data[$i];
            $sumX2 += $i * $i;
        }

        $denominator = ($n * $sumX2) - ($sumX * $sumX);
        if ($denominator == 0) {
            return 0;
        }

        return (($n * $sumXY) - ($sumX * $sumY)) / $denominator;
    }

    protected function calculateConversionRate($startDate, $endDate)
    {
        // This is a simplified conversion rate calculation
        // In a real application, you'd track page views, clicks, etc.
        $totalBookings = Booking::whereBetween('booking_date', [$startDate, $endDate])->count();
        $completedBookings = Booking::whereBetween('booking_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();

        return $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100, 1) : 0;
    }

    // Placeholder methods for complex analytics (to be implemented)
    protected function getRevenueTrend($startDate, $endDate)
    {
        return [];
    }

    protected function getBookingTrend($startDate, $endDate)
    {
        return [];
    }

    protected function getPopularServicesTrend($startDate, $endDate)
    {
        return [];
    }

    protected function getPeakHoursTrend($startDate, $endDate)
    {
        return [];
    }

    protected function getGeographicTrend($startDate, $endDate)
    {
        return [];
    }

    protected function analyzeRevenueGrowth($period)
    {
        return ['significant' => false];
    }

    protected function analyzeCustomerSatisfaction($period)
    {
        return ['noteworthy' => false];
    }

    protected function analyzeMerchantPerformance($period)
    {
        return [];
    }

    protected function analyzeSeasonalPatterns()
    {
        return [];
    }

    protected function getLowPerformingMerchants()
    {
        return collect();
    }

    protected function checkRevenueDropAlert()
    {
        return null;
    }

    protected function getSystemPerformanceAlerts()
    {
        return [];
    }

    public function getCustomerAcquisition($period)
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getCustomerRetention($period)
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getCustomerSegments()
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getCustomerJourney($period)
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getCustomerSatisfaction($period)
    {
        // TODO: Implement actual logic
        return ['current' => 0, 'previous' => 0, 'change' => 0];
    }

    public function getGeographicDistribution($period)
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getMerchantRankings($period, $sortBy)
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getMerchantPerformanceMetrics($period)
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getServiceCategoryAnalysis($period)
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getMerchantGrowthAnalysis($period)
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getQualityMetrics($period)
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getSystemPerformance($period)
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getBookingFunnel($period)
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getPeakHours($period)
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getDeviceAnalytics($period)
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getErrorTracking($period)
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getFeatureUsage($period)
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getActiveUsers()
    {
        // TODO: Implement actual logic
        return 0;
    }

    public function getLiveBookings()
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getTodayRevenue()
    {
        // TODO: Implement actual logic
        return 0;
    }

    public function getTrendingServices()
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getSystemStatus()
    {
        // TODO: Implement actual logic
        return ['status' => 'ok'];
    }

    public function getDemandPrediction($period)
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getChurnPrediction()
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getSeasonalAnalysis()
    {
        // TODO: Implement actual logic
        return [];
    }

    public function getRecommendations()
    {
        // TODO: Implement actual logic
        return [];
    }

    public function buildCustomQuery(array $metrics, array $dimensions, array $filters, array $dateRange)
    {
        // TODO: Implement actual logic
        return [];
    }
}
