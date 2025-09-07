@extends('layouts.app')

@section('title', 'لوحة التحكم - التاجر')

@section('content')
<div class="min-h-screen bg-gray-50" dir="rtl">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">مرحباً، {{ $user->name }}</h1>
                    <p class="text-gray-600 mt-1">{{ $user->business_name ?? 'عملك التجاري' }} - لوحة التحكم</p>
                </div>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <a href="{{ route('merchant.services.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        إضافة خدمة جديدة
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Services -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">إجمالي الخدمات</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total_services'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center space-x-2 space-x-reverse">
                    <span class="text-sm text-green-600">{{ $stats['active_services'] }} نشطة</span>
                    <span class="text-sm text-gray-400">•</span>
                    <span class="text-sm text-red-600">{{ $stats['inactive_services'] }} معطلة</span>
                </div>
            </div>

            <!-- Total Bookings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">إجمالي الحجوزات</p>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['total_bookings'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center space-x-2 space-x-reverse">
                    <span class="text-sm text-yellow-600">{{ $stats['pending_bookings'] }} معلقة</span>
                    <span class="text-sm text-gray-400">•</span>
                    <span class="text-sm text-green-600">{{ $stats['confirmed_bookings'] }} مؤكدة</span>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">إجمالي الإيرادات</p>
                        <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['total_revenue']) }} ريال</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-orange-600">{{ number_format($stats['pending_revenue']) }} ريال معلقة</span>
                </div>
            </div>

            <!-- Featured Services -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">الخدمات المميزة</p>
                        <p class="text-3xl font-bold text-orange-600">{{ $stats['featured_services'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('merchant.services.index', ['status' => 'featured']) }}" class="text-sm text-orange-600 hover:text-orange-800">
                        عرض الخدمات المميزة ←
                    </a>
                </div>
            </div>
        </div>

        <!-- Charts and Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Revenue Chart -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">الإيرادات الشهرية</h3>
                    @if($monthlyRevenue->count() > 0)
                        <div class="space-y-4">
                            @foreach($monthlyRevenue as $month)
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="font-medium">
                                            {{ $month->year }}/{{ str_pad($month->month, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <span class="text-sm text-gray-500 mr-2">
                                            ({{ $month->bookings_count }} حجز)
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-bold text-green-600">
                                            {{ number_format($month->total_revenue) }} ريال
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">لا توجد إيرادات بعد</p>
                            <p class="text-xs text-gray-400">ابدأ بإضافة خدماتك واستقبال الحجوزات</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">إجراءات سريعة</h3>
                    <div class="space-y-3">
                        <a href="{{ route('merchant.services.index') }}" 
                           class="w-full flex items-center justify-between p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition duration-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span class="text-blue-700 font-medium">إدارة الخدمات</span>
                            </div>
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>

                        <a href="{{ route('merchant.bookings.index') }}" 
                           class="w-full flex items-center justify-between p-3 bg-green-50 hover:bg-green-100 rounded-lg transition duration-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <span class="text-green-700 font-medium">إدارة الحجوزات</span>
                            </div>
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>

                        <a href="{{ route('merchant.analytics.index') }}" 
                           class="w-full flex items-center justify-between p-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition duration-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-purple-600 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <span class="text-purple-700 font-medium">التحليلات والتقارير</span>
                            </div>
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>

                        <a href="{{ route('merchant.profile.index') }}" 
                           class="w-full flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition duration-200">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-600 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-gray-700 font-medium">الملف الشخصي</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Top Services -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">أفضل الخدمات</h3>
                    <a href="{{ route('merchant.services.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        عرض الكل ←
                    </a>
                </div>
                
                @if($topServices->count() > 0)
                    <div class="space-y-4">
                        @foreach($topServices as $service)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    @if($service->image)
                                        <img src="{{ Storage::url($service->image) }}" 
                                             alt="{{ $service->name }}" 
                                             class="w-10 h-10 rounded-lg object-cover ml-3">
                                    @else
                                        <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center ml-3">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $service->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $service->bookings_count }} حجز</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-green-600">
                                        {{ number_format($service->total_revenue ?? 0) }} ريال
                                    </p>
                                    <p class="text-xs text-gray-500">{{ number_format($service->price) }} ريال/حجز</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">لا توجد خدمات بعد</p>
                        <a href="{{ route('merchant.services.create') }}" class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                            إضافة خدمة جديدة ←
                        </a>
                    </div>
                @endif
            </div>

            <!-- Recent Bookings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">أحدث الحجوزات</h3>
                    <a href="{{ route('merchant.bookings.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        عرض الكل ←
                    </a>
                </div>
                
                @if($recentBookings->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentBookings as $booking)
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ $booking->customer_name ?? $booking->user->name ?? 'عميل غير محدد' }}
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $booking->service->name ?? 'خدمة محذوفة' }}</p>
                                    <p class="text-xs text-gray-400">{{ $booking->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-1 text-xs rounded-full font-medium
                                        {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                           ($booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($booking->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                           'bg-blue-100 text-blue-800')) }}">
                                        {{ match($booking->status) {
                                            'confirmed' => 'مؤكدة',
                                            'pending' => 'معلقة',
                                            'cancelled' => 'ملغاة',
                                            'completed' => 'مكتملة',
                                            default => $booking->status
                                        } }}
                                    </span>
                                    <p class="text-sm font-semibold text-gray-900 mt-1">
                                        {{ number_format($booking->total_amount) }} ريال
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">لا توجد حجوزات بعد</p>
                        <p class="text-xs text-gray-400">الحجوزات ستظهر هنا عند استقبالها</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection