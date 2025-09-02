@extends('layouts.app')

@section('title', 'التقارير المتقدمة - Advanced Reports')
@section('description', 'تقارير تحليلية متقدمة ومخصصة للمنصة')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/date-fns@2.29.3/index.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@2.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
<style>
    .report-container {
        min-height: calc(100vh - 120px);
        background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
    }
    
    .report-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .report-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px 20px 0 0;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }
    
    .report-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 20%, transparent 50%);
        animation: float 8s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(180deg); }
    }
    
    .filter-panel {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }
    
    .metric-card {
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .metric-card:hover {
        border-color: #3b82f6;
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
    }
    
    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.05), transparent);
        transition: left 0.5s;
    }
    
    .metric-card:hover::before {
        left: 100%;
    }
    
    .chart-container {
        position: relative;
        height: 400px;
        width: 100%;
        padding: 1rem;
    }
    
    .chart-tabs {
        display: flex;
        background: #f1f5f9;
        border-radius: 12px;
        padding: 4px;
        margin-bottom: 1rem;
    }
    
    .chart-tab {
        flex: 1;
        padding: 8px 16px;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
        color: #64748b;
    }
    
    .chart-tab.active {
        background: white;
        color: #3b82f6;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
    }
    
    .export-dropdown {
        position: relative;
        display: inline-block;
    }
    
    .export-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        border: 1px solid #e2e8f0;
        min-width: 200px;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }
    
    .export-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    
    .export-item {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: background-color 0.2s ease;
        display: flex;
        align-items: center;
        color: #374151;
    }
    
    .export-item:hover {
        background: #f8fafc;
    }
    
    .export-item:first-child {
        border-radius: 12px 12px 0 0;
    }
    
    .export-item:last-child {
        border-radius: 0 0 12px 12px;
        border-bottom: none;
    }
    
    .progress-ring {
        transform: rotate(-90deg);
    }
    
    .progress-ring-circle {
        stroke-dasharray: 283;
        stroke-dashoffset: 283;
        transition: stroke-dashoffset 1s ease-in-out;
    }
    
    .report-section {
        margin-bottom: 2rem;
    }
    
    .comparison-table {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .comparison-table th {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        color: #374151;
        font-weight: 600;
        padding: 1rem;
        text-align: right;
    }
    
    .comparison-table td {
        padding: 1rem;
        border-bottom: 1px solid #f1f5f9;
        color: #4b5563;
    }
    
    .comparison-table tr:hover {
        background: #f8fafc;
    }
    
    .trend-indicator {
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .trend-up {
        background: #dcfce7;
        color: #16a34a;
    }
    
    .trend-down {
        background: #fee2e2;
        color: #dc2626;
    }
    
    .trend-stable {
        background: #f3f4f6;
        color: #6b7280;
    }
    
    .heatmap-cell {
        border-radius: 4px;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .heatmap-cell:hover {
        transform: scale(1.1);
        z-index: 10;
    }
    
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        backdrop-filter: blur(4px);
    }
    
    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #e2e8f0;
        border-top: 4px solid #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .insight-card {
        background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
        border: 2px solid #fb923c;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
        font-size: 14px;
    }
    
    .form-input, .form-select {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #ffffff;
    }
    
    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
</style>
@endpush

@section('content')
<div class="report-container">
    
    <!-- Header -->
    <div class="bg-white shadow-lg border-b-4 border-purple-500 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-xl flex items-center justify-center">
                        <span class="text-white text-2xl">📊</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">التقارير المتقدمة</h1>
                        <p class="text-sm text-gray-600">تحليلات شاملة ومخصصة للأداء</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3 space-x-reverse">
                    <!-- Export Dropdown -->
                    <div class="export-dropdown">
                        <button onclick="toggleExportMenu()" 
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 space-x-reverse transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>تصدير التقارير</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="export-menu" id="exportMenu">
                            <div class="export-item" onclick="exportReport('pdf')">
                                <span class="text-red-500 text-lg mr-3">📄</span>
                                <div>
                                    <div class="font-medium">تقرير PDF شامل</div>
                                    <div class="text-xs text-gray-500">تقرير مفصل بالرسوم البيانية</div>
                                </div>
                            </div>
                            <div class="export-item" onclick="exportReport('excel')">
                                <span class="text-green-500 text-lg mr-3">📊</span>
                                <div>
                                    <div class="font-medium">جدول Excel</div>
                                    <div class="text-xs text-gray-500">بيانات خام للتحليل</div>
                                </div>
                            </div>
                            <div class="export-item" onclick="exportReport('csv')">
                                <span class="text-blue-500 text-lg mr-3">📋</span>
                                <div>
                                    <div class="font-medium">ملف CSV</div>
                                    <div class="text-xs text-gray-500">بيانات مفصولة بفواصل</div>
                                </div>
                            </div>
                            <div class="export-item" onclick="scheduleReport()">
                                <span class="text-purple-500 text-lg mr-3">⏰</span>
                                <div>
                                    <div class="font-medium">جدولة التقارير</div>
                                    <div class="text-xs text-gray-500">تقارير دورية تلقائية</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Back Button -->
                    <a href="{{ route('analytics.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 space-x-reverse transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span>العودة</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Filters Panel -->
        <div class="filter-panel mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                </svg>
                فلاتر التقرير
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                <div class="form-group">
                    <label class="form-label">الفترة الزمنية</label>
                    <select id="dateRange" class="form-select" onchange="updateReports()">
                        <option value="last_7_days">آخر 7 أيام</option>
                        <option value="last_30_days" selected>آخر 30 يوم</option>
                        <option value="last_90_days">آخر 90 يوم</option>
                        <option value="last_year">آخر سنة</option>
                        <option value="custom">فترة مخصصة</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">نوع التقرير</label>
                    <select id="reportType" class="form-select" onchange="updateReports()">
                        <option value="overview">نظرة شاملة</option>
                        <option value="revenue">تقرير الإيرادات</option>
                        <option value="performance">تقرير الأداء</option>
                        <option value="growth">تقرير النمو</option>
                        <option value="comparison">تقرير مقارن</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">التجميع</label>
                    <select id="groupBy" class="form-select" onchange="updateReports()">
                        <option value="day">يومي</option>
                        <option value="week">أسبوعي</option>
                        <option value="month" selected>شهري</option>
                        <option value="quarter">ربع سنوي</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">التجار</label>
                    <select id="merchantFilter" class="form-select" onchange="updateReports()">
                        <option value="all">جميع التجار</option>
                        <option value="top_10">أفضل 10 تجار</option>
                        <option value="new">التجار الجدد</option>
                        <option value="active">التجار النشطين</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">الخدمات</label>
                    <select id="serviceFilter" class="form-select" onchange="updateReports()">
                        <option value="all">جميع الخدمات</option>
                        <option value="popular">الأكثر طلباً</option>
                        <option value="profitable">الأكثر ربحية</option>
                        <option value="new">الخدمات الجديدة</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">المقارنة</label>
                    <select id="comparisonPeriod" class="form-select" onchange="updateReports()">
                        <option value="none">بدون مقارنة</option>
                        <option value="previous">الفترة السابقة</option>
                        <option value="year_over_year">نفس الفترة العام الماضي</option>
                        <option value="industry">متوسط الصناعة</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Key Metrics Overview -->
        <div class="report-section">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                المؤشرات الرئيسية
            </h2>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 mb-8">
                <div class="metric-card">
                    <div class="text-3xl mb-2">💰</div>
                    <div class="text-2xl font-bold text-gray-800" id="totalRevenue">534,250 ر.س</div>
                    <div class="text-sm text-gray-600">إجمالي الإيرادات</div>
                    <div class="trend-indicator trend-up mt-2">
                        ↗️ +15.3%
                    </div>
                </div>
                
                <div class="metric-card">
                    <div class="text-3xl mb-2">📈</div>
                    <div class="text-2xl font-bold text-gray-800" id="totalBookings">1,847</div>
                    <div class="text-sm text-gray-600">إجمالي الحجوزات</div>
                    <div class="trend-indicator trend-up mt-2">
                        ↗️ +22.7%
                    </div>
                </div>
                
                <div class="metric-card">
                    <div class="text-3xl mb-2">👥</div>
                    <div class="text-2xl font-bold text-gray-800" id="activeCustomers">3,456</div>
                    <div class="text-sm text-gray-600">العملاء النشطين</div>
                    <div class="trend-indicator trend-up mt-2">
                        ↗️ +8.9%
                    </div>
                </div>
                
                <div class="metric-card">
                    <div class="text-3xl mb-2">🏪</div>
                    <div class="text-2xl font-bold text-gray-800" id="activeMerchants">127</div>
                    <div class="text-sm text-gray-600">التجار النشطين</div>
                    <div class="trend-indicator trend-stable mt-2">
                        → +1.2%
                    </div>
                </div>
                
                <div class="metric-card">
                    <div class="text-3xl mb-2">⭐</div>
                    <div class="text-2xl font-bold text-gray-800" id="avgRating">4.7</div>
                    <div class="text-sm text-gray-600">متوسط التقييم</div>
                    <div class="trend-indicator trend-up mt-2">
                        ↗️ +0.3
                    </div>
                </div>
                
                <div class="metric-card">
                    <div class="text-3xl mb-2">💎</div>
                    <div class="text-2xl font-bold text-gray-800" id="conversionRate">67.8%</div>
                    <div class="text-sm text-gray-600">معدل التحويل</div>
                    <div class="trend-indicator trend-down mt-2">
                        ↘️ -2.1%
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Charts Section -->
        <div class="report-section">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                التحليلات المتقدمة
            </h2>
            
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
                <!-- Revenue Breakdown -->
                <div class="report-card">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">تفصيل الإيرادات</h3>
                        <div class="chart-tabs">
                            <div class="chart-tab active" onclick="switchTab(this, 'revenue-daily')">يومي</div>
                            <div class="chart-tab" onclick="switchTab(this, 'revenue-weekly')">أسبوعي</div>
                            <div class="chart-tab" onclick="switchTab(this, 'revenue-monthly')">شهري</div>
                        </div>
                        <div class="chart-container">
                            <canvas id="revenueBreakdownChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Service Performance -->
                <div class="report-card">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">أداء الخدمات</h3>
                        <div class="chart-container">
                            <canvas id="servicePerformanceChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Customer Segmentation -->
                <div class="report-card">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">تقسيم العملاء</h3>
                        <div class="chart-container">
                            <canvas id="customerSegmentationChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Geographic Analysis -->
                <div class="report-card">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">التحليل الجغرافي</h3>
                        <div class="chart-container">
                            <canvas id="geographicChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparative Analysis -->
        <div class="report-section">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                </svg>
                التحليل المقارن
            </h2>
            
            <div class="comparison-table">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th>المؤشر</th>
                            <th>هذا الشهر</th>
                            <th>الشهر الماضي</th>
                            <th>التغيير</th>
                            <th>نفس الشهر العام الماضي</th>
                            <th>التغيير السنوي</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-medium">إجمالي الإيرادات</td>
                            <td>534,250 ر.س</td>
                            <td>463,180 ر.س</td>
                            <td><span class="trend-indicator trend-up">+15.3%</span></td>
                            <td>425,670 ر.س</td>
                            <td><span class="trend-indicator trend-up">+25.5%</span></td>
                        </tr>
                        <tr>
                            <td class="font-medium">عدد الحجوزات</td>
                            <td>1,847</td>
                            <td>1,505</td>
                            <td><span class="trend-indicator trend-up">+22.7%</span></td>
                            <td>1,234</td>
                            <td><span class="trend-indicator trend-up">+49.7%</span></td>
                        </tr>
                        <tr>
                            <td class="font-medium">متوسط قيمة الحجز</td>
                            <td>289 ر.س</td>
                            <td>308 ر.س</td>
                            <td><span class="trend-indicator trend-down">-6.2%</span></td>
                            <td>345 ر.س</td>
                            <td><span class="trend-indicator trend-down">-16.2%</span></td>
                        </tr>
                        <tr>
                            <td class="font-medium">العملاء الجدد</td>
                            <td>456</td>
                            <td>389</td>
                            <td><span class="trend-indicator trend-up">+17.2%</span></td>
                            <td>312</td>
                            <td><span class="trend-indicator trend-up">+46.2%</span></td>
                        </tr>
                        <tr>
                            <td class="font-medium">معدل الاحتفاظ</td>
                            <td>78.5%</td>
                            <td>76.3%</td>
                            <td><span class="trend-indicator trend-up">+2.9%</span></td>
                            <td>71.2%</td>
                            <td><span class="trend-indicator trend-up">+10.3%</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Performance Insights -->
        <div class="report-section">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                رؤى الأداء والتوصيات
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="insight-card">
                    <div class="flex items-start">
                        <span class="text-3xl mr-4">🚀</span>
                        <div>
                            <h4 class="text-lg font-semibold text-orange-900 mb-2">نمو متسارع</h4>
                            <p class="text-orange-800 text-sm mb-3">نمو الإيرادات بنسبة 25% مقارنة بالعام الماضي يظهر أداءً ممتازاً</p>
                            <div class="bg-orange-600 text-white px-3 py-1 rounded text-xs font-medium">
                                توصية: زيادة الاستثمار في التسويق
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="insight-card">
                    <div class="flex items-start">
                        <span class="text-3xl mr-4">⚠️</span>
                        <div>
                            <h4 class="text-lg font-semibold text-orange-900 mb-2">انخفاض متوسط القيمة</h4>
                            <p class="text-orange-800 text-sm mb-3">متوسط قيمة الحجز انخفض بنسبة 16% - قد يشير لتغيير سلوك العملاء</p>
                            <div class="bg-orange-600 text-white px-3 py-1 rounded text-xs font-medium">
                                توصية: مراجعة استراتيجية التسعير
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="insight-card">
                    <div class="flex items-start">
                        <span class="text-3xl mr-4">💡</span>
                        <div>
                            <h4 class="text-lg font-semibold text-orange-900 mb-2">فرصة تحسين</h4>
                            <p class="text-orange-800 text-sm mb-3">معدل التحويل يمكن تحسينه بتطوير تجربة المستخدم</p>
                            <div class="bg-orange-600 text-white px-3 py-1 rounded text-xs font-medium">
                                توصية: تحسين واجهة الحجز
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="insight-card">
                    <div class="flex items-start">
                        <span class="text-3xl mr-4">📊</span>
                        <div>
                            <h4 class="text-lg font-semibold text-orange-900 mb-2">تحليل الموسمية</h4>
                            <p class="text-orange-800 text-sm mb-3">ذروة النشاط في عطلة نهاية الأسبوع - يجب استغلال هذا النمط</p>
                            <div class="bg-orange-600 text-white px-3 py-1 rounded text-xs font-medium">
                                توصية: عروض خاصة نهاية الأسبوع
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="insight-card">
                    <div class="flex items-start">
                        <span class="text-3xl mr-4">🎯</span>
                        <div>
                            <h4 class="text-lg font-semibold text-orange-900 mb-2">استهداف دقيق</h4>
                            <p class="text-orange-800 text-sm mb-3">شريحة العملاء من 25-35 سنة الأكثر ربحية</p>
                            <div class="bg-orange-600 text-white px-3 py-1 rounded text-xs font-medium">
                                توصية: حملات مستهدفة لهذه الفئة
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="insight-card">
                    <div class="flex items-start">
                        <span class="text-3xl mr-4">🔔</span>
                        <div>
                            <h4 class="text-lg font-semibold text-orange-900 mb-2">تنبيه أداء</h4>
                            <p class="text-orange-800 text-sm mb-3">5 تجار بحاجة لمتابعة - انخفاض في معدل الرضا</p>
                            <div class="bg-orange-600 text-white px-3 py-1 rounded text-xs font-medium">
                                توصية: برنامج تدريب ودعم
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Items -->
        <div class="report-section">
            <div class="report-card">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                        خطة العمل المقترحة
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                            <h4 class="font-bold text-red-800 mb-2">عاجل - هذا الأسبوع</h4>
                            <ul class="text-sm text-red-700 space-y-1">
                                <li>• متابعة التجار ذوي الأداء المنخفض</li>
                                <li>• تحليل انخفاض متوسط قيمة الحجز</li>
                                <li>• مراجعة شكاوى العملاء الأخيرة</li>
                            </ul>
                        </div>
                        
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                            <h4 class="font-bold text-yellow-800 mb-2">مهم - هذا الشهر</h4>
                            <ul class="text-sm text-yellow-700 space-y-1">
                                <li>• إطلاق حملة تسويقية مستهدفة</li>
                                <li>• تطوير عروض نهاية الأسبوع</li>
                                <li>• تحسين تجربة الحجز للجوال</li>
                            </ul>
                        </div>
                        
                        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                            <h4 class="font-bold text-green-800 mb-2">تخطيط طويل المدى</h4>
                            <ul class="text-sm text-green-700 space-y-1">
                                <li>• برنامج ولاء العملاء</li>
                                <li>• توسيع قاعدة التجار</li>
                                <li>• تطوير خدمات جديدة</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentCharts = {};

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    setupEventListeners();
});

