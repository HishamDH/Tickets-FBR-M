<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ReviewRequestNotification extends Notification
{
    use Queueable;

    /**
     * The booking instance.
     *
     * @var \App\Models\Booking
     */
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
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $service = $this->booking->bookable;
        $reviewUrl = route('reviews.create', ['service' => $service->id]);
        
        return (new MailMessage)
            ->subject('شاركنا رأيك في تجربتك مع ' . $service->name)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('نأمل أن تكون قد استمتعت بتجربتك مع ' . $service->name)
            ->line('نود الاستماع إلى رأيك وتقييمك للخدمة المقدمة.')
            ->action('إضافة تقييم', $reviewUrl)
            ->line(new HtmlString('تقييمك سيساعدنا على تحسين خدماتنا وسيساعد عملاء آخرين على اتخاذ قرار أفضل.'))
            ->salutation('شكراً لثقتك بنا');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $service = $this->booking->bookable;
        
        return [
            'title' => 'شاركنا رأيك',
            'message' => 'نود معرفة رأيك في تجربتك مع ' . $service->name,
            'booking_id' => $this->booking->id,
            'service_id' => $service->id,
            'action_url' => route('reviews.create', ['service' => $service->id]),
            'action_text' => 'إضافة تقييم',
            'icon' => 'star',
            'color' => 'yellow',
        ];
    }
}
