<?php

namespace App\Livewire;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationBell extends Component
{
    public $unreadCount = 0;

    public $notifications = [];

    public $showDropdown = false;

    public $maxNotifications = 5;

    public function mount()
    {
        $this->loadNotifications();
        $this->updateUnreadCount();
    }

    public function loadNotifications()
    {
        if (! Auth::check()) {
            return;
        }

        $this->notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit($this->maxNotifications)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type ?? 'info',
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at,
                    'time_ago' => $notification->created_at->diffForHumans(),
                    'icon' => $this->getNotificationIcon($notification->type),
                    'color' => $this->getNotificationColor($notification->type),
                    'url' => $notification->url,
                ];
            });
    }

    public function updateUnreadCount()
    {
        if (! Auth::check()) {
            $this->unreadCount = 0;

            return;
        }

        $this->unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
    }

    public function toggleDropdown()
    {
        $this->showDropdown = ! $this->showDropdown;

        if ($this->showDropdown) {
            $this->loadNotifications();
        }
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', Auth::id())
            ->first();

        if ($notification && ! $notification->is_read) {
            $notification->update(['is_read' => true]);
            $this->updateUnreadCount();
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $this->updateUnreadCount();
        $this->loadNotifications();

        $this->dispatch('notifications-marked-read');
    }

    public function deleteNotification($notificationId)
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', Auth::id())
            ->first();

        if ($notification) {
            $notification->delete();
            $this->updateUnreadCount();
            $this->loadNotifications();
        }
    }

    public function viewAllNotifications()
    {
        return redirect()->route('notifications.index');
    }

    #[On('echo:notifications.{userId},NotificationSent')]
    public function handleNewNotification($data)
    {
        if (Auth::check() && Auth::id() == $data['userId']) {
            $this->updateUnreadCount();
            $this->loadNotifications();

            // Show browser notification if supported
            $this->dispatch('show-browser-notification', [
                'title' => $data['title'] ?? 'New Notification',
                'message' => $data['message'] ?? 'You have a new notification',
                'icon' => asset('images/notification-icon.png'),
            ]);
        }
    }

    #[On('notification-received')]
    public function handleNotificationReceived()
    {
        $this->updateUnreadCount();
        $this->loadNotifications();
    }

    private function getNotificationIcon($type)
    {
        return match ($type) {
            'booking' => 'heroicon-o-calendar-days',
            'payment' => 'heroicon-o-currency-dollar',
            'message' => 'heroicon-o-chat-bubble-left',
            'system' => 'heroicon-o-cog-6-tooth',
            'promotion' => 'heroicon-o-megaphone',
            'reminder' => 'heroicon-o-bell',
            'success' => 'heroicon-o-check-circle',
            'warning' => 'heroicon-o-exclamation-triangle',
            'error' => 'heroicon-o-x-circle',
            default => 'heroicon-o-information-circle',
        };
    }

    private function getNotificationColor($type)
    {
        return match ($type) {
            'booking' => 'blue',
            'payment' => 'green',
            'message' => 'indigo',
            'system' => 'gray',
            'promotion' => 'purple',
            'reminder' => 'yellow',
            'success' => 'green',
            'warning' => 'yellow',
            'error' => 'red',
            default => 'blue',
        };
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