function initializeCharts() {
    initializeRevenueBreakdownChart();
    initializeServicePerformanceChart();
    initializeCustomerSegmentationChart();
    initializeGeographicChart();
}

function initializeRevenueBreakdownChart() {
    const ctx = document.getElementById('revenueBreakdownChart').getContext('2d');
    currentCharts.revenueBreakdown = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['1 يناير', '8 يناير', '15 يناير', '22 يناير', '29 يناير', '5 فبراير', '12 فبراير'],
            datasets: [{
                label: 'الإيرادات',
                data: [12000, 15000, 18000, 22000, 19000, 25000, 28000],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'التكاليف',
                data: [8000, 9500, 11000, 13000, 12000, 15000, 16000],
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        font: { family: 'Cairo, sans-serif' }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: { family: 'Cairo, sans-serif' },
                        callback: function(value) {
                            return value.toLocaleString('ar-SA') + ' ر.س';
                        }
                    }
                },
                x: {
                    ticks: {
                        font: { family: 'Cairo, sans-serif' }
                    }
                }
            }
        }
    });
}

function initializeServicePerformanceChart() {
    const ctx = document.getElementById('servicePerformanceChart').getContext('2d');
    currentCharts.servicePerformance = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['الجودة', 'السرعة', 'التوفر', 'السعر', 'رضا العملاء', 'النمو'],
            datasets: [{
                label: 'الأداء الحالي',
                data: [85, 78, 92, 73, 88, 82],
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                pointBackgroundColor: 'rgb(16, 185, 129)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgb(16, 185, 129)'
            }, {
                label: 'الهدف المطلوب',
                data: [90, 85, 95, 80, 90, 90],
                borderColor: 'rgb(251, 146, 60)',
                backgroundColor: 'rgba(251, 146, 60, 0.2)',
                pointBackgroundColor: 'rgb(251, 146, 60)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgb(251, 146, 60)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        font: { family: 'Cairo, sans-serif' }
                    }
                }
            },
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        font: { family: 'Cairo, sans-serif' }
                    },
                    pointLabels: {
                        font: { family: 'Cairo, sans-serif' }
                    }
                }
            }
        }
    });
}

