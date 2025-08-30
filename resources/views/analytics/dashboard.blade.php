@extends('layouts.app')

@section('title', 'لوحة التحليلات')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<style>
    .chart-container {
        position: relative;
        height: 400px;
        width: 100%;
    }
    .kpi-card {
        transition: all 0.3s ease;
    }
    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .trend-up { color: #10b981; }
    .trend-down { color: #ef4444; }
    .trend-stable { color: #6b7280; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 py-8" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl mb-8 p-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent mb-2">
                        📊 لوحة التحليلات المتقدمة
                    </h1>
                    <p class="text-slate-600 text-lg">رؤى عميقة وتحليلات شاملة لأداء المنصة</p>
                </div>
                
                <!-- Controls -->
                <div class="mt-6 lg:mt-0 flex flex-wrap gap-4">
                    <select id="periodSelect" class="rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="7" {{ $period == '7' ? 'selected' : '' }}>آخر 7 أيام</option>
                        <option value="30" {{ $period == '30' ? 'selected' : '' }}>آخر 30 يوم</option>
                        <option value="90" {{ $period == '90' ? 'selected' : '' }}>آخر 3 أشهر</option>
                        <option value="365" {{ $period == '365' ? 'selected' : '' }}>آخر سنة</option>
                    </select>
                    
                    <select id="comparisonSelect" class="rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="previous" {{ $comparison == 'previous' ? 'selected' : '' }}>مقارنة بالفترة السابقة</option>
                        <option value="year_ago" {{ $comparison == 'year_ago' ? 'selected' : '' }}>مقارنة بنفس الفترة العام الماضي</option>
                    </select>
                    
                    <button onclick="refreshData()" class="bg-blue-600 text-white px-6 py-2 rounded-xl font-medium hover:bg-blue-700 transition-colors duration-200">
                        🔄 تحديث البيانات
                    </button>
                    
                    <div class="dropdown relative">
                        <button class="bg-green-600 text-white px-6 py-2 rounded-xl font-medium hover:bg-green-700 transition-colors duration-200 dropdown-toggle">
                            📥 تصدير التقرير
                        </button>
                        <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 z-50 hidden">
                            <a href="#" onclick="exportReport('pdf')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-xl">📄 تصدير PDF</a>
                            <a href="#" onclick="exportReport('excel')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">📊 تصدير Excel</a>
                            <a href="#" onclick="exportReport('csv')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-xl">📋 تصدير CSV</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Alerts -->
        @if(isset($data['alerts']) && count($data['alerts']) > 0)
        <div class="mb-8">
            @foreach($data['alerts'] as $alert)
                <div class="bg-{{ $alert['type'] === 'warning' ? 'yellow' : 'red' }}-50 border border-{{ $alert['type'] === 'warning' ? 'yellow' : 'red' }}-200 rounded-2xl p-4 mb-4">
                    <div class="flex items-center">
                        <span class="text-2xl mr-3">{{ $alert['icon'] }}</span>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-{{ $alert['type'] === 'warning' ? 'yellow' : 'red' }}-800">{{ $alert['title'] }}</h4>
                            <p class="text-{{ $alert['type'] === 'warning' ? 'yellow' : 'red' }}-600">{{ $alert['message'] }}</p>
                        </div>
                        @if(isset($alert['action_url']))
                        <a href="{{ $alert['action_url'] }}" class="bg-{{ $alert['type'] === 'warning' ? 'yellow' : 'red' }}-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-{{ $alert['type'] === 'warning' ? 'yellow' : 'red' }}-700">
                            {{ $alert['action_text'] }}
                        </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <!-- KPIs Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
            <!-- Total Revenue KPI -->
            <div class="kpi-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">💰</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">إجمالي الإيرادات</div>
                        <div class="text-2xl font-bold text-slate-800">{{ number_format($data['kpis']['total_revenue']['current'], 0) }}</div>
                        <div class="text-xs {{ $data['kpis']['total_revenue']['change'] >= 0 ? 'trend-up' : 'trend-down' }}">
                            {{ $data['kpis']['total_revenue']['change'] >= 0 ? '↗️' : '↘️' }} {{ abs($data['kpis']['total_revenue']['change']) }}%
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, ($data['kpis']['total_revenue']['current'] / max($data['kpis']['total_revenue']['current'], $data['kpis']['total_revenue']['previous'])) * 100) }}%"></div>
                </div>
            </div>

            <!-- Total Bookings KPI -->
            <div class="kpi-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">📅</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">إجمالي الحجوزات</div>
                        <div class="text-2xl font-bold text-slate-800">{{ number_format($data['kpis']['total_bookings']['current'], 0) }}</div>
                        <div class="text-xs {{ $data['kpis']['total_bookings']['change'] >= 0 ? 'trend-up' : 'trend-down' }}">
                            {{ $data['kpis']['total_bookings']['change'] >= 0 ? '↗️' : '↘️' }} {{ abs($data['kpis']['total_bookings']['change']) }}%
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ min(100, ($data['kpis']['total_bookings']['current'] / max($data['kpis']['total_bookings']['current'], $data['kpis']['total_bookings']['previous'])) * 100) }}%"></div>
                </div>
            </div>

            <!-- Active Merchants KPI -->
            <div class="kpi-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">🏪</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">التجار النشطون</div>
                        <div class="text-2xl font-bold text-slate-800">{{ number_format($data['kpis']['active_merchants']['current'], 0) }}</div>
                        <div class="text-xs {{ $data['kpis']['active_merchants']['change'] >= 0 ? 'trend-up' : 'trend-down' }}">
                            {{ $data['kpis']['active_merchants']['change'] >= 0 ? '↗️' : '↘️' }} {{ abs($data['kpis']['active_merchants']['change']) }}%
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ min(100, ($data['kpis']['active_merchants']['current'] / max($data['kpis']['active_merchants']['current'], $data['kpis']['active_merchants']['previous'])) * 100) }}%"></div>
                </div>
            </div>

            <!-- Average Booking Value KPI -->
            <div class="kpi-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">💵</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">متوسط قيمة الحجز</div>
                        <div class="text-2xl font-bold text-slate-800">{{ number_format($data['kpis']['avg_booking_value']['current'], 0) }}</div>
                        <div class="text-xs {{ $data['kpis']['avg_booking_value']['change'] >= 0 ? 'trend-up' : 'trend-down' }}">
                            {{ $data['kpis']['avg_booking_value']['change'] >= 0 ? '↗️' : '↘️' }} {{ abs($data['kpis']['avg_booking_value']['change']) }}%
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-orange-600 h-2 rounded-full" style="width: {{ min(100, ($data['kpis']['avg_booking_value']['current'] / max($data['kpis']['avg_booking_value']['current'], $data['kpis']['avg_booking_value']['previous'])) * 100) }}%"></div>
                </div>
            </div>

            <!-- Customer Satisfaction KPI -->
            <div class="kpi-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">⭐</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">رضا العملاء</div>
                        <div class="text-2xl font-bold text-slate-800">{{ number_format($data['kpis']['customer_satisfaction']['current'], 1) }}/5</div>
                        <div class="text-xs {{ $data['kpis']['customer_satisfaction']['change'] >= 0 ? 'trend-up' : 'trend-down' }}">
                            {{ $data['kpis']['customer_satisfaction']['change'] >= 0 ? '↗️' : '↘️' }} {{ abs($data['kpis']['customer_satisfaction']['change']) }}%
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-yellow-600 h-2 rounded-full" style="width: {{ ($data['kpis']['customer_satisfaction']['current'] / 5) * 100 }}%"></div>
                </div>
            </div>

            <!-- Conversion Rate KPI -->
            <div class="kpi-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">🎯</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">معدل التحويل</div>
                        <div class="text-2xl font-bold text-slate-800">{{ number_format($data['kpis']['conversion_rate']['current'], 1) }}%</div>
                        <div class="text-xs {{ $data['kpis']['conversion_rate']['change'] >= 0 ? 'trend-up' : 'trend-down' }}">
                            {{ $data['kpis']['conversion_rate']['change'] >= 0 ? '↗️' : '↘️' }} {{ abs($data['kpis']['conversion_rate']['change']) }}%
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min(100, $data['kpis']['conversion_rate']['current']) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue Chart -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-800">📈 اتجاه الإيرادات</h3>
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <select id="revenueChartPeriod" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="day">يومي</option>
                            <option value="week">أسبوعي</option>
                            <option value="month">شهري</option>
                        </select>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Bookings Chart -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-800">📊 إحصائيات الحجوزات</h3>
                </div>
                <div class="chart-container">
                    <canvas id="bookingsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Secondary Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Top Services -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">🏆 أفضل الخدمات</h3>
                <div class="chart-container">
                    <canvas id="servicesChart"></canvas>
                </div>
            </div>

            <!-- Top Merchants -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">🥇 أفضل التجار</h3>
                <div class="chart-container">
                    <canvas id="merchantsChart"></canvas>
                </div>
            </div>

            <!-- Customer Satisfaction -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">😊 رضا العملاء</h3>
                <div class="chart-container">
                    <canvas id="satisfactionChart"></canvas>
                </div>
                <div class="mt-4 text-center">
                    <div class="text-3xl font-bold text-slate-800">{{ $data['charts']['satisfaction_gauge']['average_rating'] }}/5</div>
                    <div class="text-sm text-slate-600">من {{ $data['charts']['satisfaction_gauge']['total_ratings'] }} تقييم</div>
                </div>
            </div>
        </div>

        <!-- Insights Section -->
        @if(isset($data['insights']) && count($data['insights']) > 0)
        <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-8 mb-8">
            <h3 class="text-2xl font-bold text-slate-800 mb-6">💡 رؤى ذكية</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($data['insights'] as $insight)
                <div class="bg-{{ $insight['priority'] === 'success' ? 'green' : ($insight['priority'] === 'warning' ? 'yellow' : 'blue') }}-50 border border-{{ $insight['priority'] === 'success' ? 'green' : ($insight['priority'] === 'warning' ? 'yellow' : 'blue') }}-200 rounded-2xl p-6">
                    <div class="flex items-start">
                        <span class="text-3xl mr-4">{{ $insight['icon'] }}</span>
                        <div>
                            <h4 class="text-lg font-semibold text-slate-800 mb-2">{{ $insight['title'] }}</h4>
                            <p class="text-slate-600 text-sm mb-3">{{ $insight['description'] }}</p>
                            @if(isset($insight['action']))
                            <button class="bg-{{ $insight['priority'] === 'success' ? 'green' : ($insight['priority'] === 'warning' ? 'yellow' : 'blue') }}-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-{{ $insight['priority'] === 'success' ? 'green' : ($insight['priority'] === 'warning' ? 'yellow' : 'blue') }}-700">
                                {{ $insight['action'] }}
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Quick Links -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('analytics.revenue') }}" class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6 hover:shadow-2xl transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                        <span class="text-2xl">💰</span>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-slate-800">تحليل الإيرادات</h4>
                        <p class="text-sm text-slate-600">تحليل مفصل للإيرادات والتنبؤات</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('analytics.customers') }}" class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6 hover:shadow-2xl transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                        <span class="text-2xl">👥</span>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-slate-800">تحليل العملاء</h4>
                        <p class="text-sm text-slate-600">سلوك العملاء والتجزئة</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('analytics.merchants') }}" class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6 hover:shadow-2xl transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                        <span class="text-2xl">🏪</span>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-slate-800">أداء التجار</h4>
                        <p class="text-sm text-slate-600">مقاييس الأداء والجودة</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('analytics.operations') }}" class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6 hover:shadow-2xl transition-all duration-300 group">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-300">
                        <span class="text-2xl">⚙️</span>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-slate-800">العمليات التشغيلية</h4>
                        <p class="text-sm text-slate-600">أداء النظام والعمليات</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Chart data from backend
