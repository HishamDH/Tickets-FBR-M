<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database', 'broadcast'];

        // Add email if user has email notifications enabled
        if ($notifiable->notification_preferences['email'] ?? true) {
            $channels[] = 'mail';
        }

        // Add SMS if user has SMS notifications enabled
        if ($notifiable->notification_preferences['sms'] ?? false) {
            $channels[] = 'sms';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->isForMerchant($notifiable)
            ? 'حجز جديد - '.$this->booking->service->name
            : 'تأكيد الحجز - '.$this->booking->service->name;

        $greeting = $this->isForMerchant($notifiable)
            ? 'مرحباً '.$notifiable->name
            : 'مرحباً '.$this->booking->customer_name;

        $line = $this->isForMerchant($notifiable)
            ? 'لديك حجز جديد للخدمة: '.$this->booking->service->name
            : 'تم تأكيد حجزك للخدمة: '.$this->booking->service->name;

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($line)
            ->line('رقم الحجز: '.$this->booking->booking_reference)
            ->line('تاريخ الحجز: '.$this->booking->booking_date->format('Y-m-d H:i'))
            ->line('المبلغ الإجمالي: '.number_format($this->booking->total_amount, 2).' ريال')
            ->action('عرض تفاصيل الحجز', url('/bookings/'.$this->booking->id))
            ->line('شكراً لاستخدامك منصتنا!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'booking_confirmed',
            'booking_id' => $this->booking->id,
            'booking_reference' => $this->booking->booking_reference,
            'service_name' => $this->booking->service->name,
            'customer_name' => $this->booking->customer_name,
            'booking_date' => $this->booking->booking_date->toISOString(),
            'total_amount' => $this->booking->total_amount,
            'message' => $this->isForMerchant($notifiable)
                ? 'حجز جديد للخدمة: '.$this->booking->service->name
                : 'تم تأكيد حجزك للخدمة: '.$this->booking->service->name,
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'type' => 'booking_confirmed',
            'title' => $this->isForMerchant($notifiable) ? 'حجز جديد' : 'تأكيد الحجز',
            'message' => $this->isForMerchant($notifiable)
                ? 'حجز جديد للخدمة: '.$this->booking->service->name
                : 'تم تأكيد حجزك للخدمة: '.$this->booking->service->name,
            'data' => $this->toDatabase($notifiable),
            'created_at' => now()->toISOString(),
        ]);
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): string
    {
        $message = $this->isForMerchant($notifiable)
            ? 'حجز جديد للخدمة: '.$this->booking->service->name
            : 'تم تأكيد حجزك للخدمة: '.$this->booking->service->name;

        return $message.' - رقم الحجز: '.$this->booking->booking_reference;
    }

    /**
     * Check if notification is for merchant.
     */
    protected function isForMerchant(object $notifiable): bool
    {
        return $notifiable->id === $this->booking->service->merchant_id;
    }
}
