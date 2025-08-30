@extends('layouts.app')

@section('title', 'أداء التجار')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .chart-container {
        position: relative;
        height: 400px;
        width: 100%;
    }
    .merchant-card {
        transition: all 0.3s ease;
    }
    .merchant-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .performance-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .performance-excellent { background-color: #dcfce7; color: #166534; }
    .performance-good { background-color: #dbeafe; color: #1e40af; }
    .performance-average { background-color: #fef3c7; color: #92400e; }
    .performance-poor { background-color: #fecaca; color: #991b1b; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 py-8" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl mb-8 p-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-800 to-purple-600 bg-clip-text text-transparent mb-2">
                        🏪 تحليل أداء التجار
                    </h1>
                    <p class="text-slate-600 text-lg">مقاييس شاملة لأداء وجودة الخدمات</p>
                </div>
                
                <!-- Controls -->
                <div class="mt-6 lg:mt-0 flex flex-wrap gap-4">
                    <select id="timeRange" class="rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="30" {{ $period == '30' ? 'selected' : '' }}>آخر 30 يوم</option>
                        <option value="90" {{ $period == '90' ? 'selected' : '' }}>آخر 3 أشهر</option>
                        <option value="180" {{ $period == '180' ? 'selected' : '' }}>آخر 6 أشهر</option>
                        <option value="365" {{ $period == '365' ? 'selected' : '' }}>آخر سنة</option>
                    </select>
                    
                    <select id="performanceFilter" class="rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="all">جميع التجار</option>
                        <option value="excellent">أداء ممتاز</option>
                        <option value="good">أداء جيد</option>
                        <option value="average">أداء متوسط</option>
                        <option value="poor">أداء ضعيف</option>
                    </select>
                    
                    <select id="categoryFilter" class="rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="all">جميع الفئات</option>
                        @foreach($data['categories'] as $category)
                        <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                        @endforeach
                    </select>
                    
                    <button onclick="exportMerchantReport()" class="bg-green-600 text-white px-6 py-2 rounded-xl font-medium hover:bg-green-700 transition-colors duration-200">
                        📊 تصدير التقرير
                    </button>
                    
                    <a href="{{ route('analytics.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded-xl font-medium hover:bg-gray-700 transition-colors duration-200">
                        ← العودة للوحة الرئيسية
                    </a>
                </div>
            </div>
        </div>

        <!-- Merchant Overview Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Merchants -->
            <div class="merchant-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">🏪</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">إجمالي التجار</div>
                        <div class="text-3xl font-bold text-slate-800">{{ number_format($data['metrics']['total_merchants']) }}</div>
                        <div class="text-sm {{ $data['metrics']['merchant_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $data['metrics']['merchant_growth'] >= 0 ? '↗️' : '↘️' }} {{ abs($data['metrics']['merchant_growth']) }}% نمو
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full" style="width: 100%"></div>
                </div>
            </div>

            <!-- Active Merchants -->
            <div class="merchant-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">✅</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">التجار النشطون</div>
                        <div class="text-3xl font-bold text-slate-800">{{ number_format($data['metrics']['active_merchants']) }}</div>
                        <div class="text-sm text-slate-600">
                            {{ number_format(($data['metrics']['active_merchants'] / $data['metrics']['total_merchants']) * 100, 1) }}% من الإجمالي
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($data['metrics']['active_merchants'] / $data['metrics']['total_merchants']) * 100 }}%"></div>
                </div>
            </div>

            <!-- Average Rating -->
            <div class="merchant-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">⭐</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">متوسط التقييم</div>
                        <div class="text-3xl font-bold text-slate-800">{{ number_format($data['metrics']['avg_rating'], 1) }}/5</div>
                        <div class="text-sm {{ $data['metrics']['rating_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $data['metrics']['rating_change'] >= 0 ? '↗️' : '↘️' }} {{ abs($data['metrics']['rating_change']) }}% تغيير
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ ($data['metrics']['avg_rating'] / 5) * 100 }}%"></div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="merchant-card bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-2xl">💰</span>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">إجمالي الإيرادات</div>
                        <div class="text-3xl font-bold text-slate-800">{{ number_format($data['metrics']['total_revenue'], 0) }}k</div>
                        <div class="text-sm {{ $data['metrics']['revenue_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $data['metrics']['revenue_change'] >= 0 ? '↗️' : '↘️' }} {{ abs($data['metrics']['revenue_change']) }}% نمو
                        </div>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: 100%"></div>
                </div>
            </div>
        </div>

        <!-- Performance Distribution -->
        <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6 mb-8">
            <h3 class="text-2xl font-bold text-slate-800 mb-6">📊 توزيع الأداء</h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Performance Chart -->
                <div>
                    <h4 class="text-lg font-semibold text-slate-700 mb-4">توزيع التجار حسب مستوى الأداء</h4>
                    <div class="chart-container">
                        <canvas id="performanceDistributionChart"></canvas>
                    </div>
                </div>
                
                <!-- Performance Details -->
                <div>
                    <h4 class="text-lg font-semibold text-slate-700 mb-4">تفاصيل مستويات الأداء</h4>
                    <div class="space-y-4">
                        @foreach($data['performance_levels'] as $level)
                        <div class="border border-gray-200 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <span class="performance-badge performance-{{ strtolower($level['level']) }}">
                                        {{ $level['label'] }}
                                    </span>
                                    <span class="mr-3 text-2xl">{{ $level['icon'] }}</span>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-slate-800">{{ number_format($level['count']) }}</div>
                                    <div class="text-sm text-slate-600">{{ $level['percentage'] }}%</div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-slate-600">متوسط التقييم:</span>
                                    <span class="font-semibold text-slate-800">{{ $level['avg_rating'] }}/5</span>
                                </div>
                                <div>
                                    <span class="text-slate-600">متوسط الإيرادات:</span>
                                    <span class="font-semibold text-slate-800">{{ number_format($level['avg_revenue']) }} ر.س</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue and Rating Analysis -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Top Performing Merchants -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">🏆 أفضل التجار أداءً</h3>
                <div class="chart-container">
                    <canvas id="topMerchantsChart"></canvas>
                </div>
                <div class="mt-4 space-y-3">
                    @foreach($data['top_merchants'] as $index => $merchant)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-{{ $index == 0 ? 'yellow' : ($index == 1 ? 'gray' : 'orange') }}-400 rounded-full flex items-center justify-center mr-3">
                                <span class="font-bold text-white">{{ $index + 1 }}</span>
                            </div>
                            <div>
                                <div class="font-medium text-slate-800">{{ $merchant['name'] }}</div>
                                <div class="text-sm text-slate-600">{{ $merchant['category'] }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-slate-800">{{ number_format($merchant['revenue']) }} ر.س</div>
                            <div class="text-sm text-yellow-600">⭐ {{ $merchant['rating'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Merchant Rating Distribution -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">📊 توزيع التقييمات</h3>
                <div class="chart-container">
                    <canvas id="ratingDistributionChart"></canvas>
                </div>
                <div class="mt-4 space-y-2">
                    @foreach($data['rating_distribution'] as $rating => $count)
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <span class="text-yellow-400 mr-2">{{ str_repeat('⭐', $rating) }}</span>
                            <span class="text-sm text-slate-600">{{ $rating }} نجوم</span>
                        </div>
                        <div class="flex items-center">
                            <span class="font-semibold text-slate-800">{{ $count }}</span>
                            <span class="text-sm text-slate-600 mr-2">تاجر</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Category Performance -->
        <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6 mb-8">
            <h3 class="text-2xl font-bold text-slate-800 mb-6">🎯 أداء الفئات</h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Category Revenue Chart -->
                <div class="lg:col-span-2">
                    <h4 class="text-lg font-semibold text-slate-700 mb-4">الإيرادات حسب الفئة</h4>
                    <div class="chart-container">
                        <canvas id="categoryRevenueChart"></canvas>
                    </div>
                </div>
                
                <!-- Category Statistics -->
                <div>
                    <h4 class="text-lg font-semibold text-slate-700 mb-4">إحصائيات الفئات</h4>
                    <div class="space-y-4">
                        @foreach($data['category_performance'] as $category)
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h5 class="font-semibold text-slate-800">{{ $category['name'] }}</h5>
                                <span class="text-2xl">{{ $category['icon'] }}</span>
                            </div>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-slate-600">عدد التجار:</span>
                                    <span class="font-semibold text-slate-800">{{ $category['merchants_count'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-600">متوسط التقييم:</span>
                                    <span class="font-semibold text-slate-800">{{ $category['avg_rating'] }}/5</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-600">إجمالي الإيرادات:</span>
                                    <span class="font-semibold text-slate-800">{{ number_format($category['total_revenue']) }} ر.س</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                    <div class="bg-{{ $category['avg_rating'] >= 4 ? 'green' : ($category['avg_rating'] >= 3 ? 'yellow' : 'red') }}-500 h-2 rounded-full" 
                                         style="width: {{ ($category['avg_rating'] / 5) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Quality Metrics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Response Time Analysis -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">⏱️ تحليل زمن الاستجابة</h3>
                <div class="chart-container">
                    <canvas id="responseTimeChart"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 rounded-xl p-3 text-center">
                        <div class="text-sm text-blue-600">متوسط زمن الاستجابة</div>
                        <div class="text-xl font-bold text-blue-800">{{ $data['service_quality']['avg_response_time'] }} دقيقة</div>
                    </div>
                    <div class="bg-green-50 rounded-xl p-3 text-center">
                        <div class="text-sm text-green-600">أسرع زمن استجابة</div>
                        <div class="text-xl font-bold text-green-800">{{ $data['service_quality']['fastest_response'] }} دقيقة</div>
                    </div>
                </div>
            </div>

            <!-- Completion Rate Analysis -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">✅ معدل إتمام الخدمات</h3>
                <div class="chart-container">
                    <canvas id="completionRateChart"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div class="bg-green-50 rounded-xl p-3 text-center">
                        <div class="text-sm text-green-600">معدل الإتمام العام</div>
                        <div class="text-xl font-bold text-green-800">{{ $data['service_quality']['overall_completion_rate'] }}%</div>
                    </div>
                    <div class="bg-red-50 rounded-xl p-3 text-center">
                        <div class="text-sm text-red-600">معدل الإلغاء</div>
                        <div class="text-xl font-bold text-red-800">{{ $data['service_quality']['cancellation_rate'] }}%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Merchant Insights -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Performance Insights -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">💡 رؤى الأداء</h3>
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

            <!-- Improvement Recommendations -->
            <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
                <h3 class="text-xl font-bold text-slate-800 mb-6">🎯 توصيات التحسين</h3>
                <div class="space-y-4">
                    @foreach($data['recommendations'] as $recommendation)
                    <div class="bg-gradient-to-l from-purple-50 to-indigo-50 rounded-xl p-4">
                        <div class="flex items-start">
                            <span class="text-2xl mr-3">{{ $recommendation['icon'] }}</span>
                            <div class="flex-1">
                                <h4 class="font-semibold text-slate-800 mb-1">{{ $recommendation['title'] }}</h4>
                                <p class="text-sm text-slate-600 mb-3">{{ $recommendation['description'] }}</p>
                                <div class="flex items-center justify-between">
                                    <div class="text-xs text-purple-600">
                                        أولوية: {{ $recommendation['priority'] }}
                                    </div>
                                    <div class="text-xs text-green-600">
                                        تأثير متوقع: {{ $recommendation['expected_impact'] }}
                                    </div>
                                </div>
                                @if(isset($recommendation['action_url']))
                                <button onclick="window.location='{{ $recommendation['action_url'] }}'" 
                                        class="mt-3 bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-700">
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

        <!-- Detailed Merchant Table -->
        <div class="bg-white/60 backdrop-blur-lg rounded-3xl border border-white/20 shadow-xl p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-slate-800">📋 قائمة التجار التفصيلية</h3>
                <div class="flex gap-4">
                    <input type="text" id="merchantSearch" placeholder="البحث عن تاجر..." 
                           class="rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <select id="sortBy" class="rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="revenue">ترتيب حسب الإيرادات</option>
                        <option value="rating">ترتيب حسب التقييم</option>
                        <option value="bookings">ترتيب حسب الحجوزات</option>
                        <option value="name">ترتيب أبجدي</option>
                    </select>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-right py-3 px-4 font-semibold text-slate-700">التاجر</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-700">الفئة</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-700">الأداء</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-700">التقييم</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-700">الإيرادات</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-700">الحجوزات</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-700">الاستجابة</th>
                            <th class="text-right py-3 px-4 font-semibold text-slate-700">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['merchant_details'] as $merchant)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-r from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                        {{ substr($merchant['name'], 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-slate-800">{{ $merchant['name'] }}</div>
                                        <div class="text-sm text-slate-600">{{ $merchant['email'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-sm text-slate-600">{{ $merchant['category'] }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="performance-badge performance-{{ strtolower($merchant['performance']) }}">
                                    {{ $merchant['performance_label'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center">
                                    <span class="text-yellow-400 mr-1">⭐</span>
                                    <span class="font-medium text-slate-800">{{ $merchant['rating'] }}</span>
                                    <span class="text-sm text-slate-600 mr-1">({{ $merchant['reviews_count'] }})</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-800">
                                {{ number_format($merchant['revenue']) }} ر.س
                            </td>
                            <td class="py-3 px-4 text-slate-700">
                                {{ $merchant['bookings_count'] }}
                            </td>
                            <td class="py-3 px-4 text-slate-600 text-sm">
                                {{ $merchant['avg_response_time'] }} دقيقة
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex gap-2">
                                    <button onclick="viewMerchantDetails({{ $merchant['id'] }})" 
                                            class="bg-blue-100 text-blue-600 px-3 py-1 rounded-lg text-sm hover:bg-blue-200">
                                        عرض
                                    </button>
                                    <button onclick="contactMerchant({{ $merchant['id'] }})" 
                                            class="bg-green-100 text-green-600 px-3 py-1 rounded-lg text-sm hover:bg-green-200">
                                        تواصل
                                    </button>
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
    initializePerformanceDistributionChart();
    initializeTopMerchantsChart();
    initializeRatingDistributionChart();
    initializeCategoryRevenueChart();
    initializeResponseTimeChart();
    initializeCompletionRateChart();
    setupEventListeners();
});

function initializePerformanceDistributionChart() {
    const ctx = document.getElementById('performanceDistributionChart').getContext('2d');
    const chartData = chartsData.performance_distribution;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: chartData.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { family: 'Cairo, sans-serif' } }
                }
            }
        }
    });
}

function initializeTopMerchantsChart() {
    const ctx = document.getElementById('topMerchantsChart').getContext('2d');
    const chartData = chartsData.top_merchants;
    
    new Chart(ctx, {
        type: 'bar',
        data: chartData.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            scales: { x: { beginAtZero: true } },
            plugins: { legend: { display: false } }
        }
    });
}

function initializeRatingDistributionChart() {
    const ctx = document.getElementById('ratingDistributionChart').getContext('2d');
    const chartData = chartsData.rating_distribution;
    
    new Chart(ctx, {
        type: 'bar',
        data: chartData.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } }
        }
    });
}

function initializeCategoryRevenueChart() {
    const ctx = document.getElementById('categoryRevenueChart').getContext('2d');
    const chartData = chartsData.category_revenue;
    
    new Chart(ctx, {
        type: 'bar',
        data: chartData.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });
}

function initializeResponseTimeChart() {
    const ctx = document.getElementById('responseTimeChart').getContext('2d');
    const chartData = chartsData.response_time;
    
    new Chart(ctx, {
        type: 'line',
        data: chartData.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });
}

function initializeCompletionRateChart() {
    const ctx = document.getElementById('completionRateChart').getContext('2d');
    const chartData = chartsData.completion_rate;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: chartData.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

function setupEventListeners() {
    document.getElementById('timeRange').addEventListener('change', updateAnalysis);
    document.getElementById('performanceFilter').addEventListener('change', updateAnalysis);
    document.getElementById('categoryFilter').addEventListener('change', updateAnalysis);
    document.getElementById('merchantSearch').addEventListener('input', filterTable);
    document.getElementById('sortBy').addEventListener('change', sortTable);
}

function updateAnalysis() {
    showLoading();
    // Implementation for updating analysis
    setTimeout(hideLoading, 1000);
}

function filterTable() {
    // Implementation for filtering table
}

function sortTable() {
    // Implementation for sorting table
}

function viewMerchantDetails(id) {
    // Implementation for viewing merchant details
    window.location.href = `/merchants/${id}`;
}

function contactMerchant(id) {
    // Implementation for contacting merchant
    window.location.href = `/merchants/${id}/contact`;
}

function exportMerchantReport() {
    const timeRange = document.getElementById('timeRange').value;
    const performance = document.getElementById('performanceFilter').value;
    const category = document.getElementById('categoryFilter').value;
    const url = `{{ route('analytics.export') }}?type=merchants&period=${timeRange}&performance=${performance}&category=${category}&format=pdf`;
    window.open(url, '_blank');
}

function showLoading() {
    const loading = document.createElement('div');
    loading.id = 'loadingOverlay';
    loading.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    loading.innerHTML = '<div class="bg-white rounded-lg p-6"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto"></div><p class="mt-4 text-center">جاري تحديث البيانات...</p></div>';
    document.body.appendChild(loading);
}

function hideLoading() {
    const loading = document.getElementById('loadingOverlay');
    if (loading) loading.remove();
}
</script>
@endpush
@endsection
