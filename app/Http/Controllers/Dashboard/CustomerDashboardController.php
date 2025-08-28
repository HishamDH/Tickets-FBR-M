<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CustomerDashboardController extends Controller
{
    /**
     * عرض لوحة تحكم العميل
     */
    public function index()
    {
        $user = Auth::user();

        // إحصائيات العميل
        $stats = [
            'total_bookings' => $user->customerBookings()->count(),
            'pending_bookings' => $user->customerBookings()->where('booking_status', 'pending')->count(),
            'confirmed_bookings' => $user->customerBookings()->where('booking_status', 'confirmed')->count(),
            'completed_bookings' => $user->customerBookings()->where('booking_status', 'completed')->count(),
            'cancelled_bookings' => $user->customerBookings()->where('booking_status', 'cancelled')->count(),
            'total_spent' => $user->customerBookings()->where('payment_status', 'paid')->sum('total_amount'),
        ];

        // الحجوزات القادمة
        $upcomingBookings = $user->customerBookings()
            ->with(['service', 'merchant'])
            ->where('booking_date', '>=', Carbon::today())
            ->where('booking_status', '!=', 'cancelled')
            ->orderBy('booking_date', 'asc')
            ->orderBy('booking_time', 'asc')
            ->limit(5)
            ->get();

        // آخر الحجوزات
        $recentBookings = $user->customerBookings()
            ->with(['service', 'merchant'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // الخدمات المفضلة (الأكثر حجزاً)
        $favoriteServices = Service::whereHas('bookings', function($query) use ($user) {
                $query->where('customer_id', $user->id);
            })
            ->withCount(['bookings' => function($query) use ($user) {
                $query->where('customer_id', $user->id);
            }])
            ->with('merchant')
            ->orderBy('bookings_count', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.customer.index', compact(
            'user',
            'stats',
            'upcomingBookings',
            'recentBookings',
            'favoriteServices'
        ));
    }

    /**
     * عرض جميع الحجوزات
     */
    public function bookings(Request $request)
    {
        $user = Auth::user();

        $query = $user->customerBookings()->with(['service', 'merchant']);

        // فلترة حسب الحالة
        if ($request->has('status') && $request->status != '') {
            $query->where('booking_status', $request->status);
        }

        // فلترة حسب التاريخ
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        // البحث في اسم الخدمة أو التاجر
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('service', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })->orWhereHas('merchant', function($q) use ($search) {
                $q->where('business_name', 'like', '%' . $search . '%');
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('dashboard.customer.bookings', compact('user', 'bookings'));
    }

    /**
     * تفاصيل حجز معين
     */
    public function bookingDetails($bookingId)
    {
        $user = Auth::user();

        $booking = $user->customerBookings()
            ->with(['service', 'merchant'])
            ->findOrFail($bookingId);

        return view('dashboard.customer.booking-details', compact('user', 'booking'));
    }

    /**
     * إلغاء حجز
     */
    public function cancelBooking(Request $request, $bookingId)
    {
        $user = Auth::user();

        $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ]);

        $booking = $user->customerBookings()->findOrFail($bookingId);

        // التحقق من إمكانية الإلغاء (مثلاً، قبل 24 ساعة من الموعد)
        $bookingDateTime = Carbon::parse($booking->booking_date . ' ' . $booking->booking_time);
        if ($bookingDateTime->diffInHours(Carbon::now()) < 24) {
            return redirect()->back()->with('error', 'لا يمكن إلغاء الحجز قبل أقل من 24 ساعة من الموعد');
        }

        if ($booking->booking_status === 'cancelled') {
            return redirect()->back()->with('error', 'هذا الحجز ملغي مسبقاً');
        }

        $booking->update([
            'booking_status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_at' => Carbon::now(),
            'cancelled_by' => $user->id,
        ]);

        return redirect()->back()->with('success', 'تم إلغاء الحجز بنجاح');
    }

    /**
     * طلب تعديل موعد الحجز
     */
    public function rescheduleRequest(Request $request, $bookingId)
    {
        $user = Auth::user();

        $request->validate([
            'new_booking_date' => 'required|date|after:today',
            'new_booking_time' => 'required',
            'reschedule_reason' => 'required|string|max:500'
        ]);

        $booking = $user->customerBookings()->findOrFail($bookingId);

        if ($booking->booking_status === 'cancelled' || $booking->booking_status === 'completed') {
            return redirect()->back()->with('error', 'لا يمكن تعديل هذا الحجز');
        }

        // إنشاء طلب تعديل (يمكن إضافة جدول منفصل لطلبات التعديل)
        $booking->update([
            'reschedule_requested' => true,
            'new_booking_date' => $request->new_booking_date,
            'new_booking_time' => $request->new_booking_time,
            'reschedule_reason' => $request->reschedule_reason,
        ]);

        return redirect()->back()->with('success', 'تم إرسال طلب تعديل الموعد للتاجر');
    }

    /**
     * تقييم خدمة
     */
    public function rateService(Request $request, $bookingId)
    {
        $user = Auth::user();

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000'
        ]);

        $booking = $user->customerBookings()->findOrFail($bookingId);

        if ($booking->booking_status !== 'completed') {
            return redirect()->back()->with('error', 'يمكن تقييم الخدمة فقط بعد اكتمال الحجز');
        }

        $booking->update([
            'rating' => $request->rating,
            'review' => $request->review,
            'reviewed_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'تم إضافة تقييمك بنجاح');
    }

    /**
     * ملف العميل الشخصي
     */
    public function profile()
    {
        $user = Auth::user();

        // إحصائيات متقدمة
        $bookingStats = [
            'total_bookings' => $user->customerBookings()->count(),
            'favorite_service_type' => $this->getFavoriteServiceType($user),
            'average_rating_given' => $user->customerBookings()->whereNotNull('rating')->avg('rating'),
            'total_spent' => $user->customerBookings()->where('payment_status', 'paid')->sum('total_amount'),
            'member_since' => $user->created_at,
        ];

        return view('dashboard.customer.profile', compact('user', 'bookingStats'));
    }

    /**
     * الحصول على نوع الخدمة المفضل للعميل
     */
    private function getFavoriteServiceType($user)
    {
        $serviceType = $user->customerBookings()
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->select('services.service_type', \DB::raw('count(*) as count'))
            ->groupBy('services.service_type')
            ->orderBy('count', 'desc')
            ->first();

        return $serviceType ? $serviceType->service_type : null;
    }

    /**
     * إعادة الحجز (حجز نفس الخدمة مرة أخرى)
     */
    public function rebookService($bookingId)
    {
        $user = Auth::user();

        $originalBooking = $user->customerBookings()
            ->with('service')
            ->findOrFail($bookingId);

        // إعادة توجيه لصفحة حجز الخدمة
        return redirect()->route('merchant.service.booking', [
            'merchant' => $originalBooking->merchant_id,
            'service' => $originalBooking->service_id
        ])->with('rebook_data', [
            'customer_name' => $originalBooking->customer_name,
            'customer_phone' => $originalBooking->customer_phone,
            'customer_email' => $originalBooking->customer_email,
        ]);
    }
}
