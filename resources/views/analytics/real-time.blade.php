@extends('layouts.app')

@section('title', 'التحليلات الفورية - Real-time Analytics')
@section('description', 'متابعة البيانات والأنشطة في الوقت الفعلي')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/date-fns@2.29.3/index.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@2.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-streaming@2.0.0/dist/chartjs-plugin-streaming.min.js"></script>
<style>
    .realtime-container {
        min-height: calc(100vh - 120px);
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    }
    
    .realtime-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
    }
    
    .realtime-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
        transition: left 0.8s;
    }
    
    .realtime-card:hover::before {
        left: 100%;
    }
    
    .realtime-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
    }
    
    .live-indicator {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        z-index: 10;
        animation: livePulse 2s infinite;
    }
    
    .live-indicator::before {
        content: '';
        width: 8px;
        height: 8px;
        background: #fff;
        border-radius: 50%;
        margin-left: 6px;
        animation: blinkDot 1s infinite;
    }
    
    @keyframes livePulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        50% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
    }
    
    @keyframes blinkDot {
        0%, 50% { opacity: 1; }
        51%, 100% { opacity: 0.3; }
    }
    
    .metric-live {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: white;
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        position: relative;
        transition: all 0.3s ease;
        border: 2px solid #334155;
    }
    
    .metric-live:hover {
        border-color: #3b82f6;
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(59, 130, 246, 0.2);
    }
    
    .metric-value {
        font-size: 2rem;
        font-weight: bold;
        margin: 0.5rem 0;
        transition: all 0.5s ease;
    }
    
    .metric-change {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }
    
    .change-positive {
        color: #10b981;
    }
    
    .change-negative {
        color: #ef4444;
    }
    
    .change-neutral {
        color: #6b7280;
    }
    
    .activity-feed {
        max-height: 400px;
        overflow-y: auto;
        padding: 1rem;
    }
    
    .activity-item {
        display: flex;
        align-items: start;
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        animation: slideInRight 0.5s ease;
    }
    
    .activity-item:hover {
        background: #f8fafc;
        border-left-color: #3b82f6;
        transform: translateX(-5px);
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 1rem;
        font-size: 1.2rem;
    }
    
    .activity-booking {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e40af;
    }
    
    .activity-payment {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #16a34a;
    }
    
    .activity-user {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #d97706;
    }
    
    .activity-error {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #dc2626;
    }
    
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
        padding: 1rem;
    }
    
    .status-indicator {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin: 4px;
    }
    
    .status-online {
        background: #dcfce7;
        color: #16a34a;
    }
    
    .status-busy {
        background: #fef3c7;
        color: #d97706;
    }
    
    .status-offline {
        background: #fee2e2;
        color: #dc2626;
    }
    
    .performance-gauge {
        width: 150px;
        height: 150px;
        position: relative;
        margin: 0 auto;
    }
    
    .gauge-bg {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: conic-gradient(from 0deg, #ef4444 0deg 60deg, #f59e0b 60deg 120deg, #10b981 120deg 180deg, #e5e7eb 180deg 360deg);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .gauge-bg::before {
        content: '';
        width: 70%;
        height: 70%;
        background: white;
        border-radius: 50%;
        position: absolute;
    }
    
    .gauge-value {
        position: relative;
        z-index: 10;
        font-size: 1.5rem;
        font-weight: bold;
        color: #374151;
    }
    
    .server-status {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .server-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
    }
    
    .server-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    }
    
    .heatmap-container {
        display: grid;
        grid-template-columns: repeat(24, 1fr);
        gap: 2px;
        padding: 1rem;
    }
    
    .heatmap-cell {
        aspect-ratio: 1;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: bold;
        color: white;
    }
    
    .heatmap-cell:hover {
        transform: scale(1.2);
        z-index: 10;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }
    
    .alert-panel {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border: 2px solid #ef4444;
        border-radius: 16px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .alert-item {
        display: flex;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid rgba(239, 68, 68, 0.2);
    }
    
    .alert-item:last-child {
        border-bottom: none;
    }
    
    .refresh-button {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border-radius: 50%;
        color: white;
        border: none;
        cursor: pointer;
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        transition: all 0.3s ease;
        z-index: 40;
    }
    
    .refresh-button:hover {
        transform: scale(1.1);
        box-shadow: 0 12px 35px rgba(59, 130, 246, 0.4);
    }
    
    .refresh-button.spinning {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .connection-status {
        position: fixed;
        top: 120px;
        right: 1rem;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 0.5rem 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
        z-index: 50;
    }
    
    .data-stream {
        font-family: 'Courier New', monospace;
        background: #0f172a;
        color: #10b981;
        padding: 1rem;
        border-radius: 8px;
        height: 200px;
        overflow-y: auto;
        font-size: 12px;
        line-height: 1.4;
    }
    
    .stream-line {
        margin: 2px 0;
        opacity: 0;
        animation: fadeInStream 0.5s ease forwards;
    }
    
    @keyframes fadeInStream {
        to {
            opacity: 1;
        }
    }
</style>
@endpush

@section('content')
<div class="realtime-container">
    
    <!-- Header -->
    <div class="bg-white shadow-lg border-b-4 border-red-500 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-pink-500 rounded-xl flex items-center justify-center">
                        <span class="text-white text-2xl">⚡</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">التحليلات الفورية</h1>
                        <p class="text-sm text-gray-600">متابعة الأنشطة في الوقت الفعلي</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3 space-x-reverse">
                    <!-- Connection Status -->
                    <div class="connection-status">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse ml-2"></div>
                            <span class="text-green-700 font-medium text-sm">متصل</span>
                        </div>
                    </div>
                    
                    <!-- Auto-refresh Toggle -->
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="autoRefresh" class="sr-only" checked onchange="toggleAutoRefresh()">
                        <div class="relative">
                            <div class="block bg-gray-600 w-14 h-8 rounded-full"></div>
                            <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition transform"></div>
                        </div>
                        <span class="mr-3 text-gray-700 text-sm font-medium">تحديث تلقائي</span>
                    </label>
                    
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
        
        <!-- Live Metrics -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6 mb-8">
            <div class="metric-live">
                <div class="live-indicator">LIVE</div>
                <div class="text-3xl mb-2">👥</div>
                <div class="metric-value" id="activeUsers">247</div>
                <div class="text-sm opacity-75">المستخدمون النشطون</div>
                <div class="metric-change change-positive">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    +12
                </div>
            </div>
            
            <div class="metric-live">
                <div class="live-indicator">LIVE</div>
                <div class="text-3xl mb-2">📋</div>
                <div class="metric-value" id="pendingBookings">23</div>
                <div class="text-sm opacity-75">حجوزات في الانتظار</div>
                <div class="metric-change change-negative">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                    -5
                </div>
            </div>
            
            <div class="metric-live">
                <div class="live-indicator">LIVE</div>
                <div class="text-3xl mb-2">💰</div>
                <div class="metric-value" id="todayRevenue">12,450</div>
                <div class="text-sm opacity-75">إيرادات اليوم (ر.س)</div>
                <div class="metric-change change-positive">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    +2,340
                </div>
            </div>
            
            <div class="metric-live">
                <div class="live-indicator">LIVE</div>
                <div class="text-3xl mb-2">📊</div>
                <div class="metric-value" id="conversionRate">68.5%</div>
                <div class="text-sm opacity-75">معدل التحويل</div>
                <div class="metric-change change-positive">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    +1.2%
                </div>
            </div>
            
            <div class="metric-live">
                <div class="live-indicator">LIVE</div>
                <div class="text-3xl mb-2">🏪</div>
                <div class="metric-value" id="onlineMerchants">89</div>
                <div class="text-sm opacity-75">التجار المتاحون</div>
                <div class="metric-change change-neutral">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                    0
                </div>
            </div>
            
            <div class="metric-live">
                <div class="live-indicator">LIVE</div>
                <div class="text-3xl mb-2">⚡</div>
                <div class="metric-value" id="systemLoad">34%</div>
                <div class="text-sm opacity-75">حمولة النظام</div>
                <div class="metric-change change-positive">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                    -8%
                </div>
            </div>
        </div>

        <!-- Real-time Charts -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
            <!-- Live Traffic -->
            <div class="realtime-card">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        حركة المرور المباشرة
                    </h3>
                    <div class="chart-container">
                        <canvas id="liveTrafficChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Revenue Stream -->
            <div class="realtime-card">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                        تدفق الإيرادات
                    </h3>
                    <div class="chart-container">
                        <canvas id="revenueStreamChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Feed & Performance Monitor -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
            <!-- Live Activity Feed -->
            <div class="realtime-card xl:col-span-2">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 4v10a1 1 0 001 1h8a1 1 0 001-1V8M9 4h6M9 8v8m2-8v8m2-8v8"/>
                        </svg>
                        التحديثات المباشرة
                    </h3>
                    <div class="activity-feed" id="activityFeed">
                        <!-- Activities will be populated by JavaScript -->
                    </div>
                </div>
            </div>
            
            <!-- System Performance -->
            <div class="realtime-card">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        أداء النظام
                    </h3>
                    
                    <div class="space-y-6">
                        <div class="text-center">
                            <h4 class="font-medium text-gray-700 mb-3">الأداء العام</h4>
                            <div class="performance-gauge">
                                <div class="gauge-bg">
                                    <div class="gauge-value">87%</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-600">استخدام المعالج</span>
                                <span class="text-sm font-bold text-blue-600">34%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-1000" style="width: 34%"></div>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-600">استخدام الذاكرة</span>
                                <span class="text-sm font-bold text-green-600">67%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full transition-all duration-1000" style="width: 67%"></div>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-600">استخدام قاعدة البيانات</span>
                                <span class="text-sm font-bold text-yellow-600">45%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-600 h-2 rounded-full transition-all duration-1000" style="width: 45%"></div>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-600">سرعة الاستجابة</span>
                                <span class="text-sm font-bold text-purple-600">156ms</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full transition-all duration-1000" style="width: 78%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Heatmap & Alerts -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
            <!-- User Activity Heatmap -->
            <div class="realtime-card">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                        </svg>
                        خريطة النشاط (آخر 24 ساعة)
                    </h3>
                    <div class="heatmap-container" id="activityHeatmap">
                        <!-- Heatmap cells will be generated by JavaScript -->
                    </div>
                    <div class="flex justify-between items-center mt-4 text-sm text-gray-600">
                        <span>منخفض</span>
                        <div class="flex space-x-1 space-x-reverse">
                            <div class="w-4 h-4 bg-green-200 rounded"></div>
                            <div class="w-4 h-4 bg-green-400 rounded"></div>
                            <div class="w-4 h-4 bg-green-600 rounded"></div>
                            <div class="w-4 h-4 bg-green-800 rounded"></div>
                        </div>
                        <span>مرتفع</span>
                    </div>
                </div>
            </div>
            
            <!-- System Alerts -->
            <div class="realtime-card">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        تنبيهات النظام
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="alert-panel">
                            <h4 class="font-bold text-red-800 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/>
                                </svg>
                                تنبيهات عاجلة
                            </h4>
                            <div class="alert-item">
                                <span class="text-2xl mr-3">🚨</span>
                                <div>
                                    <div class="font-medium text-red-800">خطأ في معالجة الدفع</div>
                                    <div class="text-sm text-red-600">منذ 2 دقيقة - يؤثر على 3 معاملات</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 border-2 border-yellow-300 rounded-16 p-4">
                            <h4 class="font-bold text-yellow-800 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01"/>
                                </svg>
                                تحذيرات
                            </h4>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <span class="text-lg mr-3">⚠️</span>
                                    <div class="text-sm">
                                        <div class="font-medium text-yellow-800">ارتفاع في زمن الاستجابة</div>
                                        <div class="text-yellow-600">منذ 8 دقائق</div>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-lg mr-3">📊</span>
                                    <div class="text-sm">
                                        <div class="font-medium text-yellow-800">استخدام مرتفع للذاكرة</div>
                                        <div class="text-yellow-600">منذ 15 دقيقة</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 border-2 border-blue-300 rounded-16 p-4">
                            <h4 class="font-bold text-blue-800 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01"/>
                                </svg>
                                معلومات
                            </h4>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <span class="text-lg mr-3">✅</span>
                                    <div class="text-sm">
                                        <div class="font-medium text-blue-800">تم تحديث قاعدة البيانات</div>
                                        <div class="text-blue-600">منذ 30 دقيقة</div>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-lg mr-3">🔄</span>
                                    <div class="text-sm">
                                        <div class="font-medium text-blue-800">بدء نسخة احتياطية جديدة</div>
                                        <div class="text-blue-600">منذ ساعة</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Stream Monitor -->
        <div class="realtime-card">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    مراقب تدفق البيانات
                </h3>
                <div class="data-stream" id="dataStream">
                    <!-- Data stream will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Refresh Button -->
    <button class="refresh-button" onclick="refreshAllData()" title="تحديث البيانات">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
    </button>
</div>

@push('scripts')
<script>
let realtimeCharts = {};
let realtimeIntervals = {};
let autoRefreshEnabled = true;

document.addEventListener('DOMContentLoaded', function() {
    initializeRealtimeCharts();
    startRealtimeUpdates();
    generateActivityHeatmap();
    populateActivityFeed();
    startDataStream();
});

function initializeRealtimeCharts() {
    initializeLiveTrafficChart();
    initializeRevenueStreamChart();
}

function initializeLiveTrafficChart() {
    const ctx = document.getElementById('liveTrafficChart').getContext('2d');
    realtimeCharts.traffic = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: [{
                label: 'زوار',
                data: [],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'حجوزات',
                data: [],
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
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
                x: {
                    type: 'realtime',
                    realtime: {
                        duration: 60000,
                        refresh: 2000,
                        delay: 1000,
                        onRefresh: function(chart) {
                            if (autoRefreshEnabled) {
                                chart.data.datasets[0].data.push({
                                    x: Date.now(),
                                    y: Math.floor(Math.random() * 50) + 30
                                });
                                chart.data.datasets[1].data.push({
                                    x: Date.now(),
                                    y: Math.floor(Math.random() * 15) + 5
                                });
                            }
                        }
                    },
                    ticks: {
                        font: { family: 'Cairo, sans-serif' }
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: { family: 'Cairo, sans-serif' }
                    }
                }
            }
        }
    });
}

