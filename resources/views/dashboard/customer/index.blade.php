@extends('layouts.app')

@section('title', 'لوحة تحكم العميل')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 to-orange-100" dir="rtl">
    <!-- Hero Header -->
    <div class="bg-gradient-to-r from-orange-500 to-orange-600 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-10 right-10 w-32 h-32 bg-white rounded-full opacity-10 animate-pulse"></div>
            <div class="absolute bottom-10 left-10 w-24 h-24 bg-white rounded-full opacity-15 animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute top-20 left-1/3 w-16 h-16 bg-white rounded-full opacity-10 animate-pulse" style="animation-delay: 2s;"></div>
        </div>
        
        <div class="relative container mx-auto px-4 py-12">
            <div class="flex items-center justify-between text-white">
                <div>
                    <h1 class="text-4xl font-bold mb-2">مرحباً بك، {{ $user->name }} 👋</h1>
                    <p class="text-orange-100 text-lg">إدارة حجوزاتك وتفضيلاتك بسهولة وأمان</p>
                </div>
                <div class="hidden md:block">
                    <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 -mt-16 relative z-10">
            <!-- إجمالي الحجوزات -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-orange-100 hover:shadow-2xl transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="p-4 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 text-white group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="mr-4 flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">إجمالي حجوزاتي</p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-orange-600 to-orange-700 bg-clip-text text-transparent">{{ number_format($stats['total_bookings']) }}</p>
                        <p class="text-xs text-orange-600 font-medium">{{ $stats['confirmed_bookings'] }} مؤكدة</p>
                    </div>
                </div>
            </div>

            <!-- الحجوزات القادمة -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-green-100 hover:shadow-2xl transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="p-4 rounded-xl bg-gradient-to-br from-green-500 to-green-600 text-white group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="mr-4 flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">الحجوزات القادمة</p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-green-600 to-green-700 bg-clip-text text-transparent">{{ number_format($stats['upcoming_bookings']) }}</p>
                        <p class="text-xs text-green-600 font-medium">في الجدولة</p>
                    </div>
                </div>
            </div>

            <!-- إجمالي المبلغ المدفوع -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-purple-100 hover:shadow-2xl transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="p-4 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 text-white group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="mr-4 flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">إجمالي المبلغ المدفوع</p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-purple-700 bg-clip-text text-transparent">{{ number_format($stats['total_spent'], 2) }} ريال</p>
                        <p class="text-xs text-purple-600 font-medium">جميع الأوقات</p>
                    </div>
                </div>
            </div>

            <!-- الخدمات المفضلة -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-yellow-100 hover:shadow-2xl transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="p-4 rounded-xl bg-gradient-to-br from-yellow-500 to-yellow-600 text-white group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <div class="mr-4 flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">الخدمات المفضلة</p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-yellow-600 to-yellow-700 bg-clip-text text-transparent">{{ number_format($stats['favorite_services']) }}</p>
                        <p class="text-xs text-yellow-600 font-medium">محفوظة</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Bookings -->
        @if($upcomingBookings->count() > 0)
        <div class="bg-white rounded-2xl shadow-xl border border-orange-100 mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white">حجوزاتك القادمة ⏰</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($upcomingBookings as $booking)
                        <div class="flex items-center justify-between p-6 bg-gradient-to-r from-orange-50 to-orange-100 rounded-2xl border border-orange-200 hover:shadow-lg transition-all duration-300">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center text-white font-bold text-lg mr-4">
                                    🎫
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 text-lg">{{ $booking->service->name }}</p>
                                    <p class="text-orange-600 font-medium">{{ $booking->service->merchant->business_name }}</p>
                                    <p class="text-sm text-gray-600">📅 {{ Carbon\Carbon::parse($booking->booking_date)->format('Y/m/d') }} - ⏰ {{ Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <p class="font-bold text-2xl bg-gradient-to-r from-orange-600 to-orange-700 bg-clip-text text-transparent mb-3">{{ number_format($booking->total_amount) }} ريال</p>
                                <div class="flex space-x-2">
                                    <button class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 shadow-lg hover:shadow-xl">
                                        إعادة جدولة
                                    </button>
                                    <button class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 shadow-lg hover:shadow-xl">
                                        إلغاء
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 text-center">
                    <a href="{{ route('customer.bookings.index') }}" class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-6 py-3 rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl">
                        عرض جميع الحجوزات
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Booking History -->
            <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white">سجل الحجوزات الأخيرة 📋</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($recentBookings->take(5) as $booking)
                            <div class="flex justify-between items-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl border border-blue-200 hover:shadow-lg transition-all duration-300">
                                <div>
                                    <p class="font-bold text-gray-900">{{ $booking->service->name }}</p>
                                    <p class="text-blue-600 font-medium text-sm">{{ $booking->service->merchant->business_name }}</p>
                                    <p class="text-xs text-gray-600">📅 {{ Carbon\Carbon::parse($booking->booking_date)->format('Y/m/d') }}</p>
                                </div>
                                <div class="text-left">
                                    <p class="text-lg font-bold bg-gradient-to-r from-blue-600 to-blue-700 bg-clip-text text-transparent">{{ number_format($booking->total_amount) }} ريال</p>
                                    <span @class([
                                        'inline-flex items-center px-3 py-1 rounded-full text-xs font-bold',
                                        'bg-green-500 text-white' => $booking->booking_status === 'confirmed',
                                        'bg-yellow-500 text-white' => $booking->booking_status === 'pending',
                                        'bg-blue-500 text-white' => $booking->booking_status === 'completed',
                                        'bg-red-500 text-white' => !in_array($booking->booking_status, ['confirmed', 'pending', 'completed'])
                                    ])>
                                        {{ $booking->booking_status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Favorite Services -->
            <div class="bg-white rounded-2xl shadow-xl border border-purple-100 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white">خدماتك المفضلة ❤️</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($favoriteServices as $service)
                            <div class="flex justify-between items-center p-4 bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl border border-purple-200 hover:shadow-lg transition-all duration-300">
                                <div>
                                    <p class="font-bold text-gray-900">{{ $service->name }}</p>
                                    <p class="text-purple-600 font-medium text-sm">{{ $service->merchant->business_name }}</p>
                                    <p class="text-xs text-gray-600">📊 {{ $service->bookings_count }} حجز</p>
                                </div>
                                <div class="text-left">
                                    <p class="text-lg font-bold bg-gradient-to-r from-purple-600 to-purple-700 bg-clip-text text-transparent mb-2">{{ number_format($service->price) }} ريال</p>
                                    <button class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 shadow-lg hover:shadow-xl">
                                        احجز الآن
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-xl border border-orange-100 mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white">إجراءات سريعة ⚡</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <a href="{{ route('booking.form') }}" 
                       class="group flex flex-col items-center p-6 bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl border-2 border-orange-200 hover:border-orange-300 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 text-white mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-900">حجز جديد</span>
                        <span class="text-sm text-orange-600 font-medium">ابدأ رحلتك</span>
                    </a>

                    <a href="{{ route('customer.bookings.index') }}" 
                       class="group flex flex-col items-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl border-2 border-green-200 hover:border-green-300 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 text-white mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-900">حجوزاتي</span>
                        <span class="text-sm text-green-600 font-medium">تتبع طلباتك</span>
                    </a>

                    <a href="{{ route('customer.profile.show') }}" 
                       class="group flex flex-col items-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl border-2 border-purple-200 hover:border-purple-300 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 text-white mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-900">الملف الشخصي</span>
                        <span class="text-sm text-purple-600 font-medium">بياناتك</span>
                    </a>

                    <a href="{{ route('customer.favorites.index') }}" 
                       class="group flex flex-col items-center p-6 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl border-2 border-yellow-200 hover:border-yellow-300 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-yellow-500 to-yellow-600 text-white mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-900">المفضلة</span>
                        <span class="text-sm text-yellow-600 font-medium">اختياراتك</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        @if($notifications->count() > 0)
        <div class="bg-white rounded-2xl shadow-xl border border-blue-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white">الإشعارات 🔔</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($notifications as $notification)
                        <div @class([
                            'flex items-center p-4 rounded-xl border hover:shadow-lg transition-all duration-300',
                            'bg-gray-50 border-gray-200' => $notification->read_at,
                            'bg-blue-50 border-blue-200' => !$notification->read_at
                        ])>
                            <div @class([
                                'w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg mr-4',
                                'bg-gray-400' => $notification->read_at,
                                'bg-blue-500' => !$notification->read_at
                            ])>
                                🔔
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-gray-900">{{ $notification->data['title'] ?? 'إشعار جديد' }}</p>
                                <p class="text-sm text-gray-600">{{ $notification->data['message'] ?? $notification->data['body'] ?? 'لديك إشعار جديد' }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="mr-auto">
                                @if(!$notification->read_at)
                                    <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 text-center">
                    <a href="{{ route('customer.notifications.index') }}" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-xl">
                        عرض جميع الإشعارات
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Welcome Tips -->
        <div class="bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600 rounded-2xl shadow-xl p-8 text-white relative overflow-hidden mt-8">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -mr-16 -mt-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white opacity-10 rounded-full -ml-12 -mb-12"></div>
            
            <div class="relative z-10">
                <h3 class="text-2xl font-bold mb-4">💡 نصائح لتحسين تجربتك</h3>
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="flex items-start space-x-3 space-x-reverse">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-lg">🎯</span>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-1">احفظ المفضلة</h4>
                            <p class="text-orange-100 text-sm">أضف خدماتك المفضلة للوصول السريع إليها لاحقاً</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3 space-x-reverse">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-lg">⏰</span>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-1">احجز مبكراً</h4>
                            <p class="text-orange-100 text-sm">للحصول على أفضل الأوقات والأسعار المناسبة</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3 space-x-reverse">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-lg">⭐</span>
                        </div>
                        <div>
                            <h4 class="font-semibold mb-1">قيم الخدمة</h4>
                            <p class="text-orange-100 text-sm">ساعد العملاء الآخرين بتقييمك الصادق</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
