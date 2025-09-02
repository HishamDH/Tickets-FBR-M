@extends('layouts.app')

@section('title', 'التحليلات التنبؤية - Predictive Analytics')
@section('description', 'تنبؤات ذكية لاتجاهات الأعمال والنمو المستقبلي')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/date-fns@2.29.3/index.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@2.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@2.2.1/dist/chartjs-plugin-annotation.min.js"></script>
<style>
    .predictive-container {
        min-height: calc(100vh - 120px);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .prediction-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
    }
    
    .prediction-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        transition: left 0.6s;
    }
    
    .prediction-card:hover::before {
        left: 100%;
    }
    
    .prediction-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
    }
    
    .ai-indicator {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        z-index: 10;
    }
    
    .ai-indicator::before {
        content: '🤖';
        margin-left: 4px;
    }
    
    .prediction-header {
        background: linear-gradient(135deg, #1e40af 0%, #3730a3 100%);
        color: white;
        border-radius: 24px 24px 0 0;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }
    
    .prediction-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 20%, transparent 50%);
        animation: predictiveFloat 10s ease-in-out infinite;
    }
    
    @keyframes predictiveFloat {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        33% { transform: translateY(-15px) rotate(120deg); }
        66% { transform: translateY(-10px) rotate(240deg); }
    }
    
    .forecast-metric {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 2px solid #0ea5e9;
        border-radius: 20px;
        padding: 1.5rem;
        text-align: center;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .forecast-metric:hover {
        transform: translateY(-5px);
        border-color: #0284c7;
        box-shadow: 0 15px 30px rgba(14, 165, 233, 0.2);
    }
    
    .confidence-indicator {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(0,0,0,0.1);
        border-radius: 12px;
        padding: 4px 8px;
        font-size: 10px;
        font-weight: 600;
    }
    
    .confidence-high {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #16a34a;
    }
    
    .confidence-medium {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #d97706;
    }
    
    .confidence-low {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #dc2626;
    }
    
    .trend-arrow {
        font-size: 2rem;
        animation: bounce 2s infinite;
    }
    
    .trend-up {
        color: #10b981;
        transform: rotate(-45deg);
    }
    
    .trend-down {
        color: #ef4444;
        transform: rotate(45deg);
    }
    
    .trend-stable {
        color: #6b7280;
        transform: rotate(0deg);
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }
    
    .scenario-tab {
        padding: 12px 24px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
        border: 2px solid transparent;
        background: #f8fafc;
        color: #64748b;
        margin: 0 4px;
    }
    
    .scenario-tab.active {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        border-color: #1e40af;
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
    }
    
    .risk-indicator {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin: 4px;
    }
    
    .risk-low {
        background: #dcfce7;
        color: #16a34a;
    }
    
    .risk-medium {
        background: #fef3c7;
        color: #d97706;
    }
    
    .risk-high {
        background: #fee2e2;
        color: #dc2626;
    }
    
    .ml-model-card {
        background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
        border: 2px solid #a855f7;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .accuracy-meter {
        width: 120px;
        height: 120px;
        position: relative;
        margin: 0 auto;
    }
    
    .accuracy-circle {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: conic-gradient(from 0deg, #10b981 0deg 288deg, #e5e7eb 288deg 360deg);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .accuracy-circle::before {
        content: '';
        width: 80%;
        height: 80%;
        background: white;
        border-radius: 50%;
        position: absolute;
    }
    
    .accuracy-value {
        position: relative;
        z-index: 10;
        font-size: 1.5rem;
        font-weight: bold;
        color: #374151;
    }
    
    .prediction-timeline {
        position: relative;
        padding: 2rem 0;
    }
    
    .timeline-item {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
        position: relative;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 50px;
        width: 2px;
        height: 60px;
        background: linear-gradient(to bottom, #3b82f6, #e5e7eb);
    }
    
    .timeline-item:last-child::before {
        display: none;
    }
    
    .timeline-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 1rem;
        z-index: 10;
        position: relative;
    }
    
    .factor-impact {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 8px;
        border-left: 4px solid #3b82f6;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .impact-bar {
        height: 8px;
        background: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 8px;
    }
    
    .impact-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981, #059669);
        border-radius: 4px;
        transition: width 1s ease;
    }
    
    .chart-container {
        position: relative;
        height: 400px;
        width: 100%;
        padding: 1rem;
    }
    
    .insight-bubble {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 2px solid #f59e0b;
        border-radius: 20px;
        padding: 1rem;
        margin: 1rem 0;
        position: relative;
    }
    
    .insight-bubble::before {
        content: '💡';
        position: absolute;
        top: -15px;
        left: 20px;
        background: #f59e0b;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
    
    .loading-brain {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        animation: thinkingPulse 2s ease-in-out infinite;
    }
    
    .loading-brain::before {
        content: '🧠';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 2rem;
    }
    
    @keyframes thinkingPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
</style>
@endpush

@section('content')
<div class="predictive-container">
    
    <!-- Header -->
    <div class="bg-white shadow-lg border-b-4 border-blue-500 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded-xl flex items-center justify-center">
                        <span class="text-white text-2xl">🔮</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">التحليلات التنبؤية</h1>
                        <p class="text-sm text-gray-600">تنبؤات ذكية مدعومة بالذكاء الاصطناعي</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3 space-x-reverse">
                    <!-- AI Status -->
                    <div class="bg-green-100 border border-green-300 rounded-lg px-4 py-2 flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse ml-2"></div>
                        <span class="text-green-700 font-medium text-sm">نموذج الذكاء الاصطناعي نشط</span>
                    </div>
                    
                    <!-- Refresh Predictions -->
                    <button onclick="refreshPredictions()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 space-x-reverse transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>تحديث التنبؤات</span>
                    </button>
                    
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
        
        <!-- Forecast Metrics -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6 mb-8">
            <div class="forecast-metric">
                <div class="ai-indicator">AI</div>
                <div class="confidence-indicator confidence-high">دقة 95%</div>
                <div class="text-3xl mb-2">💰</div>
                <div class="text-2xl font-bold text-blue-800">725,000 ر.س</div>
                <div class="text-sm text-blue-600 mb-2">الإيرادات المتوقعة (الشهر القادم)</div>
                <div class="trend-arrow trend-up">↗</div>
                <div class="text-xs text-green-600 font-medium">+35.8% نمو متوقع</div>
            </div>
            
            <div class="forecast-metric">
                <div class="ai-indicator">AI</div>
                <div class="confidence-indicator confidence-high">دقة 92%</div>
                <div class="text-3xl mb-2">📈</div>
                <div class="text-2xl font-bold text-blue-800">2,450</div>
                <div class="text-sm text-blue-600 mb-2">الحجوزات المتوقعة</div>
                <div class="trend-arrow trend-up">↗</div>
                <div class="text-xs text-green-600 font-medium">+28.2% زيادة</div>
            </div>
            
            <div class="forecast-metric">
                <div class="ai-indicator">AI</div>
                <div class="confidence-indicator confidence-medium">دقة 87%</div>
                <div class="text-3xl mb-2">👥</div>
                <div class="text-2xl font-bold text-blue-800">5,680</div>
                <div class="text-sm text-blue-600 mb-2">عملاء جدد متوقعين</div>
                <div class="trend-arrow trend-up">↗</div>
                <div class="text-xs text-green-600 font-medium">+42.1% نمو</div>
            </div>
            
            <div class="forecast-metric">
                <div class="ai-indicator">AI</div>
                <div class="confidence-indicator confidence-high">دقة 94%</div>
                <div class="text-3xl mb-2">⭐</div>
                <div class="text-2xl font-bold text-blue-800">4.85</div>
                <div class="text-sm text-blue-600 mb-2">متوسط التقييم المتوقع</div>
                <div class="trend-arrow trend-up">↗</div>
                <div class="text-xs text-green-600 font-medium">+3.2% تحسن</div>
            </div>
            
            <div class="forecast-metric">
                <div class="ai-indicator">AI</div>
                <div class="confidence-indicator confidence-medium">دقة 89%</div>
                <div class="text-3xl mb-2">🏪</div>
                <div class="text-2xl font-bold text-blue-800">165</div>
                <div class="text-sm text-blue-600 mb-2">تجار جدد متوقعين</div>
                <div class="trend-arrow trend-up">↗</div>
                <div class="text-xs text-green-600 font-medium">+29.9% نمو</div>
            </div>
            
            <div class="forecast-metric">
                <div class="ai-indicator">AI</div>
                <div class="confidence-indicator confidence-low">دقة 78%</div>
                <div class="text-3xl mb-2">📊</div>
                <div class="text-2xl font-bold text-blue-800">73.5%</div>
                <div class="text-sm text-blue-600 mb-2">معدل التحويل المتوقع</div>
                <div class="trend-arrow trend-up">↗</div>
                <div class="text-xs text-green-600 font-medium">+8.4% تحسن</div>
            </div>
        </div>

        <!-- Scenario Analysis -->
        <div class="prediction-card mb-8">
            <div class="prediction-header">
                <h2 class="text-2xl font-bold mb-2">تحليل السيناريوهات</h2>
                <p class="opacity-90">مقارنة السيناريوهات المختلفة للأشهر القادمة</p>
            </div>
            
            <div class="p-6">
                <div class="flex justify-center mb-6">
                    <div class="flex bg-gray-100 rounded-2xl p-2">
                        <div class="scenario-tab active" onclick="switchScenario(this, 'optimistic')">السيناريو المتفائل</div>
                        <div class="scenario-tab" onclick="switchScenario(this, 'realistic')">السيناريو الواقعي</div>
                        <div class="scenario-tab" onclick="switchScenario(this, 'pessimistic')">السيناريو المتشائم</div>
                    </div>
                </div>
                
                <div class="chart-container">
                    <canvas id="scenarioChart"></canvas>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div class="text-center">
                        <h4 class="font-bold text-green-700 mb-2">السيناريو المتفائل</h4>
                        <div class="text-2xl font-bold text-green-600">+45%</div>
                        <div class="text-sm text-gray-600">نمو في الإيرادات</div>
                        <div class="risk-indicator risk-medium mt-2">مخاطر متوسطة</div>
                    </div>
                    
                    <div class="text-center">
                        <h4 class="font-bold text-blue-700 mb-2">السيناريو الواقعي</h4>
                        <div class="text-2xl font-bold text-blue-600">+25%</div>
                        <div class="text-sm text-gray-600">نمو في الإيرادات</div>
                        <div class="risk-indicator risk-low mt-2">مخاطر منخفضة</div>
                    </div>
                    
                    <div class="text-center">
                        <h4 class="font-bold text-red-700 mb-2">السيناريو المتشائم</h4>
                        <div class="text-2xl font-bold text-red-600">+8%</div>
                        <div class="text-sm text-gray-600">نمو في الإيرادات</div>
                        <div class="risk-indicator risk-high mt-2">مخاطر عالية</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Predictive Models Performance -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
            <!-- ML Models Status -->
            <div class="prediction-card">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        أداء نماذج التعلم الآلي
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="ml-model-card">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-purple-800">نموذج تنبؤ الإيرادات</h4>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-bold">نشط</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="accuracy-meter">
                                    <div class="accuracy-circle">
                                        <div class="accuracy-value">95%</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-purple-700 mb-1">آخر تدريب: منذ 2 ساعة</div>
                                    <div class="text-sm text-purple-700 mb-1">عدد العينات: 45,000</div>
                                    <div class="text-sm text-purple-700">معدل الخطأ: 2.1%</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ml-model-card">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-purple-800">نموذج سلوك العملاء</h4>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-bold">نشط</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="accuracy-meter">
                                    <div class="accuracy-circle" style="background: conic-gradient(from 0deg, #10b981 0deg 331deg, #e5e7eb 331deg 360deg);">
                                        <div class="accuracy-value">92%</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-purple-700 mb-1">آخر تدريب: منذ 4 ساعات</div>
                                    <div class="text-sm text-purple-700 mb-1">عدد العينات: 28,500</div>
                                    <div class="text-sm text-purple-700">معدل الخطأ: 3.8%</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="ml-model-card">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-purple-800">نموذج تحليل المخاطر</h4>
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-bold">تدريب</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="accuracy-meter">
                                    <div class="accuracy-circle" style="background: conic-gradient(from 0deg, #10b981 0deg 310deg, #e5e7eb 310deg 360deg);">
                                        <div class="accuracy-value">86%</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-purple-700 mb-1">آخر تدريب: جاري الآن</div>
                                    <div class="text-sm text-purple-700 mb-1">عدد العينات: 15,200</div>
                                    <div class="text-sm text-purple-700">معدل الخطأ: 6.2%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Trend Predictions -->
            <div class="prediction-card">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        تنبؤات الاتجاهات
                    </h3>
                    
                    <div class="chart-container">
                        <canvas id="trendPredictionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prediction Timeline -->
        <div class="prediction-card mb-8">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    الجدول الزمني للتنبؤات
                </h3>
                
                <div class="prediction-timeline">
                    <div class="timeline-item">
                        <div class="timeline-icon">1</div>
                        <div class="flex-1 mr-4">
                            <h4 class="font-bold text-gray-800">الأسبوع القادم</h4>
                            <p class="text-gray-600 mb-2">نمو متوقع في الحجوزات بنسبة 12%</p>
                            <div class="insight-bubble">
                                <strong>توقع:</strong> زيادة الطلب على خدمات نهاية الأسبوع بسبب العطلة المدرسية
                            </div>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-icon">2</div>
                        <div class="flex-1 mr-4">
                            <h4 class="font-bold text-gray-800">خلال شهر</h4>
                            <p class="text-gray-600 mb-2">دخول 85 تاجر جديد للمنصة</p>
                            <div class="insight-bubble">
                                <strong>توقع:</strong> نمو قطاع الخدمات المنزلية سيجذب المزيد من مقدمي الخدمات
                            </div>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-icon">3</div>
                        <div class="flex-1 mr-4">
                            <h4 class="font-bold text-gray-800">خلال 3 أشهر</h4>
                            <p class="text-gray-600 mb-2">الوصول إلى مليون ريال إيرادات شهرية</p>
                            <div class="insight-bubble">
                                <strong>توقع:</strong> النمو المتسارع والتوسع في المدن الجديدة سيحقق هذا الهدف
                            </div>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-icon">4</div>
                        <div class="flex-1 mr-4">
                            <h4 class="font-bold text-gray-800">خلال 6 أشهر</h4>
                            <p class="text-gray-600 mb-2">تحقيق ريادة السوق في قطاع الخدمات</p>
                            <div class="insight-bubble">
                                <strong>توقع:</strong> الاستثمار في التكنولوجيا وتجربة المستخدم سيمنح ميزة تنافسية قوية
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Influencing Factors -->
        <div class="prediction-card mb-8">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                    العوامل المؤثرة في التنبؤات
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-bold text-gray-700 mb-4">العوامل الإيجابية</h4>
                        
                        <div class="factor-impact">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium">النمو الاقتصادي</span>
                                <span class="text-green-600 font-bold">+15%</span>
                            </div>
                            <div class="impact-bar">
                                <div class="impact-fill" style="width: 75%"></div>
                            </div>
                        </div>
                        
                        <div class="factor-impact">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium">زيادة الوعي الرقمي</span>
                                <span class="text-green-600 font-bold">+22%</span>
                            </div>
                            <div class="impact-bar">
                                <div class="impact-fill" style="width: 88%"></div>
                            </div>
                        </div>
                        
                        <div class="factor-impact">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium">تحسين تجربة المستخدم</span>
                                <span class="text-green-600 font-bold">+18%</span>
                            </div>
                            <div class="impact-bar">
                                <div class="impact-fill" style="width: 72%"></div>
                            </div>
                        </div>
                        
                        <div class="factor-impact">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium">التوسع الجغرافي</span>
                                <span class="text-green-600 font-bold">+25%</span>
                            </div>
                            <div class="impact-bar">
                                <div class="impact-fill" style="width: 90%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-bold text-gray-700 mb-4">التحديات والمخاطر</h4>
                        
                        <div class="factor-impact">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium">المنافسة القوية</span>
                                <span class="text-red-600 font-bold">-8%</span>
                            </div>
                            <div class="impact-bar">
                                <div class="impact-fill bg-red-500" style="width: 40%"></div>
                            </div>
                        </div>
                        
                        <div class="factor-impact">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium">تقلبات الطلب الموسمية</span>
                                <span class="text-red-600 font-bold">-12%</span>
                            </div>
                            <div class="impact-bar">
                                <div class="impact-fill bg-red-500" style="width: 60%"></div>
                            </div>
                        </div>
                        
                        <div class="factor-impact">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium">التغييرات التنظيمية</span>
                                <span class="text-red-600 font-bold">-5%</span>
                            </div>
                            <div class="impact-bar">
                                <div class="impact-fill bg-red-500" style="width: 25%"></div>
                            </div>
                        </div>
                        
                        <div class="factor-impact">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-medium">تكاليف التشغيل</span>
                                <span class="text-red-600 font-bold">-7%</span>
                            </div>
                            <div class="impact-bar">
                                <div class="impact-fill bg-red-500" style="width: 35%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Recommendations -->
        <div class="prediction-card">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    توصيات الذكاء الاصطناعي
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-300 rounded-2xl p-6">
                        <div class="text-3xl mb-4">🎯</div>
                        <h4 class="font-bold text-blue-800 mb-3">استراتيجية قصيرة المدى</h4>
                        <ul class="text-sm text-blue-700 space-y-2">
                            <li>• تركيز على التسويق في المدن الثانوية</li>
                            <li>• إطلاق عروض خاصة لعطلة نهاية الأسبوع</li>
                            <li>• تحسين معدل التحويل بتبسيط عملية الحجز</li>
                        </ul>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-300 rounded-2xl p-6">
                        <div class="text-3xl mb-4">🚀</div>
                        <h4 class="font-bold text-green-800 mb-3">استراتيجية متوسطة المدى</h4>
                        <ul class="text-sm text-green-700 space-y-2">
                            <li>• الاستثمار في نماذج ذكاء اصطناعي أكثر تقدماً</li>
                            <li>• تطوير برنامج ولاء العملاء</li>
                            <li>• توسيع فريق خدمة العملاء</li>
                        </ul>
                    </div>
                    
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-300 rounded-2xl p-6">
                        <div class="text-3xl mb-4">🌟</div>
                        <h4 class="font-bold text-purple-800 mb-3">استراتيجية طويلة المدى</h4>
                        <ul class="text-sm text-purple-700 space-y-2">
                            <li>• التوسع إلى أسواق إقليمية جديدة</li>
                            <li>• تطوير خدمات جديدة مبتكرة</li>
                            <li>• بناء شراكات استراتيجية</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentPredictiveCharts = {};

document.addEventListener('DOMContentLoaded', function() {
    initializePredictiveCharts();
    animateMetrics();
});

function initializePredictiveCharts() {
    initializeScenarioChart();
    initializeTrendPredictionChart();
}

function initializeScenarioChart() {
    const ctx = document.getElementById('scenarioChart').getContext('2d');
    currentPredictiveCharts.scenario = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            datasets: [{
                label: 'السيناريو المتفائل',
                data: [100, 125, 155, 185, 220, 265],
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3
            }, {
                label: 'السيناريو الواقعي',
                data: [100, 115, 135, 150, 170, 195],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3
            }, {
                label: 'السيناريو المتشائم',
                data: [100, 105, 115, 125, 130, 140],
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: { family: 'Cairo, sans-serif' },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                annotation: {
                    annotations: {
                        currentLine: {
                            type: 'line',
                            mode: 'vertical',
                            scaleID: 'x',
                            value: 'يناير',
                            borderColor: 'rgb(107, 114, 128)',
                            borderWidth: 2,
                            borderDash: [5, 5],
                            label: {
                                content: 'الوضع الحالي',
                                enabled: true,
                                position: 'top'
                            }
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        font: { family: 'Cairo, sans-serif' },
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    title: {
                        display: true,
                        text: 'نسبة النمو (%)',
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

function initializeTrendPredictionChart() {
    const ctx = document.getElementById('trendPredictionChart').getContext('2d');
    currentPredictiveCharts.trend = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['الأسبوع 1', 'الأسبوع 2', 'الأسبوع 3', 'الأسبوع 4', 'الأسبوع 5', 'الأسبوع 6', 'الأسبوع 7', 'الأسبوع 8'],
            datasets: [{
                label: 'الحجوزات الفعلية',
                data: [150, 180, 165, 220, 195, 240, null, null],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: false,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderWidth: 2
            }, {
                label: 'التنبؤ المتوقع',
                data: [null, null, null, null, null, 240, 285, 310],
                borderColor: 'rgb(168, 85, 247)',
                backgroundColor: 'rgba(168, 85, 247, 0.1)',
                fill: false,
                tension: 0.4,
                borderWidth: 3,
                borderDash: [10, 5],
                pointBackgroundColor: 'rgb(168, 85, 247)',
                pointBorderWidth: 2
            }, {
                label: 'نطاق الثقة',
                data: [null, null, null, null, null, 220, 260, 280],
                borderColor: 'rgba(168, 85, 247, 0.3)',
                backgroundColor: 'rgba(168, 85, 247, 0.1)',
                fill: '+1',
                tension: 0.4,
                borderWidth: 1,
                pointRadius: 0
            }, {
                label: '',
                data: [null, null, null, null, null, 260, 310, 340],
                borderColor: 'rgba(168, 85, 247, 0.3)',
                backgroundColor: 'rgba(168, 85, 247, 0.1)',
                fill: false,
                tension: 0.4,
                borderWidth: 1,
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        font: { family: 'Cairo, sans-serif' },
                        filter: function(item) {
                            return item.text !== '';
                        }
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

function switchScenario(element, scenario) {
    // Remove active class from all tabs
    document.querySelectorAll('.scenario-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Add active class to clicked tab
    element.classList.add('active');
    
    // Update chart highlight
    updateScenarioHighlight(scenario);
}

function updateScenarioHighlight(scenario) {
    const chart = currentPredictiveCharts.scenario;
    
    // Reset all dataset styles
    chart.data.datasets.forEach((dataset, index) => {
        dataset.borderWidth = 2;
        dataset.backgroundColor = dataset.backgroundColor.replace('0.3', '0.1');
    });
    
    // Highlight selected scenario
    let highlightIndex = 0;
    switch(scenario) {
        case 'optimistic': highlightIndex = 0; break;
        case 'realistic': highlightIndex = 1; break;
        case 'pessimistic': highlightIndex = 2; break;
    }
    
    chart.data.datasets[highlightIndex].borderWidth = 4;
    chart.data.datasets[highlightIndex].backgroundColor = chart.data.datasets[highlightIndex].backgroundColor.replace('0.1', '0.3');
    
    chart.update();
}

function animateMetrics() {
    const metrics = document.querySelectorAll('.forecast-metric');
    
    metrics.forEach((metric, index) => {
        setTimeout(() => {
            metric.style.transform = 'translateY(20px)';
            metric.style.opacity = '0';
            
            setTimeout(() => {
                metric.style.transition = 'all 0.6s ease';
                metric.style.transform = 'translateY(0)';
                metric.style.opacity = '1';
            }, 100);
        }, index * 200);
    });
}

function refreshPredictions() {
    // Show AI thinking animation
    showAIThinking();
    
    // Simulate AI processing
    setTimeout(() => {
        // Update predictions with new data
        updatePredictions();
        hideAIThinking();
        showSuccessMessage('تم تحديث التنبؤات بنجاح!');
    }, 3000);
}

function updatePredictions() {
    // Update forecast metrics with new values
    const newPredictions = [
        { element: document.querySelector('.forecast-metric:nth-child(1) .text-2xl'), value: '756,800 ر.س' },
        { element: document.querySelector('.forecast-metric:nth-child(2) .text-2xl'), value: '2,680' },
        { element: document.querySelector('.forecast-metric:nth-child(3) .text-2xl'), value: '6,120' },
        { element: document.querySelector('.forecast-metric:nth-child(4) .text-2xl'), value: '4.91' },
        { element: document.querySelector('.forecast-metric:nth-child(5) .text-2xl'), value: '178' },
        { element: document.querySelector('.forecast-metric:nth-child(6) .text-2xl'), value: '76.2%' }
    ];
    
    newPredictions.forEach(pred => {
        if (pred.element) {
            pred.element.style.transition = 'all 0.5s ease';
            pred.element.style.transform = 'scale(1.2)';
            pred.element.style.color = '#3b82f6';
            
            setTimeout(() => {
                pred.element.textContent = pred.value;
                pred.element.style.transform = 'scale(1)';
                pred.element.style.color = '';
            }, 250);
        }
    });
    
    // Update charts
    Object.values(currentPredictiveCharts).forEach(chart => {
        chart.update();
    });
}

function showAIThinking() {
    const thinking = document.createElement('div');
    thinking.id = 'aiThinking';
    thinking.className = 'fixed inset-0 bg-gradient-to-br from-purple-900 to-blue-900 bg-opacity-80 flex items-center justify-center z-50';
    thinking.innerHTML = `
        <div class="bg-white rounded-2xl p-8 text-center max-w-md">
            <div class="loading-brain mx-auto mb-6"></div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">الذكاء الاصطناعي يعمل</h3>
            <p class="text-gray-600 mb-4">جاري تحليل البيانات وتحديث التنبؤات...</p>
            <div class="flex justify-center space-x-1 space-x-reverse">
                <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-2 h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
            </div>
        </div>
    `;
    document.body.appendChild(thinking);
}

function hideAIThinking() {
    const thinking = document.getElementById('aiThinking');
    if (thinking) {
        thinking.style.opacity = '0';
        setTimeout(() => thinking.remove(), 300);
    }
}

function showSuccessMessage(message) {
    const success = document.createElement('div');
    success.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform';
    success.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            ${message}
        </div>
    `;
    
    document.body.appendChild(success);
    
    setTimeout(() => {
        success.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        success.style.transform = 'translateX(full)';
        setTimeout(() => success.remove(), 300);
    }, 3000);
}
</script>
@endpush
@endsection
