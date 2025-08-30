@extends('layouts.app')

@section('title', 'تقرير الإيرادات')

@section('content')
<div class="min-h-screen bg-gray-50" dir="rtl">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">تقرير الإيرادات</h1>
                <p class="text-gray-600">تحليل مفصل للإيرادات والأرباح</p>
            </div>
            
            <!-- Date Range Selector -->
            <div class="flex items-center space-x-4 space-x-reverse">
                <form method="GET" action="{{ route('merchant.dashboard.revenue-report') }}" class="flex items-center space-x-3 space-x-reverse">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">من تاريخ</label>
                        <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" 
                               class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">إلى تاريخ</label>
                        <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" 
                               class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <div class="pt-5">
                        <button type="submit" class="btn-primary px-4 py-2 text-sm">
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            فلترة
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            @php
                $totalRevenue = $dailyRevenue->sum('total_revenue');
                $totalBookings = $dailyRevenue->sum('bookings_count');
                $totalCommission = $dailyRevenue->sum('commission_amount');
                $totalNet = $dailyRevenue->sum('net_revenue');
                $averageOrderValue = $totalBookings > 0 ? $totalRevenue / $totalBookings : 0;
            @endphp

            <div class="glass-effect rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">إجمالي الإيرادات</p>
                        <p class="text-2xl font-bold gradient-text">{{ number_format($totalRevenue) }} ريال</p>
                        <p class="text-xs text-gray-500">{{ $totalBookings }} حجز</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-xl">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">صافي الأرباح</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($totalNet) }} ريال</p>
                        <p class="text-xs text-gray-500">بعد العمولات</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-xl">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">العمولات المدفوعة</p>
                        <p class="text-2xl font-bold text-red-600">{{ number_format($totalCommission) }} ريال</p>
                        <p class="text-xs text-gray-500">{{ $totalRevenue > 0 ? number_format(($totalCommission / $totalRevenue) * 100, 1) : 0 }}% من الإيرادات</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-xl">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">متوسط قيمة الحجز</p>
                        <p class="text-2xl font-bold text-purple-600">{{ number_format($averageOrderValue) }} ريال</p>
                        <p class="text-xs text-gray-500">لكل حجز</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-xl">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Daily Revenue Chart -->
            <div class="glass-effect rounded-xl p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    الإيرادات اليومية
                </h2>
                
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($dailyRevenue as $day)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($day->date)->format('Y/m/d') }}</span>
                            <span class="text-sm text-gray-500">{{ $day->bookings_count }} حجز</span>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-3 text-sm">
                            <div>
                                <span class="block text-gray-600">الإيرادات</span>
                                <span class="font-semibold text-blue-600">{{ number_format($day->total_revenue) }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-600">العمولة</span>
                                <span class="font-semibold text-red-600">{{ number_format($day->commission_amount) }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-600">الصافي</span>
                                <span class="font-semibold text-green-600">{{ number_format($day->net_revenue) }}</span>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        @php
                            $maxRevenue = $dailyRevenue->max('total_revenue');
                            $percentage = $maxRevenue > 0 ? ($day->total_revenue / $maxRevenue) * 100 : 0;
                        @endphp
                        <div class="mt-3 bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <div class="text-4xl text-gray-300 mb-2">📊</div>
                        <p class="text-gray-500">لا توجد بيانات للفترة المحددة</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Revenue by Service -->
            <div class="glass-effect rounded-xl p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    الإيرادات حسب الخدمة
                </h2>
                
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @forelse($revenueByService as $serviceData)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $serviceData['service']->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $serviceData['bookings_count'] }} حجز</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-green-600">{{ number_format($serviceData['net_revenue']) }} ريال</p>
                                <p class="text-xs text-gray-500">صافي</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-3 text-xs mb-3">
                            <div>
                                <span class="block text-gray-600">إجمالي</span>
                                <span class="font-semibold">{{ number_format($serviceData['total_revenue']) }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-600">عمولة</span>
                                <span class="font-semibold text-red-600">{{ number_format($serviceData['commission_amount']) }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-600">متوسط الحجز</span>
                                <span class="font-semibold">{{ number_format($serviceData['bookings_count'] > 0 ? $serviceData['total_revenue'] / $serviceData['bookings_count'] : 0) }}</span>
                            </div>
                        </div>
                        
                        <!-- Performance Bar -->
                        @php
                            $maxServiceRevenue = $revenueByService->max(function($item) { return $item['total_revenue']; });
                            $servicePercentage = $maxServiceRevenue > 0 ? ($serviceData['total_revenue'] / $maxServiceRevenue) * 100 : 0;
                        @endphp
                        <div class="bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full" 
                                 style="width: {{ $servicePercentage }}%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <div class="text-4xl text-gray-300 mb-2">🏪</div>
                        <p class="text-gray-500">لا توجد خدمات محققة لإيرادات</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Export and Actions -->
        <div class="glass-effect rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold mb-2">تصدير التقرير</h2>
                    <p class="text-gray-600">احفظ تقرير الإيرادات للفترة المحددة</p>
                </div>
                
                <div class="flex space-x-3 space-x-reverse">
                    <button onclick="exportReport('pdf')" class="flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        تصدير PDF
                    </button>
                    
                    <button onclick="exportReport('excel')" class="flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        تصدير Excel
                    </button>
                    
                    <button onclick="printReport()" class="flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        طباعة
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportReport(format) {
    const startDate = document.querySelector('input[name="start_date"]').value;
    const endDate = document.querySelector('input[name="end_date"]').value;
    
    const url = `{{ route('merchant.dashboard.revenue-report') }}?format=${format}&start_date=${startDate}&end_date=${endDate}`;
    
    // In a real implementation, this would trigger a download
    alert(`سيتم تصدير التقرير بصيغة ${format.toUpperCase()}`);
    console.log('Export URL:', url);
}

function printReport() {
    window.print();
}

// Auto-refresh report when date changes
document.addEventListener('DOMContentLoaded', function() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Could auto-submit form or show live preview
        });
    });
});
</script>
@endpush

@push('styles')
<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    .glass-effect {
        background: white !important;
        backdrop-filter: none !important;
        border: 1px solid #e5e7eb !important;
    }
}
</style>
@endpush
@endsection
