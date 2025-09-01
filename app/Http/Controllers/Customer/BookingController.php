<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Service;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Display customer's bookings
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = $user->bookings()->with(['service', 'merchant', 'payments']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('booking_status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        // Search by service name or merchant name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('service', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('merchant', function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%");
            });
        }

        $bookings = $query->latest('booking_date')->paginate(10);

        $statuses = [
            'pending' => 'في الانتظار',
            'confirmed' => 'مؤكد',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'no_show' => 'لم يحضر',
        ];

        return view('customer.bookings.index', compact('bookings', 'statuses'));
    }

    /**
     * Show booking details
     */
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load(['service', 'merchant', 'payments']);

        return view('customer.bookings.show', compact('booking'));
    }

    /**
     * Show booking form for a service
     */
    public function create(Service $service)
    {
        $service->load(['merchant', 'availability']);

        // Check if service is available for booking
        if (! $service->is_active || $service->merchant->status !== 'active') {
            return redirect()->back()
                ->with('error', 'عذراً، هذه الخدمة غير متاحة حالياً للحجز');
        }

        return view('customer.bookings.create', compact('service'));
    }

    /**
     * Store a new booking
     */
    public function store(StoreBookingRequest $request, Service $service)
    {
        try {
            DB::beginTransaction();

            $bookingData = $request->validated();
            $bookingData['customer_id'] = Auth::id();
            $bookingData['service_id'] = $service->id;
            $bookingData['merchant_id'] = $service->merchant_id;

            $booking = $this->bookingService->createBooking($bookingData);

            DB::commit();

            return redirect()->route('customer.bookings.show', $booking)
                ->with('success', 'تم إنشاء الحجز بنجاح! يرجى إكمال عملية الدفع.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الحجز: '.$e->getMessage());
        }
    }

    /**
     * Cancel a booking
     */
    public function cancel(Booking $booking, Request $request)
    {
        $this->authorize('cancel', $booking);

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        try {
            $this->bookingService->cancelBooking(
                $booking,
                Auth::user(),
                $request->cancellation_reason
            );

            return redirect()->route('customer.bookings.show', $booking)
                ->with('success', 'تم إلغاء الحجز بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء إلغاء الحجز: '.$e->getMessage());
        }
    }

    /**
     * Download booking invoice/receipt
     */
    public function downloadInvoice(Booking $booking)
    {
        $this->authorize('view', $booking);

        if ($booking->payment_status !== 'paid') {
            return redirect()->back()
                ->with('error', 'لا يمكن تحميل الفاتورة قبل إتمام الدفع');
        }

        // Generate and return invoice PDF
        // This would integrate with a PDF generation service
        return response()->download(
            storage_path('app/invoices/booking-'.$booking->id.'.pdf')
        );
    }

    /**
     * Get available time slots for a service on a specific date
     */
    public function getAvailableSlots(Service $service, Request $request)
    {
        $request->validate([
            'date' => 'required|date|after:today',
        ]);

        $availableSlots = $this->bookingService->getAvailableTimeSlots(
            $service,
            $request->date
        );

        return response()->json([
            'success' => true,
            'slots' => $availableSlots,
        ]);
    }

    /**
     * Calculate booking price
     */
    public function calculatePrice(Service $service, Request $request)
    {
        $request->validate([
            'guest_count' => 'required|integer|min:1',
            'booking_date' => 'required|date',
            'duration_hours' => 'nullable|integer|min:1',
        ]);

        try {
            $pricing = $this->bookingService->calculateBookingPrice($service, [
                'guest_count' => $request->guest_count,
                'booking_date' => $request->booking_date,
                'duration_hours' => $request->duration_hours,
            ]);

            return response()->json([
                'success' => true,
                'pricing' => $pricing,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
