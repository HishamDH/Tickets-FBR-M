@extends('layouts.app')

@section('title', 'لوحة تحكم التاجر')

@section('content')
<div class="min-h-screen" dir="rtl">
    <!-- Hero Header -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-10 right-10 w-32 h-32 bg-white rounded-full opacity-10 animate-pulse"></div>
            <div class="absolute bottom-10 left-10 w-24 h-24 bg-white rounded-full opacity-15 animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute top-20 left-1/3 w-16 h-16 bg-white rounded-full opacity-10 animate-pulse" style="animation-delay: 2s;"></div>
        </div>
        
        <div class="relative container mx-auto px-4 py-12">
            <div class="flex items-center justify-between text-white">
                <div>
                    <h1 class="text-4xl font-bold mb-2">مرحباً بك، {{ $merchant->business_name }} 🏪</h1>
                    <p class="text-blue-100 text-lg">إدارة خدماتك وحجوزاتك من مكان واحد بتصميم متطور</p>
                </div>
                <div class="hidden md:block">
                    <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Enhanced Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 -mt-16 relative z-10">
            <!-- إجمالي الخدمات -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-blue-100 hover:shadow-2xl transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="p-4 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="mr-4 flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">إجمالي الخدمات</p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-700 bg-clip-text text-transparent">{{ number_format($stats['total_services']) }}</p>
                        <p class="text-xs text-blue-600 font-medium">{{ $stats['active_services'] }} نشطة</p>
                    </div>
                </div>
            </div>

            <!-- إجمالي الحجوزات -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-green-100 hover:shadow-2xl transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="p-4 rounded-xl bg-gradient-to-br from-green-500 to-green-600 text-white group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="mr-4 flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">إجمالي الحجوزات</p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-green-600 to-green-700 bg-clip-text text-transparent">{{ number_format($stats['total_bookings']) }}</p>
                        <p class="text-xs text-green-600 font-medium">{{ $stats['pending_bookings'] }} في الانتظار</p>
                    </div>
                </div>
            </div>

            <!-- إجمالي الإيرادات -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-purple-100 hover:shadow-2xl transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="p-4 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 text-white group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="mr-4 flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">إجمالي الإيرادات</p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-purple-700 bg-clip-text text-transparent">{{ number_format($stats['total_revenue']) }} ريال</p>
                        <p class="text-xs text-purple-600 font-medium">هذا الشهر</p>
                    </div>
                </div>
            </div>

            <!-- العملاء الجدد -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-yellow-100 hover:shadow-2xl transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="p-4 rounded-xl bg-gradient-to-br from-yellow-500 to-yellow-600 text-white group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="mr-4 flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">العملاء الجدد</p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-yellow-600 to-yellow-700 bg-clip-text text-transparent">{{ number_format($stats['new_customers'] ?? 0) }}</p>
                        <p class="text-xs text-yellow-600 font-medium">هذا الأسبوع</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-xl border border-blue-100 mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white">إجراءات سريعة ⚡</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-6 gap-6">
                    <a href="{{ route('merchant.services.index') }}" 
                       class="group flex flex-col items-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl border-2 border-blue-200 hover:border-blue-300 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-900">إدارة الخدمات</span>
                        <span class="text-sm text-blue-600 font-medium">إضافة وتعديل</span>
                    </a>

                    <a href="{{ route('merchant.bookings.index') }}" 
                       class="group flex flex-col items-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl border-2 border-green-200 hover:border-green-300 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-green-500 to-green-600 text-white mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-900">إدارة الحجوزات</span>
                        <span class="text-sm text-green-600 font-medium">متابعة الطلبات</span>
                    </a>

                    <a href="{{ route('merchant.analytics.index') }}" 
                       class="group flex flex-col items-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl border-2 border-purple-200 hover:border-purple-300 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 text-white mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-900">التحليلات</span>
                        <span class="text-sm text-purple-600 font-medium">تقارير الأداء</span>
                    </a>

                    <a href="{{ route('merchant.payments.index') }}" 
                       class="group flex flex-col items-center p-6 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl border-2 border-yellow-200 hover:border-yellow-300 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-yellow-500 to-yellow-600 text-white mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-900">إعدادات الدفع</span>
                        <span class="text-sm text-yellow-600 font-medium">طرق الدفع</span>
                    </a>

                    <a href="{{ route('merchant.branding.index') }}" 
                       class="group flex flex-col items-center p-6 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl border-2 border-indigo-200 hover:border-indigo-300 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 text-white mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4 4 4 0 004-4V5z"/>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-900">هوية المتجر</span>
                        <span class="text-sm text-indigo-600 font-medium">النطاق والعلامة</span>
                    </a>

                    <a href="{{ route('merchant.staff.index') }}" 
                       class="group flex flex-col items-center p-6 bg-gradient-to-br from-teal-50 to-teal-100 rounded-2xl border-2 border-teal-200 hover:border-teal-300 hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-teal-500 to-teal-600 text-white mb-4 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-900">إدارة الموظفين</span>
                        <span class="text-sm text-teal-600 font-medium">الفريق والمناوبات</span>
                    </a>
                </div>
            </div>
        </div>
                </div>
                <h3 class="font-semibold text-gray-900 group-hover:text-orange-600 transition-colors">إعدادات الدفع</h3>
                <p class="text-sm text-gray-600">إدارة بوابات الدفع</p>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Bookings -->
            <div class="lg:col-span-2">
                <div class="glass-effect rounded-xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold flex items-center">
                            <svg class="w-6 h-6 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            أحدث الحجوزات
                        </h2>
                        <a href="{{ route('merchant.bookings.index') }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">عرض الكل</a>
                    </div>
                    
                    <div class="space-y-4">
                        @forelse($recentBookings->take(5) as $booking)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center ml-3">
                                    <span class="text-orange-600 font-medium">{{ substr($booking->customer_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $booking->customer_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $booking->service->name }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->booking_date)->format('Y/m/d') }} - {{ $booking->booking_time }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-900">{{ number_format($booking->total_amount) }} ريال</p>
                                @switch($booking->status)
                                    @case('pending')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            في الانتظار
                                        </span>
                                        @break
                                    @case('confirmed')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            مؤكد
                                        </span>
                                        @break
                                    @case('completed')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            مكتمل
                                        </span>
                                        @break
                                @endswitch
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <div class="text-4xl text-gray-300 mb-2">📋</div>
                            <p class="text-gray-500">لا توجد حجوزات حديثة</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Today's Bookings & Top Services -->
            <div class="space-y-6">
                <!-- Today's Bookings -->
                <div class="glass-effect rounded-xl p-6">
                    <h3 class="text-lg font-bold mb-4 flex items-center">
                        <svg class="w-5 h-5 ml-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        حجوزات اليوم
                    </h3>
                    
                    <div class="space-y-3">
                        @forelse($todayBookings->take(3) as $booking)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="font-medium text-gray-900 text-sm">{{ $booking->customer_name }}</p>
                            <p class="text-xs text-gray-600">{{ $booking->service->name }}</p>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-xs text-blue-600 font-medium">{{ $booking->booking_time }}</span>
                                <span class="text-xs font-bold text-green-600">{{ number_format($booking->total_amount) }} ريال</span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <div class="text-2xl text-gray-300 mb-1">📅</div>
                            <p class="text-xs text-gray-500">لا توجد حجوزات اليوم</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Top Services -->
                <div class="glass-effect rounded-xl p-6">
                    <h3 class="text-lg font-bold mb-4 flex items-center">
                        <svg class="w-5 h-5 ml-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        أفضل الخدمات
                    </h3>
                    
                    <div class="space-y-3">
                        @forelse($topServices->take(3) as $service)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="font-medium text-gray-900 text-sm">{{ $service->name }}</p>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-xs text-gray-600">{{ $service->bookings_count ?? 0 }} حجز</span>
                                <span class="text-xs font-bold text-purple-600">{{ number_format($service->total_revenue ?? 0) }} ريال</span>
                            </div>
                            @php
                                $maxRevenue = $topServices->max('total_revenue') ?? 1;
                                $percentage = $maxRevenue > 0 ? (($service->total_revenue ?? 0) / $maxRevenue) * 100 : 0;
                            @endphp
                            <div class="mt-2 bg-gray-200 rounded-full h-1">
                                <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-1 rounded-full" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <div class="text-2xl text-gray-300 mb-1">🏆</div>
                            <p class="text-xs text-gray-500">لا توجد خدمات بعد</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Monthly Revenue Chart -->
                @if($monthlyRevenue->isNotEmpty())
                <div class="glass-effect rounded-xl p-6">
                    <h3 class="text-lg font-bold mb-4 flex items-center">
                        <svg class="w-5 h-5 ml-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        الإيرادات الشهرية
                    </h3>
                    
                    <div class="space-y-2">
                        @foreach($monthlyRevenue->take(6) as $month)
                        @php
                            $monthNames = [
                                1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
                                5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
                                9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
                            ];
                            $monthName = $monthNames[$month->month] ?? 'غير محدد';
                            $maxRevenue = $monthlyRevenue->max('total_revenue') ?? 1;
                            $percentage = $maxRevenue > 0 ? ($month->total_revenue / $maxRevenue) * 100 : 0;
                        @endphp
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600 w-16">{{ $monthName }}</span>
                            <div class="flex-1 mx-3 bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2 rounded-full" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="font-semibold text-orange-600 text-xs">{{ number_format($month->total_revenue) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Statistics Grid -->
<div class="container mx-auto px-4 -mt-8 relative z-10">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- صافي الإيرادات -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
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
                                <span @class([
                                    'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                                    'bg-green-100 text-green-800' => $booking->status === 'confirmed',
                                    'bg-yellow-100 text-yellow-800' => $booking->status === 'pending',
                                    'bg-red-100 text-red-800' => !in_array($booking->status, ['confirmed', 'pending'])
                                ])>
                                    {{ $booking->status }}
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
                                <span @class([
                                    'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                                    'bg-green-100 text-green-800' => $booking->status === 'confirmed',
                                    'bg-yellow-100 text-yellow-800' => $booking->status === 'pending',
                                    'bg-blue-100 text-blue-800' => $booking->status === 'completed',
                                    'bg-red-100 text-red-800' => !in_array($booking->status, ['confirmed', 'pending', 'completed'])
                                ])>
                                    {{ $booking->status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('merchant.services.index') }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-blue-700 transition duration-200">
                إدارة الخدمات
            </a>
            <a href="{{ route('merchant.bookings.index') }}" 
               class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-green-700 transition duration-200">
                إدارة الحجوزات
            </a>
            <a href="{{ route('merchant.analytics.revenue') }}" 
               class="bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-purple-700 transition duration-200">
                تقرير الإيرادات
            </a>
            <a href="{{ route('merchant.analytics.index') }}" 
               class="bg-yellow-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-yellow-700 transition duration-200">
                التحليلات
            </a>
        </div>
    </div>
</div>
@endsection
