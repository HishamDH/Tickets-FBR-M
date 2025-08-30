<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;
    protected $amount;
    protected $paymentMethod;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, $amount, $paymentMethod = 'online')
    {
        $this->booking = $booking;
        $this->amount = $amount;
        $this->paymentMethod = $paymentMethod;
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

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->isForMerchant($notifiable) 
            ? 'تم استلام دفعة - ' . $this->booking->service->name
            : 'تأكيد الدفع - ' . $this->booking->service->name;

        $greeting = $this->isForMerchant($notifiable)
            ? 'مرحباً ' . $notifiable->name
            : 'مرحباً ' . $this->booking->customer_name;

        $line = $this->isForMerchant($notifiable)
            ? 'تم استلام دفعة للحجز: ' . $this->booking->booking_reference
            : 'تم تأكيد دفعتك للحجز: ' . $this->booking->booking_reference;

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($line)
            ->line('المبلغ المدفوع: ' . number_format($this->amount, 2) . ' ريال')
            ->line('طريقة الدفع: ' . $this->getPaymentMethodText())
            ->line('الخدمة: ' . $this->booking->service->name)
            ->line('تاريخ الحجز: ' . $this->booking->booking_date->format('Y-m-d H:i'))
            ->action('عرض تفاصيل الحجز', url('/bookings/' . $this->booking->id))
            ->line('شكراً لك!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'payment_received',
            'booking_id' => $this->booking->id,
            'booking_reference' => $this->booking->booking_reference,
            'service_name' => $this->booking->service->name,
            'amount' => $this->amount,
            'payment_method' => $this->paymentMethod,
            'customer_name' => $this->booking->customer_name,
            'message' => $this->isForMerchant($notifiable) 
                ? 'تم استلام دفعة بمبلغ ' . number_format($this->amount, 2) . ' ريال'
                : 'تم تأكيد دفعتك بمبلغ ' . number_format($this->amount, 2) . ' ريال',
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'type' => 'payment_received',
            'title' => $this->isForMerchant($notifiable) ? 'دفعة جديدة' : 'تأكيد الدفع',
            'message' => $this->isForMerchant($notifiable) 
                ? 'تم استلام دفعة بمبلغ ' . number_format($this->amount, 2) . ' ريال'
                : 'تم تأكيد دفعتك بمبلغ ' . number_format($this->amount, 2) . ' ريال',
            'data' => $this->toDatabase($notifiable),
            'created_at' => now()->toISOString(),
        ]);
    }

    /**
     * Check if notification is for merchant.
     */
    protected function isForMerchant(object $notifiable): bool
    {
        return $notifiable->id === $this->booking->service->merchant_id;
    }

    /**
     * Get payment method text in Arabic.
     */
    protected function getPaymentMethodText(): string
    {
        $methods = [
            'online' => 'دفع إلكتروني',
            'cash' => 'نقداً',
            'bank_transfer' => 'تحويل بنكي',
            'credit_card' => 'بطاقة ائتمان',
        ];

        return $methods[$this->paymentMethod] ?? 'غير محدد';
    }
}
