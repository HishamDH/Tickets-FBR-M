@extends('layouts.app')

@section('title', 'التحليلات والتقارير')

@section('content')
<div class="min-h-screen bg-gray-50" dir="rtl">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">التحليلات والتقارير</h1>
            <p class="text-gray-600">تحليل شامل لأداء أعمالك ومبيعاتك</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Revenue -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">إجمالي الإيرادات</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_revenue'] ?? 0) }} ريال</p>
                        <p class="text-sm text-green-600 mt-1">+12% من الشهر الماضي</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-xl">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Bookings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">إجمالي الحجوزات</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_bookings'] ?? 0) }}</p>
                        <p class="text-sm text-blue-600 mt-1">+8% من الشهر الماضي</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-xl">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 6l4-4m4 4l-4-4m0 8h.01"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Services -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">الخدمات النشطة</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['active_services'] ?? 0) }}</p>
                        <p class="text-sm text-purple-600 mt-1">من {{ $stats['total_services'] ?? 0 }} خدمة</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-xl">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Conversion Rate -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">معدل التحويل</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['conversion_rate'] ?? 0, 1) }}%</p>
                        <p class="text-sm text-orange-600 mt-1">من إجمالي الزيارات</p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-xl">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Navigation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">التقارير المتخصصة</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('merchant.analytics.revenue') }}" 
                   class="group p-4 border border-gray-200 rounded-lg hover:border-green-300 hover:bg-green-50 transition-all duration-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg group-hover:bg-green-200 transition-colors">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                        <div class="mr-3">
                            <p class="font-semibold text-gray-900">تقرير الإيرادات</p>
                            <p class="text-sm text-gray-600">تحليل مفصل للإيرادات</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('merchant.analytics.customers') }}" 
                   class="group p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-all duration-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="mr-3">
                            <p class="font-semibold text-gray-900">تحليل العملاء</p>
                            <p class="text-sm text-gray-600">إحصائيات العملاء</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('merchant.analytics.operations') }}" 
                   class="group p-4 border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition-all duration-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="mr-3">
                            <p class="font-semibold text-gray-900">تحليل العمليات</p>
                            <p class="text-sm text-gray-600">أداء الحجوزات</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('merchant.analytics.real-time') }}" 
                   class="group p-4 border border-gray-200 rounded-lg hover:border-red-300 hover:bg-red-50 transition-all duration-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-lg group-hover:bg-red-200 transition-colors">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="mr-3">
                            <p class="font-semibold text-gray-900">البيانات المباشرة</p>
                            <p class="text-sm text-gray-600">تحديثات لحظية</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">النشاط الأخير</h2>
            <div class="space-y-4">
                @if(isset($recent_activities) && $recent_activities->count() > 0)
                    @foreach($recent_activities as $activity)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="p-2 bg-blue-100 rounded-lg ml-3">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $activity['description'] }}</p>
                                <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500">لا يوجد نشاط حديث</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Chart Placeholder -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">الإيرادات الشهرية</h2>
            <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <p class="text-gray-500 font-medium">الرسم البياني للإيرادات</p>
                    <p class="text-gray-400 text-sm">سيتم إضافة البيانات قريباً</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection