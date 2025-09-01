<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceAvailability;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingService
{
    public function __construct(
        protected PaymentService $paymentService,
        protected NotificationService $notificationService
    ) {}

    /**
     * إنشاء حجز جديد
     */
    public function createBooking(array $data, ?User $customer = null): Booking
    {
        return DB::transaction(function () use ($data, $customer) {
            // التحقق من توفر الخدمة
            $service = Service::findOrFail($data['service_id']);

            if (! $service->isBookable()) {
                throw new \Exception('هذه الخدمة غير متاحة للحجز حالياً');
            }

            // التحقق من توفر التاريخ
            $this->validateBookingDate($service, $data['booking_date']);

            // حساب المبالغ
            $amounts = $this->calculateAmounts($service, $data);

            // إنشاء الحجز
            $booking = Booking::create([
                'booking_number' => $this->generateBookingNumber(),
                'customer_id' => $customer?->id,
                'service_id' => $service->id,
                'merchant_id' => $service->merchant_id,
                'booking_date' => $data['booking_date'],
                'booking_time' => $data['booking_time'] ?? null,
                'guest_count' => $data['guest_count'] ?? 1,
                'total_amount' => $amounts['total'],
                'commission_amount' => $amounts['commission'],
                'commission_rate' => $amounts['commission_rate'],
                'special_requests' => $data['special_requests'] ?? null,
                'qr_code' => $this->generateQrCode(),

                // للعملاء غير المسجلين
                'customer_name' => $data['customer_name'] ?? $customer?->name,
                'customer_phone' => $data['customer_phone'] ?? $customer?->phone,
                'customer_email' => $data['customer_email'] ?? $customer?->email,
                'number_of_people' => $data['number_of_people'] ?? $data['guest_count'],
                'number_of_tables' => $data['number_of_tables'] ?? null,
                'duration_hours' => $data['duration_hours'] ?? $service->duration_hours,
                'notes' => $data['notes'] ?? $data['special_requests'],
            ]);

            // تحديث توفر الخدمة
            $this->updateServiceAvailability($booking);

            // إرسال الإشعارات
            $this->notificationService->sendBookingNotifications($booking);

            Log::info('Booking created successfully', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'service_id' => $service->id,
                'customer_id' => $customer?->id,
            ]);

            return $booking;
        });
    }

    /**
     * تأكيد الحجز
     */
    public function confirmBooking(Booking $booking): bool
    {
        try {
            $booking->update(['booking_status' => 'confirmed']);

            $this->notificationService->sendBookingConfirmation($booking);

            Log::info('Booking confirmed', ['booking_id' => $booking->id]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to confirm booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * إلغاء الحجز
     */
    public function cancelBooking(Booking $booking, string $reason, User $cancelledBy): bool
    {
        try {
            DB::transaction(function () use ($booking, $reason, $cancelledBy) {
                $booking->update([
                    'booking_status' => 'cancelled',
                    'cancellation_reason' => $reason,
                    'cancelled_at' => now(),
                    'cancelled_by' => $cancelledBy->id,
                ]);

                // إعادة توفر الخدمة
                $this->restoreServiceAvailability($booking);

                // معالجة الاسترداد إذا كان مدفوعاً
                if ($booking->isPaid()) {
                    $this->paymentService->processRefund($booking);
                }

                // إرسال الإشعارات
                $this->notificationService->sendBookingCancellation($booking);
            });

            Log::info('Booking cancelled', [
                'booking_id' => $booking->id,
                'reason' => $reason,
                'cancelled_by' => $cancelledBy->id,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to cancel booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * التحقق من صحة تاريخ الحجز
     */
    protected function validateBookingDate(Service $service, string $bookingDate): void
    {
        $date = Carbon::parse($bookingDate);

        if ($date->isPast()) {
            throw new \Exception('لا يمكن الحجز في تاريخ مضى');
        }

        // التحقق من توفر التاريخ
        $availability = ServiceAvailability::where('service_id', $service->id)
            ->where('available_date', $date->toDateString())
            ->first();

        if (! $availability || ! $availability->isAvailableForBooking()) {
            throw new \Exception('التاريخ المحدد غير متاح للحجز');
        }
    }

    /**
     * حساب المبالغ
     */
    protected function calculateAmounts(Service $service, array $data): array
    {
        $basePrice = $service->base_price ?? $service->price;
        $guestCount = $data['guest_count'] ?? 1;

        // حساب السعر الأساسي
        $subtotal = match ($service->pricing_model) {
            'per_person' => $basePrice * $guestCount,
            'per_hour' => $basePrice * ($data['duration_hours'] ?? $service->duration_hours ?? 1),
            'per_table' => $basePrice * ($data['number_of_tables'] ?? 1),
            default => $basePrice
        };

        // حساب العمولة
        $commissionRate = $service->merchant->commission_rate ?? config('app.default_commission_rate', 3.0);
        $commission = ($subtotal * $commissionRate) / 100;

        return [
            'subtotal' => $subtotal,
            'commission' => $commission,
            'commission_rate' => $commissionRate,
            'total' => $subtotal,
        ];
    }

    /**
     * تحديث توفر الخدمة
     */
    protected function updateServiceAvailability(Booking $booking): void
    {
        $availability = ServiceAvailability::where('service_id', $booking->service_id)
            ->where('available_date', $booking->booking_date)
            ->first();

        if ($availability) {
            $availability->increment('booked_slots', $booking->guest_count ?? 1);
        }
    }

    /**
     * استعادة توفر الخدمة
     */
    protected function restoreServiceAvailability(Booking $booking): void
    {
        $availability = ServiceAvailability::where('service_id', $booking->service_id)
            ->where('available_date', $booking->booking_date)
            ->first();

        if ($availability) {
            $availability->decrement('booked_slots', $booking->guest_count ?? 1);
        }
    }

    /**
     * توليد رقم حجز فريد
     */
    protected function generateBookingNumber(): string
    {
        do {
            $number = 'TKT-'.date('Y').'-'.str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Booking::where('booking_number', $number)->exists());

        return $number;
    }

    /**
     * توليد رمز QR فريد
     */
    protected function generateQrCode(): string
    {
        return \Illuminate\Support\Str::uuid()->toString();
    }

    /**
     * الحصول على الحجوزات القادمة للخدمة
     */
    public function getUpcomingBookings(Service $service, int $days = 30): \Illuminate\Database\Eloquent\Collection
    {
        return $service->bookings()
            ->where('booking_status', '!=', 'cancelled')
            ->where('booking_date', '>=', now()->toDateString())
            ->where('booking_date', '<=', now()->addDays($days)->toDateString())
            ->with(['customer', 'service'])
            ->orderBy('booking_date')
            ->orderBy('booking_time')
            ->get();
    }

    /**
     * الحصول على إحصائيات الحجوزات
     */
    public function getBookingStatistics(?Service $service = null): array
    {
        $query = Booking::query();

        if ($service) {
            $query->where('service_id', $service->id);
        }

        return [
            'total_bookings' => $query->count(),
            'confirmed_bookings' => $query->where('booking_status', 'confirmed')->count(),
            'pending_bookings' => $query->where('booking_status', 'pending')->count(),
            'cancelled_bookings' => $query->where('booking_status', 'cancelled')->count(),
            'completed_bookings' => $query->where('booking_status', 'completed')->count(),
            'total_revenue' => $query->where('payment_status', 'paid')->sum('total_amount'),
            'today_bookings' => $query->whereDate('created_at', today())->count(),
            'this_month_bookings' => $query->whereMonth('created_at', now()->month)->count(),
        ];
    }
}
