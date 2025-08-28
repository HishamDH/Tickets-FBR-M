<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Service;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MerchantDashboardController extends Controller
{
    /**
     * عرض لوحة تحكم التاجر
     */
    public function index()
    {
        $user = Auth::user();
        $merchant = $user->merchant;

        if (!$merchant) {
            abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
        }

        // إحصائيات التاجر
        $stats = [
            'total_services' => $merchant->services()->count(),
            'active_services' => $merchant->services()->where('is_active', true)->count(),
            'total_bookings' => $merchant->bookings()->count(),
            'pending_bookings' => $merchant->bookings()->where('booking_status', 'pending')->count(),
            'confirmed_bookings' => $merchant->bookings()->where('booking_status', 'confirmed')->count(),
            'total_revenue' => $merchant->bookings()->where('payment_status', 'paid')->sum('total_amount'),
            'commission_paid' => $merchant->bookings()->where('payment_status', 'paid')->sum('commission_amount'),
            'net_revenue' => $merchant->bookings()->where('payment_status', 'paid')->sum('total_amount') - 
                           $merchant->bookings()->where('payment_status', 'paid')->sum('commission_amount'),
        ];

        // إيرادات شهرية
        $monthlyRevenue = $merchant->bookings()
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as bookings_count'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('SUM(commission_amount) as commission_amount')
            )
            ->where('payment_status', 'paid')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get();

        // أفضل خدمات التاجر
        $topServices = $merchant->services()
            ->withCount('bookings')
            ->withSum(['bookings as total_revenue' => function($query) {
                $query->where('payment_status', 'paid');
            }], 'total_amount')
            ->orderBy('bookings_count', 'desc')
            ->limit(5)
            ->get();

        // أحدث الحجوزات
        $recentBookings = $merchant->bookings()
            ->with(['service', 'customer'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // حجوزات اليوم
        $todayBookings = $merchant->bookings()
            ->with(['service', 'customer'])
            ->whereDate('booking_date', Carbon::today())
            ->orderBy('booking_time')
            ->get();

        // إحصائيات الحجوزات حسب الحالة
        $bookingsByStatus = $merchant->bookings()
            ->select('booking_status', DB::raw('count(*) as count'))
            ->groupBy('booking_status')
            ->get();

        return view('dashboard.merchant.index', compact(
            'merchant',
            'stats',
            'monthlyRevenue',
            'topServices',
            'recentBookings',
            'todayBookings',
            'bookingsByStatus'
        ));
    }

    /**
     * إدارة الخدمات
     */
    public function services()
    {
        $user = Auth::user();
        $merchant = $user->merchant;

        $services = $merchant->services()
            ->withCount('bookings')
            ->withSum(['bookings as total_revenue' => function($query) {
                $query->where('payment_status', 'paid');
            }], 'total_amount')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.merchant.services', compact('merchant', 'services'));
    }

    /**
     * إدارة الحجوزات
     */
    public function bookings(Request $request)
    {
        $user = Auth::user();
        $merchant = $user->merchant;

        $query = $merchant->bookings()->with(['service', 'customer']);

        // فلترة حسب الحالة
        if ($request->has('status') && $request->status != '') {
            $query->where('booking_status', $request->status);
        }

        // فلترة حسب الخدمة
        if ($request->has('service_id') && $request->service_id != '') {
            $query->where('service_id', $request->service_id);
        }

        // فلترة حسب التاريخ
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        $services = $merchant->services()->orderBy('name')->get();

        return view('dashboard.merchant.bookings', compact('merchant', 'bookings', 'services'));
    }

    /**
     * تفاصيل حجز معين
     */
    public function bookingDetails($bookingId)
    {
        $user = Auth::user();
        $merchant = $user->merchant;

        $booking = $merchant->bookings()
            ->with(['service', 'customer'])
            ->findOrFail($bookingId);

        return view('dashboard.merchant.booking-details', compact('merchant', 'booking'));
    }

    /**
     * تحديث حالة الحجز
     */
    public function updateBookingStatus(Request $request, $bookingId)
    {
        $user = Auth::user();
        $merchant = $user->merchant;

        $request->validate([
            'booking_status' => 'required|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string|max:500'
        ]);

        $booking = $merchant->bookings()->findOrFail($bookingId);

        $booking->update([
            'booking_status' => $request->booking_status,
            'merchant_notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'تم تحديث حالة الحجز بنجاح');
    }

    /**
     * تقرير الإيرادات
     */
    public function revenueReport(Request $request)
    {
        $user = Auth::user();
        $merchant = $user->merchant;

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        // إيرادات يومية
        $dailyRevenue = $merchant->bookings()
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as bookings_count'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('SUM(commission_amount) as commission_amount'),
                DB::raw('SUM(total_amount - commission_amount) as net_revenue')
            )
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // إيرادات حسب الخدمة
        $revenueByService = $merchant->services()
            ->with(['bookings' => function($query) use ($startDate, $endDate) {
                $query->where('payment_status', 'paid')
                      ->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function($service) {
                $totalRevenue = $service->bookings->sum('total_amount');
                $totalCommission = $service->bookings->sum('commission_amount');
                $bookingsCount = $service->bookings->count();

                return [
                    'service' => $service,
                    'bookings_count' => $bookingsCount,
                    'total_revenue' => $totalRevenue,
                    'commission_amount' => $totalCommission,
                    'net_revenue' => $totalRevenue - $totalCommission,
                ];
            })
            ->sortByDesc('total_revenue');

        return view('dashboard.merchant.revenue-report', compact(
            'merchant',
            'dailyRevenue',
            'revenueByService',
            'startDate',
            'endDate'
        ));
    }

    /**
     * تحليلات الأداء
     */
    public function analytics()
    {
        $user = Auth::user();
        $merchant = $user->merchant;

        // معدل الحجز (عدد المشاهدات مقابل الحجوزات)
        // هذا يتطلب إضافة tracking للمشاهدات في المستقبل

        // أوقات الذروة للحجوزات
        $peakHours = $merchant->bookings()
            ->select(
                DB::raw('HOUR(booking_time) as hour'),
                DB::raw('COUNT(*) as bookings_count')
            )
            ->groupBy('hour')
            ->orderBy('bookings_count', 'desc')
            ->get();

        // أيام الأسبوع الأكثر حجزاً
        $peakDays = $merchant->bookings()
            ->select(
                DB::raw('DAYOFWEEK(booking_date) as day_of_week'),
                DB::raw('COUNT(*) as bookings_count')
            )
            ->groupBy('day_of_week')
            ->orderBy('bookings_count', 'desc')
            ->get();

        // متوسط قيمة الحجز
        $averageBookingValue = $merchant->bookings()
            ->where('payment_status', 'paid')
            ->avg('total_amount');

        // معدل الإلغاء
        $totalBookings = $merchant->bookings()->count();
        $cancelledBookings = $merchant->bookings()->where('booking_status', 'cancelled')->count();
        $cancellationRate = $totalBookings > 0 ? ($cancelledBookings / $totalBookings) * 100 : 0;

        // نمو الحجوزات (مقارنة بالشهر الماضي)
        $currentMonthBookings = $merchant->bookings()
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $lastMonthBookings = $merchant->bookings()
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        $growthRate = $lastMonthBookings > 0 ? 
            (($currentMonthBookings - $lastMonthBookings) / $lastMonthBookings) * 100 : 0;

        return view('dashboard.merchant.analytics', compact(
            'merchant',
            'peakHours',
            'peakDays',
            'averageBookingValue',
            'cancellationRate',
            'growthRate',
            'currentMonthBookings',
            'lastMonthBookings'
        ));
    }
}
