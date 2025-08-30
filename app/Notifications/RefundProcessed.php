<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RefundProcessed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Payment $refundPayment
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        if ($notifiable->email) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->refundPayment->booking;
        $refundAmount = abs($this->refundPayment->amount);
        
        return (new MailMessage)
            ->subject('تأكيد الاسترداد - ' . $booking->booking_number)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم معالجة طلب الاسترداد الخاص بك.')
            ->line('رقم الحجز: ' . $booking->booking_number)
            ->line('المبلغ المسترد: ' . number_format($refundAmount) . ' ريال')
            ->line('رقم عملية الاسترداد: ' . $this->refundPayment->payment_number)
            ->line('تاريخ المعالجة: ' . $this->refundPayment->completed_at?->format('d/m/Y H:i'))
            ->line('سيظهر المبلغ في حسابك خلال 3-5 أيام عمل حسب بنكك.')
            ->action('عرض تفاصيل الحجز', url('/customer/dashboard/bookings/' . $booking->id))
            ->line('شكراً لاستخدامك منصة شباك.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $booking = $this->refundPayment->booking;
        $refundAmount = abs($this->refundPayment->amount);
        
        return [
            'type' => 'refund_processed',
            'payment_id' => $this->refundPayment->id,
            'payment_number' => $this->refundPayment->payment_number,
            'booking_id' => $booking->id,
            'booking_number' => $booking->booking_number,
            'refund_amount' => $refundAmount,
            'completed_at' => $this->refundPayment->completed_at?->toISOString(),
            'title' => 'تم معالجة الاسترداد',
            'message' => 'تم استرداد مبلغ ' . number_format($refundAmount) . ' ريال لحجزك رقم ' . $booking->booking_number,
            'action_url' => '/customer/dashboard/bookings/' . $booking->id,
        ];
    }
}
