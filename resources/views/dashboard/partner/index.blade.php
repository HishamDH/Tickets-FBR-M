@extends('layouts.app')

@section('title', 'لوحة تحكم الشريك')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-green-100" dir="rtl">
    <!-- Hero Header -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-10 right-10 w-32 h-32 bg-white rounded-full opacity-10 animate-pulse"></div>
            <div class="absolute bottom-10 left-10 w-24 h-24 bg-white rounded-full opacity-15 animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute top-20 left-1/3 w-16 h-16 bg-white rounded-full opacity-10 animate-pulse" style="animation-delay: 2s;"></div>
        </div>
        
        <div class="relative container mx-auto px-4 py-12">
            <div class="flex items-center justify-between text-white">
                <div>
                    <h1 class="text-4xl font-bold mb-2">مرحباً بك، {{ $partner->company_name ?? 'شريك' }} 🤝</h1>
                    <p class="text-green-100 text-lg">متابعة شراكاتك وعمولاتك بكل سهولة ووضوح</p>
                </div>
                <div class="hidden md:block">
                    <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 -mt-16 relative z-10">
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
                        <p class="text-3xl font-bold bg-gradient-to-r from-green-600 to-green-700 bg-clip-text text-transparent">{{ number_format($stats['total_merchants'] ?? 0) }}</p>
                        <p class="text-xs text-green-600 font-medium">{{ $stats['active_merchants'] ?? 0 }} نشط</p>
                    </div>
                </div>
            </div>

            <!-- إجمالي العمولات -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-blue-100 hover:shadow-2xl transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="p-4 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="mr-4 flex-1">
                        <p class="text-sm font-medium text-gray-600 mb-1">إجمالي العمولات</p>
                        <p class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-700 bg-clip-text text-transparent">{{ number_format($stats['partner_commission'] ?? 0, 2) }} ريال</p>
                        <p class="text-xs text-blue-600 font-medium">{{ number_format($stats['monthly_growth'] ?? 0, 2) }}% نمو شهري</p>
                    </div>
                </div>
            </div>

            <!-- إجمالي الحجوزات -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-purple-100 hover:shadow-2xl transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="p-4 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 text-white group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <p class="text-sm font-medium text-gray-600">إجمالي الحجوزات</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_bookings'] ?? 0) }}</p>
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
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($partner->commission_rate ?? 10, 1) }}%</p>
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
                    @forelse($topMerchants->take(5) as $merchant)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-200">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                                    {{ substr($merchant->business_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $merchant->business_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $merchant->contact_email }}</p>
                                    <p class="text-xs text-gray-500">{{ $merchant->bookings_count ?? 0 }} حجز • {{ $merchant->services_count ?? 0 }} خدمة نشطة</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <p class="font-semibold text-gray-900">{{ number_format($merchant->total_revenue ?? 0, 2) }} ريال</p>
                                <p class="text-sm text-blue-600">عمولة: {{ number_format(($merchant->total_revenue ?? 0) * ($merchant->commission_rate ?? 10) / 100, 2) }} ريال</p>
                                <p class="text-xs text-green-600">{{ number_format($merchant->commission_rate ?? 10, 1) }}% معدل العمولة</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-8">لا توجد بيانات تجار حتى الآن</p>
                    @endforelse
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
                    @forelse($recentBookings->take(5) as $booking)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 text-sm">حجز #{{ $booking->id }}</p>
                                    <p class="text-xs text-gray-600">{{ $booking->service->name ?? 'خدمة' }} - {{ $booking->merchant->business_name ?? '' }}</p>
                                    <p class="text-xs text-gray-500">{{ $booking->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-semibold text-green-600">{{ number_format($booking->total_amount ?? 0, 2) }} ريال</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-8">لا توجد أنشطة حديثة</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('partner.merchants') }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-blue-700 transition duration-200">
                إدارة التجار
            </a>
            <a href="{{ route('partner.commissions') }}" 
               class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-green-700 transition duration-200">
                تقارير العمولات
            </a>
            <a href="{{ route('partner.analytics.index') }}" 
               class="bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-purple-700 transition duration-200">
                التحليلات المتقدمة
            </a>
            <a href="{{ route('partner.reports') }}" 
               class="bg-yellow-600 text-white px-6 py-3 rounded-lg font-semibold text-center hover:bg-yellow-700 transition duration-200">
                تقرير النمو
            </a>
        </div>
    </div>
</div>
@endsection
