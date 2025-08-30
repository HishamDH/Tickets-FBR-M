<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Notifications\BookingCreated;
use App\Notifications\BookingConfirmed;
use App\Notifications\BookingCancelled;
use App\Notifications\PaymentCompleted;
use App\Notifications\RefundProcessed;

class NotificationService
{
    /**
     * إرسال إشعارات الحجز الجديد
     */
    public function sendBookingNotifications(Booking $booking): void
    {
        try {
            // إشعار العميل
            if ($booking->customer) {
                $booking->customer->notify(new BookingCreated($booking));
            } elseif ($booking->customer_email) {
                // إرسال إيميل للعملاء غير المسجلين
                $this->sendEmailToGuest($booking->customer_email, 'إشعار حجز جديد', $booking);
            }

            // إشعار التاجر
            if ($booking->merchant && $booking->merchant->user) {
                $booking->merchant->user->notify(new BookingCreated($booking));
            }

            // إشعار مدير الحساب (إن وجد)
            if ($booking->merchant?->accountManager) {
                $booking->merchant->accountManager->notify(new BookingCreated($booking));
            }

            Log::info('Booking notifications sent successfully', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send booking notifications', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * إرسال إشعار تأكيد الحجز
     */
    public function sendBookingConfirmation(Booking $booking): void
    {
        try {
            // إشعار العميل
            if ($booking->customer) {
                $booking->customer->notify(new BookingConfirmed($booking));
            } elseif ($booking->customer_email) {
                $this->sendEmailToGuest($booking->customer_email, 'تأكيد الحجز', $booking);
            }

            Log::info('Booking confirmation sent', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * إرسال إشعار إلغاء الحجز
     */
    public function sendBookingCancellation(Booking $booking): void
    {
        try {
            // إشعار العميل
            if ($booking->customer) {
                $booking->customer->notify(new BookingCancelled($booking));
            } elseif ($booking->customer_email) {
                $this->sendEmailToGuest($booking->customer_email, 'إلغاء الحجز', $booking);
            }

            // إشعار التاجر
            if ($booking->merchant && $booking->merchant->user) {
                $booking->merchant->user->notify(new BookingCancelled($booking));
            }

            Log::info('Booking cancellation notifications sent', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send cancellation notifications', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * إرسال إشعار تأكيد الدفع
     */
    public function sendPaymentConfirmation(Payment $payment): void
    {
        try {
            $booking = $payment->booking;

            // إشعار العميل
            if ($booking->customer) {
                $booking->customer->notify(new PaymentCompleted($payment));
            } elseif ($booking->customer_email) {
                $this->sendEmailToGuest($booking->customer_email, 'تأكيد الدفع', $booking, $payment);
            }

            // إشعار التاجر
            if ($booking->merchant && $booking->merchant->user) {
                $booking->merchant->user->notify(new PaymentCompleted($payment));
            }

            Log::info('Payment confirmation sent', [
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
                'amount' => $payment->amount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmation', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * إرسال إشعار تأكيد الاسترداد
     */
    public function sendRefundConfirmation(Payment $refundPayment): void
    {
        try {
            $booking = $refundPayment->booking;

            // إشعار العميل
            if ($booking->customer) {
                $booking->customer->notify(new RefundProcessed($refundPayment));
            } elseif ($booking->customer_email) {
                $this->sendEmailToGuest($booking->customer_email, 'تأكيد الاسترداد', $booking, $refundPayment);
            }

            Log::info('Refund confirmation sent', [
                'payment_id' => $refundPayment->id,
                'booking_id' => $booking->id,
                'refund_amount' => abs($refundPayment->amount)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send refund confirmation', [
                'payment_id' => $refundPayment->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * إرسال تذكير بالحجز
     */
    public function sendBookingReminder(Booking $booking, string $reminderType = '24h'): void
    {
        try {
            $reminderTitles = [
                '24h' => 'تذكير: حجزك غداً',
                '2h' => 'تذكير: حجزك خلال ساعتين',
                '30m' => 'تذكير: حجزك خلال نصف ساعة',
            ];

            $title = $reminderTitles[$reminderType] ?? 'تذكير بالحجز';

            // إشعار العميل
            if ($booking->customer) {
                $booking->customer->notify(new \App\Notifications\BookingReminder($booking, $reminderType));
            } elseif ($booking->customer_email) {
                $this->sendEmailToGuest($booking->customer_email, $title, $booking);
            }

            Log::info('Booking reminder sent', [
                'booking_id' => $booking->id,
                'reminder_type' => $reminderType
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send booking reminder', [
                'booking_id' => $booking->id,
                'reminder_type' => $reminderType,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * إرسال إشعار للمدراء والموظفين
     */
    public function sendAdminNotification(string $title, string $message, array $data = []): void
    {
        try {
            $admins = User::where('user_type', 'admin')->get();

            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\AdminNotification($title, $message, $data));
            }

            Log::info('Admin notification sent', [
                'title' => $title,
                'recipients_count' => $admins->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send admin notification', [
                'title' => $title,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * إرسال إشعار جماعي للمستخدمين
     */
    public function sendBulkNotification(array $userIds, string $title, string $message, array $data = []): int
    {
        try {
            $users = User::whereIn('id', $userIds)->get();
            $sentCount = 0;

            foreach ($users as $user) {
                try {
                    $user->notify(new \App\Notifications\BulkNotification($title, $message, $data));
                    $sentCount++;
                } catch (\Exception $e) {
                    Log::warning('Failed to send notification to user', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Bulk notification completed', [
                'title' => $title,
                'total_users' => $users->count(),
                'successful_sends' => $sentCount
            ]);

            return $sentCount;

        } catch (\Exception $e) {
            Log::error('Failed to send bulk notification', [
                'title' => $title,
                'user_count' => count($userIds),
                'error' => $e->getMessage()
            ]);

            return 0;
        }
    }

    /**
     * إرسال إشعار SMS (يتطلب تكامل مع موفر SMS)
     */
    public function sendSMS(string $phoneNumber, string $message): bool
    {
        try {
            // هنا يمكن إضافة التكامل مع موفر SMS مثل Twilio أو موفر محلي
            // حالياً سنسجل الرسالة في اللوغ
            
            Log::info('SMS notification sent', [
                'phone' => $phoneNumber,
                'message' => $message
            ]);

            // محاكاة نجاح الإرسال
            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send SMS', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * إرسال إيميل للزوار غير المسجلين
     */
    protected function sendEmailToGuest(string $email, string $subject, Booking $booking, Payment $payment = null): void
    {
        try {
            // هنا يمكن إنشاء قالب إيميل مخصص أو استخدام قالب بسيط
            $data = [
                'booking' => $booking,
                'payment' => $payment,
                'subject' => $subject
            ];

            // استخدام Mail facade لإرسال الإيميل
            Mail::send('emails.guest-notification', $data, function ($message) use ($email, $subject) {
                $message->to($email)
                        ->subject($subject);
            });

            Log::info('Guest email sent', [
                'email' => $email,
                'subject' => $subject,
                'booking_id' => $booking->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send guest email', [
                'email' => $email,
                'subject' => $subject,
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * إرسال تقرير دوري للتجار
     */
    public function sendMerchantReport(User $merchant, array $reportData, string $period = 'weekly'): void
    {
        try {
            $merchant->notify(new \App\Notifications\MerchantReport($reportData, $period));

            Log::info('Merchant report sent', [
                'merchant_id' => $merchant->id,
                'period' => $period
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send merchant report', [
                'merchant_id' => $merchant->id,
                'period' => $period,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * جدولة الإشعارات التلقائية
     */
    public function scheduleAutomaticNotifications(Booking $booking): void
    {
        try {
            $bookingDate = $booking->booking_date;
            $bookingTime = $booking->booking_time;

            if (!$bookingDate) {
                return;
            }

            // تذكير قبل 24 ساعة
            $reminder24h = $bookingDate->copy()->subDay();
            if ($reminder24h > now()) {
                // هنا يمكن استخدام Laravel Queues لجدولة الإشعارات
                // dispatch(new SendBookingReminderJob($booking, '24h'))->delay($reminder24h);
            }

            // تذكير قبل ساعتين
            if ($bookingTime) {
                $reminder2h = $bookingDate->copy()->setTimeFromTimeString($bookingTime)->subHours(2);
                if ($reminder2h > now()) {
                    // dispatch(new SendBookingReminderJob($booking, '2h'))->delay($reminder2h);
                }
            }

            Log::info('Automatic notifications scheduled', [
                'booking_id' => $booking->id,
                'booking_date' => $bookingDate->toDateString()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to schedule automatic notifications', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * إلغاء الإشعارات المجدولة
     */
    public function cancelScheduledNotifications(Booking $booking): void
    {
        try {
            // هنا يمكن إلغاء المهام المجدولة من Queue
            // يتطلب تتبع job IDs أو استخدام tags
            
            Log::info('Scheduled notifications cancelled', [
                'booking_id' => $booking->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cancel scheduled notifications', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * الحصول على إحصائيات الإشعارات
     */
    public function getNotificationStatistics(User $user, int $days = 30): array
    {
        try {
            // يمكن استخدام جدول notifications لحساب الإحصائيات
            $notifications = $user->notifications()
                ->where('created_at', '>=', now()->subDays($days))
                ->get();

            $stats = [
                'total' => $notifications->count(),
                'read' => $notifications->whereNotNull('read_at')->count(),
                'unread' => $notifications->whereNull('read_at')->count(),
                'types' => $notifications->groupBy('type')->map->count(),
                'daily_average' => round($notifications->count() / $days, 2),
            ];

            return $stats;

        } catch (\Exception $e) {
            Log::error('Failed to get notification statistics', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return [
                'total' => 0,
                'read' => 0,
                'unread' => 0,
                'types' => [],
                'daily_average' => 0,
            ];
        }
    }
}
