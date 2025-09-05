<?php

namespace App\Listeners;

use App\Events\BookingStatusChanged;
use App\Notifications\ReviewRequestNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendReviewRequest implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookingStatusChanged $event): void
    {
        $booking = $event->booking;
        
        // فقط إذا كانت حالة الحجز مكتملة أو مدفوعة
        if ($event->newStatus === 'completed' || $event->newStatus === 'paid') {
            // انتظر لفترة قبل إرسال طلب التقييم (24 ساعة)
            $delay = now()->addHours(24);
            
            // تحقق من أن المستخدم هو عميل وليس موظف
            $customer = $booking->customer;
            
            if ($customer) {
                // الإخطار سيرسل بعد 24 ساعة
                $customer->notify((new ReviewRequestNotification($booking))->delay($delay));
            }
        }
    }
}
