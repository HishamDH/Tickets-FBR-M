@extends('layouts.app')

@section('title', 'العمليات التشغيلية')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .chart-container {
        position: relative;
        height: 400px;
        width: 100%;
    }
    .operations-card {
        transition: all 0.3s ease;
    }
    .operations-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .status-excellent { background-color: #dcfce7; color: #166534; }
    .status-good { background-color: #dbeafe; color: #1e40af; }
    .status-warning { background-color: #fef3c7; color: #92400e; }
    .status-critical { background-color: #fecaca; color: #991b1b; }
    
    .real-time-indicator {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
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
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-orange-800 to-orange-600 bg-clip-text text-transparent mb-2">
                        ⚙️ لوحة العمليات التشغيلية
                    </h1>
                    <p class="text-slate-600 text-lg">مراقبة شاملة لأداء النظام والعمليات في الوقت الفعلي</p>
                    <div class="flex items-center mt-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full real-time-indicator mr-2"></div>
                        <span class="text-sm text-slate-600">آخر تحديث: {{ now()->format('H:i:s') }}</span>
                    </div>
                </div>
                
                <!-- Controls -->
                <div class="mt-6 lg:mt-0 flex flex-wrap gap-4">
                    <select id="timeRange" class="rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="1" {{ $period == '1' ? 'selected' : '' }}>آخر ساعة</option>
                        <option value="24" {{ $period == '24' ? 'selected' : '' }}>آخر 24 ساعة</option>
                        <option value="168" {{ $period == '168' ? 'selected' : '' }}>آخر أسبوع</option>
                        <option value="720" {{ $period == '720' ? 'selected' : '' }}>آخر شهر</option>
                    </select>
                    
                    <button onclick="refreshRealTimeData()" class="bg-blue-600 text-white px-6 py-2 rounded-xl font-medium hover:bg-blue-700 transition-colors duration-200">
                        🔄 تحديث فوري
                    </button>
                    
                    <button onclick="exportOperationsReport()" class="bg-green-600 text-white px-6 py-2 rounded-xl font-medium hover:bg-green-700 transition-colors duration-200">
                        📊 تصدير التقرير
                    </button>
                    
                    <a href="{{ route('merchant.analytics.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded-xl font-medium hover:bg-gray-700 transition-colors duration-200">
                        ← العودة للوحة الرئيسية
                    </a>
                </div>
            </div>
        </div>

        <!-- System Health Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- System Status -->
            <div class="operations-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-{{ $data['system_health']['status'] === 'excellent' ? 'green' : ($data['system_health']['status'] === 'good' ? 'blue' : ($data['system_health']['status'] === 'warning' ? 'yellow' : 'red')) }}-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">{{ $data['system_health']['status'] === 'excellent' ? '🟢' : ($data['system_health']['status'] === 'good' ? '🔵' : ($data['system_health']['status'] === 'warning' ? '🟡' : '🔴')) }}</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">حالة النظام</div>
                        <div class="text-2xl font-bold text-slate-800">{{ $data['system_health']['uptime'] }}%</div>
                        <div class="text-sm">
                            <span class="status-badge status-{{ $data['system_health']['status'] }}">
                                {{ $data['system_health']['status_label'] }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-{{ $data['system_health']['status'] === 'excellent' ? 'green' : ($data['system_health']['status'] === 'good' ? 'blue' : ($data['system_health']['status'] === 'warning' ? 'yellow' : 'red')) }}-600 h-2 rounded-full" style="width: {{ $data['system_health']['uptime'] }}%"></div>
                </div>
            </div>

            <!-- Active Users -->
            <div class="operations-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">👥</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">المستخدمون النشطون</div>
                        <div class="text-3xl font-bold text-slate-800">{{ number_format($data['real_time']['active_users']) }}</div>
                        <div class="text-sm {{ $data['real_time']['users_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $data['real_time']['users_change'] >= 0 ? '↗️' : '↘️' }} {{ abs($data['real_time']['users_change']) }}% خلال الساعة
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, ($data['real_time']['active_users'] / 1000) * 100) }}%"></div>
                </div>
            </div>

            <!-- Server Response Time -->
            <div class="operations-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">⚡</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">زمن استجابة الخادم</div>
                        <div class="text-3xl font-bold text-slate-800">{{ $data['performance']['avg_response_time'] }}ms</div>
                        <div class="text-sm {{ $data['performance']['response_time_change'] <= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $data['performance']['response_time_change'] <= 0 ? '↘️' : '↗️' }} {{ abs($data['performance']['response_time_change']) }}% تغيير
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-{{ $data['performance']['avg_response_time'] <= 200 ? 'green' : ($data['performance']['avg_response_time'] <= 500 ? 'yellow' : 'red') }}-600 h-2 rounded-full" style="width: {{ min(100, 100 - (($data['performance']['avg_response_time'] / 1000) * 100)) }}%"></div>
                </div>
            </div>

            <!-- Error Rate -->
            <div class="operations-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">⚠️</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">معدل الأخطاء</div>
                        <div class="text-3xl font-bold text-slate-800">{{ number_format($data['performance']['error_rate'], 2) }}%</div>
                        <div class="text-sm {{ $data['performance']['error_rate_change'] <= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $data['performance']['error_rate_change'] <= 0 ? '↘️' : '↗️' }} {{ abs($data['performance']['error_rate_change']) }}% تغيير
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-{{ $data['performance']['error_rate'] <= 1 ? 'green' : ($data['performance']['error_rate'] <= 3 ? 'yellow' : 'red') }}-600 h-2 rounded-full" style="width: {{ min(100, $data['performance']['error_rate'] * 20) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Real-time Activity -->
        <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6 mb-8">
            <h3 class="text-2xl font-bold text-slate-800 mb-6">📊 النشاط في الوقت الفعلي</h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Real-time Bookings -->
                <div>
                    <h4 class="text-lg font-semibold text-slate-700 mb-4">الحجوزات المباشرة</h4>
                    <div class="chart-container">
                        <canvas id="realTimeBookingsChart"></canvas>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-4 text-center">
                        <div class="bg-blue-50 rounded-xl p-3">
                            <div class="text-sm text-blue-600">هذه الساعة</div>
                            <div class="text-xl font-bold text-blue-800">{{ $data['real_time']['bookings_this_hour'] }}</div>
                        </div>
                        <div class="bg-green-50 rounded-xl p-3">
                            <div class="text-sm text-green-600">اليوم</div>
                            <div class="text-xl font-bold text-green-800">{{ $data['real_time']['bookings_today'] }}</div>
                        </div>
                        <div class="bg-purple-50 rounded-xl p-3">
                            <div class="text-sm text-purple-600">المتوقع</div>
                            <div class="text-xl font-bold text-purple-800">{{ $data['real_time']['expected_bookings'] }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- System Load -->
                <div>
                    <h4 class="text-lg font-semibold text-slate-700 mb-4">حمولة النظام</h4>
                    <div class="chart-container">
                        <canvas id="systemLoadChart"></canvas>
                    </div>
                    <div class="mt-4 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">استخدام المعالج</span>
                            <span class="font-bold text-slate-800">{{ $data['system_load']['cpu_usage'] }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">استخدام الذاكرة</span>
                            <span class="font-bold text-slate-800">{{ $data['system_load']['memory_usage'] }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">استخدام قاعدة البيانات</span>
                            <span class="font-bold text-slate-800">{{ $data['system_load']['db_connections'] }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Response Time Trends -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">📈 اتجاهات زمن الاستجابة</h3>
                <div class="chart-container">
                    <canvas id="responseTimeTrendsChart"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 rounded-xl p-3 text-center">
                        <div class="text-sm text-blue-600">أسرع استجابة</div>
                        <div class="text-xl font-bold text-blue-800">{{ $data['performance']['min_response_time'] }}ms</div>
                    </div>
                    <div class="bg-red-50 rounded-xl p-3 text-center">
                        <div class="text-sm text-red-600">أبطأ استجابة</div>
                        <div class="text-xl font-bold text-red-800">{{ $data['performance']['max_response_time'] }}ms</div>
                    </div>
                </div>
            </div>

            <!-- API Performance -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">🔌 أداء واجهات البرمجة</h3>
                <div class="space-y-4">
                    @foreach($data['api_performance'] as $endpoint)
                    <div class="border border-gray-200 rounded-xl p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-medium text-slate-800">{{ $endpoint['endpoint'] }}</span>
                            <span class="status-badge status-{{ $endpoint['status'] }}">{{ $endpoint['status_label'] }}</span>
                        </div>
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-slate-600">الطلبات:</span>
                                <span class="font-semibold text-slate-800">{{ number_format($endpoint['requests']) }}</span>
                            </div>
                            <div>
                                <span class="text-slate-600">الاستجابة:</span>
                                <span class="font-semibold text-slate-800">{{ $endpoint['avg_response'] }}ms</span>
                            </div>
                            <div>
                                <span class="text-slate-600">معدل النجاح:</span>
                                <span class="font-semibold text-slate-800">{{ $endpoint['success_rate'] }}%</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                            <div class="bg-{{ $endpoint['success_rate'] >= 99 ? 'green' : ($endpoint['success_rate'] >= 95 ? 'yellow' : 'red') }}-600 h-2 rounded-full" style="width: {{ $endpoint['success_rate'] }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Database and Cache Performance -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Database Performance -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">🗄️ أداء قاعدة البيانات</h3>
                <div class="space-y-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-slate-800">{{ $data['database']['active_connections'] }}</div>
                        <div class="text-sm text-slate-600">الاتصالات النشطة</div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">متوسط الاستعلام</span>
                            <span class="font-bold text-slate-800">{{ $data['database']['avg_query_time'] }}ms</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">الاستعلامات البطيئة</span>
                            <span class="font-bold text-slate-800">{{ $data['database']['slow_queries'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">حجم قاعدة البيانات</span>
                            <span class="font-bold text-slate-800">{{ $data['database']['size'] }}GB</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cache Performance -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">⚡ أداء التخزين المؤقت</h3>
                <div class="space-y-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-slate-800">{{ $data['cache']['hit_ratio'] }}%</div>
                        <div class="text-sm text-slate-600">معدل الإصابة</div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">الطلبات المحفوظة</span>
                            <span class="font-bold text-slate-800">{{ number_format($data['cache']['hits']) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">الطلبات المفقودة</span>
                            <span class="font-bold text-slate-800">{{ number_format($data['cache']['misses']) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">استخدام الذاكرة</span>
                            <span class="font-bold text-slate-800">{{ $data['cache']['memory_usage'] }}MB</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Queue Performance -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">📋 أداء المهام</h3>
                <div class="space-y-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-slate-800">{{ $data['queue']['pending_jobs'] }}</div>
                        <div class="text-sm text-slate-600">المهام المعلقة</div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">المهام المكتملة</span>
                            <span class="font-bold text-slate-800">{{ number_format($data['queue']['completed_jobs']) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">المهام الفاشلة</span>
                            <span class="font-bold text-slate-800">{{ number_format($data['queue']['failed_jobs']) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">متوسط وقت المعالجة</span>
                            <span class="font-bold text-slate-800">{{ $data['queue']['avg_processing_time'] }}s</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts and Monitoring -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Active Alerts -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">🚨 التنبيهات النشطة</h3>
                @if(count($data['alerts']) > 0)
                <div class="space-y-3">
                    @foreach($data['alerts'] as $alert)
                    <div class="border-r-4 border-{{ $alert['severity'] === 'critical' ? 'red' : ($alert['severity'] === 'warning' ? 'yellow' : 'blue') }}-400 pr-4 bg-{{ $alert['severity'] === 'critical' ? 'red' : ($alert['severity'] === 'warning' ? 'yellow' : 'blue') }}-50 rounded-xl p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start">
                                <span class="text-2xl mr-3">{{ $alert['icon'] }}</span>
                                <div>
                                    <h4 class="font-semibold text-slate-800 mb-1">{{ $alert['title'] }}</h4>
                                    <p class="text-sm text-slate-600 mb-2">{{ $alert['message'] }}</p>
                                    <div class="text-xs text-slate-500">{{ $alert['timestamp'] }}</div>
                                </div>
                            </div>
                            <button onclick="dismissAlert('{{ $alert['id'] }}')" class="text-slate-400 hover:text-slate-600">
                                ✕
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <span class="text-6xl">✅</span>
                    <div class="mt-4 text-lg font-semibold text-slate-800">لا توجد تنبيهات نشطة</div>
                    <div class="text-sm text-slate-600">جميع الأنظمة تعمل بشكل طبيعي</div>
                </div>
                @endif
            </div>

            <!-- Recent Activities -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">📝 الأنشطة الأخيرة</h3>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($data['recent_activities'] as $activity)
                    <div class="flex items-start p-3 bg-gray-50 rounded-xl">
                        <span class="text-xl mr-3">{{ $activity['icon'] }}</span>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-slate-800">{{ $activity['action'] }}</div>
                            <div class="text-xs text-slate-600">{{ $activity['details'] }}</div>
                            <div class="text-xs text-slate-500 mt-1">{{ $activity['timestamp'] }}</div>
                        </div>
                        <span class="status-badge status-{{ $activity['status'] }}">{{ $activity['status_label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- System Statistics -->
        <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
            <h3 class="text-2xl font-bold text-slate-800 mb-6">📊 إحصائيات النظام التفصيلية</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($data['system_stats'] as $stat)
                <div class="text-center p-4 bg-gray-50 rounded-xl">
                    <div class="text-3xl mb-2">{{ $stat['icon'] }}</div>
                    <div class="text-2xl font-bold text-slate-800">{{ $stat['value'] }}</div>
                    <div class="text-sm text-slate-600">{{ $stat['label'] }}</div>
                    @if(isset($stat['change']))
                    <div class="text-xs {{ $stat['change'] >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">
                        {{ $stat['change'] >= 0 ? '↗️' : '↘️' }} {{ abs($stat['change']) }}%
                    </div>
                    @endif
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
    initializeRealTimeBookingsChart();
    initializeSystemLoadChart();
    initializeResponseTimeTrendsChart();
    setupEventListeners();
    startRealTimeUpdates();
});

function initializeRealTimeBookingsChart() {
    const ctx = document.getElementById('realTimeBookingsChart').getContext('2d');
    const chartData = chartsData.real_time_bookings;
    
    new Chart(ctx, {
        type: 'line',
        data: chartData.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 0 },
            scales: {
                x: { type: 'time', time: { unit: 'minute' } },
                y: { beginAtZero: true }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
}

function initializeSystemLoadChart() {
    const ctx = document.getElementById('systemLoadChart').getContext('2d');
    const chartData = chartsData.system_load;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: chartData.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

function initializeResponseTimeTrendsChart() {
    const ctx = document.getElementById('responseTimeTrendsChart').getContext('2d');
    const chartData = chartsData.response_time_trends;
    
    new Chart(ctx, {
        type: 'line',
        data: chartData.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { type: 'time' },
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
}

function startRealTimeUpdates() {
    // Update every 30 seconds
    setInterval(refreshRealTimeData, 30000);
}

function refreshRealTimeData() {
    fetch(`{{ route('merchant.analytics.real-time') }}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        updateRealTimeMetrics(data);
        updateTimestamp();
    })
    .catch(error => {
        console.error('Error fetching real-time data:', error);
    });
}

function updateRealTimeMetrics(data) {
    // Update active users
    document.querySelector('[data-metric="active_users"]').textContent = data.active_users.toLocaleString();
    
    // Update system load
    if (data.system_load) {
        document.querySelector('[data-metric="cpu_usage"]').textContent = data.system_load.cpu_usage + '%';
        document.querySelector('[data-metric="memory_usage"]').textContent = data.system_load.memory_usage + '%';
    }
    
    // Update other metrics as needed
}

function updateTimestamp() {
    const now = new Date();
    const timestamp = now.toLocaleTimeString('ar-SA');
    document.querySelector('.real-time-indicator').parentElement.querySelector('span').textContent = `آخر تحديث: ${timestamp}`;
}

function updateAnalysis() {
    const timeRange = document.getElementById('timeRange').value;
    
    showLoading();
    
    fetch(`{{ route('merchant.analytics.operations') }}?period=${timeRange}`, {
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

function dismissAlert(alertId) {
    fetch(`/analytics/alerts/${alertId}/dismiss`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`[data-alert-id="${alertId}"]`).remove();
        }
    })
    .catch(error => {
        console.error('Error dismissing alert:', error);
    });
}

function exportOperationsReport() {
    const timeRange = document.getElementById('timeRange').value;
    const url = `{{ route('merchant.analytics.export') }}?type=operations&period=${timeRange}&format=pdf`;
    window.open(url, '_blank');
}

function showLoading() {
    const loading = document.createElement('div');
    loading.id = 'loadingOverlay';
    loading.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    loading.innerHTML = '<div class="bg-white rounded-lg p-6"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-600 mx-auto"></div><p class="mt-4 text-center">جاري تحديث البيانات...</p></div>';
    document.body.appendChild(loading);
}

function hideLoading() {
    const loading = document.getElementById('loadingOverlay');
    if (loading) loading.remove();
}

// Real-time notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 bg-${type === 'success' ? 'green' : (type === 'error' ? 'red' : 'blue')}-500 text-white px-6 py-4 rounded-lg shadow-lg z-50`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>
@endpush
@endsection
