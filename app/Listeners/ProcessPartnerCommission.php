<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Events\PaymentReceived;
use App\Services\PartnerCommissionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessPartnerCommission implements ShouldQueue
{
    use InteractsWithQueue;

    protected $commissionService;

    public function __construct(PartnerCommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    /**
     * معالجة العمولة عند إتمام الدفع
     */
    public function handle($event)
    {
        try {
            // معالجة العمولة حسب نوع الحدث
            if ($event instanceof PaymentReceived) {
                $this->handlePaymentReceived($event);
            } elseif ($event instanceof BookingCreated) {
                $this->handleBookingCreated($event);
            }
        } catch (\Exception $e) {
            Log::error('خطأ في معالجة عمولة الشريك', [
                'event' => get_class($event),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * معالجة العمولة عند استلام الدفع
     */
    private function handlePaymentReceived(PaymentReceived $event)
    {
        $booking = $event->booking;
        
        // التأكد من أن الحجز مدفوع بالكامل
        if ($booking->payment_status === 'paid') {
            $success = $this->commissionService->processBookingCommission($booking);
            
            if ($success) {
                Log::info('تم معالجة عمولة الشريك بنجاح', [
                    'booking_id' => $booking->id,
                    'merchant_id' => $booking->merchant_id,
                    'partner_id' => $booking->merchant->partner_id ?? null
                ]);
            }
        }
    }

    /**
     * معالجة العمولة عند إنشاء الحجز (إذا كان مدفوع مسبقاً)
     */
    private function handleBookingCreated(BookingCreated $event)
    {
        $booking = $event->booking;
        
        // معالجة العمولة فقط إذا كان الحجز مدفوع
        if ($booking->payment_status === 'paid') {
            $success = $this->commissionService->processBookingCommission($booking);
            
            if ($success) {
                Log::info('تم معالجة عمولة الشريك من حجز جديد', [
                    'booking_id' => $booking->id,
                    'merchant_id' => $booking->merchant_id,
                    'partner_id' => $booking->merchant->partner_id ?? null
                ]);
            }
        }
    }

    /**
     * معالجة فشل العملية
     */
    public function failed($event, $exception)
    {
        Log::error('فشل في معالجة عمولة الشريك', [
            'event' => get_class($event),
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
