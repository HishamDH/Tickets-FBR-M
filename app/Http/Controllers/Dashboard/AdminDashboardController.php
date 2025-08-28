<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Merchant;
use App\Models\Partner;
use App\Models\Service;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * عرض لوحة تحكم الإدارة
     */
    public function index()
    {
        // إحصائيات عامة
        $stats = [
            'total_users' => User::count(),
            'total_merchants' => Merchant::count(),
            'total_partners' => Partner::count(),
            'total_services' => Service::count(),
            'total_bookings' => Booking::count(),
            'total_revenue' => Booking::where('payment_status', 'paid')->sum('total_amount'),
            'total_commission' => Booking::where('payment_status', 'paid')->sum('commission_amount'),
            'pending_bookings' => Booking::where('booking_status', 'pending')->count(),
        ];

        // إحصائيات شهرية للحجوزات
        $monthlyBookings = Booking::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_amount) as revenue')
        )
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // أفضل الخدمات حجزاً
        $topServices = Service::withCount('bookings')
            ->with('merchant')
            ->orderBy('bookings_count', 'desc')
            ->limit(10)
            ->get();

        // أحدث الحجوزات
        $recentBookings = Booking::with(['service', 'customer', 'merchant'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // إحصائيات التجار النشطين
        $activeMerchants = Merchant::whereHas('bookings', function($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(30));
            })
            ->withCount(['bookings' => function($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(30));
            }])
            ->orderBy('bookings_count', 'desc')
            ->limit(10)
            ->get();

        // توزيع أنواع الخدمات
        $serviceTypeDistribution = Service::select('service_type', DB::raw('count(*) as count'))
            ->groupBy('service_type')
            ->get();

        // إيرادات الشركاء
        $partnerRevenues = Partner::with(['merchants.bookings' => function($query) {
                $query->where('payment_status', 'paid');
            }])
            ->get()
            ->map(function($partner) {
                $totalRevenue = $partner->merchants->sum(function($merchant) {
                    return $merchant->bookings->sum('total_amount');
                });
                $totalCommission = $partner->merchants->sum(function($merchant) {
                    return $merchant->bookings->sum('commission_amount');
                });
                
                return [
                    'partner' => $partner,
                    'total_revenue' => $totalRevenue,
                    'total_commission' => $totalCommission,
                    'merchants_count' => $partner->merchants->count(),
                ];
            })
            ->sortByDesc('total_revenue')
            ->take(10);

        return view('dashboard.admin.index', compact(
            'stats',
            'monthlyBookings',
            'topServices',
            'recentBookings',
            'activeMerchants',
            'serviceTypeDistribution',
            'partnerRevenues'
        ));
    }

    /**
     * تقرير مفصل عن الإيرادات
     */
    public function revenueReport(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        // إيرادات يومية
        $dailyRevenue = Booking::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as bookings_count'),
            DB::raw('SUM(total_amount) as total_revenue'),
            DB::raw('SUM(commission_amount) as total_commission')
        )
        ->where('payment_status', 'paid')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // إيرادات حسب نوع الخدمة
        $revenueByServiceType = Booking::join('services', 'bookings.service_id', '=', 'services.id')
            ->select(
                'services.service_type',
                DB::raw('COUNT(bookings.id) as bookings_count'),
                DB::raw('SUM(bookings.total_amount) as total_revenue'),
                DB::raw('SUM(bookings.commission_amount) as total_commission')
            )
            ->where('bookings.payment_status', 'paid')
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->groupBy('services.service_type')
            ->get();

        // أفضل التجار بالإيرادات
        $topMerchantsByRevenue = Merchant::select('merchants.*')
            ->join('bookings', 'merchants.id', '=', 'bookings.merchant_id')
            ->select(
                'merchants.*',
                DB::raw('COUNT(bookings.id) as bookings_count'),
                DB::raw('SUM(bookings.total_amount) as total_revenue'),
                DB::raw('SUM(bookings.commission_amount) as total_commission')
            )
            ->where('bookings.payment_status', 'paid')
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->groupBy('merchants.id', 'merchants.business_name', 'merchants.business_type', 'merchants.verification_status', 'merchants.commission_rate', 'merchants.created_at', 'merchants.updated_at', 'merchants.user_id', 'merchants.cr_number', 'merchants.business_address', 'merchants.city', 'merchants.partner_id', 'merchants.account_manager_id', 'merchants.settings')
            ->orderBy('total_revenue', 'desc')
            ->limit(20)
            ->get();

        return view('dashboard.admin.revenue-report', compact(
            'dailyRevenue',
            'revenueByServiceType', 
            'topMerchantsByRevenue',
            'startDate',
            'endDate'
        ));
    }

    /**
     * تقرير عن أداء التجار
     */
    public function merchantsReport()
    {
        $merchants = Merchant::with(['user', 'partner'])
            ->withCount(['services', 'bookings'])
            ->withSum(['bookings as total_revenue' => function($query) {
                $query->where('payment_status', 'paid');
            }], 'total_amount')
            ->withSum(['bookings as total_commission' => function($query) {
                $query->where('payment_status', 'paid');
            }], 'commission_amount')
            ->orderBy('total_revenue', 'desc')
            ->paginate(20);

        return view('dashboard.admin.merchants-report', compact('merchants'));
    }

    /**
     * تقرير عن أداء الشركاء
     */
    public function partnersReport()
    {
        $partners = Partner::with('user')
            ->withCount('merchants')
            ->get()
            ->map(function($partner) {
                $totalRevenue = $partner->merchants->sum(function($merchant) {
                    return $merchant->bookings()->where('payment_status', 'paid')->sum('total_amount');
                });
                
                $totalCommission = $partner->merchants->sum(function($merchant) {
                    return $merchant->bookings()->where('payment_status', 'paid')->sum('commission_amount');
                });

                $totalBookings = $partner->merchants->sum(function($merchant) {
                    return $merchant->bookings()->count();
                });

                $partner->total_revenue = $totalRevenue;
                $partner->total_commission = $totalCommission;
                $partner->total_bookings = $totalBookings;

                return $partner;
            })
            ->sortByDesc('total_revenue');

        return view('dashboard.admin.partners-report', compact('partners'));
    }

    /**
     * إحصائيات مفصلة للخدمات
     */
    public function servicesAnalytics()
    {
        // أكثر الخدمات حجزاً
        $mostBookedServices = Service::withCount('bookings')
            ->with('merchant')
            ->orderBy('bookings_count', 'desc')
            ->limit(20)
            ->get();

        // أعلى الخدمات إيراداً
        $highestRevenueServices = Service::with(['merchant', 'bookings' => function($query) {
                $query->where('payment_status', 'paid');
            }])
            ->get()
            ->map(function($service) {
                $service->total_revenue = $service->bookings->sum('total_amount');
                $service->total_bookings = $service->bookings->count();
                return $service;
            })
            ->sortByDesc('total_revenue')
            ->take(20);

        // توزيع الخدمات حسب النوع
        $servicesByType = Service::select('service_type', DB::raw('count(*) as count'))
            ->groupBy('service_type')
            ->get();

        // متوسط أسعار الخدمات حسب النوع
        $averagePricesByType = Service::select(
            'service_type',
            DB::raw('AVG(base_price) as average_price'),
            DB::raw('MIN(base_price) as min_price'),
            DB::raw('MAX(base_price) as max_price')
        )
        ->groupBy('service_type')
        ->get();

        return view('dashboard.admin.services-analytics', compact(
            'mostBookedServices',
            'highestRevenueServices',
            'servicesByType',
            'averagePricesByType'
        ));
    }
}
