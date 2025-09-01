<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\ChartDataService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    protected $chartDataService;

    public function __construct(AnalyticsService $analyticsService, ChartDataService $chartDataService)
    {
        $this->middleware('auth');
        $this->analyticsService = $analyticsService;
        $this->chartDataService = $chartDataService;
    }

    /**
     * Main Analytics Dashboard
     */
    public function index(Request $request)
    {
        $period = $request->get('period', '30'); // 7, 30, 90, 365 days
        $comparison = $request->get('comparison', 'previous'); // previous, year_ago

        // Cache key for analytics data
        $cacheKey = "analytics_dashboard_{$period}_{$comparison}_".auth()->id();

        $data = Cache::remember($cacheKey, 300, function () use ($period, $comparison) {
            return [
                'kpis' => $this->analyticsService->getKPIs($period, $comparison),
                'charts' => $this->chartDataService->getDashboardCharts($period),
                'trends' => $this->analyticsService->getTrends($period),
                'insights' => $this->analyticsService->getInsights($period),
                'alerts' => $this->analyticsService->getPerformanceAlerts(),
            ];
        });

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('analytics.dashboard', compact('data', 'period', 'comparison'));
    }

    /**
     * Revenue Analytics
     */
    public function revenue(Request $request)
    {
        $startDate = Carbon::parse($request->get('start_date', Carbon::now()->subDays(30)));
        $endDate = Carbon::parse($request->get('end_date', Carbon::now()));
        $groupBy = $request->get('group_by', 'day'); // day, week, month

        $data = [
            'revenue_chart' => $this->chartDataService->getRevenueChart($startDate, $endDate, $groupBy),
            'revenue_by_service' => $this->analyticsService->getRevenueByService($startDate, $endDate),
            'revenue_by_merchant' => $this->analyticsService->getRevenueByMerchant($startDate, $endDate),
            'payment_methods' => $this->analyticsService->getPaymentMethodsAnalysis($startDate, $endDate),
            'forecasting' => $this->analyticsService->getRevenueForecast($startDate, $endDate),
        ];

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('analytics.revenue', compact('data', 'startDate', 'endDate', 'groupBy'));
    }

    /**
     * Customer Analytics
     */
    public function customers(Request $request)
    {
        $period = $request->get('period', '30');

        $data = [
            'customer_acquisition' => $this->analyticsService->getCustomerAcquisition($period),
            'customer_retention' => $this->analyticsService->getCustomerRetention($period),
            'customer_segments' => $this->analyticsService->getCustomerSegments(),
            'customer_journey' => $this->analyticsService->getCustomerJourney($period),
            'satisfaction_scores' => $this->analyticsService->getCustomerSatisfaction($period),
            'geographic_distribution' => $this->analyticsService->getGeographicDistribution($period),
        ];

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('analytics.customers', compact('data', 'period'));
    }

    /**
     * Merchant Performance Analytics
     */
    public function merchants(Request $request)
    {
        $period = $request->get('period', '30');
        $sortBy = $request->get('sort_by', 'revenue'); // revenue, bookings, rating

        $data = [
            'merchant_rankings' => $this->analyticsService->getMerchantRankings($period, $sortBy),
            'merchant_performance' => $this->analyticsService->getMerchantPerformanceMetrics($period),
            'service_categories' => $this->analyticsService->getServiceCategoryAnalysis($period),
            'merchant_growth' => $this->analyticsService->getMerchantGrowthAnalysis($period),
            'quality_metrics' => $this->analyticsService->getQualityMetrics($period),
        ];

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('analytics.merchants', compact('data', 'period', 'sortBy'));
    }

    /**
     * Operations Analytics
     */
    public function operations(Request $request)
    {
        $period = $request->get('period', '7');

        $data = [
            'system_performance' => $this->analyticsService->getSystemPerformance($period),
            'booking_funnel' => $this->analyticsService->getBookingFunnel($period),
            'peak_hours' => $this->analyticsService->getPeakHours($period),
            'device_analytics' => $this->analyticsService->getDeviceAnalytics($period),
            'error_tracking' => $this->analyticsService->getErrorTracking($period),
            'feature_usage' => $this->analyticsService->getFeatureUsage($period),
        ];

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('analytics.operations', compact('data', 'period'));
    }

    /**
     * Real-time Analytics
     */
    public function realtime(Request $request)
    {
        $data = [
            'active_users' => $this->analyticsService->getActiveUsers(),
            'live_bookings' => $this->analyticsService->getLiveBookings(),
            'revenue_today' => $this->analyticsService->getTodayRevenue(),
            'popular_services' => $this->analyticsService->getTrendingServices(),
            'system_status' => $this->analyticsService->getSystemStatus(),
        ];

        return response()->json($data);
    }

    /**
     * Predictive Analytics
     */
    public function predictions(Request $request)
    {
        $type = $request->get('type', 'revenue'); // revenue, demand, churn
        $period = $request->get('period', '30');

        $data = [
            'revenue_forecast' => $this->analyticsService->getRevenueForecast(
                Carbon::now()->subDays($period),
                Carbon::now()
            ),
            'demand_prediction' => $this->analyticsService->getDemandPrediction($period),
            'churn_prediction' => $this->analyticsService->getChurnPrediction(),
            'seasonal_analysis' => $this->analyticsService->getSeasonalAnalysis(),
            'recommendation_engine' => $this->analyticsService->getRecommendations(),
        ];

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('analytics.predictions', compact('data', 'type', 'period'));
    }

    /**
     * Export Analytics Data
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'dashboard'); // dashboard, revenue, customers, etc.
        $format = $request->get('format', 'pdf'); // pdf, excel, csv
        $period = $request->get('period', '30');

        switch ($type) {
            case 'revenue':
                return $this->exportRevenueAnalytics($format, $period);
            case 'customers':
                return $this->exportCustomerAnalytics($format, $period);
            case 'merchants':
                return $this->exportMerchantAnalytics($format, $period);
            default:
                return $this->exportDashboardAnalytics($format, $period);
        }
    }

    /**
     * Get chart data for AJAX requests
     */
    public function chartData(Request $request)
    {
        $chartType = $request->get('chart_type');
        $period = $request->get('period', '30');
        $filters = $request->get('filters', []);

        $data = $this->chartDataService->getChartData($chartType, $period, $filters);

        return response()->json($data);
    }

    /**
     * Custom Query Builder for Analytics
     */
    public function customQuery(Request $request)
    {
        $request->validate([
            'metrics' => 'required|array',
            'dimensions' => 'required|array',
            'filters' => 'array',
            'date_range' => 'required|array',
        ]);

        $data = $this->analyticsService->buildCustomQuery(
            $request->metrics,
            $request->dimensions,
            $request->filters ?? [],
            $request->date_range
        );

        return response()->json($data);
    }

    /**
     * Save/Load Custom Dashboards
     */
    public function saveDashboard(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'config' => 'required|array',
        ]);

        $dashboard = auth()->user()->customDashboards()->create([
            'name' => $request->name,
            'config' => $request->config,
        ]);

        return response()->json(['dashboard' => $dashboard]);
    }

    public function loadDashboard($id)
    {
        $dashboard = auth()->user()->customDashboards()->findOrFail($id);

        return response()->json(['dashboard' => $dashboard]);
    }

    // Protected export methods
    protected function exportDashboardAnalytics($format, $period)
    {
        $data = $this->analyticsService->getKPIs($period, 'previous');

        return $this->generateExport('dashboard', $data, $format);
    }

    protected function exportRevenueAnalytics($format, $period)
    {
        $startDate = Carbon::now()->subDays($period);
        $endDate = Carbon::now();

        $data = [
            'revenue_chart' => $this->chartDataService->getRevenueChart($startDate, $endDate, 'day'),
            'revenue_by_service' => $this->analyticsService->getRevenueByService($startDate, $endDate),
        ];

        return $this->generateExport('revenue', $data, $format);
    }

    protected function exportCustomerAnalytics($format, $period)
    {
        $data = [
            'customer_acquisition' => $this->analyticsService->getCustomerAcquisition($period),
            'customer_retention' => $this->analyticsService->getCustomerRetention($period),
        ];

        return $this->generateExport('customers', $data, $format);
    }

    protected function exportMerchantAnalytics($format, $period)
    {
        $data = [
            'merchant_rankings' => $this->analyticsService->getMerchantRankings($period, 'revenue'),
            'merchant_performance' => $this->analyticsService->getMerchantPerformanceMetrics($period),
        ];

        return $this->generateExport('merchants', $data, $format);
    }

    protected function generateExport($type, $data, $format)
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "analytics_{$type}_{$timestamp}";

        switch ($format) {
            case 'pdf':
                return $this->generatePDF($type, $data, $filename);
            case 'excel':
                return $this->generateExcel($type, $data, $filename);
            case 'csv':
                return $this->generateCSV($type, $data, $filename);
            default:
                return response()->json(['error' => 'Unsupported format'], 400);
        }
    }

    protected function generatePDF($type, $data, $filename)
    {
        // PDF generation logic will be implemented
        return response()->json(['message' => 'PDF export coming soon']);
    }

    protected function generateExcel($type, $data, $filename)
    {
        // Excel generation logic will be implemented
        return response()->json(['message' => 'Excel export coming soon']);
    }

    protected function generateCSV($type, $data, $filename)
    {
        // CSV generation logic will be implemented
        return response()->json(['message' => 'CSV export coming soon']);
    }
}
