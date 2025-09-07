@extends('layouts.app')

@section('title', 'تحليل العملاء')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .chart-container {
        position: relative;
        height: 400px;
        width: 100%;
    }
    .customer-card {
        transition: all 0.3s ease;
    }
    .customer-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .segment-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .segment-vip { background-color: #fef3c7; color: #92400e; }
    .segment-regular { background-color: #dbeafe; color: #1e40af; }
    .segment-new { background-color: #d1fae5; color: #065f46; }
    .segment-inactive { background-color: #fecaca; color: #991b1b; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 py-8" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl mb-8 p-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-green-800 to-green-600 bg-clip-text text-transparent mb-2">
                        👥 تحليل العملاء المتقدم
                    </h1>
                    <p class="text-slate-600 text-lg">فهم عميق لسلوك العملاء وتجزئة الجمهور</p>
                </div>
                
                <!-- Controls -->
                <div class="mt-6 lg:mt-0 flex flex-wrap gap-4">
                    <select id="timeRange" class="rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="30" {{ $period == '30' ? 'selected' : '' }}>آخر 30 يوم</option>
                        <option value="90" {{ $period == '90' ? 'selected' : '' }}>آخر 3 أشهر</option>
                        <option value="180" {{ $period == '180' ? 'selected' : '' }}>آخر 6 أشهر</option>
                        <option value="365" {{ $period == '365' ? 'selected' : '' }}>آخر سنة</option>
                    </select>
                    
                    <select id="segmentFilter" class="rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="all">جميع الشرائح</option>
                        <option value="vip">عملاء VIP</option>
                        <option value="regular">عملاء عاديين</option>
                        <option value="new">عملاء جدد</option>
                        <option value="inactive">عملاء غير نشطين</option>
                    </select>
                    
                    <button onclick="exportCustomerReport()" class="bg-green-600 text-white px-6 py-2 rounded-xl font-medium hover:bg-green-700 transition-colors duration-200">
                        📊 تصدير التقرير
                    </button>
                    
                    <a href="{{ route('merchant.analytics.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded-xl font-medium hover:bg-gray-700 transition-colors duration-200">
                        ← العودة للوحة الرئيسية
                    </a>
                </div>
            </div>
        </div>

        <!-- Customer Overview Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Customers -->
            <div class="customer-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">👥</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">إجمالي العملاء</div>
                        <div class="text-3xl font-bold text-slate-800">{{ number_format($data['metrics']['total_customers']) }}</div>
                        <div class="text-sm {{ $data['metrics']['customer_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $data['metrics']['customer_growth'] >= 0 ? '↗️' : '↘️' }} {{ abs($data['metrics']['customer_growth']) }}% نمو
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: 100%"></div>
                </div>
            </div>

            <!-- Active Customers -->
            <div class="customer-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">✅</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">العملاء النشطون</div>
                        <div class="text-3xl font-bold text-slate-800">{{ number_format($data['metrics']['active_customers']) }}</div>
                        <div class="text-sm text-slate-600">
                            {{ number_format(($data['metrics']['active_customers'] / $data['metrics']['total_customers']) * 100, 1) }}% من الإجمالي
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($data['metrics']['active_customers'] / $data['metrics']['total_customers']) * 100 }}%"></div>
                </div>
            </div>

            <!-- Customer Lifetime Value -->
            <div class="customer-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">💎</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">القيمة الحياتية للعميل</div>
                        <div class="text-3xl font-bold text-slate-800">{{ number_format($data['metrics']['avg_clv']) }} ر.س</div>
                        <div class="text-sm {{ $data['metrics']['clv_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $data['metrics']['clv_growth'] >= 0 ? '↗️' : '↘️' }} {{ abs($data['metrics']['clv_growth']) }}% نمو
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ min(100, ($data['metrics']['avg_clv'] / 5000) * 100) }}%"></div>
                </div>
            </div>

            <!-- Customer Retention Rate -->
            <div class="customer-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">🔒</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">معدل الاحتفاظ</div>
                        <div class="text-3xl font-bold text-slate-800">{{ number_format($data['metrics']['retention_rate'], 1) }}%</div>
                        <div class="text-sm {{ $data['metrics']['retention_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $data['metrics']['retention_change'] >= 0 ? '↗️' : '↘️' }} {{ abs($data['metrics']['retention_change']) }}% تغيير
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-orange-600 h-2 rounded-full" style="width: {{ $data['metrics']['retention_rate'] }}%"></div>
                </div>
            </div>
        </div>

        <!-- Customer Segmentation -->
        <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6 mb-8">
            <h3 class="text-2xl font-bold text-slate-800 mb-6">🎯 تجزئة العملاء</h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Segmentation Chart -->
                <div>
                    <h4 class="text-lg font-semibold text-slate-700 mb-4">توزيع العملاء حسب الشريحة</h4>
                    <div class="chart-container">
                        <canvas id="customerSegmentationChart"></canvas>
                    </div>
                </div>
                
                <!-- Segment Details -->
                <div>
                    <h4 class="text-lg font-semibold text-slate-700 mb-4">تفاصيل الشرائح</h4>
                    <div class="space-y-4">
                        @foreach($data['segments'] as $segment)
                        <div class="border border-gray-200 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <span class="segment-badge segment-{{ strtolower($segment['name']) }}">
                                        {{ $segment['label'] }}
                                    </span>
                                    <span class="mr-3 text-2xl">{{ $segment['icon'] }}</span>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-slate-800">{{ number_format($segment['count']) }}</div>
                                    <div class="text-sm text-slate-600">{{ $segment['percentage'] }}%</div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-slate-600">متوسط الإنفاق:</span>
                                    <span class="font-semibold text-slate-800">{{ number_format($segment['avg_spending']) }} ر.س</span>
                                </div>
                                <div>
                                    <span class="text-slate-600">تكرار الزيارات:</span>
                                    <span class="font-semibold text-slate-800">{{ $segment['visit_frequency'] }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Behavior Analysis -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Customer Acquisition Trend -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">📈 اتجاه اكتساب العملاء</h3>
                <div class="chart-container">
                    <canvas id="customerAcquisitionChart"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-4 text-center">
                    <div class="bg-blue-50 rounded-xl p-3">
                        <div class="text-sm text-blue-600">هذا الشهر</div>
                        <div class="text-xl font-bold text-blue-800">{{ $data['acquisition']['this_month'] }}</div>
                    </div>
                    <div class="bg-green-50 rounded-xl p-3">
                        <div class="text-sm text-green-600">الشهر الماضي</div>
                        <div class="text-xl font-bold text-green-800">{{ $data['acquisition']['last_month'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Customer Churn Analysis -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">⚠️ تحليل فقدان العملاء</h3>
                <div class="chart-container">
                    <canvas id="customerChurnChart"></canvas>
                </div>
                <div class="mt-4 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">معدل الفقدان الشهري</span>
                        <span class="font-bold text-red-600">{{ $data['churn']['monthly_rate'] }}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">العملاء المعرضون للخطر</span>
                        <span class="font-bold text-yellow-600">{{ $data['churn']['at_risk_customers'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Journey & Preferences -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Preferred Services -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">⭐ الخدمات المفضلة</h3>
                <div class="chart-container">
                    <canvas id="preferredServicesChart"></canvas>
                </div>
                <div class="mt-4 space-y-2">
                    @foreach($data['preferred_services'] as $service)
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">{{ $service['name'] }}</span>
                        <span class="font-semibold text-slate-800">{{ $service['percentage'] }}%</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Booking Patterns -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">📅 أنماط الحجز</h3>
                <div class="chart-container">
                    <canvas id="bookingPatternsChart"></canvas>
                </div>
                <div class="mt-4 space-y-3">
                    <div class="text-center">
                        <div class="text-sm text-slate-600">أفضل يوم للحجز</div>
                        <div class="text-lg font-bold text-slate-800">{{ $data['booking_patterns']['best_day'] }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm text-slate-600">أفضل وقت للحجز</div>
                        <div class="text-lg font-bold text-slate-800">{{ $data['booking_patterns']['best_time'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Customer Satisfaction by Segment -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">😊 الرضا حسب الشريحة</h3>
                <div class="space-y-4">
                    @foreach($data['satisfaction_by_segment'] as $segment)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="segment-badge segment-{{ strtolower($segment['segment']) }}">
                                {{ $segment['label'] }}
                            </span>
                            <span class="font-bold text-slate-800">{{ $segment['rating'] }}/5</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-{{ $segment['rating'] >= 4 ? 'green' : ($segment['rating'] >= 3 ? 'yellow' : 'red') }}-500 h-2 rounded-full" 
                                 style="width: {{ ($segment['rating'] / 5) * 100 }}%"></div>
                        </div>
                        <div class="text-xs text-slate-600 mt-1">{{ $segment['reviews_count'] }} تقييم</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Customer Insights & Recommendations -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Key Insights -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">💡 رؤى رئيسية</h3>
                <div class="space-y-4">
                    @foreach($data['insights'] as $insight)
                    <div class="border-r-4 border-{{ $insight['type'] === 'positive' ? 'green' : ($insight['type'] === 'warning' ? 'yellow' : 'blue') }}-400 pr-4">
                        <div class="flex items-start">
                            <span class="text-2xl mr-3">{{ $insight['icon'] }}</span>
                            <div>
                                <h4 class="font-semibold text-slate-800 mb-1">{{ $insight['title'] }}</h4>
                                <p class="text-sm text-slate-600 mb-2">{{ $insight['description'] }}</p>
                                <div class="text-xs bg-{{ $insight['type'] === 'positive' ? 'green' : ($insight['type'] === 'warning' ? 'yellow' : 'blue') }}-100 
                                           text-{{ $insight['type'] === 'positive' ? 'green' : ($insight['type'] === 'warning' ? 'yellow' : 'blue') }}-800 
                                           px-2 py-1 rounded-full">
                                    تأثير: {{ $insight['impact'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Actionable Recommendations -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">🎯 توصيات قابلة للتنفيذ</h3>
                <div class="space-y-4">
                    @foreach($data['recommendations'] as $recommendation)
                    <div class="bg-gradient-to-l from-blue-50 to-indigo-50 rounded-xl p-4">
                        <div class="flex items-start">
                            <span class="text-2xl mr-3">{{ $recommendation['icon'] }}</span>
                            <div class="flex-1">
                                <h4 class="font-semibold text-slate-800 mb-1">{{ $recommendation['title'] }}</h4>
                                <p class="text-sm text-slate-600 mb-3">{{ $recommendation['description'] }}</p>
                                <div class="flex items-center justify-between">
                                    <div class="text-xs text-blue-600">
                                        أولوية: {{ $recommendation['priority'] }}
                                    </div>
                                    <div class="text-xs text-green-600">
                                        تأثير متوقع: {{ $recommendation['expected_impact'] }}
                                    </div>
                                </div>
                                @if(isset($recommendation['action_url']))
                                <button onclick="window.location='{{ $recommendation['action_url'] }}'" 
                                        class="mt-3 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">
                                    {{ $recommendation['action_text'] }}
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
            <h3 class="text-2xl font-bold text-slate-800 mb-6">🏆 أهم العملاء</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-right py-3 px-4 font-semibold text-slate-700">العميل</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-700">الشريحة</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-700">إجمالي الإنفاق</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-700">عدد الحجوزات</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-700">آخر نشاط</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-700">التقييم</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['top_customers'] as $customer)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                        {{ substr($customer['name'], 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-slate-800">{{ $customer['name'] }}</div>
                                        <div class="text-sm text-slate-600">{{ $customer['email'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="segment-badge segment-{{ strtolower($customer['segment']) }}">
                                    {{ $customer['segment_label'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-800">
                                {{ number_format($customer['total_spent']) }} ر.س
                            </td>
                            <td class="py-3 px-4 text-slate-700">
                                {{ $customer['bookings_count'] }}
                            </td>
                            <td class="py-3 px-4 text-slate-600 text-sm">
                                {{ $customer['last_activity'] }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center">
                                    <span class="text-yellow-400 mr-1">⭐</span>
                                    <span class="font-medium text-slate-800">{{ $customer['avg_rating'] }}</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Chart data from backend
const chartsData = @json($data['charts']);

document.addEventListener('DOMContentLoaded', function() {
    initializeCustomerSegmentationChart();
    initializeCustomerAcquisitionChart();
    initializeCustomerChurnChart();
    initializePreferredServicesChart();
    initializeBookingPatternsChart();
    setupEventListeners();
});

function initializeCustomerSegmentationChart() {
    const ctx = document.getElementById('customerSegmentationChart').getContext('2d');
    const chartData = chartsData.customer_segmentation;
    
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
                        font: { family: 'Cairo, sans-serif' }
                    }
                }
            }
        }
    });
}

function initializeCustomerAcquisitionChart() {
    const ctx = document.getElementById('customerAcquisitionChart').getContext('2d');
    const chartData = chartsData.customer_acquisition;
    
    new Chart(ctx, {
        type: 'line',
        data: chartData.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
}

function initializeCustomerChurnChart() {
    const ctx = document.getElementById('customerChurnChart').getContext('2d');
    const chartData = chartsData.customer_churn;
    
    new Chart(ctx, {
        type: 'bar',
        data: chartData.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
}

function initializePreferredServicesChart() {
    const ctx = document.getElementById('preferredServicesChart').getContext('2d');
    const chartData = chartsData.preferred_services;
    
    new Chart(ctx, {
        type: 'pie',
        data: chartData.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { family: 'Cairo, sans-serif' }
                    }
                }
            }
        }
    });
}

function initializeBookingPatternsChart() {
    const ctx = document.getElementById('bookingPatternsChart').getContext('2d');
    const chartData = chartsData.booking_patterns;
    
    new Chart(ctx, {
        type: 'bar',
        data: chartData.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
}

function setupEventListeners() {
    document.getElementById('timeRange').addEventListener('change', updateAnalysis);
    document.getElementById('segmentFilter').addEventListener('change', updateAnalysis);
}

function updateAnalysis() {
    const timeRange = document.getElementById('timeRange').value;
    const segment = document.getElementById('segmentFilter').value;
    
    showLoading();
    
    fetch(`{{ route('merchant.analytics.customers') }}?period=${timeRange}&segment=${segment}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        location.reload(); // Temporary - should update dynamically
    })
    .catch(error => {
        console.error('Error updating analysis:', error);
        hideLoading();
    });
}

function exportCustomerReport() {
    const timeRange = document.getElementById('timeRange').value;
    const segment = document.getElementById('segmentFilter').value;
    const url = `{{ route('merchant.analytics.export') }}?type=customers&period=${timeRange}&segment=${segment}&format=pdf`;
    window.open(url, '_blank');
}

function showLoading() {
    const loading = document.createElement('div');
    loading.id = 'loadingOverlay';
    loading.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    loading.innerHTML = '<div class="bg-white rounded-lg p-6"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mx-auto"></div><p class="mt-4 text-center">جاري تحديث البيانات...</p></div>';
    document.body.appendChild(loading);
}

function hideLoading() {
    const loading = document.getElementById('loadingOverlay');
    if (loading) loading.remove();
}
</script>
@endpush
@endsection
