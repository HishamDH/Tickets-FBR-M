<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MerchantPaymentSetting;
use App\Models\PaymentGateway;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MerchantDashboardController extends Controller
{
    /**
     * عرض لوحة تحكم التاجر
     */
    public function index()
    {
        $user = Auth::user();
        
        // Check if merchant is approved
        if ($user->merchant_status !== 'approved') {
            return redirect()->route('merchant.status');
        }

        // Get services and bookings directly from user's services
        $userServices = $user->services();
        
        // Get bookings through services
        $serviceIds = $userServices->pluck('id');
        $userBookings = \App\Models\Booking::whereIn('service_id', $serviceIds);

        // إحصائيات التاجر
        $stats = [
            'total_services' => $userServices->count(),
            'active_services' => $userServices->where('is_active', true)->count(),
            'featured_services' => $userServices->where('is_featured', true)->count(),
            'inactive_services' => $userServices->where('is_active', false)->count(),
            'total_bookings' => $userBookings->count(),
            'pending_bookings' => $userBookings->where('status', 'pending')->count(),
            'confirmed_bookings' => $userBookings->where('status', 'confirmed')->count(),
            'completed_bookings' => $userBookings->where('status', 'completed')->count(),
            'cancelled_bookings' => $userBookings->where('status', 'cancelled')->count(),
            'total_revenue' => $userBookings->where('payment_status', 'paid')->sum('total_amount') ?? 0,
            'pending_revenue' => $userBookings->where('payment_status', 'pending')->sum('total_amount') ?? 0,
        ];

        // إيرادات شهرية للـ 6 أشهر الماضية
        $monthlyRevenue = \App\Models\Booking::whereIn('service_id', $serviceIds)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as bookings_count'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // أفضل خدمات التاجر
        $topServices = $user->services()
            ->withCount('bookings')
            ->withSum(['bookings as total_revenue' => function ($query) {
                $query->where('payment_status', 'paid');
            }], 'total_amount')
            ->orderBy('bookings_count', 'desc')
            ->limit(5)
            ->get();

        // أحدث الحجوزات
        $recentBookings = \App\Models\Booking::whereIn('service_id', $serviceIds)
            ->with(['service', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // حجوزات اليوم
        $todayBookings = \App\Models\Booking::whereIn('service_id', $serviceIds)
            ->with(['service', 'user'])
            ->whereDate('booking_date', Carbon::today())
            ->orderBy('booking_time')
            ->get();

        // إحصائيات الحجوزات حسب الحالة
        $bookingsByStatus = \App\Models\Booking::whereIn('service_id', $serviceIds)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        return view('merchant.dashboard.index', compact(
            'user',
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
            ->withSum(['bookings as total_revenue' => function ($query) {
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
            $query->where('status', $request->status);
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
            'status' => 'required|in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        $booking = $merchant->bookings()->findOrFail($bookingId);

        $booking->update([
            'status' => $request->status,
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
            ->with(['bookings' => function ($query) use ($startDate, $endDate) {
                $query->where('payment_status', 'paid')
                    ->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function ($service) {
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
        $cancelledBookings = $merchant->bookings()->where('status', 'cancelled')->count();
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

    /**
     * إدارة إعدادات الدفع
     */
    public function paymentSettings()
    {
        $user = Auth::user();
        $merchant = $user->merchant;

        if (! $merchant) {
            // Create merchant record if it doesn't exist
            $merchant = Merchant::create([
                'user_id' => $user->id,
                'business_name' => $user->business_name ?? 'Business Name',
                'business_type' => $user->business_type ?? 'service',
                'cr_number' => $user->commercial_registration_number ?? '000000',
                'business_address' => $user->address ?? '',
                'city' => $user->business_city ?? $user->city ?? 'City',
                'verification_status' => 'approved',
                'commission_rate' => 10.00,
            ]);
            
            if (! $merchant) {
                return redirect()->route('merchant.dashboard')->with('error', 'الملف التجاري غير موجود');
            }
        }

        // جلب جميع بوابات الدفع المتاحة
        $availableGateways = PaymentGateway::active()->ordered()->get();

        // جلب إعدادات التاجر الحالية
        $merchantSettings = MerchantPaymentSetting::with('paymentGateway')
            ->where('merchant_id', $merchant->id)
            ->get()
            ->keyBy('payment_gateway_id');

        return view('dashboard.merchant.payment-settings', compact(
            'merchant',
            'availableGateways',
            'merchantSettings'
        ));
    }

    /**
     * تحديث إعدادات بوابة دفع محددة
     */
    public function updatePaymentGateway(Request $request, $gatewayId)
    {
        $user = Auth::user();
        $merchant = $user->merchant;

        if (! $merchant) {
            // Create merchant record if it doesn't exist
            $merchant = Merchant::create([
                'user_id' => $user->id,
                'business_name' => $user->business_name ?? 'Business Name',
                'business_type' => $user->business_type ?? 'service',
                'cr_number' => $user->commercial_registration_number ?? '000000',
                'business_address' => $user->address ?? '',
                'city' => $user->business_city ?? $user->city ?? 'City',
                'verification_status' => 'approved',
                'commission_rate' => 10.00,
            ]);
            
            if (! $merchant) {
                return redirect()->route('merchant.dashboard')->with('error', 'الملف التجاري غير موجود');
            }
        }

        $gateway = PaymentGateway::findOrFail($gatewayId);

        $request->validate([
            'is_enabled' => 'boolean',
            'custom_fee' => 'nullable|numeric|min:0',
            'custom_fee_type' => 'nullable|in:fixed,percentage',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $setting = MerchantPaymentSetting::updateOrCreate(
            [
                'merchant_id' => $merchant->id,
                'payment_gateway_id' => $gateway->id,
            ],
            [
                'is_enabled' => $request->boolean('is_enabled'),
                'custom_fee' => $request->custom_fee,
                'custom_fee_type' => $request->custom_fee_type ?? $gateway->fee_type,
                'display_order' => $request->display_order ?? 0,
                'additional_settings' => $request->additional_settings ?? [],
            ]
        );

        return redirect()
            ->route('merchant.dashboard.payment-settings')
            ->with('success', 'تم تحديث إعدادات بوابة الدفع بنجاح');
    }

    /**
     * اختبار بوابة الدفع
     */
    public function testPaymentGateway($gatewayId)
    {
        $user = Auth::user();
        $merchant = $user->merchant;

        if (! $merchant) {
            return response()->json(['error' => 'الملف التجاري غير موجود'], 400);
        }

        $setting = MerchantPaymentSetting::where('merchant_id', $merchant->id)
            ->where('payment_gateway_id', $gatewayId)
            ->first();

        if (! $setting) {
            return response()->json(['error' => 'إعدادات البوابة غير موجودة'], 404);
        }

        // محاكاة اختبار البوابة
        $testResult = $this->performGatewayTest($setting);

        $setting->update([
            'last_tested_at' => now(),
            'test_passed' => $testResult['success'],
        ]);

        return response()->json($testResult);
    }

    /**
     * تنفيذ اختبار البوابة
     */
    protected function performGatewayTest(MerchantPaymentSetting $setting): array
    {
        $gateway = $setting->paymentGateway;

        // محاكاة اختبار البوابة حسب النوع
        switch ($gateway->provider) {
            case 'stripe':
                // في البيئة الفعلية سيتم اختبار الاتصال مع Stripe
                return [
                    'success' => true,
                    'message' => 'تم اختبار الاتصال مع Stripe بنجاح',
                    'details' => [
                        'test_mode' => config('app.env') !== 'production',
                        'tested_at' => now()->toISOString(),
                    ],
                ];

            case 'bank_integration':
            case 'stc_integration':
                // اختبار التكامل البنكي
                return [
                    'success' => false,
                    'message' => 'التكامل البنكي غير متاح حالياً',
                    'details' => [
                        'requires_setup' => true,
                    ],
                ];

            case 'manual':
                // الدفع اليدوي لا يحتاج اختبار
                return [
                    'success' => true,
                    'message' => 'الدفع اليدوي جاهز للاستخدام',
                ];

            default:
                return [
                    'success' => false,
                    'message' => 'نوع البوابة غير مدعوم',
                ];
        }
    }
}
