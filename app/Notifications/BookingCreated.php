<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCreated extends Notification implements ShouldQueue
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
                ->subject('تأكيد حجزك - ' . $this->booking->booking_number)
                ->greeting('مرحباً ' . $notifiable->name)
                ->line('تم إنشاء حجزك بنجاح.')
                ->line('رقم الحجز: ' . $this->booking->booking_number)
                ->line('الخدمة: ' . $this->booking->service->name)
                ->line('التاريخ: ' . $this->booking->booking_date?->format('d/m/Y'))
                ->line('الوقت: ' . $this->booking->booking_time?->format('H:i'))
                ->line('المبلغ الإجمالي: ' . number_format($this->booking->total_amount) . ' ريال')
                ->action('عرض الحجز', url('/customer/dashboard/bookings/' . $this->booking->id))
                ->line('شكراً لاستخدامك منصة شباك!');
        } else {
            return (new MailMessage)
                ->subject('حجز جديد - ' . $this->booking->booking_number)
                ->greeting('مرحباً ' . $notifiable->name)
                ->line('لديك حجز جديد.')
                ->line('رقم الحجز: ' . $this->booking->booking_number)
                ->line('العميل: ' . ($this->booking->customer?->name ?? $this->booking->customer_name))
                ->line('الخدمة: ' . $this->booking->service->name)
                ->line('التاريخ: ' . $this->booking->booking_date?->format('d/m/Y'))
                ->line('المبلغ: ' . number_format($this->booking->total_amount) . ' ريال')
                ->action('إدارة الحجز', url('/merchant/dashboard/bookings/' . $this->booking->id))
                ->line('يرجى مراجعة تفاصيل الحجز واتخاذ الإجراء المناسب.');
        }
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $isCustomer = $this->booking->customer_id === $notifiable->id;
        
        return [
            'type' => 'booking_created',
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'service_name' => $this->booking->service->name,
            'booking_date' => $this->booking->booking_date?->format('Y-m-d'),
            'booking_time' => $this->booking->booking_time?->format('H:i'),
            'amount' => $this->booking->total_amount,
            'customer_name' => $this->booking->customer?->name ?? $this->booking->customer_name,
            'is_customer_notification' => $isCustomer,
            'title' => $isCustomer ? 'تم إنشاء حجزك بنجاح' : 'حجز جديد',
            'message' => $isCustomer 
                ? 'تم إنشاء حجزك رقم ' . $this->booking->booking_number . ' بنجاح'
                : 'لديك حجز جديد رقم ' . $this->booking->booking_number,
            'action_url' => $isCustomer 
                ? '/customer/dashboard/bookings/' . $this->booking->id
                : '/merchant/dashboard/bookings/' . $this->booking->id,
        ];
    }
}
