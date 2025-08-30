<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmed extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('تأكيد الحجز - ' . $this->booking->booking_number)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم تأكيد حجزك بنجاح!')
            ->line('رقم الحجز: ' . $this->booking->booking_number)
            ->line('الخدمة: ' . $this->booking->service->name)
            ->line('التاريخ: ' . $this->booking->booking_date?->format('d/m/Y'))
            ->line('الوقت: ' . $this->booking->booking_time?->format('H:i'))
            ->when($this->booking->special_requests, function ($mail) {
                return $mail->line('الطلبات الخاصة: ' . $this->booking->special_requests);
            })
            ->action('عرض تفاصيل الحجز', url('/customer/dashboard/bookings/' . $this->booking->id))
            ->line('نتطلع لخدمتك! في حالة وجود أي استفسار، لا تتردد في التواصل معنا.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'booking_confirmed',
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'service_name' => $this->booking->service->name,
            'booking_date' => $this->booking->booking_date?->format('Y-m-d'),
            'booking_time' => $this->booking->booking_time?->format('H:i'),
            'title' => 'تم تأكيد حجزك',
            'message' => 'تم تأكيد حجزك رقم ' . $this->booking->booking_number . ' بنجاح',
            'action_url' => '/customer/dashboard/bookings/' . $this->booking->id,
        ];
    }
}
