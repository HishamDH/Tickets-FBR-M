<!-- Notifications Dropdown Component -->
<div class="relative" x-data="{ open: false, notifications: [], unreadCount: 0 }" x-init="loadNotifications()">
    <!-- Notification Bell Button -->
    <button @click="open = !open" 
            class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg transition-colors duration-200">
        <!-- Bell Icon -->
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        <!-- Unread Count Badge -->
        <span x-show="unreadCount > 0" 
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full min-w-[20px] h-5 flex items-center justify-center px-1 font-bold">
        </span>
    </button>

    <!-- Dropdown Panel -->
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-96 bg-white rounded-2xl shadow-2xl border border-gray-200 z-50 max-h-[32rem] overflow-hidden"
         style="display: none;">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">الإشعارات</h3>
                <div class="flex items-center space-x-2 space-x-reverse">
                    <button @click="markAllAsRead()" 
                            class="text-blue-100 hover:text-white text-sm font-medium transition-colors duration-200">
                        وضع علامة على الكل
                    </button>
                    <a href="{{ route('notifications.index') }}" 
                       class="text-blue-100 hover:text-white text-sm font-medium transition-colors duration-200">
                        عرض الكل
                    </a>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-80 overflow-y-auto">
            <template x-if="notifications.length === 0">
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl">📭</span>
                    </div>
                    <p class="text-gray-500 text-sm">لا توجد إشعارات جديدة</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <div class="border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200"
                     :class="{'bg-blue-50': !notification.read_at}">
                    <div class="p-4">
                        <div class="flex items-start space-x-3 space-x-reverse">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                     :class="{
                                         'bg-blue-100': notification.type.includes('booking'),
                                         'bg-green-100': notification.type.includes('payment'),
                                         'bg-purple-100': !notification.type.includes('booking') && !notification.type.includes('payment')
                                     }">
                                    <span x-text="notification.type.includes('booking') ? '📅' : 
                                                  notification.type.includes('payment') ? '💰' : '🔔'" 
                                          class="text-lg"></span>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="text-sm font-semibold text-gray-900 truncate" x-text="notification.title"></h4>
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <!-- Priority Badge -->
                                        <template x-if="notification.priority === 'urgent'">
                                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded-lg text-xs font-medium">
                                                عاجل
                                            </span>
                                        </template>
                                        <template x-if="notification.priority === 'high'">
                                            <span class="bg-orange-100 text-orange-700 px-2 py-1 rounded-lg text-xs font-medium">
                                                مهم
                                            </span>
                                        </template>
                                        <!-- Unread Indicator -->
                                        <template x-if="!notification.read_at">
                                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                        </template>
                                    </div>
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-2 line-clamp-2" x-text="notification.message"></p>
                                
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500" x-text="formatTime(notification.created_at)"></span>
                                    <div class="flex items-center space-x-1 space-x-reverse">
                                        <template x-if="!notification.read_at">
                                            <button @click.stop="markAsRead(notification.id)" 
                                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                                وضع علامة
                                            </button>
                                        </template>
                                        <a :href="`/notifications/${notification.id}`" 
                                           class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                            عرض
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-3">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500" x-text="`${unreadCount} إشعار غير مقروء`"></span>
                <a href="{{ route('notifications.preferences') }}" 
                   class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                   ⚙️ الإعدادات
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function loadNotifications() {
    fetch('/api/notifications/realtime')
        .then(response => response.json())
        .then(data => {
            this.notifications = data.notifications;
            this.unreadCount = data.total_unread;
        })
        .catch(error => console.error('Error loading notifications:', error));
}

function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the notification as read in the list
            const notification = this.notifications.find(n => n.id === notificationId);
            if (notification) {
                notification.read_at = new Date().toISOString();
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            }
        }
    })
    .catch(error => console.error('Error marking notification as read:', error));
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mark all notifications as read
            this.notifications.forEach(notification => {
                notification.read_at = new Date().toISOString();
            });
            this.unreadCount = 0;
        }
    })
    .catch(error => console.error('Error marking all notifications as read:', error));
}

function formatTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) {
        return 'الآن';
    } else if (diffInSeconds < 3600) {
        const minutes = Math.floor(diffInSeconds / 60);
        return `${minutes} دقيقة`;
    } else if (diffInSeconds < 86400) {
        const hours = Math.floor(diffInSeconds / 3600);
        return `${hours} ساعة`;
    } else {
        const days = Math.floor(diffInSeconds / 86400);
        return `${days} يوم`;
    }
}

// Auto refresh notifications every 30 seconds
setInterval(() => {
    loadNotifications();
}, 30000);
</script>
