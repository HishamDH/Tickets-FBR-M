<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تقرير أداء الشريك</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;600;700&display=swap');
        
        body {
            font-family: 'Noto Sans Arabic', Arial, sans-serif;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #1e40af;
            font-size: 28px;
            margin: 0;
            font-weight: 700;
        }
        
        .header .subtitle {
            color: #6b7280;
            font-size: 16px;
            margin: 10px 0;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-box {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border-right: 4px solid #3b82f6;
        }
        
        .info-box h3 {
            margin: 0 0 10px 0;
            color: #1e40af;
            font-size: 16px;
        }
        
        .metrics-section {
            margin: 30px 0;
        }
        
        .section-title {
            background: #1e40af;
            color: white;
            padding: 12px 20px;
            margin: 20px 0 15px 0;
            font-size: 18px;
            font-weight: 600;
        }
        
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        
        .metric-card {
            background: #f1f5f9;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            border-top: 4px solid #3b82f6;
        }
        
        .metric-card .value {
            font-size: 24px;
            font-weight: 700;
            color: #1e40af;
            margin: 0;
        }
        
        .metric-card .label {
            font-size: 12px;
            color: #64748b;
            margin: 5px 0 0 0;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
        }
        
        .table th {
            background: #1e40af;
            color: white;
            padding: 12px;
            text-align: right;
            font-weight: 600;
        }
        
        .table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        
        .comparison-table {
            width: 100%;
            margin: 20px 0;
        }
        
        .comparison-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .comparison-table .metric-name {
            font-weight: 600;
            color: #374151;
        }
        
        .change-positive {
            color: #059669;
            font-weight: 600;
        }
        
        .change-negative {
            color: #dc2626;
            font-weight: 600;
        }
        
        .recommendations {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .recommendations h3 {
            color: #92400e;
            margin: 0 0 15px 0;
        }
        
        .recommendation-item {
            margin: 15px 0;
            padding: 15px;
            background: white;
            border-radius: 6px;
            border-right: 4px solid #f59e0b;
        }
        
        .recommendation-item h4 {
            color: #92400e;
            margin: 0 0 8px 0;
            font-size: 16px;
        }
        
        .recommendation-item p {
            margin: 0 0 10px 0;
            color: #6b7280;
        }
        
        .recommendation-item ul {
            margin: 10px 0 0 20px;
            color: #6b7280;
        }
        
        .footer {
            border-top: 2px solid #e5e7eb;
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .chart-placeholder {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-style: italic;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>تقرير أداء الشريك</h1>
        <div class="subtitle">{{ $partner->name }}</div>
        <div class="subtitle">
            الفترة: {{ $period['start']->format('Y/m/d') }} - {{ $period['end']->format('Y/m/d') }}
        </div>
        <div class="subtitle">
            تاريخ الإنشاء: {{ $generated_at->format('Y/m/d H:i') }}
        </div>
    </div>

    <!-- معلومات الشريك -->
    <div class="info-grid">
        <div class="info-box">
            <h3>معلومات الشريك</h3>
            <p><strong>الاسم:</strong> {{ $partner->name }}</p>
            <p><strong>البريد الإلكتروني:</strong> {{ $partner->user->email }}</p>
            <p><strong>الهاتف:</strong> {{ $partner->phone ?? 'غير محدد' }}</p>
            <p><strong>معدل العمولة:</strong> {{ $partner->commission_rate }}%</p>
        </div>
        
        <div class="info-box">
            <h3>الملخص العام</h3>
            <p><strong>إجمالي التجار:</strong> {{ $performance['referrals']['total_merchants'] }}</p>
            <p><strong>التجار النشطون:</strong> {{ $performance['referrals']['active_merchants'] }}</p>
            <p><strong>معدل القبول:</strong> {{ $performance['referrals']['acceptance_rate'] }}%</p>
            <p><strong>أيام الفترة:</strong> {{ $period['days_count'] }}</p>
        </div>
    </div>

    <!-- المقاييس المالية -->
    <div class="section-title">المقاييس المالية</div>
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="value">{{ number_format($performance['financial']['current_balance']) }}</div>
            <div class="label">الرصيد الحالي (ر.س)</div>
        </div>
        
        <div class="metric-card">
            <div class="value">{{ number_format($performance['financial']['total_earned']) }}</div>
            <div class="label">إجمالي المكتسب (ر.س)</div>
        </div>
        
        <div class="metric-card">
            <div class="value">{{ number_format($performance['financial']['period_earnings']) }}</div>
            <div class="label">أرباح الفترة (ر.س)</div>
        </div>
        
        <div class="metric-card">
            <div class="value">{{ number_format($performance['financial']['period_withdrawals']) }}</div>
            <div class="label">مسحوبات الفترة (ر.س)</div>
        </div>
    </div>

    <!-- تفاصيل العمولات -->
    <div class="section-title">تفاصيل العمولات</div>
    <table class="table">
        <thead>
            <tr>
                <th>نوع العمولة</th>
                <th>المبلغ (ر.س)</th>
                <th>النسبة من الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalCommissions = $performance['financial']['commission_breakdown']['booking_commissions'] + 
                                  $performance['financial']['commission_breakdown']['referral_bonuses'] + 
                                  $performance['financial']['commission_breakdown']['performance_bonuses'] + 
                                  $performance['financial']['commission_breakdown']['other'];
            @endphp
            
            <tr>
                <td>عمولات الحجوزات</td>
                <td>{{ number_format($performance['financial']['commission_breakdown']['booking_commissions']) }}</td>
                <td>{{ $totalCommissions > 0 ? round(($performance['financial']['commission_breakdown']['booking_commissions'] / $totalCommissions) * 100, 1) : 0 }}%</td>
            </tr>
            
            <tr>
                <td>مكافآت الإحالة</td>
                <td>{{ number_format($performance['financial']['commission_breakdown']['referral_bonuses']) }}</td>
                <td>{{ $totalCommissions > 0 ? round(($performance['financial']['commission_breakdown']['referral_bonuses'] / $totalCommissions) * 100, 1) : 0 }}%</td>
            </tr>
            
            <tr>
                <td>مكافآت الأداء</td>
                <td>{{ number_format($performance['financial']['commission_breakdown']['performance_bonuses']) }}</td>
                <td>{{ $totalCommissions > 0 ? round(($performance['financial']['commission_breakdown']['performance_bonuses'] / $totalCommissions) * 100, 1) : 0 }}%</td>
            </tr>
            
            <tr>
                <td>أخرى</td>
                <td>{{ number_format($performance['financial']['commission_breakdown']['other']) }}</td>
                <td>{{ $totalCommissions > 0 ? round(($performance['financial']['commission_breakdown']['other'] / $totalCommissions) * 100, 1) : 0 }}%</td>
            </tr>
        </tbody>
    </table>

    <!-- مقارنة مع الفترة السابقة -->
    @if(isset($comparison))
    <div class="section-title">المقارنة مع الفترة السابقة</div>
    <table class="comparison-table">
        <tr>
            <td class="metric-name">تغيير الأرباح</td>
            <td class="{{ $comparison['earnings_change'] >= 0 ? 'change-positive' : 'change-negative' }}">
                {{ $comparison['earnings_change'] > 0 ? '+' : '' }}{{ $comparison['earnings_change'] }}%
            </td>
        </tr>
        
        <tr>
            <td class="metric-name">تغيير التجار الجدد</td>
            <td class="{{ $comparison['merchants_change'] >= 0 ? 'change-positive' : 'change-negative' }}">
                {{ $comparison['merchants_change'] > 0 ? '+' : '' }}{{ $comparison['merchants_change'] }}%
            </td>
        </tr>
        
        <tr>
            <td class="metric-name">تغيير الحجوزات</td>
            <td class="{{ $comparison['bookings_change'] >= 0 ? 'change-positive' : 'change-negative' }}">
                {{ $comparison['bookings_change'] > 0 ? '+' : '' }}{{ $comparison['bookings_change'] }}%
            </td>
        </tr>
        
        <tr>
            <td class="metric-name">تغيير الإيرادات</td>
            <td class="{{ $comparison['revenue_change'] >= 0 ? 'change-positive' : 'change-negative' }}">
                {{ $comparison['revenue_change'] > 0 ? '+' : '' }}{{ $comparison['revenue_change'] }}%
            </td>
        </tr>
    </table>
    @endif

    <!-- أفضل التجار أداءً -->
    @if(isset($merchants_analysis['merchants']) && count($merchants_analysis['merchants']) > 0)
    <div class="page-break"></div>
    <div class="section-title">أفضل التجار أداءً</div>
    <table class="table">
        <thead>
            <tr>
                <th>اسم التاجر</th>
                <th>عدد الحجوزات</th>
                <th>إجمالي الإيرادات (ر.س)</th>
                <th>العمولة المكتسبة (ر.س)</th>
                <th>تقييم الأداء</th>
            </tr>
        </thead>
        <tbody>
            @foreach(array_slice($merchants_analysis['merchants'], 0, 10) as $merchant)
            <tr>
                <td>{{ $merchant['business_name'] }}</td>
                <td>{{ number_format($merchant['bookings_count']) }}</td>
                <td>{{ number_format($merchant['total_revenue']) }}</td>
                <td>{{ number_format($merchant['commission_earned']) }}</td>
                <td>{{ $merchant['performance_rating'] ?? 'غير محدد' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- التوصيات -->
    @if(isset($recommendations) && count($recommendations) > 0)
    <div class="recommendations">
        <h3>توصيات التحسين</h3>
        
        @foreach($recommendations as $recommendation)
        <div class="recommendation-item">
            <h4>{{ $recommendation['title'] }}</h4>
            <p>{{ $recommendation['description'] }}</p>
            
            @if(isset($recommendation['actions']))
            <ul>
                @foreach($recommendation['actions'] as $action)
                <li>{{ $action }}</li>
                @endforeach
            </ul>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    <!-- التوقعات -->
    @if(isset($forecasts))
    <div class="section-title">التوقعات والتنبؤات</div>
    <div class="info-grid">
        <div class="info-box">
            <h3>توقع الأرباح</h3>
            <p><strong>الشهر القادم:</strong> {{ number_format($forecasts['next_month_earnings']) }} ر.س</p>
            <p><strong>مستوى الثقة:</strong> {{ $forecasts['confidence'] === 'high' ? 'عالي' : 'متوسط' }}</p>
        </div>
        
        <div class="info-box">
            <h3>اتجاه الأداء</h3>
            <p><strong>الاتجاه:</strong> 
                {{ $forecasts['trend_direction'] === 'up' ? 'صاعد' : ($forecasts['trend_direction'] === 'down' ? 'هابط' : 'مستقر') }}
            </p>
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>تم إنشاء هذا التقرير تلقائياً بواسطة منصة تذاكر</p>
        <p>{{ $generated_at->format('Y/m/d H:i:s') }}</p>
        <p>للاستفسارات: support@tickets-platform.com</p>
    </div>
</body>
</html>
