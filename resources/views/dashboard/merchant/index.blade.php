@extends('layouts.app')

@section('title', 'لوحة تحكم التاجر')

@section('content')
<div class="min-h-screen bg-gray-50" dir="rtl">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">مرحباً، {{ $merchant->business_name }}</h1>
            <p class="text-gray-600">إدارة خدماتك وحجوزاتك من مكان واحد</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- إجمالي الخدمات -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-600">إجمالي الخدمات</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_services']) }}</p>
                        <p class="text-xs text-green-600">{{ $stats['active_services'] }} نشطة</p>
                    </div>
                </div>
            </div>

            <!-- إجمالي الحجوزات -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-600">إجمالي الحجوزات</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_bookings']) }}</p>
                        <p class="text-xs text-yellow-600">{{ $stats['pending_bookings'] }} في الانتظار</p>
                    </div>
                </div>
            </div>

            <!-- صافي الإيرادات -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-600">صافي الإيرادات</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['net_revenue'], 2) }} ريال</p>
                        <p class="text-xs text-gray-500">بعد العمولة</p>
                    </div>
                </div>
            </div>

            <!-- الحجوزات المؤكدة -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-600">الحجوزات المؤكدة</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['confirmed_bookings']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Bookings -->
        @if($todayBookings->count() > 0)
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">حجوزات اليوم</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($todayBookings as $booking)
                        <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-center">
                                <div class="mr-4">
                                    <p class="font-medium text-gray-900">{{ $booking->service->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $booking->customer->name ?? $booking->customer_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $booking->customer_phone }}</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <p class="font-semibold text-gray-900">{{ Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}</p>
                                <p class="text-sm text-gray-600">{{ number_format($booking->total_amount) }} ريال</p>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($booking->booking_status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($booking->booking_status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $booking->booking_status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Charts and Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Monthly Revenue -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">الإيرادات الشهرية</h3>
                <div class="h-64 flex items-center justify-center">
                    <div class="text-center w-full">
                        @if($monthlyRevenue->count() > 0)
                            <div class="space-y-3">
                                @foreach($monthlyRevenue as $month)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-700">الشهر {{ $month->month }}</span>
                                        <div class="text-left">
                                            <span class="text-sm font-semibold text-gray-900">{{ number_format($month->total_revenue, 2) }} ريال</span>
                                            <div class="text-xs text-gray-500">{{ $month->bookings_count }} حجز</div>
                                        </div>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" 
                                             style="width: {{ $monthlyRevenue->max('total_revenue') > 0 ? ($month->total_revenue / $monthlyRevenue->max('total_revenue')) * 100 : 0 }}%"></div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">لا توجد بيانات متاحة</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Top Services -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">أفضل خدماتك أداءً</h3>
                <div class="space-y-4">
                    @foreach($topServices as $service)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $service->name }}</p>
                                <p class="text-sm text-gray-600">{{ $service->bookings_count }} حجز</p>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-semibold text-gray-900">{{ number_format($service->total_revenue ?? 0, 2) }} ريال</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">أحدث الحجوزات</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($recentBookings->take(5) as $booking)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $booking->service->name }}</p>
                                <p class="text-sm text-gray-600">{{ $booking->customer->name ?? $booking->customer_name }}</p>
                                <p class="text-xs text-gray-500">{{ Carbon\Carbon::parse($booking->booking_date)->format('Y/m/d') }} - {{ Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}</p>
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
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('merchant.dashboard.services') }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-blue-700 transition duration-200">
                إدارة الخدمات
            </a>
            <a href="{{ route('merchant.dashboard.bookings') }}" 
               class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-green-700 transition duration-200">
                إدارة الحجوزات
            </a>
            <a href="{{ route('merchant.dashboard.revenue-report') }}" 
               class="bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-purple-700 transition duration-200">
                تقرير الإيرادات
            </a>
            <a href="{{ route('merchant.dashboard.analytics') }}" 
               class="bg-yellow-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-yellow-700 transition duration-200">
                التحليلات
            </a>
        </div>
    </div>
</div>
@endsection