const chartsData = @json($data['charts']);

// Initialize charts
document.addEventListener('DOMContentLoaded', function() {
    initializeRevenueChart();
    initializeBookingsChart();
    initializeServicesChart();
    initializeMerchantsChart();
    initializeSatisfactionChart();
    setupEventListeners();
});

function initializeRevenueChart() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const chartData = chartsData.revenue_chart;
    
    new Chart(ctx, {
        type: chartData.type,
        data: chartData.data,
        options: {
            ...chartData.options,
            plugins: {
                ...chartData.options.plugins,
                legend: {
                    labels: {
                        font: {
                            family: 'Cairo, sans-serif'
                        }
                    }
                }
            }
        }
    });
}

function initializeBookingsChart() {
    const ctx = document.getElementById('bookingsChart').getContext('2d');
    const chartData = chartsData.bookings_chart;
    
    new Chart(ctx, {
        type: chartData.type,
        data: chartData.data,
        options: {
            ...chartData.options,
            plugins: {
                ...chartData.options.plugins,
                legend: {
                    labels: {
                        font: {
                            family: 'Cairo, sans-serif'
                        }
                    }
                }
            }
        }
    });
}

function initializeServicesChart() {
    const ctx = document.getElementById('servicesChart').getContext('2d');
    const chartData = chartsData.services_pie_chart;
    
    new Chart(ctx, {
        type: chartData.type,
        data: chartData.data,
        options: {
            ...chartData.options,
            plugins: {
                ...chartData.options.plugins,
                legend: {
                    labels: {
                        font: {
                            family: 'Cairo, sans-serif'
                        }
                    }
                }
            }
        }
    });
}

