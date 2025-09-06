<div class="relative" x-data="{ open: @entangle('showDropdown'), bellShake: false }">
    <!-- Enhanced Notification Bell Button -->
    <button @click="$wire.toggleDropdown(); bellShake = true; setTimeout(() => bellShake = false, 500)" 
            class="relative p-3 text-gray-600 hover:text-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 rounded-2xl transition-all duration-300 transform hover:scale-110 group"
            :class="{ 'animate-bounce': bellShake }">
        
        <!-- Enhanced Bell Icon with Gradient -->
        <div class="relative">
            <svg class="w-7 h-7 transition-transform duration-300 group-hover:rotate-12" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            
            <!-- Bell Ring Animation Lines -->
            <div class="absolute -top-1 -right-1 w-3 h-3 {{ $unreadCount > 0 ? 'animate-ping' : 'hidden' }}">
                <div class="absolute inset-0 w-full h-full bg-orange-400 rounded-full opacity-75"></div>
            </div>
        </div>
        
        <!-- Enhanced Unread Count Badge -->
        @if($unreadCount > 0)
            <span class="absolute -top-2 -right-2 inline-flex items-center justify-center min-w-6 h-6 px-2 text-xs font-bold leading-none text-white bg-gradient-to-r from-red-500 to-red-600 rounded-full shadow-lg border-2 border-white animate-pulse transform scale-110">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                <!-- Pulsing Ring Effect -->
                <span class="absolute inset-0 rounded-full bg-red-400 animate-ping opacity-75"></span>
            </span>
        @endif
        
        <!-- Hover Glow Effect -->
        <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-orange-400 to-red-400 opacity-0 group-hover:opacity-20 transition-opacity duration-300 blur-xl"></div>
    </button>

    <!-- Enhanced Notification Dropdown -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95 translate-y-2"
         x-transition:enter-end="opacity-1 transform scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-1 transform scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 transform scale-95 translate-y-2"
         @click.away="open = false"
         class="absolute right-0 mt-4 w-96 bg-white rounded-3xl shadow-2xl border-2 border-orange-100 z-50 max-h-96 overflow-hidden backdrop-blur-lg bg-opacity-95">

        <!-- Enhanced Header with Gradient -->
        <div class="px-6 py-4 bg-gradient-to-r from-orange-500 to-red-500 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <span class="text-2xl">🔔</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">الإشعارات</h3>
                        <p class="text-sm text-orange-100">
                            {{ $unreadCount > 0 ? $unreadCount . ' جديد' : 'لا توجد إشعارات جديدة' }}
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2 space-x-reverse">
                    @if($unreadCount > 0)
                        <button wire:click="markAllAsRead" 
                                class="px-3 py-1 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm font-medium rounded-full transition-all duration-300 transform hover:scale-105">
                            قراءة الكل
                        </button>
                    @endif
                    
                    <button wire:click="viewAllNotifications"
                            class="px-3 py-1 bg-white bg-opacity-20 hover:bg-opacity-30 text-white text-sm rounded-full transition-all duration-300">
                        عرض الكل
                    </button>
                </div>
            </div>
            
            <!-- Decorative Wave -->
            <div class="absolute bottom-0 left-0 w-full">
                <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative block w-full h-4">
                    <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" 
                          fill="white" fill-opacity="0.1"/>
                </svg>
            </div>
        </div>

        <!-- Enhanced Notifications List -->
        <div class="max-h-80 overflow-y-auto custom-scrollbar">
            @forelse($notifications as $index => $notification)
                <div class="notification-item px-6 py-4 border-b border-gray-100 hover:bg-gradient-to-r hover:from-orange-50 hover:to-red-50 transition-all duration-300 transform hover:scale-102 {{ !$notification['is_read'] ? 'bg-gradient-to-r from-orange-25 to-red-25 border-l-4 border-orange-400' : '' }}"
                     style="animation: slideInNotification 0.5s ease-out {{ $index * 0.1 }}s both;">
                    
                    <div class="flex items-start space-x-4 space-x-reverse">
                        <!-- Enhanced Notification Icon -->
                        <div class="flex-shrink-0 mt-1 relative">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-{{ $notification['color'] }}-400 to-{{ $notification['color'] }}-600 flex items-center justify-center text-white shadow-lg transform rotate-3 hover:rotate-0 transition-transform duration-300">
                                @php
                                    $icons = [
                                        'info' => '💬',
                                        'success' => '✅',
                                        'warning' => '⚠️',
                                        'error' => '❌',
                                        'message' => '📩',
                                        'booking' => '🎫',
                                        'payment' => '💳',
                                        'system' => '⚙️'
                                    ];
                                    $emoji = $icons[$notification['type']] ?? '🔔';
                                @endphp
                                <span class="text-lg">{{ $emoji }}</span>
                            </div>
                            
                            <!-- Unread Pulse -->
                            @if(!$notification['is_read'])
                                <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full animate-ping"></div>
                                <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-600 rounded-full"></div>
                            @endif
                        </div>

                        <!-- Enhanced Notification Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-gray-900 {{ !$notification['is_read'] ? 'text-orange-800' : '' }} mb-1">
                                        {{ $notification['title'] }}
                                    </p>
                                    <p class="text-sm text-gray-600 mb-2 leading-relaxed">
                                        {{ $notification['message'] }}
                                    </p>
                                    
                                    <!-- Enhanced Time with Icon -->
                                    <div class="flex items-center space-x-2 space-x-reverse text-xs text-gray-500">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $notification['time_ago'] }}</span>
                                    </div>
                                </div>

                                <!-- Enhanced Actions -->
                                <div class="flex items-center space-x-1 space-x-reverse ml-3">
                                    @if(!$notification['is_read'])
                                        <button wire:click="markAsRead({{ $notification['id'] }})"
                                                title="تمييز كمقروء"
                                                class="p-2 text-gray-400 hover:text-green-600 rounded-full hover:bg-green-50 transition-all duration-300 transform hover:scale-110">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    @endif
                                    
                                    <button wire:click="deleteNotification({{ $notification['id'] }})"
                                            title="حذف الإشعار"
                                            class="p-2 text-gray-400 hover:text-red-600 rounded-full hover:bg-red-50 transition-all duration-300 transform hover:scale-110">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Enhanced Action Button -->
                            @if($notification['url'])
                                <div class="mt-3">
                                    <a href="{{ $notification['url'] }}" 
                                       wire:click="markAsRead({{ $notification['id'] }})"
                                       class="inline-flex items-center px-3 py-1 text-xs font-medium text-orange-600 bg-orange-50 hover:bg-orange-100 rounded-full transition-all duration-300 transform hover:scale-105 group">
                                        <span>عرض التفاصيل</span>
                                        <svg class="w-3 h-3 mr-1 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <div class="relative mb-6">
                        <div class="w-20 h-20 mx-auto bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center animate-float">
                            <span class="text-4xl">🔔</span>
                        </div>
                        <!-- Floating Decorative Elements -->
                        <div class="absolute -top-2 -right-2 w-4 h-4 bg-orange-300 rounded-full opacity-60 animate-bounce" style="animation-delay: 0.1s;"></div>
                        <div class="absolute -bottom-1 -left-3 w-3 h-3 bg-red-300 rounded-full opacity-40 animate-bounce" style="animation-delay: 0.3s;"></div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">لا توجد إشعارات</h3>
                    <p class="text-gray-500 text-sm">ستظهر الإشعارات الجديدة هنا</p>
                </div>
            @endforelse
        </div>

        <!-- Enhanced Footer -->
        @if(count($notifications) > 0)
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200">
                <button wire:click="viewAllNotifications"
                        class="w-full text-center text-sm text-orange-600 hover:text-orange-800 font-bold py-2 bg-white hover:bg-orange-50 rounded-2xl transition-all duration-300 transform hover:scale-105 shadow-sm">
                    عرض جميع الإشعارات
                </button>
            </div>
        @endif
    </div>

    <!-- Enhanced Loading State -->
    <div wire:loading wire:target="markAsRead,markAllAsRead,deleteNotification" 
         class="absolute right-0 mt-4 w-96 bg-white rounded-3xl shadow-2xl border-2 border-orange-100 z-50 backdrop-blur-lg bg-opacity-95">
        <div class="px-6 py-12 text-center">
            <div class="relative mb-4">
                <div class="w-16 h-16 mx-auto border-4 border-orange-200 rounded-full"></div>
                <div class="w-16 h-16 mx-auto border-4 border-orange-500 rounded-full border-t-transparent animate-spin absolute top-0 left-1/2 transform -translate-x-1/2"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-orange-500 text-2xl animate-pulse">⚡</span>
                </div>
            </div>
            <p class="text-gray-600 font-medium">جاري المعالجة...</p>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/creative-design-system.css') }}">
