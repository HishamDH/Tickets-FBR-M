<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تحليل الأداء التفصيلي - {{ $partner->name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300;400;600;700&display=swap');
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Noto Sans Arabic', Arial, sans-serif;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.6;
            color: #1f2937;
            background: #ffffff;
        }
        
        .header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 30px;
            margin: -20px -20px 30px -20px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 32px;
            margin: 0 0 10px 0;
            font-weight: 700;
        }
        
        .header .partner-name {
            font-size: 20px;
            margin: 0 0 15px 0;
            opacity: 0.9;
        }
        
        .header .report-meta {
            font-size: 14px;
            opacity: 0.8;
        }
        
        .executive-summary {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
        }
        
        .executive-summary h2 {
            color: #1e40af;
            margin: 0 0 20px 0;
            font-size: 22px;
            text-align: center;
        }
        
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }
        
        .kpi-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .kpi-card.primary {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            border: none;
        }
        
        .kpi-card.success {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            border: none;
        }
        
        .kpi-card.warning {
            background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
            color: white;
            border: none;
        }
        
        .kpi-value {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 8px 0;
        }
        
        .kpi-label {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }
        
        .kpi-change {
            font-size: 12px;
            margin: 8px 0 0 0;
            font-weight: 600;
        }
        
        .section {
            margin: 40px 0;
            page-break-inside: avoid;
        }
        
        .section-header {
            background: #1e40af;
            color: white;
            padding: 15px 25px;
            margin: 0 -20px 20px -20px;
            font-size: 20px;
            font-weight: 600;
        }
        
        .subsection {
            margin: 30px 0;
        }
        
        .subsection h3 {
            color: #1e40af;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
            font-size: 18px;
            margin: 0 0 15px 0;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .data-table th {
            background: #f9fafb;
            color: #374151;
            padding: 15px 12px;
            text-align: right;
            font-weight: 600;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .data-table td {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .data-table tbody tr:hover {
            background: #f8fafc;
        }
        
        .data-table .number {
            text-align: center;
            font-weight: 600;
        }
        
        .data-table .currency {
            color: #059669;
            font-weight: 600;
        }
        
        .performance-indicator {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .performance-indicator.excellent {
            background: #d1fae5;
            color: #065f46;
        }
        
        .performance-indicator.good {
            background: #dbeafe;
            color: #1e3a8a;
        }
        
        .performance-indicator.average {
            background: #fef3c7;
            color: #92400e;
        }
        
        .performance-indicator.poor {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .trend-analysis {
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .trend-analysis h4 {
            color: #0369a1;
            margin: 0 0 15px 0;
        }
        
        .insight-box {
            background: #fffbeb;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .insight-box h4 {
            color: #92400e;
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        
        .insight-box .icon {
            color: #f59e0b;
            margin-left: 8px;
        }
        
        .recommendations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }
        
        .recommendation-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .recommendation-card h4 {
            color: #1e40af;
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        
        .recommendation-card .priority {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .priority.high {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .priority.medium {
            background: #fef3c7;
            color: #92400e;
        }
        
        .priority.low {
            background: #d1fae5;
            color: #065f46;
        }
        
        .chart-placeholder {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-style: italic;
            margin: 20px 0;
        }
        
        .footer {
            border-top: 2px solid #e5e7eb;
            padding-top: 30px;
            margin-top: 50px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body { padding: 10px; }
            .header { margin: -10px -10px 20px -10px; }
            .section-header { margin: 0 -10px 15px -10px; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>تحليل الأداء التفصيلي</h1>
        <div class="partner-name">{{ $partner->name }}</div>
        <div class="report-meta">
            فترة التحليل: {{ $period['start']->format('d/m/Y') }} - {{ $period['end']->format('d/m/Y') }}
            <br>
            تاريخ الإنشاء: {{ $generated_at->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Executive Summary -->
    <div class="executive-summary">
        <h2>الملخص التنفيذي</h2>
        
        <div class="kpi-grid">
            <div class="kpi-card primary">
                <div class="kpi-value">{{ number_format($performance['financial']['period_earnings']) }}</div>
                <div class="kpi-label">أرباح الفترة (ر.س)</div>
                @if(isset($comparison['earnings_change']))
                <div class="kpi-change">
                    {{ $comparison['earnings_change'] > 0 ? '+' : '' }}{{ $comparison['earnings_change'] }}% من الفترة السابقة
                </div>
                @endif
            </div>
            
            <div class="kpi-card success">
                <div class="kpi-value">{{ $performance['referrals']['new_merchants'] }}</div>
                <div class="kpi-label">تجار جدد</div>
                @if(isset($comparison['merchants_change']))
                <div class="kpi-change">
                    {{ $comparison['merchants_change'] > 0 ? '+' : '' }}{{ $comparison['merchants_change'] }}% من الفترة السابقة
                </div>
                @endif
            </div>
            
            <div class="kpi-card warning">
                <div class="kpi-value">{{ number_format($performance['referrals']['acceptance_rate']) }}%</div>
                <div class="kpi-label">معدل القبول</div>
            </div>
            
            <div class="kpi-card">
                <div class="kpi-value">{{ number_format($performance['booking_metrics']['total_bookings']) }}</div>
                <div class="kpi-label">إجمالي الحجوزات</div>
                @if(isset($comparison['bookings_change']))
                <div class="kpi-change">
                    {{ $comparison['bookings_change'] > 0 ? '+' : '' }}{{ $comparison['bookings_change'] }}% من الفترة السابقة
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- الأداء المالي التفصيلي -->
    <div class="section">
        <div class="section-header">الأداء المالي التفصيلي</div>
        
        <div class="subsection">
            <h3>تحليل الإيرادات</h3>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>نوع الإيراد</th>
                        <th>المبلغ (ر.س)</th>
                        <th>النسبة</th>
                        <th>المتوسط اليومي</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalEarnings = $performance['financial']['period_earnings'];
                        $daysCount = $period['days_count'];
                    @endphp
                    
                    <tr>
                        <td>عمولات الحجوزات</td>
                        <td class="currency">{{ number_format($performance['financial']['commission_breakdown']['booking_commissions']) }}</td>
                        <td class="number">{{ $totalEarnings > 0 ? round(($performance['financial']['commission_breakdown']['booking_commissions'] / $totalEarnings) * 100, 1) : 0 }}%</td>
                        <td class="number">{{ $daysCount > 0 ? number_format($performance['financial']['commission_breakdown']['booking_commissions'] / $daysCount) : 0 }}</td>
                    </tr>
                    
                    <tr>
                        <td>مكافآت الإحالة</td>
                        <td class="currency">{{ number_format($performance['financial']['commission_breakdown']['referral_bonuses']) }}</td>
                        <td class="number">{{ $totalEarnings > 0 ? round(($performance['financial']['commission_breakdown']['referral_bonuses'] / $totalEarnings) * 100, 1) : 0 }}%</td>
                        <td class="number">{{ $daysCount > 0 ? number_format($performance['financial']['commission_breakdown']['referral_bonuses'] / $daysCount) : 0 }}</td>
                    </tr>
                    
                    <tr>
                        <td>مكافآت الأداء</td>
                        <td class="currency">{{ number_format($performance['financial']['commission_breakdown']['performance_bonuses']) }}</td>
                        <td class="number">{{ $totalEarnings > 0 ? round(($performance['financial']['commission_breakdown']['performance_bonuses'] / $totalEarnings) * 100, 1) : 0 }}%</td>
                        <td class="number">{{ $daysCount > 0 ? number_format($performance['financial']['commission_breakdown']['performance_bonuses'] / $daysCount) : 0 }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="trend-analysis">
            <h4>📊 تحليل الاتجاهات المالية</h4>
            <p>
                @if(isset($trends['financial_trend']))
                    @switch($trends['financial_trend'])
                        @case('upward')
                            يُظهر الأداء المالي اتجاهاً إيجابياً مع نمو مستمر في الإيرادات.
                            @break
                        @case('downward')
                            هناك انخفاض في الإيرادات يتطلب مراجعة الاستراتيجية.
                            @break
                        @default
                            الأداء المالي مستقر مع إمكانية للتحسين.
                    @endswitch
                @else
                    بيانات الاتجاهات قيد التحليل.
                @endif
            </p>
        </div>
    </div>

    <!-- تحليل أداء التجار -->
    <div class="section">
        <div class="section-header">تحليل أداء التجار المُحالين</div>
        
        @if(isset($merchants_analysis['merchants']) && count($merchants_analysis['merchants']) > 0)
        <div class="subsection">
            <h3>أفضل 10 تجار أداءً</h3>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>اسم التاجر</th>
                        <th>عدد الحجوزات</th>
                        <th>الإيرادات (ر.س)</th>
                        <th>العمولة المكتسبة (ر.س)</th>
                        <th>تقييم الأداء</th>
                        <th>نشاط الفترة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(array_slice($merchants_analysis['merchants'], 0, 10) as $index => $merchant)
                    <tr>
                        <td>{{ $merchant['business_name'] }}</td>
                        <td class="number">{{ number_format($merchant['bookings_count']) }}</td>
                        <td class="currency">{{ number_format($merchant['total_revenue']) }}</td>
                        <td class="currency">{{ number_format($merchant['commission_earned']) }}</td>
                        <td>
                            @php
                                $rating = $merchant['performance_rating'] ?? 'average';
                                $ratingText = match($rating) {
                                    'excellent' => 'ممتاز',
                                    'good' => 'جيد',
                                    'average' => 'متوسط',
                                    'poor' => 'ضعيف',
                                    default => 'غير محدد'
                                };
                            @endphp
                            <span class="performance-indicator {{ $rating }}">{{ $ratingText }}</span>
                        </td>
                        <td class="number">{{ $merchant['activity_score'] ?? 'N/A' }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <div class="insight-box">
            <h4><span class="icon">💡</span>رؤى مهمة</h4>
            <ul>
                <li>معدل نجاح الإحالات: {{ $performance['referrals']['acceptance_rate'] }}%</li>
                <li>متوسط العمولة لكل تاجر: {{ $performance['referrals']['total_merchants'] > 0 ? number_format($performance['financial']['period_earnings'] / $performance['referrals']['total_merchants']) : 0 }} ر.س</li>
                <li>التجار النشطون من المجموع: {{ $performance['referrals']['total_merchants'] > 0 ? round(($performance['referrals']['active_merchants'] / $performance['referrals']['total_merchants']) * 100, 1) : 0 }}%</li>
            </ul>
        </div>
    </div>

    <!-- تحليل الحجوزات -->
    <div class="section page-break">
        <div class="section-header">تحليل أداء الحجوزات</div>
        
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-value">{{ number_format($performance['booking_metrics']['total_bookings']) }}</div>
                <div class="kpi-label">إجمالي الحجوزات</div>
            </div>
            
            <div class="kpi-card">
                <div class="kpi-value">{{ number_format($performance['booking_metrics']['confirmed_bookings']) }}</div>
                <div class="kpi-label">الحجوزات المؤكدة</div>
            </div>
            
            <div class="kpi-card">
                <div class="kpi-value">{{ $performance['booking_metrics']['total_bookings'] > 0 ? round(($performance['booking_metrics']['confirmed_bookings'] / $performance['booking_metrics']['total_bookings']) * 100, 1) : 0 }}%</div>
                <div class="kpi-label">معدل التأكيد</div>
            </div>
            
            <div class="kpi-card">
                <div class="kpi-value">{{ number_format($performance['booking_metrics']['total_revenue']) }}</div>
                <div class="kpi-label">إجمالي إيرادات الحجوزات (ر.س)</div>
            </div>
        </div>

        @if(isset($booking_trends))
        <div class="subsection">
            <h3>اتجاهات الحجوزات</h3>
            <div class="chart-placeholder">
                📈 رسم بياني لاتجاهات الحجوزات عبر الفترة الزمنية
                <br>
                <small>(يمكن دمج مكتبة Chart.js هنا لإنشاء رسوم بيانية تفاعلية)</small>
            </div>
        </div>
        @endif
    </div>

    <!-- التوصيات والنصائح -->
    @if(isset($recommendations) && count($recommendations) > 0)
    <div class="section">
        <div class="section-header">التوصيات وخطة التحسين</div>
        
        <div class="recommendations-grid">
            @foreach($recommendations as $recommendation)
            <div class="recommendation-card">
                <h4>{{ $recommendation['title'] }}</h4>
                <span class="priority {{ strtolower($recommendation['priority'] ?? 'medium') }}">
                    {{ $recommendation['priority'] === 'high' ? 'أولوية عالية' : ($recommendation['priority'] === 'low' ? 'أولوية منخفضة' : 'أولوية متوسطة') }}
                </span>
                
                <p>{{ $recommendation['description'] }}</p>
                
                @if(isset($recommendation['actions']))
                <strong>الإجراءات المطلوبة:</strong>
                <ul>
                    @foreach($recommendation['actions'] as $action)
                    <li>{{ $action }}</li>
                    @endforeach
                </ul>
                @endif
                
                @if(isset($recommendation['expected_impact']))
                <small><strong>التأثير المتوقع:</strong> {{ $recommendation['expected_impact'] }}</small>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- التوقعات المستقبلية -->
    @if(isset($forecasts))
    <div class="section">
        <div class="section-header">التوقعات والتخطيط المستقبلي</div>
        
        <div class="kpi-grid">
            <div class="kpi-card primary">
                <div class="kpi-value">{{ number_format($forecasts['next_month_earnings']) }}</div>
                <div class="kpi-label">توقع أرباح الشهر القادم (ر.س)</div>
            </div>
            
            <div class="kpi-card">
                <div class="kpi-value">{{ $forecasts['confidence'] === 'high' ? 'عالي' : 'متوسط' }}</div>
                <div class="kpi-label">مستوى ثقة التوقع</div>
            </div>
            
            <div class="kpi-card success">
                <div class="kpi-value">
                    {{ $forecasts['trend_direction'] === 'up' ? '📈 صاعد' : ($forecasts['trend_direction'] === 'down' ? '📉 هابط' : '➡️ مستقر') }}
                </div>
                <div class="kpi-label">اتجاه الأداء</div>
            </div>
        </div>
        
        <div class="trend-analysis">
            <h4>🔮 تحليل التوقعات</h4>
            <p>
                بناءً على البيانات التاريخية والاتجاهات الحالية، نتوقع 
                @if($forecasts['trend_direction'] === 'up')
                    استمرار النمو الإيجابي في الأداء خلال الفترة القادمة.
                @elseif($forecasts['trend_direction'] === 'down')
                    تحديات في الأداء تتطلب اتخاذ إجراءات تصحيحية.
                @else
                    استقرار في الأداء مع إمكانيات للنمو من خلال تطبيق التوصيات.
                @endif
            </p>
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>تقرير محدود الإفشاء</strong> - هذا التقرير يحتوي على معلومات حساسة وخاصة بالشريك المذكور</p>
        <p>تم إنشاؤه تلقائياً بواسطة منصة إدارة الشركاء - تذاكر</p>
        <p>تاريخ الإنشاء: {{ $generated_at->format('Y/m/d H:i:s') }} | إصدار التقرير: 2.1</p>
        <p>للاستفسارات والدعم الفني: support@tickets-platform.com</p>
    </div>
</body>
</html>
