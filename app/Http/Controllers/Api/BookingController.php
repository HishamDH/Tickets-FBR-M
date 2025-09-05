<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Service;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of bookings
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status' => 'sometimes|string',
            'payment_status' => 'sometimes|string',
            'booking_date_from' => 'sometimes|date',
            'booking_date_to' => 'sometimes|date',
            'merchant_id' => 'sometimes|exists:users,id',
            'customer_id' => 'sometimes|exists:users,id',
            'service_id' => 'sometimes|exists:services,id',
            'search' => 'sometimes|string|max:255',
            'sort_by' => ['sometimes', Rule::in(['booking_date', 'created_at', 'total_amount', 'status'])],
            'sort_direction' => ['sometimes', Rule::in(['asc', 'desc'])],
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        $user = $request->user();
        $query = Booking::query()->with(['customer', 'merchant', 'bookable', 'payments']);

        // Apply user-based filtering
        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        } elseif ($user->isMerchant()) {
            $query->where('merchant_id', $user->id);
        }
        // Admins can see all bookings (no additional filtering)

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('booking_date_from')) {
            $query->where('booking_date', '>=', $request->booking_date_from);
        }

        if ($request->filled('booking_date_to')) {
            $query->where('booking_date', '<=', $request->booking_date_to);
        }

        if ($request->filled('merchant_id') && $user->isAdmin()) {
            $query->where('merchant_id', $request->merchant_id);
        }

        if ($request->filled('customer_id') && ($user->isAdmin() || $user->isMerchant())) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('service_id')) {
            $query->where('bookable_type', Service::class)
                  ->where('bookable_id', $request->service_id);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('booking_number', 'like', '%' . $searchTerm . '%')
                  ->orWhere('customer_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('customer_phone', 'like', '%' . $searchTerm . '%')
                  ->orWhere('customer_email', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('customer', function (Builder $customerQuery) use ($searchTerm) {
                      $customerQuery->where('name', 'like', '%' . $searchTerm . '%')
                                   ->orWhere('email', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Paginate results
        $perPage = $request->get('per_page', 15);
        $bookings = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($bookings->items()),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
                'from' => $bookings->firstItem(),
                'to' => $bookings->lastItem(),
            ],
            'links' => [
                'first' => $bookings->url(1),
                'last' => $bookings->url($bookings->lastPage()),
                'prev' => $bookings->previousPageUrl(),
                'next' => $bookings->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Store a newly created booking
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'booking_date' => 'required|date|after:today',
            'booking_time' => 'sometimes|string',
            'guest_count' => 'required|integer|min:1',
            'customer_name' => 'sometimes|string|max:255',
            'customer_phone' => 'sometimes|string|max:20',
            'customer_email' => 'sometimes|email|max:255',
            'special_requests' => 'sometimes|string|max:1000',
            'payment_method' => 'required|string',
            'payment_gateway_id' => 'required|exists:payment_gateways,id',
        ]);

        $service = Service::findOrFail($validated['service_id']);
        $user = $request->user();

        // Check if service is bookable
        if (!$service->isBookable()) {
            return response()->json([
                'success' => false,
                'message' => 'This service is not available for booking.'
            ], 422);
        }

        // Check capacity
        if ($validated['guest_count'] > $service->capacity) {
            return response()->json([
                'success' => false,
                'message' => 'Requested guest count exceeds service capacity.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Calculate pricing
            $totalAmount = $this->calculateBookingAmount($service, $validated['guest_count']);
            $commissionRate = config('app.commission_rate', 5); // 5% default
            $commissionAmount = $totalAmount * ($commissionRate / 100);

            // Create booking
            $bookingData = [
                'customer_id' => $user->id,
                'bookable_id' => $service->id,
                'bookable_type' => Service::class,
                'merchant_id' => $service->merchant_id,
                'booking_date' => $validated['booking_date'],
                'booking_time' => $validated['booking_time'] ?? null,
                'guest_count' => $validated['guest_count'],
                'total_amount' => $totalAmount,
                'commission_amount' => $commissionAmount,
                'commission_rate' => $commissionRate,
                'payment_status' => 'pending',
                'status' => 'pending',
                'booking_source' => 'api',
                'special_requests' => $validated['special_requests'] ?? null,
                'customer_name' => $validated['customer_name'] ?? $user->name,
                'customer_phone' => $validated['customer_phone'] ?? $user->phone,
                'customer_email' => $validated['customer_email'] ?? $user->email,
            ];

            $booking = Booking::create($bookingData);

            // Initialize payment
            $paymentResult = $this->paymentService->initiatePayment(
                $booking,
                $validated['payment_gateway_id'],
                $validated['payment_method'],
                $totalAmount,
                $service->currency ?? 'SAR'
            );

            if (!$paymentResult['success']) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Payment initialization failed.',
                    'error' => $paymentResult['message']
                ], 422);
            }

            DB::commit();

            $booking->load(['customer', 'merchant', 'bookable', 'payments']);

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully.',
                'data' => new BookingResource($booking),
                'payment' => $paymentResult['data'],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified booking
     */
    public function show(Request $request, Booking $booking): JsonResponse
    {
        $user = $request->user();

        // Check access permissions
        if (!$this->canAccessBooking($user, $booking)) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found or access denied.'
            ], 404);
        }

        $booking->load(['customer', 'merchant', 'bookable', 'payments', 'seatReservations']);

        return response()->json([
            'success' => true,
            'data' => new BookingResource($booking),
        ]);
    }

    /**
     * Update the specified booking
     */
    public function update(Request $request, Booking $booking): JsonResponse
    {
        $user = $request->user();

        if (!$this->canModifyBooking($user, $booking)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to modify this booking.'
            ], 403);
        }

        $validated = $request->validate([
            'booking_date' => 'sometimes|date|after:today',
            'booking_time' => 'sometimes|string',
            'guest_count' => 'sometimes|integer|min:1',
            'special_requests' => 'sometimes|string|max:1000',
            'customer_name' => 'sometimes|string|max:255',
            'customer_phone' => 'sometimes|string|max:20',
            'customer_email' => 'sometimes|email|max:255',
        ]);

        // Check if booking can be modified
        if (!$this->isBookingModifiable($booking)) {
            return response()->json([
                'success' => false,
                'message' => 'This booking cannot be modified. It may be too close to the booking date or already completed.'
            ], 422);
        }

        // If guest count changed, recalculate amount
        if (isset($validated['guest_count']) && $validated['guest_count'] !== $booking->guest_count) {
            $service = $booking->bookable;
            if ($validated['guest_count'] > $service->capacity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Requested guest count exceeds service capacity.'
                ], 422);
            }

            $newAmount = $this->calculateBookingAmount($service, $validated['guest_count']);
            $commissionAmount = $newAmount * ($booking->commission_rate / 100);

            $validated['total_amount'] = $newAmount;
            $validated['commission_amount'] = $commissionAmount;
        }

        $booking->update($validated);
        $booking->load(['customer', 'merchant', 'bookable', 'payments']);

        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully.',
            'data' => new BookingResource($booking),
        ]);
    }

    /**
     * Cancel the specified booking
     */
    public function cancel(Request $request, Booking $booking): JsonResponse
    {
        $user = $request->user();

        if (!$this->canCancelBooking($user, $booking)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to cancel this booking.'
            ], 403);
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        // Check if booking can be cancelled
        if ($booking->isCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'Booking is already cancelled.'
            ], 422);
        }

        if ($booking->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel a completed booking.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Update booking status
            $booking->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->cancellation_reason,
                'cancelled_at' => now(),
                'cancelled_by' => $user->id,
            ]);

            // Process refund if payment was made
            if ($booking->isPaid()) {
                $refundResult = $this->paymentService->processRefund($booking);
                // Note: Refund processing might be handled asynchronously
            }

            DB::commit();

            $booking->load(['customer', 'merchant', 'bookable', 'payments']);

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully.',
                'data' => new BookingResource($booking),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel booking.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm a booking (for merchants)
     */
    public function confirm(Request $request, Booking $booking): JsonResponse
    {
        $user = $request->user();

        if (!$user->isMerchant() || $booking->merchant_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to confirm this booking.'
            ], 403);
        }

        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending bookings can be confirmed.'
            ], 422);
        }

        $booking->update(['status' => 'confirmed']);
        $booking->load(['customer', 'merchant', 'bookable', 'payments']);

        return response()->json([
            'success' => true,
            'message' => 'Booking confirmed successfully.',
            'data' => new BookingResource($booking),
        ]);
    }

    /**
     * Mark booking as completed (for merchants)
     */
    public function complete(Request $request, Booking $booking): JsonResponse
    {
        $user = $request->user();

        if (!$user->isMerchant() || $booking->merchant_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to complete this booking.'
            ], 403);
        }

        if (!in_array($booking->status, ['confirmed', 'pending'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only confirmed or pending bookings can be completed.'
            ], 422);
        }

        $booking->update(['status' => 'completed']);
        $booking->load(['customer', 'merchant', 'bookable', 'payments']);

        return response()->json([
            'success' => true,
            'message' => 'Booking completed successfully.',
            'data' => new BookingResource($booking),
        ]);
    }

    /**
     * Get booking statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Booking::query();

        // Apply user-based filtering
        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        } elseif ($user->isMerchant()) {
            $query->where('merchant_id', $user->id);
        }

        $stats = [
            'total_bookings' => (clone $query)->count(),
            'pending_bookings' => (clone $query)->where('status', 'pending')->count(),
            'confirmed_bookings' => (clone $query)->where('status', 'confirmed')->count(),
            'completed_bookings' => (clone $query)->where('status', 'completed')->count(),
            'cancelled_bookings' => (clone $query)->where('status', 'cancelled')->count(),
            'total_revenue' => (clone $query)->where('payment_status', 'paid')->sum('total_amount'),
            'pending_payments' => (clone $query)->where('payment_status', 'pending')->sum('total_amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Calculate booking amount based on service and guest count
     */
    private function calculateBookingAmount(Service $service, int $guestCount): float
    {
        $basePrice = $service->base_price ?? $service->price;

        return match ($service->pricing_model) {
            'per_person' => $basePrice * $guestCount,
            'per_table' => $basePrice * ceil($guestCount / 8), // Assuming 8 people per table
            'hourly', 'per_hour' => $basePrice * ($service->duration_hours ?? 1),
            default => $basePrice, // fixed, package
        };
    }

    /**
     * Check if user can access booking
     */
    private function canAccessBooking($user, Booking $booking): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCustomer() && $booking->customer_id === $user->id) {
            return true;
        }

        if ($user->isMerchant() && $booking->merchant_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can modify booking
     */
    private function canModifyBooking($user, Booking $booking): bool
    {
        // Only customers can modify their own bookings, and only if not cancelled/completed
        return $user->isCustomer() && 
               $booking->customer_id === $user->id && 
               !in_array($booking->status, ['cancelled', 'completed']);
    }

    /**
     * Check if user can cancel booking
     */
    private function canCancelBooking($user, Booking $booking): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCustomer() && $booking->customer_id === $user->id) {
            return true;
        }

        if ($user->isMerchant() && $booking->merchant_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Check if booking is modifiable (based on timing and status)
     */
    private function isBookingModifiable(Booking $booking): bool
    {
        if (in_array($booking->status, ['cancelled', 'completed'])) {
            return false;
        }

        // Cannot modify if booking is within 48 hours
        if ($booking->booking_date && $booking->booking_date->subHours(48)->isPast()) {
            return false;
        }

        return true;
    }
}