@extends('layouts.admin')

@section('title', 'لوحة تحكم الإدارة')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100" dir="rtl">
    <!-- Hero Header -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-900 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-10 right-10 w-32 h-32 bg-white rounded-full opacity-10 animate-pulse"></div>
            <div class="absolute bottom-10 left-10 w-24 h-24 bg-white rounded-full opacity-15 animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute top-20 left-1/3 w-16 h-16 bg-white rounded-full opacity-10 animate-pulse" style="animation-delay: 2s;"></div>
        </div>
        
        <div class="relative container mx-auto px-4 py-12">
            <div class="flex items-center justify-between text-white">
                <div>
                    <h1 class="text-4xl font-bold mb-2">لوحة تحكم الإدارة ⚙️</h1>
                    <p class="text-gray-300 text-lg">نظرة شاملة على أداء المنصة وإدارة جميع العمليات</p>
                </div>
                <div class="hidden md:block">
                    <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 -mt-16 relative z-10">
            <!-- إجمالي المستخدمين -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-blue-100 hover:shadow-2xl transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="p-4 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                    <div class="mr-4 flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">إجمالي المستخدمين</p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-700 bg-clip-text text-transparent">{{ number_format($stats['total_users']) }}</p>
                        <p class="text-xs text-blue-600 font-medium">مسجلين</p>
                    </div>
                </div>
            </div>

            <!-- إجمالي التجار -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-green-100 hover:shadow-2xl transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="p-4 rounded-xl bg-gradient-to-br from-green-500 to-green-600 text-white group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="mr-4 flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">إجمالي التجار</p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-green-600 to-green-700 bg-clip-text text-transparent">{{ number_format($stats['total_merchants']) }}</p>
                        <p class="text-xs text-green-600 font-medium">نشطين</p>
                    </div>
                </div>
            </div>

            <!-- إجمالي الحجوزات -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-yellow-100 hover:shadow-2xl transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="p-4 rounded-xl bg-gradient-to-br from-yellow-500 to-yellow-600 text-white group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-600">إجمالي الحجوزات</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_bookings']) }}</p>
                    </div>
                </div>
            </div>

            <!-- إجمالي الإيرادات -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-600">إجمالي الإيرادات</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_revenue'], 2) }} ريال</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue and Bookings Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Monthly Revenue Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">الإيرادات الشهرية</h3>
                <div class="h-64 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-gray-500">سيتم إضافة الرسم البياني هنا</p>
                        <div class="mt-4 space-y-2">
                            @foreach($monthlyRevenue as $month)
                                <div class="flex justify-between items-center text-sm">
                                    <span>الشهر {{ $month->month }}</span>
                                    <span class="font-semibold">{{ number_format($month->revenue, 2) }} ريال</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Type Distribution -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">توزيع أنواع الخدمات</h3>
                <div class="space-y-3">
                    @foreach($serviceTypeDistribution as $type)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">
                                @switch($type->service_type)
                                    @case('event')
                                        فعاليات
                                        @break
                                    @case('exhibition')
                                        معارض
                                        @break
                                    @case('restaurant')
                                        مطاعم
                                        @break
                                    @case('experience')
                                        تجارب
                                        @break
                                    @default
                                        {{ $type->service_type }}
                                @endswitch
                            </span>
                            <span class="text-sm font-semibold text-gray-900">{{ $type->count }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($type->count / $stats['total_services']) * 100 }}%"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Top Services and Recent Bookings -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Top Services -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">أفضل الخدمات حجزاً</h3>
                <div class="space-y-4">
                    @foreach($topServices as $service)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $service->name }}</p>
                                <p class="text-sm text-gray-600">{{ $service->merchant->business_name }}</p>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-semibold text-gray-900">{{ $service->bookings_count }} حجز</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">أحدث الحجوزات</h3>
                <div class="space-y-4">
                    @foreach($recentBookings as $booking)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $booking->service->name }}</p>
                                <p class="text-sm text-gray-600">{{ $booking->customer->name ?? $booking->customer_name }}</p>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-semibold text-gray-900">{{ number_format($booking->total_amount) }} ريال</p>
                                <span @class([
                                    'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                                    'bg-green-100 text-green-800' => $booking->booking_status === 'confirmed',
                                    'bg-yellow-100 text-yellow-800' => $booking->booking_status === 'pending',
                                    'bg-red-100 text-red-800' => !in_array($booking->booking_status, ['confirmed', 'pending'])
                                ])>
                                    {{ $booking->booking_status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.revenue-report') }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-blue-700 transition duration-200">
                تقرير الإيرادات
            </a>
            <a href="{{ route('admin.merchants-report') }}" 
               class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-green-700 transition duration-200">
                تقرير التجار
            </a>
            <a href="{{ route('admin.partners-report') }}" 
               class="bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-purple-700 transition duration-200">
                تقرير الشركاء
            </a>
            <a href="{{ route('admin.services-analytics') }}" 
               class="bg-yellow-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-yellow-700 transition duration-200">
                تحليلات الخدمات
            </a>
        </div>
    </div>
</div>
@endsection
