<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->get('type', 'all'); // all, unread, read
        $priority = $request->get('priority'); // normal, high, urgent
        $category = $request->get('category'); // booking, payment, system
        $dateRange = $request->get('date_range', '30'); // 7, 30, 90 days

        // Build query
        $query = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($type === 'unread') {
            $query->unread();
        } elseif ($type === 'read') {
            $query->read();
        }

        if ($priority) {
            $query->byPriority($priority);
        }

        if ($category) {
            $query->where('type', 'like', $category.'%');
        }

        if ($dateRange !== 'all') {
            $query->where('created_at', '>=', Carbon::now()->subDays((int) $dateRange));
        }

        $notifications = $query->paginate(20);

        // Get counts for filters
        $counts = $this->getNotificationCounts($user);

        // Get analytics data
        $analytics = $this->getNotificationAnalytics($user);

        if ($request->wantsJson()) {
            return response()->json([
                'notifications' => $notifications,
                'counts' => $counts,
                'analytics' => $analytics,
            ]);
        }

        return view('notifications.index', compact('notifications', 'counts', 'type', 'priority', 'category', 'analytics'));
    }

    public function show(Notification $notification)
    {
        // Check if user owns this notification
        if ($notification->notifiable_id !== Auth::id() ||
            $notification->notifiable_type !== get_class(Auth::user())) {
            abort(403);
        }

        // Mark as read if not already read
        if (! $notification->isRead()) {
            $notification->markAsRead();
        }

        return view('notifications.show', compact('notification'));
    }

    public function markAsRead(Notification $notification)
    {
        // Check ownership
        if ($notification->notifiable_id !== Auth::id() ||
            $notification->notifiable_type !== get_class(Auth::user())) {
            abort(403);
        }

        $notification->markAsRead();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'تم تمييز الإشعار كمقروء');
    }

    public function markAllAsRead()
    {
        $user = Auth::user();

        Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->unread()
            ->update(['read_at' => now()]);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'تم تمييز جميع الإشعارات كمقروءة');
    }

    public function destroy(Notification $notification)
    {
        // Check ownership
        if ($notification->notifiable_id !== Auth::id() ||
            $notification->notifiable_type !== get_class(Auth::user())) {
            abort(403);
        }

        $notification->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'تم حذف الإشعار');
    }

    public function getUnreadCount()
    {
        $user = Auth::user();

        $count = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    public function getRecent()
    {
        $user = Auth::user();

        $notifications = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json(['notifications' => $notifications]);
    }

    // Admin Methods
    public function adminIndex(Request $request)
    {
        $this->middleware('role:admin');

        $type = $request->get('type');
        $priority = $request->get('priority');
        $notifiable_type = $request->get('notifiable_type');

        $query = Notification::with('notifiable')
            ->orderBy('created_at', 'desc');

        if ($type) {
            $query->byType($type);
        }

        if ($priority) {
            $query->byPriority($priority);
        }

        if ($notifiable_type) {
            $query->where('notifiable_type', $notifiable_type);
        }

        $notifications = $query->paginate(50);

        $stats = [
            'total' => Notification::count(),
            'unread' => Notification::unread()->count(),
            'urgent' => Notification::byPriority('urgent')->count(),
            'today' => Notification::whereDate('created_at', today())->count(),
        ];

        return view('admin.notifications.index', compact('notifications', 'stats'));
    }

    public function sendBulkNotification(Request $request)
    {
        $this->middleware('role:admin');

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'recipient_type' => 'required|in:all,merchants,customers,partners',
            'type' => 'required|string',
        ]);

        $recipients = $this->getRecipientsByType($request->recipient_type);

        foreach ($recipients as $recipient) {
            Notification::createForUser(
                $recipient,
                $request->type,
                $request->title,
                $request->message,
                [],
                $request->priority
            );
        }

        return redirect()->back()->with('success', 'تم إرسال الإشعارات بنجاح');
    }

    private function getRecipientsByType($type)
    {
        switch ($type) {
            case 'merchants':
                return \App\Models\User::whereHas('merchant')->get();
            case 'customers':
                return \App\Models\User::whereHas('customer')->get();
            case 'partners':
                return \App\Models\User::whereHas('partner')->get();
            default:
                return \App\Models\User::all();
        }
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:mark_read,delete',
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'exists:notifications,id',
        ]);

        $user = Auth::user();
        $action = $request->action;
        $notificationIds = $request->notification_ids;

        // Ensure user owns all notifications
        $notifications = Notification::whereIn('id', $notificationIds)
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id);

        $count = 0;
        if ($action === 'mark_read') {
            $count = $notifications->unread()->update(['read_at' => now()]);
            $message = "تم وضع علامة على {$count} إشعار كمقروء";
        } elseif ($action === 'delete') {
            $count = $notifications->count();
            $notifications->delete();
            $message = "تم حذف {$count} إشعار";
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => $message,
                'unread_count' => $this->getUnreadCountForUser(),
            ]);
        }

        return back()->with('success', $message);
    }

    public function getRealtimeNotifications()
    {
        $user = Auth::user();

        $notifications = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count(),
            'total_unread' => $this->getUnreadCountForUser(),
        ]);
    }

    public function updatePreferences(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'marketing_notifications' => 'boolean',
            'booking_notifications' => 'boolean',
            'payment_notifications' => 'boolean',
            'system_notifications' => 'boolean',
        ]);

        $user = Auth::user();
        $preferences = $request->only([
            'email_notifications',
            'sms_notifications',
            'push_notifications',
            'marketing_notifications',
            'booking_notifications',
            'payment_notifications',
            'system_notifications',
        ]);

        $user->update(['notification_preferences' => $preferences]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'preferences' => $preferences,
            ]);
        }

        return back()->with('success', 'تم تحديث تفضيلات الإشعارات');
    }

    protected function getUnreadCountForUser()
    {
        $user = Auth::user();

        return Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->unread()
            ->count();
    }

    protected function getNotificationCounts($user)
    {
        $base = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id);

        return [
            'all' => (clone $base)->count(),
            'unread' => (clone $base)->unread()->count(),
            'read' => (clone $base)->read()->count(),
            'urgent' => (clone $base)->byPriority('urgent')->count(),
            'high' => (clone $base)->byPriority('high')->count(),
            'booking' => (clone $base)->where('type', 'like', 'booking%')->count(),
            'payment' => (clone $base)->where('type', 'like', 'payment%')->count(),
        ];
    }

    protected function getNotificationAnalytics($user)
    {
        $base = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id);

        // Last 30 days activity
        $dailyStats = (clone $base)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Notification types breakdown
        $typeStats = (clone $base)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get();

        // Read vs Unread percentage
        $readStats = [
            'read' => (clone $base)->read()->count(),
            'unread' => (clone $base)->unread()->count(),
        ];

        return [
            'daily_stats' => $dailyStats,
            'type_stats' => $typeStats,
            'read_stats' => $readStats,
            'total_notifications' => $readStats['read'] + $readStats['unread'],
            'read_percentage' => $readStats['read'] + $readStats['unread'] > 0
                ? round(($readStats['read'] / ($readStats['read'] + $readStats['unread'])) * 100, 1)
                : 0,
        ];
    }

    // Notification Templates for different events
    public static function notifyBookingConfirmed($booking)
    {
        // Customer notification
        if ($booking->customer) {
            Notification::createForUser(
                $booking->customer,
                'booking_confirmed',
                'تم تأكيد حجزك',
                "تم تأكيد حجزك لخدمة {$booking->service->name} في تاريخ {$booking->booking_date}",
                ['booking_id' => $booking->id],
                'normal'
            );
        }

        // Merchant notification
        if ($booking->service->merchant) {
            Notification::createForUser(
                $booking->service->merchant->user,
                'new_booking',
                'حجز جديد',
                "لديك حجز جديد لخدمة {$booking->service->name} من العميل {$booking->customer_name}",
                ['booking_id' => $booking->id],
                'high'
            );
        }
    }

    public static function notifyPaymentReceived($booking)
    {
        if ($booking->service->merchant) {
            Notification::createForUser(
                $booking->service->merchant->user,
                'payment_received',
                'تم استلام الدفع',
                "تم استلام دفعة بقيمة {$booking->total_amount} ريال للحجز #{$booking->id}",
                [
                    'booking_id' => $booking->id,
                    'amount' => $booking->total_amount,
                ],
                'normal'
            );
        }
    }

    public static function notifyBookingCancelled($booking)
    {
        // Merchant notification
        if ($booking->service->merchant) {
            Notification::createForUser(
                $booking->service->merchant->user,
                'booking_cancelled',
                'تم إلغاء حجز',
                "تم إلغاء الحجز #{$booking->id} لخدمة {$booking->service->name}",
                ['booking_id' => $booking->id],
                'normal'
            );
        }

        // Customer confirmation
        if ($booking->customer) {
            Notification::createForUser(
                $booking->customer,
                'booking_cancelled',
                'تم إلغاء حجزك',
                "تم إلغاء حجزك لخدمة {$booking->service->name} بنجاح",
                ['booking_id' => $booking->id],
                'normal'
            );
        }
    }
}
