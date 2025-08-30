<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Events\BookingStatusChanged;
use App\Events\PaymentReceived;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\PaymentReceivedNotification;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBookingNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle booking created event.
     */
    public function handleBookingCreated(BookingCreated $event)
    {
        $booking = $event->booking;

        // Send notification to customer
        if ($booking->customer) {
            $booking->customer->notify(new BookingConfirmedNotification($booking));
        }

        // Send notification to merchant
        if ($booking->service && $booking->service->merchant) {
            $booking->service->merchant->notify(new BookingConfirmedNotification($booking));
        }

        // Create custom notification records
        $this->createNotificationRecords($booking, 'booking_confirmed');
    }

    /**
     * Handle booking status changed event.
     */
    public function handleBookingStatusChanged(BookingStatusChanged $event)
    {
        $booking = $event->booking;
        $oldStatus = $event->oldStatus;
        $newStatus = $event->newStatus;

        $statusMessages = [
            'confirmed' => 'تم تأكيد حجزك',
            'cancelled' => 'تم إلغاء الحجز',
            'completed' => 'تم إكمال الخدمة',
            'in_progress' => 'جاري تنفيذ الخدمة',
        ];

        $message = $statusMessages[$newStatus] ?? 'تم تحديث حالة الحجز';

        // Notify customer
        if ($booking->customer) {
            Notification::createForUser(
                $booking->customer,
                'booking_status_changed',
                'تحديث حالة الحجز',
                $message,
                [
                    'booking_id' => $booking->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'service_name' => $booking->service->name,
                ],
                $newStatus === 'cancelled' ? 'high' : 'normal'
            );
        }

        // Notify merchant
        if ($booking->service && $booking->service->merchant) {
            Notification::createForUser(
                $booking->service->merchant,
                'booking_status_changed',
                'تحديث حالة الحجز',
                "تم تحديث حالة الحجز #{$booking->booking_reference} إلى: {$message}",
                [
                    'booking_id' => $booking->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'customer_name' => $booking->customer_name,
                ],
                'normal'
            );
        }
    }

    /**
     * Handle payment received event.
     */
    public function handlePaymentReceived(PaymentReceived $event)
    {
        $booking = $event->booking;
        $amount = $event->amount;
        $paymentMethod = $event->paymentMethod;

        // Send notification to customer
        if ($booking->customer) {
            $booking->customer->notify(new PaymentReceivedNotification($booking, $amount, $paymentMethod));
        }

        // Send notification to merchant
        if ($booking->service && $booking->service->merchant) {
            $booking->service->merchant->notify(new PaymentReceivedNotification($booking, $amount, $paymentMethod));
        }

        // Create custom notification records
        $this->createPaymentNotificationRecords($booking, $amount, $paymentMethod);
    }

    /**
     * Create notification records for booking.
     */
    protected function createNotificationRecords($booking, $type)
    {
        // Customer notification
        if ($booking->customer) {
            Notification::createForUser(
                $booking->customer,
                $type,
                'تأكيد الحجز',
                "تم تأكيد حجزك للخدمة: {$booking->service->name}",
                [
                    'booking_id' => $booking->id,
                    'service_name' => $booking->service->name,
                    'booking_date' => $booking->booking_date->toISOString(),
                    'total_amount' => $booking->total_amount,
                ],
                'normal'
            );
        }

        // Merchant notification
        if ($booking->service && $booking->service->merchant) {
            Notification::createForUser(
                $booking->service->merchant,
                $type,
                'حجز جديد',
                "حجز جديد للخدمة: {$booking->service->name} من {$booking->customer_name}",
                [
                    'booking_id' => $booking->id,
                    'customer_name' => $booking->customer_name,
                    'service_name' => $booking->service->name,
                    'booking_date' => $booking->booking_date->toISOString(),
                ],
                'high'
            );
        }
    }

    /**
     * Create notification records for payment.
     */
    protected function createPaymentNotificationRecords($booking, $amount, $paymentMethod)
    {
        // Customer notification
        if ($booking->customer) {
            Notification::createForUser(
                $booking->customer,
                'payment_received',
                'تأكيد الدفع',
                "تم تأكيد دفعتك بمبلغ " . number_format($amount, 2) . " ريال",
                [
                    'booking_id' => $booking->id,
                    'amount' => $amount,
                    'payment_method' => $paymentMethod,
                ],
                'normal'
            );
        }

        // Merchant notification
        if ($booking->service && $booking->service->merchant) {
            Notification::createForUser(
                $booking->service->merchant,
                'payment_received',
                'دفعة جديدة',
                "تم استلام دفعة بمبلغ " . number_format($amount, 2) . " ريال للحجز #{$booking->booking_reference}",
                [
                    'booking_id' => $booking->id,
                    'amount' => $amount,
                    'payment_method' => $paymentMethod,
                    'customer_name' => $booking->customer_name,
                ],
                'high'
            );
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(BookingCreated|BookingStatusChanged|PaymentReceived $event, $exception)
    {
        // Log the failure or handle it appropriately
        \Log::error('Failed to send booking notifications', [
            'event' => get_class($event),
            'exception' => $exception->getMessage(),
        ]);
    }
}
