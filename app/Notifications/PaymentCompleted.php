<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Payment $payment
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
        $booking = $this->payment->booking;
        $isCustomer = $booking->customer_id === $notifiable->id;
        
        if ($isCustomer) {
            return (new MailMessage)
                ->subject('تأكيد الدفع - ' . $booking->booking_number)
                ->greeting('مرحباً ' . $notifiable->name)
                ->line('تم تأكيد دفعتك بنجاح!')
                ->line('رقم الحجز: ' . $booking->booking_number)
                ->line('رقم الدفع: ' . $this->payment->payment_number)
                ->line('المبلغ المدفوع: ' . number_format($this->payment->amount) . ' ريال')
                ->line('طريقة الدفع: ' . $this->getPaymentMethodArabic())
                ->line('تاريخ الدفع: ' . $this->payment->completed_at?->format('d/m/Y H:i'))
                ->action('عرض فاتورة الدفع', url('/customer/dashboard/bookings/' . $booking->id))
                ->line('احتفظ بهذا الإيصال كإثبات للدفع.');
        } else {
            return (new MailMessage)
                ->subject('دفعة جديدة - ' . $booking->booking_number)
                ->greeting('مرحباً ' . $notifiable->name)
                ->line('تم استلام دفعة جديدة.')
                ->line('رقم الحجز: ' . $booking->booking_number)
                ->line('العميل: ' . ($booking->customer?->name ?? $booking->customer_name))
                ->line('المبلغ: ' . number_format($this->payment->amount) . ' ريال')
                ->line('عمولة المنصة: ' . number_format($this->payment->platform_fee) . ' ريال')
                ->line('المبلغ الصافي: ' . number_format($this->payment->amount - $this->payment->platform_fee) . ' ريال')
                ->action('عرض تفاصيل الدفع', url('/merchant/dashboard/bookings/' . $booking->id))
                ->line('سيتم تحويل المبلغ الصافي خلال دورة التسوية التالية.');
        }
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $booking = $this->payment->booking;
        $isCustomer = $booking->customer_id === $notifiable->id;
        
        return [
            'type' => 'payment_completed',
            'payment_id' => $this->payment->id,
            'payment_number' => $this->payment->payment_number,
            'booking_id' => $booking->id,
            'booking_number' => $booking->booking_number,
            'amount' => $this->payment->amount,
            'payment_method' => $this->payment->payment_method,
            'completed_at' => $this->payment->completed_at?->toISOString(),
            'is_customer_notification' => $isCustomer,
            'title' => $isCustomer ? 'تم تأكيد الدفع' : 'دفعة جديدة',
            'message' => $isCustomer 
                ? 'تم تأكيد دفعة بمبلغ ' . number_format($this->payment->amount) . ' ريال'
                : 'تم استلام دفعة بمبلغ ' . number_format($this->payment->amount) . ' ريال',
            'action_url' => $isCustomer 
                ? '/customer/dashboard/bookings/' . $booking->id
                : '/merchant/dashboard/bookings/' . $booking->id,
        ];
    }

    /**
     * Get payment method in Arabic
     */
    private function getPaymentMethodArabic(): string
    {
        $methods = [
            'card' => 'بطاقة ائتمانية',
            'bank_transfer' => 'تحويل بنكي',
            'mada' => 'مدى',
            'apple_pay' => 'آبل باي',
            'stc_pay' => 'STC Pay',
            'cash' => 'نقداً',
        ];

        return $methods[$this->payment->payment_method] ?? $this->payment->payment_method;
    }
}
