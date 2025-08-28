@extends('layouts.app')

@section('title', 'لوحة تحكم العميل')

@section('content')
<div class="min-h-screen bg-gray-50" dir="rtl">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">مرحباً، {{ $customer->name }}</h1>
            <p class="text-gray-600">إدارة حجوزاتك وتفضيلاتك</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- إجمالي الحجوزات -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-600">إجمالي حجوزاتي</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_bookings']) }}</p>
                        <p class="text-xs text-blue-600">{{ $stats['confirmed_bookings'] }} مؤكدة</p>
                    </div>
                </div>
            </div>

            <!-- الحجوزات القادمة -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-600">الحجوزات القادمة</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['upcoming_bookings']) }}</p>
                        <p class="text-xs text-green-600">في الجدولة</p>
                    </div>
                </div>
            </div>

            <!-- إجمالي المبلغ المدفوع -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-600">إجمالي المبلغ المدفوع</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_spent'], 2) }} ريال</p>
                        <p class="text-xs text-gray-500">جميع الأوقات</p>
                    </div>
                </div>
            </div>

            <!-- الخدمات المفضلة -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-600">الخدمات المفضلة</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['favorite_services']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Bookings -->
        @if($upcomingBookings->count() > 0)
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">حجوزاتك القادمة</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($upcomingBookings as $booking)
                        <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-center">
                                <div class="mr-4">
                                    <p class="font-medium text-gray-900">{{ $booking->service->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $booking->service->merchant->business_name }}</p>
                                    <p class="text-xs text-gray-500">{{ Carbon\Carbon::parse($booking->booking_date)->format('Y/m/d') }} - {{ Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <p class="font-semibold text-gray-900">{{ number_format($booking->total_amount) }} ريال</p>
                                <div class="flex space-x-2 mt-2">
                                    <button class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">
                                        إعادة جدولة
                                    </button>
                                    <button class="text-xs bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">
                                        إلغاء
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('customer.dashboard.bookings') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        عرض جميع الحجوزات
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Booking History -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">سجل الحجوزات الأخيرة</h3>
                <div class="space-y-3">
                    @foreach($recentBookings->take(5) as $booking)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 text-sm">{{ $booking->service->name }}</p>
                                <p class="text-xs text-gray-600">{{ $booking->service->merchant->business_name }}</p>
                                <p class="text-xs text-gray-500">{{ Carbon\Carbon::parse($booking->booking_date)->format('Y/m/d') }}</p>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-semibold text-gray-900">{{ number_format($booking->total_amount) }} ريال</p>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($booking->booking_status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($booking->booking_status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($booking->booking_status === 'completed') bg-blue-100 text-blue-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $booking->booking_status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Favorite Services -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">خدماتك المفضلة</h3>
                <div class="space-y-3">
                    @foreach($favoriteServices as $service)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 text-sm">{{ $service->name }}</p>
                                <p class="text-xs text-gray-600">{{ $service->merchant->business_name }}</p>
                                <p class="text-xs text-gray-500">{{ $service->bookings_count }} حجز</p>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-semibold text-gray-900">{{ number_format($service->price) }} ريال</p>
                                <button class="mt-1 text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">
                                    احجز الآن
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">إجراءات سريعة</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('booking.form') }}" 
                       class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition duration-200">
                        <div class="p-3 rounded-full bg-blue-600 text-white mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-900">حجز جديد</span>
                    </a>

                    <a href="{{ route('customer.dashboard.bookings') }}" 
                       class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition duration-200">
                        <div class="p-3 rounded-full bg-green-600 text-white mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-900">حجوزاتي</span>
                    </a>

                    <a href="{{ route('customer.dashboard.profile') }}" 
                       class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition duration-200">
                        <div class="p-3 rounded-full bg-purple-600 text-white mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-900">الملف الشخصي</span>
                    </a>

                    <a href="{{ route('customer.dashboard.favorites') }}" 
                       class="flex flex-col items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition duration-200">
                        <div class="p-3 rounded-full bg-yellow-600 text-white mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-900">المفضلة</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        @if($notifications->count() > 0)
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">الإشعارات</h2>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @foreach($notifications as $notification)
                        <div class="flex items-center p-3 {{ $notification->read_at ? 'bg-gray-50' : 'bg-blue-50' }} rounded-lg">
                            <div class="mr-3">
                                @if(!$notification->read_at)
                                    <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $notification->data['title'] ?? '' }}</p>
                                <p class="text-xs text-gray-600">{{ $notification->data['message'] ?? '' }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