function initializeRevenueStreamChart() {
    const ctx = document.getElementById('revenueStreamChart').getContext('2d');
    realtimeCharts.revenue = new Chart(ctx, {
        type: 'bar',
        data: {
            datasets: [{
                label: 'الإيرادات (ر.س)',
                data: [],
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
                x: {
                    type: 'realtime',
                    realtime: {
                        duration: 120000,
                        refresh: 5000,
                        delay: 2000,
                        onRefresh: function(chart) {
                            if (autoRefreshEnabled) {
                                chart.data.datasets[0].data.push({
                                    x: Date.now(),
                                    y: Math.floor(Math.random() * 500) + 100
                                });
                            }
                        }
                    },
                    ticks: {
                        font: { family: 'Cairo, sans-serif' }
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: { family: 'Cairo, sans-serif' },
                        callback: function(value) {
                            return value.toLocaleString('ar-SA') + ' ر.س';
                        }
                    }
                }
            }
        }
    });
}

function startRealtimeUpdates() {
    // Update live metrics every 3 seconds
    realtimeIntervals.metrics = setInterval(() => {
        if (autoRefreshEnabled) {
            updateLiveMetrics();
        }
    }, 3000);
    
    // Update activity feed every 5 seconds
    realtimeIntervals.activities = setInterval(() => {
        if (autoRefreshEnabled) {
            addNewActivity();
        }
    }, 5000);
    
    // Update system performance every 10 seconds
    realtimeIntervals.performance = setInterval(() => {
        if (autoRefreshEnabled) {
            updateSystemPerformance();
        }
    }, 10000);
}

function updateLiveMetrics() {
    const metrics = [
        { id: 'activeUsers', change: Math.floor(Math.random() * 21) - 10 },
        { id: 'pendingBookings', change: Math.floor(Math.random() * 11) - 5 },
        { id: 'todayRevenue', change: Math.floor(Math.random() * 1001) },
        { id: 'conversionRate', change: (Math.random() * 4 - 2).toFixed(1) },
        { id: 'onlineMerchants', change: Math.floor(Math.random() * 7) - 3 },
        { id: 'systemLoad', change: Math.floor(Math.random() * 11) - 5 }
    ];
    
    metrics.forEach(metric => {
        const element = document.getElementById(metric.id);
        if (element) {
            const currentValue = parseFloat(element.textContent.replace(/[^\d.-]/g, ''));
            const newValue = Math.max(0, currentValue + metric.change);
            
            // Animate the change
            element.style.transition = 'all 0.5s ease';
            element.style.transform = 'scale(1.1)';
            element.style.color = metric.change > 0 ? '#10b981' : metric.change < 0 ? '#ef4444' : '#6b7280';
            
            setTimeout(() => {
                if (metric.id === 'conversionRate') {
                    element.textContent = newValue.toFixed(1) + '%';
                } else if (metric.id === 'systemLoad') {
                    element.textContent = Math.min(100, newValue) + '%';
                } else {
                    element.textContent = Math.floor(newValue).toLocaleString('ar-SA');
                }
                
                element.style.transform = 'scale(1)';
                element.style.color = '';
            }, 250);
        }
    });
}

function populateActivityFeed() {
    const activities = [
        { type: 'booking', icon: '📋', text: 'حجز جديد من أحمد محمد', time: 'الآن', color: 'activity-booking' },
        { type: 'payment', icon: '💰', text: 'تم استلام دفعة 250 ر.س', time: 'منذ دقيقة', color: 'activity-payment' },
        { type: 'user', icon: '👤', text: 'مستخدم جديد انضم للمنصة', time: 'منذ 3 دقائق', color: 'activity-user' },
        { type: 'booking', icon: '✅', text: 'تم تأكيد حجز لخدمة التنظيف', time: 'منذ 5 دقائق', color: 'activity-booking' },
        { type: 'error', icon: '⚠️', text: 'فشل في معالجة الدفع', time: 'منذ 7 دقائق', color: 'activity-error' }
    ];
    
    const feed = document.getElementById('activityFeed');
    feed.innerHTML = '';
    
    activities.forEach(activity => {
        const item = document.createElement('div');
        item.className = 'activity-item';
        item.innerHTML = `
            <div class="activity-icon ${activity.color}">
                ${activity.icon}
            </div>
            <div class="flex-1">
                <div class="font-medium text-gray-800">${activity.text}</div>
                <div class="text-sm text-gray-500">${activity.time}</div>
            </div>
        `;
        feed.appendChild(item);
    });
}

function addNewActivity() {
    const newActivities = [
        { type: 'booking', icon: '📋', text: 'حجز جديد من سارة أحمد', color: 'activity-booking' },
        { type: 'payment', icon: '💰', text: 'تم استلام دفعة 180 ر.س', color: 'activity-payment' },
        { type: 'user', icon: '👤', text: 'تاجر جديد انضم للمنصة', color: 'activity-user' },
        { type: 'booking', icon: '✅', text: 'تم إنجاز خدمة الصيانة', color: 'activity-booking' }
    ];
    
    const randomActivity = newActivities[Math.floor(Math.random() * newActivities.length)];
    const feed = document.getElementById('activityFeed');
    
    const item = document.createElement('div');
    item.className = 'activity-item';
    item.innerHTML = `
        <div class="activity-icon ${randomActivity.color}">
            ${randomActivity.icon}
        </div>
        <div class="flex-1">
            <div class="font-medium text-gray-800">${randomActivity.text}</div>
            <div class="text-sm text-gray-500">الآن</div>
        </div>
    `;
    
    feed.insertBefore(item, feed.firstChild);
    
    // Remove old activities if more than 8
    if (feed.children.length > 8) {
        feed.removeChild(feed.lastChild);
    }
}

function generateActivityHeatmap() {
    const container = document.getElementById('activityHeatmap');
    container.innerHTML = '';
    
    for (let hour = 0; hour < 24; hour++) {
        const cell = document.createElement('div');
        cell.className = 'heatmap-cell';
        
        const intensity = Math.random();
        if (intensity < 0.25) {
            cell.style.backgroundColor = '#dcfce7';
            cell.style.color = '#166534';
        } else if (intensity < 0.5) {
            cell.style.backgroundColor = '#86efac';
            cell.style.color = '#166534';
        } else if (intensity < 0.75) {
            cell.style.backgroundColor = '#22c55e';
            cell.style.color = '#ffffff';
        } else {
            cell.style.backgroundColor = '#15803d';
            cell.style.color = '#ffffff';
        }
        
        cell.textContent = hour.toString().padStart(2, '0');
        cell.title = `الساعة ${hour}:00 - ${Math.floor(intensity * 100)} نشاط`;
        
        container.appendChild(cell);
    }
}

function updateSystemPerformance() {
    const metrics = ['cpu', 'memory', 'database', 'response'];
    metrics.forEach(metric => {
        const value = Math.floor(Math.random() * 30) + 40;
        const bar = document.querySelector(`[style*="width: ${metric}"]`);
        if (bar) {
            bar.style.width = value + '%';
        }
    });
}

function startDataStream() {
    const stream = document.getElementById('dataStream');
    const commands = [
        '[INFO] تم تسجيل دخول مستخدم جديد - IP: 192.168.1.45',
        '[SUCCESS] معالجة دفعة ناجحة - المبلغ: 250 ر.س',
        '[WARNING] استخدام مرتفع للذاكرة - 67%',
        '[INFO] تم إنشاء حجز جديد - ID: BK2024001847',
        '[ERROR] فشل في الاتصال بقاعدة البيانات',
        '[SUCCESS] تم تحديث بيانات التاجر - ID: MR00456',
        '[INFO] طلب API جديد - Endpoint: /api/bookings',
        '[SUCCESS] تم إرسال إشعار للعميل'
    ];
    
    let lineCount = 0;
    
    realtimeIntervals.dataStream = setInterval(() => {
        if (autoRefreshEnabled && lineCount < 50) {
            const randomCommand = commands[Math.floor(Math.random() * commands.length)];
            const timestamp = new Date().toLocaleTimeString('ar-SA');
            
            const line = document.createElement('div');
            line.className = 'stream-line';
            line.textContent = `[${timestamp}] ${randomCommand}`;
            
            stream.appendChild(line);
            stream.scrollTop = stream.scrollHeight;
            
            lineCount++;
            
            // Remove old lines
            if (stream.children.length > 20) {
                stream.removeChild(stream.firstChild);
                lineCount--;
            }
        }
    }, 2000);
}

function toggleAutoRefresh() {
    autoRefreshEnabled = !autoRefreshEnabled;
    const toggle = document.getElementById('autoRefresh');
    const dot = toggle.nextElementSibling.querySelector('.dot');
    
    if (autoRefreshEnabled) {
        toggle.checked = true;
        dot.style.transform = 'translateX(24px)';
        toggle.parentElement.querySelector('.bg-gray-600').classList.remove('bg-gray-600');
        toggle.parentElement.querySelector('div').classList.add('bg-blue-600');
    } else {
        toggle.checked = false;
        dot.style.transform = 'translateX(0)';
        toggle.parentElement.querySelector('.bg-blue-600').classList.remove('bg-blue-600');
        toggle.parentElement.querySelector('div').classList.add('bg-gray-600');
    }
}

function refreshAllData() {
    const button = document.querySelector('.refresh-button');
    button.classList.add('spinning');
    
    // Refresh all metrics
    updateLiveMetrics();
    addNewActivity();
    updateSystemPerformance();
    generateActivityHeatmap();
    
    // Update charts
    Object.values(realtimeCharts).forEach(chart => {
        chart.update();
    });
    
    setTimeout(() => {
        button.classList.remove('spinning');
    }, 1000);
}

// Cleanup intervals when page unloads
window.addEventListener('beforeunload', () => {
    Object.values(realtimeIntervals).forEach(interval => {
        clearInterval(interval);
    });
});
</script>
@endpush
@endsection
