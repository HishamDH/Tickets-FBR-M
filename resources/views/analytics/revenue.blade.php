@extends('layouts.app')

@section('title', 'تحليل الإيرادات')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
<style>
    .chart-container {
        position: relative;
        height: 400px;
        width: 100%;
    }
    .metric-card {
        transition: all 0.3s ease;
    }
    .metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 20px -5px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 py-8" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl mb-8 p-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-800 to-blue-600 bg-clip-text text-transparent mb-2">
                        💰 تحليل الإيرادات المتقدم
                    </h1>
                    <p class="text-slate-600 text-lg">رؤى مالية مفصلة وتنبؤات دقيقة للإيرادات</p>
                </div>
                
                <!-- Controls -->
                <div class="mt-6 lg:mt-0 flex flex-wrap gap-4">
                    <select id="timeRange" class="rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="7" {{ $period == '7' ? 'selected' : '' }}>آخر 7 أيام</option>
                        <option value="30" {{ $period == '30' ? 'selected' : '' }}>آخر 30 يوم</option>
                        <option value="90" {{ $period == '90' ? 'selected' : '' }}>آخر 3 أشهر</option>
                        <option value="365" {{ $period == '365' ? 'selected' : '' }}>آخر سنة</option>
                    </select>
                    
                    <select id="granularity" class="rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="daily">يومي</option>
                        <option value="weekly">أسبوعي</option>
                        <option value="monthly">شهري</option>
                    </select>
                    
                    <button onclick="exportReport()" class="bg-green-600 text-white px-6 py-2 rounded-xl font-medium hover:bg-green-700 transition-colors duration-200">
                        📊 تصدير التقرير
                    </button>
                    
                    <a href="{{ route('merchant.analytics.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded-xl font-medium hover:bg-gray-700 transition-colors duration-200">
                        ← العودة للوحة الرئيسية
                    </a>
                </div>
            </div>
        </div>

        <!-- Revenue Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Revenue -->
            <div class="metric-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">💰</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">إجمالي الإيرادات</div>
                        <div class="text-3xl font-bold text-slate-800">{{ number_format($data['metrics']['total_revenue'], 0) }} ر.س</div>
                        <div class="text-sm {{ $data['metrics']['revenue_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $data['metrics']['revenue_growth'] >= 0 ? '↗️' : '↘️' }} {{ abs($data['metrics']['revenue_growth']) }}% من الفترة السابقة
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: 100%"></div>
                </div>
            </div>

            <!-- Platform Commission -->
            <div class="metric-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">💵</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">عمولة المنصة</div>
                        <div class="text-3xl font-bold text-slate-800">{{ number_format($data['metrics']['platform_commission'], 0) }} ر.س</div>
                        <div class="text-sm text-slate-600">
                            {{ number_format(($data['metrics']['platform_commission'] / $data['metrics']['total_revenue']) * 100, 1) }}% من إجمالي الإيرادات
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ min(100, ($data['metrics']['platform_commission'] / $data['metrics']['total_revenue']) * 100 * 10) }}%"></div>
                </div>
            </div>

            <!-- Average Transaction Value -->
            <div class="metric-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">📊</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">متوسط قيمة المعاملة</div>
                        <div class="text-3xl font-bold text-slate-800">{{ number_format($data['metrics']['avg_transaction_value'], 0) }} ر.س</div>
                        <div class="text-sm {{ $data['metrics']['atv_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $data['metrics']['atv_growth'] >= 0 ? '↗️' : '↘️' }} {{ abs($data['metrics']['atv_growth']) }}% نمو
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ min(100, ($data['metrics']['avg_transaction_value'] / 1000) * 100) }}%"></div>
                </div>
            </div>

            <!-- Revenue Per User -->
            <div class="metric-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">👤</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">الإيراد لكل مستخدم</div>
                        <div class="text-3xl font-bold text-slate-800">{{ number_format($data['metrics']['revenue_per_user'], 0) }} ر.س</div>
                        <div class="text-sm {{ $data['metrics']['rpu_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $data['metrics']['rpu_growth'] >= 0 ? '↗️' : '↘️' }} {{ abs($data['metrics']['rpu_growth']) }}% نمو
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-orange-600 h-2 rounded-full" style="width: {{ min(100, ($data['metrics']['revenue_per_user'] / 500) * 100) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Revenue Trend Chart -->
        <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-slate-800">📈 اتجاه الإيرادات</h3>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>
                        <span class="text-sm text-slate-600">الإيرادات الفعلية</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                        <span class="text-sm text-slate-600">التوقعات</span>
                    </div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>

        <!-- Revenue Breakdown Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue by Service Type -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">🎯 الإيرادات حسب نوع الخدمة</h3>
                <div class="chart-container">
                    <canvas id="revenueByServiceChart"></canvas>
                </div>
                <div class="mt-4 space-y-2">
                    @foreach($data['charts']['revenue_by_service']['breakdown'] as $service => $amount)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">{{ $service }}</span>
                        <span class="font-semibold text-slate-800">{{ number_format($amount, 0) }} ر.س</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Revenue by Time of Day -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">⏰ الإيرادات حسب وقت اليوم</h3>
                <div class="chart-container">
                    <canvas id="revenueByTimeChart"></canvas>
                </div>
                <div class="mt-4 text-center">
                    <div class="text-sm text-slate-600">ساعة الذروة</div>
                    <div class="text-lg font-bold text-slate-800">{{ $data['charts']['revenue_by_hour']['peak_hour'] }}:00</div>
                    <div class="text-sm text-slate-600">{{ number_format($data['charts']['revenue_by_hour']['peak_revenue'], 0) }} ر.س</div>
                </div>
            </div>
        </div>

        <!-- Forecasting Section -->
        <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6 mb-8">
            <h3 class="text-2xl font-bold text-slate-800 mb-6">🔮 التنبؤات المالية</h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Revenue Forecast Chart -->
                <div class="lg:col-span-2">
                    <h4 class="text-lg font-semibold text-slate-700 mb-4">توقعات الإيرادات للشهر القادم</h4>
                    <div class="chart-container">
                        <canvas id="revenueForecastChart"></canvas>
                    </div>
                </div>
                
                <!-- Forecast Metrics -->
                <div class="space-y-6">
                    <div class="bg-blue-50 rounded-2xl p-4">
                        <div class="text-sm text-blue-600 mb-1">التنبؤ للشهر القادم</div>
                        <div class="text-2xl font-bold text-blue-800">{{ number_format($data['forecast']['next_month'], 0) }} ر.س</div>
                        <div class="text-sm text-blue-600">{{ $data['forecast']['confidence'] }}% موثوقية</div>
                    </div>
                    
                    <div class="bg-green-50 rounded-2xl p-4">
                        <div class="text-sm text-green-600 mb-1">النمو المتوقع</div>
                        <div class="text-2xl font-bold text-green-800">{{ $data['forecast']['growth_rate'] }}%</div>
                        <div class="text-sm text-green-600">مقارنة بالشهر الحالي</div>
                    </div>
                    
                    <div class="bg-purple-50 rounded-2xl p-4">
                        <div class="text-sm text-purple-600 mb-1">الهدف المطلوب</div>
                        <div class="text-2xl font-bold text-purple-800">{{ number_format($data['forecast']['target'], 0) }} ر.س</div>
                        <div class="text-sm text-purple-600">{{ $data['forecast']['target_achievement'] }}% من الهدف</div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <div class="text-sm text-gray-600 mb-1">السيناريو المتشائم</div>
                        <div class="text-2xl font-bold text-gray-800">{{ number_format($data['forecast']['worst_case'], 0) }} ر.س</div>
                        <div class="text-sm text-gray-600">أقل تقدير محتمل</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performing Segments -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Top Revenue Sources -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">🏆 أعلى مصادر الإيرادات</h3>
                <div class="space-y-4">
                    @foreach($data['top_revenue_sources'] as $index => $source)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-{{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : 'bronze') }}-100 rounded-full flex items-center justify-center mr-3">
                                <span class="font-bold text-{{ $index == 0 ? 'yellow' : ($index == 1 ? 'gray' : 'orange') }}-600">{{ $index + 1 }}</span>
                            </div>
                            <div>
                                <div class="font-semibold text-slate-800">{{ $source['name'] }}</div>
                                <div class="text-sm text-slate-600">{{ $source['transactions'] }} معاملة</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-slate-800">{{ number_format($source['revenue'], 0) }} ر.س</div>
                            <div class="text-sm text-slate-600">{{ $source['percentage'] }}%</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Revenue Trends Analysis -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">📊 تحليل الاتجاهات</h3>
                <div class="space-y-6">
                    @foreach($data['trends_analysis'] as $trend)
                    <div class="border-r-4 border-{{ $trend['type'] === 'positive' ? 'green' : ($trend['type'] === 'negative' ? 'red' : 'yellow') }}-400 pr-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-slate-800">{{ $trend['title'] }}</h4>
                            <span class="text-{{ $trend['type'] === 'positive' ? 'green' : ($trend['type'] === 'negative' ? 'red' : 'yellow') }}-600 font-bold">
                                {{ $trend['change'] }}%
                            </span>
                        </div>
                        <p class="text-sm text-slate-600">{{ $trend['description'] }}</p>
                        @if(isset($trend['action']))
                        <div class="mt-2">
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">💡 {{ $trend['action'] }}</span>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Financial Health Indicators -->
        <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
            <h3 class="text-2xl font-bold text-slate-800 mb-6">💊 مؤشرات الصحة المالية</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($data['financial_health'] as $indicator)
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-4 relative">
                        <svg class="transform -rotate-90 w-20 h-20">
                            <circle cx="40" cy="40" r="36" stroke="currentColor" stroke-width="8" fill="transparent" class="text-gray-200"/>
                            <circle cx="40" cy="40" r="36" stroke="currentColor" stroke-width="8" fill="transparent" 
                                class="text-{{ $indicator['score'] >= 80 ? 'green' : ($indicator['score'] >= 60 ? 'yellow' : 'red') }}-500"
                                stroke-dasharray="{{ 2 * 3.14159 * 36 }}" 
                                stroke-dashoffset="{{ 2 * 3.14159 * 36 * (1 - $indicator['score'] / 100) }}"
                                stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-lg font-bold text-slate-800">{{ $indicator['score'] }}%</span>
                        </div>
                    </div>
                    <h4 class="font-semibold text-slate-800 mb-1">{{ $indicator['name'] }}</h4>
                    <p class="text-sm text-slate-600">{{ $indicator['description'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Chart data from backend
const chartsData = @json($data['charts']);

document.addEventListener('DOMContentLoaded', function() {
    initializeRevenueTrendChart();
    initializeRevenueByServiceChart();
    initializeRevenueByTimeChart();
    initializeRevenueForecastChart();
    setupEventListeners();
});

function initializeRevenueTrendChart() {
    const ctx = document.getElementById('revenueTrendChart').getContext('2d');
    const chartData = chartsData.revenue_trend;
    
    new Chart(ctx, {
        type: 'line',
        data: chartData.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'day'
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('ar-SA') + ' ر.س';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            family: 'Cairo, sans-serif'
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString('ar-SA') + ' ر.س';
                        }
                    }
                }
            }
        }
    });
}

