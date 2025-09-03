<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Merchant;
use App\Models\Partner;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'total_merchants' => User::where('user_type', 'merchant')->count(),
            'total_partners' => Partner::count(),
            'total_services' => Service::count(),
            'total_bookings' => Booking::count(),
            'total_revenue' => Booking::where('payment_status', 'paid')->sum('total_amount'),
            'total_commission' => Booking::where('payment_status', 'paid')->sum('commission_amount'),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
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
        $recentBookings = Booking::with(['service', 'customer', 'service.merchant'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // إحصائيات التجار النشطين
        $activeMerchants = User::where('user_type', 'merchant')->whereHas('merchantServices.bookings', function ($query) {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        })
            ->withCount(['merchantServices as bookings_count' => function ($query) {
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
        $partnerRevenues = Partner::with(['merchants.user.merchantServices.bookings' => function ($query) {
            $query->where('payment_status', 'paid');
        }])
            ->get()
            ->map(function ($partner) {
                $totalRevenue = $partner->merchants->sum(function ($merchant) {
                    return $merchant->user->merchantServices->sum(function ($service) {
                        return $service->bookings->sum('total_amount');
                    });
                });
                $totalCommission = $partner->merchants->sum(function ($merchant) {
                    return $merchant->user->merchantServices->sum(function ($service) {
                        return $service->bookings->sum('commission_amount');
                    });
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
        $topMerchantsByRevenue = User::where('user_type', 'merchant')->select('users.*')
            ->join('services', 'users.id', '=', 'services.merchant_id')
            ->join('bookings', 'services.id', '=', 'bookings.service_id')
            ->select(
                'users.*',
                DB::raw('COUNT(bookings.id) as bookings_count'),
                DB::raw('SUM(bookings.total_amount) as total_revenue'),
                DB::raw('SUM(bookings.commission_amount) as total_commission')
            )
            ->where('bookings.payment_status', 'paid')
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->groupBy('users.id')
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
        $merchants = User::where('user_type', 'merchant')->with(['merchantServices', 'merchantServices.bookings'])
            ->withCount(['merchantServices as services_count'])
            ->withSum(['merchantServices.bookings as total_revenue' => function ($query) {
                $query->where('payment_status', 'paid');
            }], 'total_amount')
            ->withSum(['merchantServices.bookings as total_commission' => function ($query) {
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
        $partners = Partner::with('user', 'merchants.user.merchantServices.bookings')->withCount('merchants')->get();

        $partners->each(function ($partner) {
            $partner->total_revenue = $partner->merchants->sum(function ($merchant) {
                return $merchant->user->merchantServices->sum(function($service) {
                    return $service->bookings->where('payment_status', 'paid')->sum('total_amount');
                });
            });
            $partner->total_commission = $partner->merchants->sum(function ($merchant) {
                return $merchant->user->merchantServices->sum(function($service) {
                    return $service->bookings->where('payment_status', 'paid')->sum('commission_amount');
                });
            });
            $partner->total_bookings = $partner->merchants->sum(function ($merchant) {
                return $merchant->user->merchantServices->sum(function($service) {
                    return $service->bookings->count();
                });
            });
        });

        $partners = $partners->sortByDesc('total_revenue');

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
        $highestRevenueServices = Service::with(['merchant', 'bookings' => function ($query) {
            $query->where('payment_status', 'paid');
        }])
            ->get()
            ->map(function ($service) {
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