function initializeCustomerSegmentationChart() {
    const ctx = document.getElementById('customerSegmentationChart').getContext('2d');
    currentCharts.customerSegmentation = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['عملاء VIP', 'عملاء نشطين', 'عملاء عاديين', 'عملاء جدد', 'عملاء غير نشطين'],
            datasets: [{
                data: [15, 35, 30, 12, 8],
                backgroundColor: [
                    'rgb(168, 85, 247)',
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                    'rgb(251, 146, 60)',
                    'rgb(239, 68, 68)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { family: 'Cairo, sans-serif' },
                        padding: 20
                    }
                }
            }
        }
    });
}

function initializeGeographicChart() {
    const ctx = document.getElementById('geographicChart').getContext('2d');
    currentCharts.geographic = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['الرياض', 'جدة', 'الدمام', 'المدينة', 'مكة', 'الطائف', 'أبها', 'تبوك'],
            datasets: [{
                label: 'عدد الحجوزات',
                data: [450, 320, 180, 120, 200, 90, 70, 50],
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }, {
                label: 'الإيرادات (بالآلاف)',
                data: [180, 150, 85, 60, 95, 45, 35, 25],
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderColor: 'rgb(16, 185, 129)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        font: { family: 'Cairo, sans-serif' }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: { family: 'Cairo, sans-serif' }
                    }
                },
                x: {
                    ticks: {
                        font: { family: 'Cairo, sans-serif' }
                    }
                }
            }
        }
    });
}

