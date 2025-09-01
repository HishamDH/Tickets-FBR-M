<div class="relative" x-data="{ open: @entangle('showDropdown') }">
    <!-- Notification Bell Button -->
    <button @click="$wire.toggleDropdown()" 
            class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded-full transition-colors duration-200">
        
        <!-- Bell Icon -->
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        <!-- Unread Count Badge -->
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full animate-pulse">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Notification Dropdown -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-1 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-1 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         @click.away="open = false"
         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border border-gray-200 z-50 max-h-96 overflow-hidden">

        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                
                <div class="flex items-center space-x-2">
                    @if($unreadCount > 0)
                        <button wire:click="markAllAsRead" 
                                class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                            Mark all read
                        </button>
                    @endif
                    
                    <button wire:click="viewAllNotifications"
                            class="text-sm text-gray-600 hover:text-gray-800">
                        View all
                    </button>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-80 overflow-y-auto">
            @forelse($notifications as $notification)
                <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200 {{ !$notification['is_read'] ? 'bg-blue-50' : '' }}">
                    <div class="flex items-start space-x-3">
                        <!-- Notification Icon -->
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-8 h-8 rounded-full bg-{{ $notification['color'] }}-100 flex items-center justify-center">
                                @svg($notification['icon'], 'w-4 h-4 text-' . $notification['color'] . '-600')
                            </div>
                        </div>

                        <!-- Notification Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 {{ !$notification['is_read'] ? 'font-semibold' : '' }}">
                                        {{ $notification['title'] }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                        {{ $notification['message'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-2">
                                        {{ $notification['time_ago'] }}
                                    </p>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-1 ml-2">
                                    @if(!$notification['is_read'])
                                        <button wire:click="markAsRead({{ $notification['id'] }})"
                                                title="Mark as read"
                                                class="p-1 text-gray-400 hover:text-gray-600 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    @endif
                                    
                                    <button wire:click="deleteNotification({{ $notification['id'] }})"
                                            title="Delete notification"
                                            class="p-1 text-gray-400 hover:text-red-600 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Action Button -->
                            @if($notification['url'])
                                <div class="mt-2">
                                    <a href="{{ $notification['url'] }}" 
                                       wire:click="markAsRead({{ $notification['id'] }})"
                                       class="inline-flex items-center text-xs text-primary-600 hover:text-primary-800 font-medium">
                                        View details
                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Unread Indicator -->
                        @if(!$notification['is_read'])
                            <div class="w-2 h-2 bg-primary-600 rounded-full mt-2"></div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p class="text-gray-500 text-sm">No notifications yet</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if(count($notifications) > 0)
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                <button wire:click="viewAllNotifications"
                        class="w-full text-center text-sm text-primary-600 hover:text-primary-800 font-medium py-1">
                    View all notifications
                </button>
            </div>
        @endif
    </div>

    <!-- Loading State -->
    <div wire:loading wire:target="markAsRead,markAllAsRead,deleteNotification" 
         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
        <div class="px-4 py-8 text-center">
            <svg class="animate-spin w-6 h-6 mx-auto text-primary-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-500 text-sm mt-2">Processing...</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Request notification permission
    document.addEventListener('DOMContentLoaded', function() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    });

    // Handle browser notifications
    window.addEventListener('show-browser-notification', function(event) {
        if ('Notification' in window && Notification.permission === 'granted') {
            const notification = new Notification(event.detail.title, {
                body: event.detail.message,
                icon: event.detail.icon,
                badge: event.detail.icon,
                tag: 'tickets-notification',
                requireInteraction: false,
                silent: false
            });

            // Auto close after 5 seconds
            setTimeout(() => {
                notification.close();
            }, 5000);

            // Handle click
            notification.onclick = function() {
                window.focus();
                notification.close();
            };
        }
    });

    // Listen for real-time notifications via Laravel Echo (if available)
    if (typeof Echo !== 'undefined' && window.Laravel && window.Laravel.userId) {
        Echo.private(`notifications.${window.Laravel.userId}`)
            .listen('NotificationSent', (e) => {
                Livewire.dispatch('notification-received');
            });
    }
</script>
@endpush
