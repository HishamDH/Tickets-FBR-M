<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PartnerDashboardController extends Controller
{
    /**
     * عرض لوحة تحكم الشريك
     */
    public function index()
    {
        $user = Auth::user();
        $partner = $user->partner;

        if (! $partner) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        // إحصائيات الشريك
        $stats = [
            'total_merchants' => $partner->merchants()->count(),
            'active_merchants' => $partner->merchants()->where('verification_status', 'approved')->count(),
            'pending_merchants' => $partner->merchants()->where('verification_status', 'pending')->count(),
            'total_services' => $this->getTotalServices($partner),
            'total_bookings' => $this->getTotalBookings($partner),
            'total_revenue' => $this->getTotalRevenue($partner),
            'partner_commission' => $this->getPartnerCommission($partner),
            'monthly_growth' => $this->getMonthlyGrowth($partner),
        ];

        // أفضل التجار أداءً
        $topMerchants = $partner->merchants()
            ->with('user')
            ->withCount('bookings')
            ->withSum(['bookings as total_revenue' => function ($query) {
                $query->where('payment_status', 'paid');
            }], 'total_amount')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

        // إيرادات شهرية
        $monthlyRevenue = $this->getMonthlyRevenue($partner);

        // أحدث الحجوزات من التجار التابعين
        $recentBookings = Booking::whereHas('merchant', function ($query) use ($partner) {
            $query->where('partner_id', $partner->id);
        })
            ->with(['service', 'customer', 'merchant'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // توزيع التجار حسب الحالة
        $merchantsByStatus = $partner->merchants()
            ->select('verification_status', DB::raw('count(*) as count'))
            ->groupBy('verification_status')
            ->get();

        return view('dashboard.partner.index', compact(
            'partner',
            'stats',
            'topMerchants',
            'monthlyRevenue',
            'recentBookings',
            'merchantsByStatus'
        ));
    }

    /**
     * إدارة التجار التابعين
     */
    public function merchants(Request $request)
    {
        $user = Auth::user();
        $partner = $user->partner;

        $query = $partner->merchants()->with('user');

        // فلترة حسب الحالة
        if ($request->has('status') && $request->status != '') {
            $query->where('verification_status', $request->status);
        }

        // البحث
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', '%'.$search.'%')
                    ->orWhere('business_type', 'like', '%'.$search.'%')
                    ->orWhere('cr_number', 'like', '%'.$search.'%');
            });
        }

        $merchants = $query->withCount('bookings')
            ->withSum(['bookings as total_revenue' => function ($query) {
                $query->where('payment_status', 'paid');
            }], 'total_amount')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.partner.merchants', compact('partner', 'merchants'));
    }

    /**
     * تفاصيل تاجر معين
     */
    public function merchantDetails($merchantId)
    {
        $user = Auth::user();
        $partner = $user->partner;

        $merchant = $partner->merchants()
            ->with(['user', 'services', 'bookings'])
            ->findOrFail($merchantId);

        // إحصائيات التاجر
        $merchantStats = [
            'total_services' => $merchant->services()->count(),
            'active_services' => $merchant->services()->where('is_active', true)->count(),
            'total_bookings' => $merchant->bookings()->count(),
            'pending_bookings' => $merchant->bookings()->where('booking_status', 'pending')->count(),
            'total_revenue' => $merchant->bookings()->where('payment_status', 'paid')->sum('total_amount'),
            'commission_earned' => $merchant->bookings()->where('payment_status', 'paid')->sum('commission_amount'),
        ];

        // أداء شهري للتاجر
        $merchantMonthlyPerformance = $merchant->bookings()
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as bookings_count'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->where('payment_status', 'paid')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('dashboard.partner.merchant-details', compact(
            'partner',
            'merchant',
            'merchantStats',
            'merchantMonthlyPerformance'
        ));
    }

    /**
     * تقرير عمولات الشريك
     */
    public function commissionReport(Request $request)
    {
        $user = Auth::user();
        $partner = $user->partner;

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        // عمولات يومية
        $dailyCommissions = Booking::whereHas('merchant', function ($query) use ($partner) {
            $query->where('partner_id', $partner->id);
        })
            ->select(
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

        // عمولات حسب التاجر
        $commissionsByMerchant = $partner->merchants()
            ->with(['bookings' => function ($query) use ($startDate, $endDate) {
                $query->where('payment_status', 'paid')
                    ->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function ($merchant) {
                $totalRevenue = $merchant->bookings->sum('total_amount');
                $totalCommission = $merchant->bookings->sum('commission_amount');
                $bookingsCount = $merchant->bookings->count();

                return [
                    'merchant' => $merchant,
                    'bookings_count' => $bookingsCount,
                    'total_revenue' => $totalRevenue,
                    'total_commission' => $totalCommission,
                    'commission_rate' => $merchant->commission_rate,
                ];
            })
            ->sortByDesc('total_commission');

        return view('dashboard.partner.commission-report', compact(
            'partner',
            'dailyCommissions',
            'commissionsByMerchant',
            'startDate',
            'endDate'
        ));
    }

    /**
     * تحليلات أداء الشريك
     */
    public function analytics()
    {
        $user = Auth::user();
        $partner = $user->partner;

        // نمو عدد التجار
        $merchantGrowth = $this->getMerchantGrowth($partner);

        // توزيع التجار حسب نوع العمل
        $merchantsByBusinessType = $partner->merchants()
            ->select('business_type', DB::raw('count(*) as count'))
            ->groupBy('business_type')
            ->get();

        // متوسط العمولة المكتسبة شهرياً
        $averageMonthlyCommission = $this->getAverageMonthlyCommission($partner);

        // معدل نجاح التجار (التجار الذين لديهم حجوزات)
        $totalMerchants = $partner->merchants()->count();
        $activeMerchantsWithBookings = $partner->merchants()
            ->whereHas('bookings')
            ->count();

        $merchantSuccessRate = $totalMerchants > 0 ?
            ($activeMerchantsWithBookings / $totalMerchants) * 100 : 0;

        // أفضل أنواع الخدمات أداءً
        $topServiceTypes = Booking::join('services', 'bookings.service_id', '=', 'services.id')
            ->join('merchants', 'bookings.merchant_id', '=', 'merchants.id')
            ->where('merchants.partner_id', $partner->id)
            ->where('bookings.payment_status', 'paid')
            ->select(
                'services.service_type',
                DB::raw('COUNT(bookings.id) as bookings_count'),
                DB::raw('SUM(bookings.total_amount) as total_revenue')
            )
            ->groupBy('services.service_type')
            ->orderBy('total_revenue', 'desc')
            ->get();

        return view('dashboard.partner.analytics', compact(
            'partner',
            'merchantGrowth',
            'merchantsByBusinessType',
            'averageMonthlyCommission',
            'merchantSuccessRate',
            'topServiceTypes'
        ));
    }

    /**
     * الحصول على إجمالي الخدمات للشريك
     */
    private function getTotalServices($partner)
    {
        return DB::table('services')
            ->join('merchants', 'services.merchant_id', '=', 'merchants.id')
            ->where('merchants.partner_id', $partner->id)
            ->count();
    }

    /**
     * الحصول على إجمالي الحجوزات للشريك
     */
    private function getTotalBookings($partner)
    {
        return Booking::whereHas('merchant', function ($query) use ($partner) {
            $query->where('partner_id', $partner->id);
        })->count();
    }

    /**
     * الحصول على إجمالي الإيرادات للشريك
     */
    private function getTotalRevenue($partner)
    {
        return Booking::whereHas('merchant', function ($query) use ($partner) {
            $query->where('partner_id', $partner->id);
        })->where('payment_status', 'paid')->sum('total_amount');
    }

    /**
     * الحصول على عمولة الشريك
     */
    private function getPartnerCommission($partner)
    {
        return Booking::whereHas('merchant', function ($query) use ($partner) {
            $query->where('partner_id', $partner->id);
        })->where('payment_status', 'paid')->sum('commission_amount');
    }

    /**
     * الحصول على نمو شهري للشريك
     */
    private function getMonthlyGrowth($partner)
    {
        $currentMonth = Booking::whereHas('merchant', function ($query) use ($partner) {
            $query->where('partner_id', $partner->id);
        })
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $lastMonth = Booking::whereHas('merchant', function ($query) use ($partner) {
            $query->where('partner_id', $partner->id);
        })
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        return $lastMonth > 0 ? (($currentMonth - $lastMonth) / $lastMonth) * 100 : 0;
    }

    /**
     * الحصول على الإيرادات الشهرية للشريك
     */
    private function getMonthlyRevenue($partner)
    {
        return Booking::whereHas('merchant', function ($query) use ($partner) {
            $query->where('partner_id', $partner->id);
        })
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as bookings_count'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('SUM(commission_amount) as commission_amount')
            )
            ->where('payment_status', 'paid')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    /**
     * الحصول على نمو التجار للشريك
     */
    private function getMerchantGrowth($partner)
    {
        return $partner->merchants()
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    /**
     * الحصول على متوسط العمولة الشهرية
     */
    private function getAverageMonthlyCommission($partner)
    {
        $monthlyCommissions = Booking::whereHas('merchant', function ($query) use ($partner) {
            $query->where('partner_id', $partner->id);
        })
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(commission_amount) as total_commission')
            )
            ->where('payment_status', 'paid')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->get();

        return $monthlyCommissions->avg('total_commission') ?? 0;
    }
}
