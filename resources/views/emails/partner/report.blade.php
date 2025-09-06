<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير أداء الشريك</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            color: #1a202c;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 8px 8px 0 0;
            text-align: center;
            margin: -20px -20px 20px -20px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .metric-card {
            background: #f7fafc;
            padding: 20px;
            border-radius: 8px;
            border-right: 4px solid #4299e1;
            text-align: center;
        }
        .metric-card h3 {
            margin: 0 0 10px 0;
            color: #2d3748;
            font-size: 14px;
            font-weight: 600;
        }
        .metric-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #1a365d;
            margin: 0;
        }
        .metric-card .currency {
            font-size: 14px;
            color: #718096;
        }
        .section {
            margin: 30px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
        }
        .section h2 {
            margin: 0 0 15px 0;
            color: #2d3748;
            font-size: 20px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }
        .comparison-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .comparison-item:last-child {
            border-bottom: none;
        }
        .change-positive {
            color: #38a169;
            font-weight: bold;
        }
        .change-negative {
            color: #e53e3e;
            font-weight: bold;
        }
        .change-neutral {
            color: #718096;
        }
        .recommendations {
            background: #fff5f5;
            border-right: 4px solid #fc8181;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .recommendations h3 {
            color: #c53030;
            margin: 0 0 15px 0;
        }
        .recommendation-item {
            margin: 10px 0;
            padding: 10px;
            background: white;
            border-radius: 4px;
            border-right: 3px solid #feb2b2;
        }
        .footer {
            background: #edf2f7;
            padding: 20px;
            margin: 20px -20px -20px -20px;
            border-radius: 0 0 8px 8px;
            text-align: center;
            color: #718096;
            font-size: 14px;
        }
        .footer a {
            color: #4299e1;
            text-decoration: none;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #c6f6d5;
            color: #22543d;
        }
        .badge-warning {
            background-color: #fef5e7;
            color: #c05621;
        }
        .badge-info {
            background-color: #bee3f8;
            color: #2c5282;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>تقرير أداء الشريك</h1>
            <p>{{ $partner->name }}</p>
            <p>الفترة: {{ $reportData['period']['start']->format('Y/m/d') }} - {{ $reportData['period']['end']->format('Y/m/d') }}</p>
        </div>

        <!-- الملخص المالي -->
        <div class="metrics-grid">
            <div class="metric-card">
                <h3>الرصيد الحالي</h3>
                <p class="value">{{ number_format($reportData['performance']['financial']['current_balance']) }}</p>
                <p class="currency">ريال سعودي</p>
            </div>
            
            <div class="metric-card">
                <h3>أرباح الفترة</h3>
                <p class="value">{{ number_format($reportData['performance']['financial']['period_earnings']) }}</p>
                <p class="currency">ريال سعودي</p>
            </div>
            
            <div class="metric-card">
                <h3>التجار النشطون</h3>
                <p class="value">{{ $reportData['performance']['referrals']['active_merchants'] }}</p>
                <p class="currency">تاجر</p>
            </div>
            
            <div class="metric-card">
                <h3>الحجوزات الجديدة</h3>
                <p class="value">{{ number_format($reportData['performance']['performance']['period_bookings']) }}</p>
                <p class="currency">حجز</p>
            </div>
        </div>

        <!-- مقارنة مع الفترة السابقة -->
        @if(isset($reportData['comparison']))
        <div class="section">
            <h2>المقارنة مع الفترة السابقة</h2>
            
            <div class="comparison-item">
                <span>تغيير الأرباح</span>
                <span class="{{ $reportData['comparison']['earnings_change'] >= 0 ? 'change-positive' : 'change-negative' }}">
                    {{ $reportData['comparison']['earnings_change'] > 0 ? '+' : '' }}{{ $reportData['comparison']['earnings_change'] }}%
                </span>
            </div>
            
            <div class="comparison-item">
                <span>تغيير التجار الجدد</span>
                <span class="{{ $reportData['comparison']['merchants_change'] >= 0 ? 'change-positive' : 'change-negative' }}">
                    {{ $reportData['comparison']['merchants_change'] > 0 ? '+' : '' }}{{ $reportData['comparison']['merchants_change'] }}%
                </span>
            </div>
            
            <div class="comparison-item">
                <span>تغيير الحجوزات</span>
                <span class="{{ $reportData['comparison']['bookings_change'] >= 0 ? 'change-positive' : 'change-negative' }}">
                    {{ $reportData['comparison']['bookings_change'] > 0 ? '+' : '' }}{{ $reportData['comparison']['bookings_change'] }}%
                </span>
            </div>
        </div>
        @endif

        <!-- أفضل التجار أداءً -->
        @if(isset($reportData['merchants_analysis']['merchants']) && count($reportData['merchants_analysis']['merchants']) > 0)
        <div class="section">
            <h2>أفضل التجار أداءً</h2>
            
            @foreach(array_slice($reportData['merchants_analysis']['merchants'], 0, 5) as $merchant)
            <div class="comparison-item">
                <div>
                    <strong>{{ $merchant['business_name'] }}</strong>
                    <div style="font-size: 12px; color: #718096;">
                        {{ $merchant['bookings_count'] }} حجز | 
                        {{ number_format($merchant['total_revenue']) }} ر.س إيرادات
                    </div>
                </div>
                <div>
                    <span class="badge badge-success">{{ number_format($merchant['commission_earned']) }} ر.س</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- التوصيات -->
        @if(isset($reportData['recommendations']) && count($reportData['recommendations']) > 0)
        <div class="recommendations">
            <h3>توصيات التحسين</h3>
            
            @foreach($reportData['recommendations'] as $recommendation)
            <div class="recommendation-item">
                <strong>{{ $recommendation['title'] }}</strong>
                <p style="margin: 5px 0 0 0; color: #4a5568;">{{ $recommendation['description'] }}</p>
                
                @if(isset($recommendation['actions']))
                <ul style="margin: 10px 0 0 20px; color: #718096; font-size: 14px;">
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
        @if(isset($reportData['forecasts']))
        <div class="section">
            <h2>التوقعات</h2>
            
            <div class="comparison-item">
                <span>توقع أرباح الشهر القادم</span>
                <span class="badge badge-info">{{ number_format($reportData['forecasts']['next_month_earnings']) }} ر.س</span>
            </div>
            
            <div class="comparison-item">
                <span>اتجاه الأداء</span>
                <span class="badge {{ $reportData['forecasts']['trend_direction'] === 'up' ? 'badge-success' : ($reportData['forecasts']['trend_direction'] === 'down' ? 'badge-warning' : 'badge-info') }}">
                    {{ $reportData['forecasts']['trend_direction'] === 'up' ? 'صاعد' : ($reportData['forecasts']['trend_direction'] === 'down' ? 'هابط' : 'مستقر') }}
                </span>
            </div>
            
            <div class="comparison-item">
                <span>مستوى الثقة</span>
                <span class="badge badge-{{ $reportData['forecasts']['confidence'] === 'high' ? 'success' : 'warning' }}">
                    {{ $reportData['forecasts']['confidence'] === 'high' ? 'عالي' : 'متوسط' }}
                </span>
            </div>
        </div>
        @endif

        <div class="footer">
            <p>
                تم إنشاء هذا التقرير تلقائياً في {{ $reportData['generated_at']->format('Y/m/d H:i') }}
            </p>
            <p>
                للمزيد من التفاصيل، تفضل بزيارة 
                <a href="{{ url('/partner/dashboard') }}">لوحة تحكم الشريك</a>
            </p>
            <p style="margin-top: 20px; font-size: 12px; color: #a0aec0;">
                منصة تذاكر - نظام إدارة الحجوزات والفعاليات
            </p>
        </div>
    </div>
</body>
</html>