<style>
    /* Enhanced Notification Styles */
    .notification-item {
        position: relative;
        overflow: hidden;
    }
    
    .notification-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(249, 115, 22, 0.1), transparent);
        transition: left 0.6s ease;
    }
    
    .notification-item:hover::before {
        left: 100%;
    }
    
    .custom-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #f97316 #f3f4f6;
    }
    
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f3f4f6;
        border-radius: 3px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #f97316, #ea580c);
        border-radius: 3px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, #ea580c, #dc2626);
    }
    
    @keyframes slideInNotification {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes bellRing {
        0% { transform: rotate(0deg); }
        10% { transform: rotate(30deg); }
        20% { transform: rotate(-28deg); }
        30% { transform: rotate(26deg); }
        40% { transform: rotate(-24deg); }
        50% { transform: rotate(22deg); }
        60% { transform: rotate(-20deg); }
        70% { transform: rotate(18deg); }
        80% { transform: rotate(-16deg); }
        90% { transform: rotate(14deg); }
        100% { transform: rotate(0deg); }
    }
    
    .bell-ring {
        animation: bellRing 1s ease-in-out;
    }
    
    .scale-102 {
        transform: scale(1.02);
    }
    
    .orange-25 {
        background-color: #fff7ed;
    }
    
    .red-25 {
        background-color: #fef2f2;
    }
    
    /* Sound Wave Animation */
    @keyframes soundWave {
        0% { transform: scale(1); opacity: 1; }
        100% { transform: scale(1.5); opacity: 0; }
    }
    
    .sound-wave {
        animation: soundWave 1s ease-out infinite;
    }
    
    /* Notification Glow */
    .notification-glow {
        box-shadow: 0 0 20px rgba(249, 115, 22, 0.3);
    }
    
    /* Enhanced hover effects */
    .notification-action-btn {
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .notification-action-btn:hover {
        transform: translateY(-2px) scale(1.1);
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/creative-interactions.js') }}"></script>
<script>
    // Enhanced notification functionality
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🔔 Enhanced Notification System - Loading...');
        
        // Request notification permission with enhanced UI
        if ('Notification' in window && Notification.permission === 'default') {
            showNotificationPermissionRequest();
        }
        
        // Initialize notification sound
        initializeNotificationSounds();
        
        // Initialize real-time updates
        initializeRealTimeNotifications();
        
        console.log('🔔 Enhanced Notification System - Ready! ✨');
    });

    function showNotificationPermissionRequest() {
        const permissionModal = document.createElement('div');
        permissionModal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        permissionModal.innerHTML = `
            <div class="bg-white rounded-3xl p-8 max-w-md mx-4 text-center shadow-2xl transform scale-95 animate-pulse">
                <div class="w-20 h-20 mx-auto bg-gradient-to-br from-orange-400 to-red-500 rounded-full flex items-center justify-center mb-6">
                    <span class="text-4xl">🔔</span>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-4">تفعيل الإشعارات</h3>
                <p class="text-gray-600 mb-6">اسمح بالإشعارات لتلقي التحديثات المهمة فوراً</p>
                <div class="flex space-x-3 space-x-reverse">
                    <button onclick="requestNotificationPermission()" 
                            class="flex-1 bg-gradient-to-r from-orange-500 to-red-500 text-white py-3 px-6 rounded-2xl font-medium hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                        تفعيل الإشعارات
                    </button>
                    <button onclick="closePermissionModal()" 
                            class="flex-1 bg-gray-200 text-gray-700 py-3 px-6 rounded-2xl font-medium hover:bg-gray-300 transition-all duration-300">
                        لاحقاً
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(permissionModal);
        
        setTimeout(() => {
            permissionModal.firstElementChild.style.transform = 'scale(1)';
            permissionModal.firstElementChild.style.animation = 'none';
        }, 100);
        
        window.closePermissionModal = function() {
            permissionModal.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => permissionModal.remove(), 300);
        };
        
        window.requestNotificationPermission = async function() {
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                showNotification('تم تفعيل الإشعارات بنجاح! 🔔', 'success');
            }
            closePermissionModal();
        };
    }

    function initializeNotificationSounds() {
        // Create notification sounds
        window.notificationSounds = {
            success: new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmETCkGh4Oi9fjELI3zM8dmZUArHntHh8bgbC0l+2Oi0dDWOqMvs5atfpBMNTbHi7bJcGAhHjLHTzmQaC0+q5O2mbRyOp8rr56pxpXKjZKhfqJRfn7PH3MheFglO3PDJdydHgbrJ3b5gEQZIgMjC4rJfFwpNqOnrqWKe4NnZ2aV9KHal6L4iB0CIgNL3qmeP1Oy4cC1vHWx1w1oPK0Wl3p9lKAaF'); // Success sound
        };
    }

    function initializeRealTimeNotifications() {
        // Listen for new notifications
        window.addEventListener('notification-received', function(event) {
            const bellButton = document.querySelector('.relative button');
            if (bellButton) {
                // Add ring animation
                bellButton.classList.add('bell-ring');
                setTimeout(() => {
                    bellButton.classList.remove('bell-ring');
                }, 1000);
                
                // Add sound wave effect
                createSoundWaveEffect(bellButton);
                
                // Play notification sound
                if (window.notificationSounds && window.notificationSounds.success) {
                    window.notificationSounds.success.play().catch(() => {
                        // Sound play failed, ignore
                    });
                }
                
                // Show browser notification
                if (event.detail) {
                    showBrowserNotification(event.detail);
                }
            }
        });
    }

    function createSoundWaveEffect(element) {
        for (let i = 0; i < 3; i++) {
            setTimeout(() => {
                const wave = document.createElement('div');
                wave.className = 'absolute inset-0 border-2 border-orange-400 rounded-full sound-wave pointer-events-none';
                element.appendChild(wave);
                
                setTimeout(() => wave.remove(), 1000);
            }, i * 200);
        }
    }

    // Enhanced browser notifications
    window.addEventListener('show-browser-notification', function(event) {
        if ('Notification' in window && Notification.permission === 'granted') {
            const options = {
                body: event.detail.message,
                icon: event.detail.icon || '/images/notification-icon.png',
                badge: '/images/notification-badge.png',
                tag: 'tickets-notification-' + Date.now(),
                requireInteraction: false,
                silent: false,
                vibrate: [200, 100, 200],
                actions: [
                    {
                        action: 'view',
                        title: 'عرض',
                        icon: '/images/view-icon.png'
                    },
                    {
                        action: 'dismiss',
                        title: 'تجاهل',
                        icon: '/images/dismiss-icon.png'
                    }
                ]
            };

            const notification = new Notification(event.detail.title, options);

            // Enhanced notification behavior
            notification.onclick = function() {
                window.focus();
                if (event.detail.url) {
                    window.location.href = event.detail.url;
                }
                notification.close();
            };

            // Auto close after 8 seconds with fade effect
            setTimeout(() => {
                notification.close();
            }, 8000);
            
            // Add to notification history
            addToNotificationHistory(event.detail);
        }
    });

    function showBrowserNotification(data) {
        const event = new CustomEvent('show-browser-notification', {
            detail: data
        });
        window.dispatchEvent(event);
    }

    function addToNotificationHistory(notification) {
        let history = JSON.parse(localStorage.getItem('notification-history') || '[]');
        history.unshift({
            ...notification,
            timestamp: new Date().toISOString(),
            read: false
        });
        
        // Keep only last 100 notifications
        history = history.slice(0, 100);
        localStorage.setItem('notification-history', JSON.stringify(history));
    }

    // Listen for real-time notifications via Laravel Echo
    if (typeof Echo !== 'undefined' && window.Laravel && window.Laravel.userId) {
        Echo.private(`notifications.${window.Laravel.userId}`)
            .listen('NotificationSent', (e) => {
                // Trigger Livewire refresh
                Livewire.dispatch('notification-received');
                
                // Trigger visual effects
                const event = new CustomEvent('notification-received', {
                    detail: {
                        title: e.notification.title,
                        message: e.notification.message,
                        icon: e.notification.icon,
                        url: e.notification.url
                    }
                });
                window.dispatchEvent(event);
                
                // Show toast notification
                showNotification(`📩 ${e.notification.title}`, 'info');
            });
            
        // Listen for notification read events
        Echo.private(`notifications.${window.Laravel.userId}`)
            .listen('NotificationRead', (e) => {
                Livewire.dispatch('notification-read', { id: e.notificationId });
            });
    }

    // Enhanced notification display with animations
    function enhanceNotificationDisplay() {
        const notifications = document.querySelectorAll('.notification-item');
        notifications.forEach((notification, index) => {
            notification.style.animationDelay = `${index * 0.1}s`;
            
            // Add hover effects
            notification.addEventListener('mouseenter', function() {
                this.classList.add('notification-glow');
            });
            
            notification.addEventListener('mouseleave', function() {
                this.classList.remove('notification-glow');
            });
        });
    }

    // Call enhancement function when notifications are loaded
    document.addEventListener('livewire:load', enhanceNotificationDisplay);
    document.addEventListener('livewire:update', enhanceNotificationDisplay);

    // Keyboard shortcuts for notifications
    document.addEventListener('keydown', function(e) {
        // Alt + N to toggle notifications
        if (e.altKey && e.key === 'n') {
            e.preventDefault();
            const bellButton = document.querySelector('button[\\@click*="toggleDropdown"]');
            if (bellButton) {
                bellButton.click();
            }
        }
        
        // Escape to close notifications
        if (e.key === 'Escape') {
            const dropdown = document.querySelector('[x-show="open"]');
            if (dropdown) {
                // Close the dropdown
                window.dispatchEvent(new CustomEvent('click'));
            }
        }
    });

    // Periodic check for new notifications (fallback)
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            Livewire.dispatch('check-new-notifications');
        }
    }, 30000); // Check every 30 seconds

    console.log('🔔 Enhanced Notification System - All features loaded! ✨');
</script>
@endpush
