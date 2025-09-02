@extends('layouts.app')

@section('title', 'تقارير نقاط البيع - POS Reports')
@section('description', 'تقارير مفصلة لمبيعات نقاط البيع')

@push('styles')
<style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
    }
    
    .chart-container {
        height: 400px;
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Header -->
    <div class="bg-white shadow-lg border-b-4 border-blue-500 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center">
                        <span class="text-white text-2xl">📊</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">تقارير نقاط البيع</h1>
                        <p class="text-sm text-gray-600">تحليل مفصل للمبيعات والأداء</p>
                    </div>
                </div>
                
                <!-- Period Filter -->
                <div class="flex items-center space-x-3 space-x-reverse">
                    <select onchange="location.href='?period='+this.value" class="bg-white border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="today" {{ $period === 'today' ? 'selected' : '' }}>اليوم</option>
                        <option value="week" {{ $period === 'week' ? 'selected' : '' }}>هذا الأسبوع</option>
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>هذا الشهر</option>
                        <option value="year" {{ $period === 'year' ? 'selected' : '' }}>هذا العام</option>
                    </select>
                    
                    <a href="{{ route('pos.dashboard') }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 space-x-reverse transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span>العودة للنظام</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Sales -->
            <div class="stats-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">إجمالي المبيعات</p>
                        <p class="text-3xl font-bold">{{ number_format($analytics['total_sales'], 2) }}</p>
                        <p class="text-blue-100 text-xs">ريال سعودي</p>
                    </div>
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <span class="text-2xl">💰</span>
                    </div>
                </div>
            </div>

            <!-- Total Transactions -->
            <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-pink-100 text-sm font-medium">عدد المعاملات</p>
                        <p class="text-3xl font-bold">{{ number_format($analytics['total_transactions']) }}</p>
                        <p class="text-pink-100 text-xs">معاملة</p>
                    </div>
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <span class="text-2xl">🛒</span>
                    </div>
                </div>
            </div>

            <!-- Average Sale -->
            <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">متوسط البيع</p>
                        <p class="text-3xl font-bold">{{ number_format($analytics['average_sale'], 2) }}</p>
                        <p class="text-blue-100 text-xs">ريال سعودي</p>
                    </div>
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <span class="text-2xl">📈</span>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">عدد العملاء</p>
                        <p class="text-3xl font-bold">{{ number_format($analytics['total_customers']) }}</p>
                        <p class="text-green-100 text-xs">عميل</p>
                    </div>
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <span class="text-2xl">👥</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <!-- Payment Methods Chart -->
            <div class="chart-container">
                <h3 class="text-xl font-bold text-gray-800 mb-4">طرق الدفع</h3>
                <canvas id="paymentMethodsChart"></canvas>
            </div>

            <!-- Hourly Sales Chart -->
            <div class="chart-container">
                <h3 class="text-xl font-bold text-gray-800 mb-4">المبيعات حسب الساعة</h3>
                <canvas id="hourlySalesChart"></canvas>
            </div>
        </div>

        <!-- Detailed Payment Methods Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <span class="text-2xl mr-3">💳</span>
                تفاصيل طرق الدفع
            </h3>
            
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">طريقة الدفع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عدد المعاملات</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجمالي المبلغ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النسبة</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($analytics['payment_methods'] as $method => $data)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <div class="flex items-center">
                                    @if($method === 'cash')
                                        <span class="text-2xl mr-3">💵</span>
                                        نقدي
                                    @elseif($method === 'card')
                                        <span class="text-2xl mr-3">💳</span>
                                        بطاقة
                                    @elseif($method === 'mixed')
                                        <span class="text-2xl mr-3">💰</span>
                                        مختلط
                                    @else
                                        <span class="text-2xl mr-3">💸</span>
                                        {{ $method }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($data['count']) }} معاملة
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($data['total'], 2) }} ريال
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $analytics['total_sales'] > 0 ? number_format(($data['total'] / $analytics['total_sales']) * 100, 1) : 0 }}%
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                لا توجد بيانات للفترة المحددة
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Sales Preview -->
        @if(isset($analytics['sales']) && $analytics['sales']->count() > 0)
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800 flex items-center">
                    <span class="text-2xl mr-3">🛍️</span>
                    آخر المبيعات ({{ $analytics['sales']->count() }} معاملة)
                </h3>
                <a href="{{ route('pos.sales.history') }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all">
                    عرض الكل
                </a>
            </div>
            
            <div class="space-y-4">
                @foreach($analytics['sales']->take(10) as $sale)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-all">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <span class="text-lg">🛒</span>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $sale->offering->title ?? 'خدمة متعددة' }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $sale->user ? $sale->user->name : 'عميل غير مسجل' }}
                                        @if($sale->user && $sale->user->phone)
                                            - {{ $sale->user->phone }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <p class="text-lg font-bold text-green-600">{{ number_format($sale->total_amount, 2) }} ريال</p>
                            <p class="text-xs text-gray-500">{{ $sale->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Payment Methods Chart
    const paymentMethodsData = @json($analytics['payment_methods']);
    const paymentLabels = [];
    const paymentData = [];
    const paymentColors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'];
    
    let colorIndex = 0;
    Object.keys(paymentMethodsData).forEach(method => {
        let label = method;
        switch(method) {
            case 'cash': label = 'نقدي'; break;
            case 'card': label = 'بطاقة'; break;
            case 'mixed': label = 'مختلط'; break;
        }
        paymentLabels.push(label);
        paymentData.push(paymentMethodsData[method].total);
        colorIndex++;
    });
    
    const paymentCtx = document.getElementById('paymentMethodsChart').getContext('2d');
    new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: paymentLabels,
            datasets: [{
                data: paymentData,
                backgroundColor: paymentColors.slice(0, paymentLabels.length),
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    rtl: true,
                    textDirection: 'rtl'
                }
            }
        }
    });
    
    // Hourly Sales Chart
    const hourlySalesData = @json($analytics['hourly_sales']);
    const hourlyLabels = [];
    const hourlyData = [];
    
    for (let hour = 0; hour < 24; hour++) {
        const hourStr = hour.toString().padStart(2, '0');
        hourlyLabels.push(hourStr + ':00');
        hourlyData.push(hourlySalesData[hourStr] || 0);
    }
    
    const hourlyCtx = document.getElementById('hourlySalesChart').getContext('2d');
    new Chart(hourlyCtx, {
        type: 'line',
        data: {
            labels: hourlyLabels,
            datasets: [{
                label: 'المبيعات (ريال)',
                data: hourlyData,
                borderColor: '#4F46E5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#4F46E5',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('ar-SA') + ' ريال';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            elements: {
                point: {
                    hoverRadius: 8
                }
            }
        }
    });
});
</script>
@endpush
@endsection