function initializeMerchantsChart() {
    const ctx = document.getElementById('merchantsChart').getContext('2d');
    const chartData = chartsData.merchants_bar_chart;
    
    new Chart(ctx, {
        type: chartData.type,
        data: chartData.data,
        options: {
            ...chartData.options,
            plugins: {
                ...chartData.options.plugins,
                legend: {
                    labels: {
                        font: {
                            family: 'Cairo, sans-serif'
                        }
                    }
                }
            }
        }
    });
}

function initializeSatisfactionChart() {
    const ctx = document.getElementById('satisfactionChart').getContext('2d');
    const chartData = chartsData.satisfaction_gauge;
    
    new Chart(ctx, {
        type: chartData.type,
        data: chartData.data,
        options: {
            ...chartData.options,
            plugins: {
                ...chartData.options.plugins,
                legend: {
                    labels: {
                        font: {
                            family: 'Cairo, sans-serif'
                        }
                    }
                }
            }
        }
    });
}

function setupEventListeners() {
    // Period selection change
    document.getElementById('periodSelect').addEventListener('change', function() {
        const period = this.value;
        const comparison = document.getElementById('comparisonSelect').value;
        updateDashboard(period, comparison);
    });
    
    // Comparison selection change
    document.getElementById('comparisonSelect').addEventListener('change', function() {
        const period = document.getElementById('periodSelect').value;
        const comparison = this.value;
        updateDashboard(period, comparison);
    });
    
    // Dropdown toggle
    document.querySelector('.dropdown-toggle').addEventListener('click', function() {
        document.querySelector('.dropdown-menu').classList.toggle('hidden');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.dropdown')) {
            document.querySelector('.dropdown-menu').classList.add('hidden');
        }
    });
}

function updateDashboard(period, comparison) {
    // Show loading state
    showLoading();
    
    // Fetch new data
    fetch(`{{ route('analytics.index') }}?period=${period}&comparison=${comparison}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Update charts and KPIs
        updateChartsAndKPIs(data);
        hideLoading();
    })
    .catch(error => {
        console.error('Error updating dashboard:', error);
        hideLoading();
        showError('حدث خطأ في تحديث البيانات');
    });
}

function refreshData() {
    const period = document.getElementById('periodSelect').value;
    const comparison = document.getElementById('comparisonSelect').value;
    updateDashboard(period, comparison);
}

function exportReport(format) {
    const period = document.getElementById('periodSelect').value;
    const url = `{{ route('analytics.export') }}?type=dashboard&format=${format}&period=${period}`;
    window.open(url, '_blank');
}

function showLoading() {
    // Add loading overlay
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

function showError(message) {
    // Show error toast
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

function updateChartsAndKPIs(data) {
    // This would update the charts and KPIs with new data
    // Implementation would depend on the specific chart library used
    location.reload(); // Temporary solution - reload the page
}

// Auto-refresh every 5 minutes
setInterval(refreshData, 300000);
</script>
@endpush
@endsection