function setupEventListeners() {
    // Close export menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.export-dropdown')) {
            document.getElementById('exportMenu').classList.remove('show');
        }
    });
}

function switchTab(element, tabId) {
    // Remove active class from all tabs
    document.querySelectorAll('.chart-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Add active class to clicked tab
    element.classList.add('active');
    
    // Update chart data based on tab
    updateChartData(tabId);
}

function updateChartData(tabId) {
    // This would update the chart with new data based on the selected tab
    // Implementation would fetch new data and update the chart
    console.log('Updating chart data for:', tabId);
}

function updateReports() {
    // Show loading
    showLoading();
    
    // Get filter values
    const filters = {
        dateRange: document.getElementById('dateRange').value,
        reportType: document.getElementById('reportType').value,
        groupBy: document.getElementById('groupBy').value,
        merchantFilter: document.getElementById('merchantFilter').value,
        serviceFilter: document.getElementById('serviceFilter').value,
        comparisonPeriod: document.getElementById('comparisonPeriod').value
    };
    
    // Simulate API call
    setTimeout(() => {
        updateChartsWithNewData(filters);
        hideLoading();
    }, 1500);
}

function updateChartsWithNewData(filters) {
    // Update metrics
    animateMetricUpdate('totalRevenue', '678,420 ر.س');
    animateMetricUpdate('totalBookings', '2,156');
    animateMetricUpdate('activeCustomers', '4,123');
    
    // Update charts with new data
    Object.values(currentCharts).forEach(chart => {
        chart.update();
    });
}

function animateMetricUpdate(elementId, newValue) {
    const element = document.getElementById(elementId);
    element.style.transform = 'scale(1.1)';
    element.style.color = '#3b82f6';
    
    setTimeout(() => {
        element.textContent = newValue;
        element.style.transform = 'scale(1)';
        element.style.color = '';
    }, 300);
}

function toggleExportMenu() {
    const menu = document.getElementById('exportMenu');
    menu.classList.toggle('show');
}

function exportReport(format) {
    const filters = {
        dateRange: document.getElementById('dateRange').value,
        reportType: document.getElementById('reportType').value,
        format: format
    };
    
    // Simulate export
    showLoading('جاري تصدير التقرير...');
    
    setTimeout(() => {
        hideLoading();
        alert(`تم تصدير التقرير بصيغة ${format.toUpperCase()} بنجاح!`);
        toggleExportMenu();
    }, 2000);
}

function scheduleReport() {
    alert('ميزة جدولة التقارير ستكون متاحة قريباً!');
    toggleExportMenu();
}

function showLoading(message = 'جاري تحديث البيانات...') {
    const loading = document.createElement('div');
    loading.id = 'loadingOverlay';
    loading.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    loading.innerHTML = `
        <div class="bg-white rounded-lg p-6 text-center">
            <div class="loading-spinner mx-auto mb-4"></div>
            <p class="text-gray-700">${message}</p>
        </div>
    `;
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
