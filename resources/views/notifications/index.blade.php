@extends('layouts.app')

@section('title', 'الإشعارات')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 py-8" dir="rtl">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl mb-8 p-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent mb-2">
                        📢 مركز الإشعارات
                    </h1>
                    <p class="text-slate-600 text-lg">إدارة جميع إشعاراتك وتحديثاتك</p>
                </div>
                
                <!-- Quick Stats -->
                <div class="mt-6 lg:mt-0 grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-blue-50 rounded-2xl p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $counts['all'] }}</div>
                        <div class="text-sm text-blue-500">الكل</div>
                    </div>
                    <div class="bg-orange-50 rounded-2xl p-4 text-center">
                        <div class="text-2xl font-bold text-orange-600">{{ $counts['unread'] }}</div>
                        <div class="text-sm text-orange-500">غير مقروء</div>
                    </div>
                    <div class="bg-red-50 rounded-2xl p-4 text-center">
                        <div class="text-2xl font-bold text-red-600">{{ $counts['urgent'] ?? 0 }}</div>
                        <div class="text-sm text-red-500">عاجل</div>
                    </div>
                    <div class="bg-green-50 rounded-2xl p-4 text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $analytics['read_percentage'] ?? 0 }}%</div>
                        <div class="text-sm text-green-500">معدل القراءة</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex space-x-8">
                    <a href="{{ route('notifications.index', ['type' => 'all']) }}" 
                       @class([
                           'text-sm font-medium pb-2',
                           'text-blue-600 border-b-2 border-blue-600' => $type === 'all',
                           'text-gray-500 hover:text-gray-700' => $type !== 'all'
                       ])>
                        الكل ({{ $counts['all'] }})
                    </a>
                    <a href="{{ route('notifications.index', ['type' => 'unread']) }}" 
                       @class([
                           'text-sm font-medium pb-2',
                           'text-blue-600 border-b-2 border-blue-600' => $type === 'unread',
                           'text-gray-500 hover:text-gray-700' => $type !== 'unread'
                       ])>
                        غير مقروءة ({{ $counts['unread'] }})
                    </a>
                    <a href="{{ route('notifications.index', ['type' => 'read']) }}" 
                       @class([
                           'text-sm font-medium pb-2',
                           'text-blue-600 border-b-2 border-blue-600' => $type === 'read',
                           'text-gray-500 hover:text-gray-700' => $type !== 'read'
                       ])>
                        مقروءة ({{ $counts['read'] }})
                    </a>
                </div>
            </div>

            <!-- Priority Filter -->
            <div class="px-6 py-3 bg-gray-50">
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-gray-700">الأولوية:</span>
                    <a href="{{ route('notifications.index', ['type' => $type]) }}" 
                       @class([
                           'text-xs px-2 py-1 rounded',
                           'bg-blue-100 text-blue-800' => !$priority,
                           'bg-gray-100 text-gray-600 hover:bg-gray-200' => $priority
                       ])>
                        الكل
                    </a>
                    <a href="{{ route('notifications.index', ['type' => $type, 'priority' => 'urgent']) }}" 
                       @class([
                           'text-xs px-2 py-1 rounded',
                           'bg-red-100 text-red-800' => $priority === 'urgent',
                           'bg-gray-100 text-gray-600 hover:bg-gray-200' => $priority !== 'urgent'
                       ])>
                        عاجل
                    </a>
                    <a href="{{ route('notifications.index', ['type' => $type, 'priority' => 'high']) }}" 
                       @class([
                           'text-xs px-2 py-1 rounded',
                           'bg-orange-100 text-orange-800' => $priority === 'high',
                           'bg-gray-100 text-gray-600 hover:bg-gray-200' => $priority !== 'high'
                       ])>
                        مهم
                    </a>
                    <a href="{{ route('notifications.index', ['type' => $type, 'priority' => 'normal']) }}" 
                       @class([
                           'text-xs px-2 py-1 rounded',
                           'bg-green-100 text-green-800' => $priority === 'normal',
                           'bg-gray-100 text-gray-600 hover:bg-gray-200' => $priority !== 'normal'
                       ])>
                        عادي
                    </a>
                </div>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="space-y-4">
            @forelse($notifications as $notification)
                <div class="bg-white rounded-lg shadow {{ !$notification->isRead() ? 'border-l-4 border-blue-500' : '' }}">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4">
                                <!-- Priority Indicator -->
                                <div class="flex-shrink-0">
                                    @if($notification->priority === 'urgent')
                                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                    @elseif($notification->priority === 'high')
                                        <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                                    @else
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    @endif
                                </div>

                                <!-- Notification Content -->
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <h3 class="text-lg font-medium text-gray-900">{{ $notification->title }}</h3>
                                        @if(!$notification->isRead())
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                جديد
                                            </span>
                                        @endif
                                        <span @class([
                                            'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                                            'bg-red-100 text-red-800' => $notification->priority === 'urgent',
                                            'bg-orange-100 text-orange-800' => $notification->priority === 'high',
                                            'bg-green-100 text-green-800' => !in_array($notification->priority, ['urgent', 'high'])
                                        ])>
                                            {{ $notification->priority }}
                                        </span>
                                    </div>
                                    <p class="mt-2 text-gray-600">{{ $notification->message }}</p>
                                    <div class="mt-3 flex items-center text-xs text-gray-500 space-x-4">
                                        <span>{{ $notification->created_at->diffForHumans() }}</span>
                                        <span>{{ $notification->type }}</span>
                                        @if($notification->isRead())
                                            <span>مقروء في {{ $notification->read_at->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center space-x-2">
                                @if(!$notification->isRead())
                                    <button onclick="markAsRead({{ $notification->id }})" 
                                            class="text-blue-600 hover:text-blue-800 text-sm">
                                        تمييز كمقروء
                                    </button>
                                @endif
                                <a href="{{ route('notifications.show', $notification->id) }}" 
                                   class="text-gray-600 hover:text-gray-800 text-sm">
                                    عرض
                                </a>
                                <button onclick="deleteNotification({{ $notification->id }})" 
                                        class="text-red-600 hover:text-red-800 text-sm">
                                    حذف
                                </button>
                            </div>
                        </div>

                        <!-- Additional Data -->
                        @if($notification->data && count($notification->data) > 0)
                            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">تفاصيل إضافية:</h4>
                                <div class="grid grid-cols-2 gap-2 text-xs text-gray-600">
                                    @foreach($notification->data as $key => $value)
                                        <div>
                                            <span class="font-medium">{{ $key }}:</span> {{ $value }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 17H4l5 5v-5zM12 3v14"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">لا توجد إشعارات</h3>
                    <p class="mt-2 text-gray-600">لم يتم العثور على إشعارات بالمعايير المحددة.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div class="mt-8">
                {{ $notifications->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function deleteNotification(notificationId) {
    if (confirm('هل أنت متأكد من حذف هذا الإشعار؟')) {
        fetch(`/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}
</script>
@endsection
