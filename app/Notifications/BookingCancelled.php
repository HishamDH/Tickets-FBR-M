<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Booking $booking
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
        $isCustomer = $this->booking->customer_id === $notifiable->id;
        
        if ($isCustomer) {
            return (new MailMessage)
                ->subject('إلغاء الحجز - ' . $this->booking->booking_number)
                ->greeting('مرحباً ' . $notifiable->name)
                ->line('تم إلغاء حجزك.')
                ->line('رقم الحجز: ' . $this->booking->booking_number)
                ->line('الخدمة: ' . $this->booking->service->name)
                ->line('التاريخ: ' . $this->booking->booking_date?->format('d/m/Y'))
                ->when($this->booking->cancellation_reason, function ($mail) {
                    return $mail->line('سبب الإلغاء: ' . $this->booking->cancellation_reason);
                })
                ->when($this->booking->isPaid(), function ($mail) {
                    return $mail->line('سيتم رد المبلغ المدفوع خلال 3-5 أيام عمل.');
                })
                ->line('نعتذر عن أي إزعاج، ونتطلع لخدمتك في المستقبل.');
        } else {
            return (new MailMessage)
                ->subject('تم إلغاء حجز - ' . $this->booking->booking_number)
                ->greeting('مرحباً ' . $notifiable->name)
                ->line('تم إلغاء أحد الحجوزات.')
                ->line('رقم الحجز: ' . $this->booking->booking_number)
                ->line('العميل: ' . ($this->booking->customer?->name ?? $this->booking->customer_name))
                ->line('الخدمة: ' . $this->booking->service->name)
                ->line('التاريخ: ' . $this->booking->booking_date?->format('d/m/Y'))
                ->when($this->booking->cancellation_reason, function ($mail) {
                    return $mail->line('سبب الإلغاء: ' . $this->booking->cancellation_reason);
                })
                ->action('عرض تفاصيل الحجز', url('/merchant/dashboard/bookings/' . $this->booking->id))
                ->line('يرجى مراجعة جدولك وتحديث توفر الخدمة.');
        }
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $isCustomer = $this->booking->customer_id === $notifiable->id;
        
        return [
            'type' => 'booking_cancelled',
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'service_name' => $this->booking->service->name,
            'booking_date' => $this->booking->booking_date?->format('Y-m-d'),
            'cancellation_reason' => $this->booking->cancellation_reason,
            'is_customer_notification' => $isCustomer,
            'title' => 'تم إلغاء الحجز',
            'message' => $isCustomer 
                ? 'تم إلغاء حجزك رقم ' . $this->booking->booking_number
                : 'تم إلغاء الحجز رقم ' . $this->booking->booking_number,
            'action_url' => $isCustomer 
                ? '/customer/dashboard/bookings/' . $this->booking->id
                : '/merchant/dashboard/bookings/' . $this->booking->id,
        ];
    }
}