function initializeRevenueByServiceChart() {
    const ctx = document.getElementById('revenueByServiceChart').getContext('2d');
    const chartData = chartsData.revenue_by_service;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: chartData.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            family: 'Cairo, sans-serif'
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed.toLocaleString('ar-SA') + ' ر.س';
                        }
                    }
                }
            }
        }
    });
}

function initializeRevenueByTimeChart() {
    const ctx = document.getElementById('revenueByTimeChart').getContext('2d');
    const chartData = chartsData.revenue_by_hour;
    
    new Chart(ctx, {
        type: 'bar',
        data: chartData.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('ar-SA') + ' ر.س';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'الإيرادات: ' + context.parsed.y.toLocaleString('ar-SA') + ' ر.س';
                        }
                    }
                }
            }
        }
    });
}

function initializeRevenueForecastChart() {
    const ctx = document.getElementById('revenueForecastChart').getContext('2d');
    const chartData = chartsData.revenue_forecast;
    
    new Chart(ctx, {
        type: 'line',
        data: chartData.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString('ar-SA') + ' ر.س';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            family: 'Cairo, sans-serif'
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString('ar-SA') + ' ر.س';
                        }
                    }
                }
            }
        }
    });
}

function setupEventListeners() {
    document.getElementById('timeRange').addEventListener('change', function() {
        updateCharts();
    });
    
    document.getElementById('granularity').addEventListener('change', function() {
        updateCharts();
    });
}

function updateCharts() {
    const timeRange = document.getElementById('timeRange').value;
    const granularity = document.getElementById('granularity').value;
    
    // Show loading
    showLoading();
    
    fetch(`{{ route('merchant.analytics.revenue') }}?period=${timeRange}&granularity=${granularity}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update charts with new data
        location.reload(); // Temporary - should update charts dynamically
    })
    .catch(error => {
        console.error('Error updating charts:', error);
        hideLoading();
    });
}

function exportReport() {
    const timeRange = document.getElementById('timeRange').value;
    const granularity = document.getElementById('granularity').value;
    const url = `{{ route('merchant.analytics.export') }}?type=revenue&period=${timeRange}&granularity=${granularity}&format=pdf`;
    window.open(url, '_blank');
}

function showLoading() {
    const loading = document.createElement('div');
    loading.id = 'loadingOverlay';
    loading.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    loading.innerHTML = '<div class="bg-white rounded-lg p-6"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div><p class="mt-4 text-center">جاري تحديث البيانات...</p></div>';
    document.body.appendChild(loading);
}

function hideLoading() {
    const loading = document.getElementById('loadingOverlay');
    if (loading) {
        loading.remove();
    }
}
</script>
@endpush
@endsection
