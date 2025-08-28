@extends('layouts.app')

@section('title', 'لوحة تحكم الشريك')

@section('content')
<div class="min-h-screen bg-gray-50" dir="rtl">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">مرحباً، {{ $partner->company_name }}</h1>
            <p class="text-gray-600">متابعة شراكاتك وعمولاتك</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- إجمالي التجار -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-600">إجمالي التجار</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_merchants']) }}</p>
                        <p class="text-xs text-green-600">{{ $stats['active_merchants'] }} نشط</p>
                    </div>
                </div>
            </div>

            <!-- إجمالي العمولات -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-600">إجمالي العمولات</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_commissions'], 2) }} ريال</p>
                        <p class="text-xs text-blue-600">{{ number_format($stats['this_month_commissions'], 2) }} هذا الشهر</p>
                    </div>
                </div>
            </div>

            <!-- إجمالي الحجوزات -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-600">إجمالي الحجوزات</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_bookings']) }}</p>
                        <p class="text-xs text-purple-600">من شبكة التجار</p>
                    </div>
                </div>
            </div>

            <!-- معدل العمولة -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-600">معدل العمولة</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['avg_commission_rate'], 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performing Merchants -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">أفضل التجار أداءً</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($topMerchants->take(5) as $merchant)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-200">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                                    {{ substr($merchant->business_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $merchant->business_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $merchant->contact_email }}</p>
                                    <p class="text-xs text-gray-500">{{ $merchant->total_bookings }} حجز • {{ $merchant->active_services }} خدمة نشطة</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <p class="font-semibold text-gray-900">{{ number_format($merchant->total_revenue ?? 0, 2) }} ريال</p>
                                <p class="text-sm text-blue-600">عمولة: {{ number_format($merchant->total_commissions ?? 0, 2) }} ريال</p>
                                <p class="text-xs text-green-600">{{ number_format($merchant->commission_rate ?? 0, 1) }}% معدل العمولة</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Commission Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Monthly Commissions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">العمولات الشهرية</h3>
                <div class="h-64 flex items-center justify-center">
                    <div class="text-center w-full">
                        @if($monthlyCommissions->count() > 0)
                            <div class="space-y-3">
                                @foreach($monthlyCommissions as $month)
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-700">الشهر {{ $month->month }}</span>
                                        <div class="text-left">
                                            <span class="text-sm font-semibold text-gray-900">{{ number_format($month->total_commissions, 2) }} ريال</span>
                                            <div class="text-xs text-gray-500">من {{ $month->bookings_count }} حجز</div>
                                        </div>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" 
                                             style="width: {{ $monthlyCommissions->max('total_commissions') > 0 ? ($month->total_commissions / $monthlyCommissions->max('total_commissions')) * 100 : 0 }}%"></div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">لا توجد بيانات متاحة</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Partner Growth -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">نمو الشراكة</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">تجار جدد هذا الشهر</p>
                            <p class="text-sm text-gray-600">مقارنة بالشهر السابق</p>
                        </div>
                        <div class="text-left">
                            <p class="text-xl font-semibold text-green-600">+{{ $stats['new_merchants_this_month'] ?? 0 }}</p>
                            <p class="text-xs text-green-500">
                                @if(($stats['merchants_growth_rate'] ?? 0) > 0)
                                    ↗ +{{ number_format($stats['merchants_growth_rate'] ?? 0, 1) }}%
                                @else
                                    ↘ {{ number_format($stats['merchants_growth_rate'] ?? 0, 1) }}%
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">نمو العمولات</p>
                            <p class="text-sm text-gray-600">مقارنة بالشهر السابق</p>
                        </div>
                        <div class="text-left">
                            <p class="text-xl font-semibold text-blue-600">{{ number_format($stats['commission_growth_rate'] ?? 0, 1) }}%</p>
                            <p class="text-xs text-blue-500">
                                @if(($stats['commission_growth_rate'] ?? 0) > 0)
                                    ↗ نمو إيجابي
                                @else
                                    ↘ تراجع
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">متوسط العمولة لكل حجز</p>
                            <p class="text-sm text-gray-600">جميع التجار</p>
                        </div>
                        <div class="text-left">
                            <p class="text-xl font-semibold text-purple-600">{{ number_format($stats['avg_commission_per_booking'] ?? 0, 2) }} ريال</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">الأنشطة الأخيرة</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($recentActivities->take(5) as $activity)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                                    @if($activity->type === 'booking')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    @elseif($activity->type === 'commission')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 text-sm">{{ $activity->description }}</p>
                                    <p class="text-xs text-gray-600">{{ $activity->merchant_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="text-left">
                                @if(isset($activity->commission_amount))
                                    <p class="text-sm font-semibold text-green-600">+{{ number_format($activity->commission_amount, 2) }} ريال</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('partner.dashboard.merchants') }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-blue-700 transition duration-200">
                إدارة التجار
            </a>
            <a href="{{ route('partner.dashboard.commissions') }}" 
               class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-green-700 transition duration-200">
                تقارير العمولات
            </a>
            <a href="{{ route('partner.dashboard.analytics') }}" 
               class="bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-purple-700 transition duration-200">
                التحليلات المتقدمة
            </a>
            <a href="{{ route('partner.dashboard.growth-report') }}" 
               class="bg-yellow-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-yellow-700 transition duration-200">
                تقرير النمو
            </a>
        </div>
    </div>
</div>
@endsection
