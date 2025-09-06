<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header with Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">إعدادات التحليل</h2>
                <p class="text-gray-600">اختر المعايير المناسبة لعرض التحليلات المطلوبة</p>
            </div>
            {{ $this->form }}
        </div>

        @if($analysisType === 'overview')
            <!-- Overview Dashboard -->
            @if(isset($performanceData['overview']))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Partners Overview -->
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium">إجمالي الشركاء</h3>
                                <p class="text-3xl font-bold">{{ number_format($performanceData['overview']['total_partners']) }}</p>
                                <p class="text-blue-100">نشط: {{ $performanceData['overview']['active_partners'] }} ({{ $performanceData['overview']['partner_activation_rate'] }}%)</p>
                            </div>
                            <div class="text-4xl opacity-50">
                                <x-heroicon-o-users />
                            </div>
                        </div>
                    </div>

                    <!-- Financial Overview -->
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium">الرصيد الإجمالي</h3>
                                <p class="text-3xl font-bold">{{ number_format($performanceData['financial']['total_balance']) }} ر.س</p>
                                <p class="text-green-100">مكتسب: {{ number_format($performanceData['financial']['total_earned']) }} ر.س</p>
                            </div>
                            <div class="text-4xl opacity-50">
                                <x-heroicon-o-banknotes />
                            </div>
                        </div>
                    </div>

                    <!-- Merchants Overview -->
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium">التجار المحالون</h3>
                                <p class="text-3xl font-bold">{{ number_format($performanceData['merchants']['total_merchants']) }}</p>
                                <p class="text-purple-100">جديد: {{ $performanceData['merchants']['new_merchants'] }}</p>
                            </div>
                            <div class="text-4xl opacity-50">
                                <x-heroicon-o-building-storefront />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Period Performance -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">الأداء المالي للفترة</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                <span class="text-green-800 font-medium">العمولات المدفوعة</span>
                                <span class="text-green-900 font-bold">{{ number_format($performanceData['financial']['period_earnings']) }} ر.س</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                                <span class="text-red-800 font-medium">المسحوبات</span>
                                <span class="text-red-900 font-bold">{{ number_format($performanceData['financial']['period_withdrawals']) }} ر.س</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                <span class="text-blue-800 font-medium">صافي التدفق</span>
                                <span class="text-blue-900 font-bold">{{ number_format($performanceData['financial']['net_period_flow']) }} ر.س</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4">أفضل الشركاء أداءً</h3>
                        <div class="space-y-3">
                            @foreach(array_slice($performanceData['top_partners'], 0, 5) as $index => $partner)
                                <div class="flex items-center justify-between p-3 {{ $index === 0 ? 'bg-yellow-50 border border-yellow-200' : 'bg-gray-50' }} rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <span class="flex items-center justify-center w-8 h-8 {{ $index === 0 ? 'bg-yellow-500 text-white' : 'bg-gray-500 text-white' }} rounded-full text-sm font-bold">
                                            {{ $index + 1 }}
                                        </span>
                                        <div>
                                            <p class="font-medium">{{ $partner['name'] }}</p>
                                            <p class="text-sm text-gray-500">{{ $partner['merchants_count'] }} تاجر</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold">{{ number_format($partner['total_earned']) }} ر.س</p>
                                        <p class="text-sm text-gray-500">معدل {{ $partner['commission_rate'] }}%</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        @elseif($analysisType === 'performance')
            <!-- Performance Analysis -->
            @if(isset($performanceData['error']))
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-yellow-800">{{ $performanceData['error'] }}</p>
                </div>
            @elseif(isset($performanceData['financial']))
                <div class="space-y-6">
                    <!-- Performance Header -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-xl font-semibold mb-4">تقرير الأداء التفصيلي</h3>
                        <div class="text-sm text-gray-600 mb-4">
                            الفترة: {{ $performanceData['period']['start_date'] }} إلى {{ $performanceData['period']['end_date'] }}
                            ({{ $performanceData['period']['days_count'] }} يوم)
                        </div>
                    </div>

                    <!-- Financial Metrics -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-white rounded-lg shadow p-4">
                            <h4 class="font-medium text-gray-700 mb-2">الرصيد الحالي</h4>
                            <p class="text-2xl font-bold text-green-600">{{ number_format($performanceData['financial']['current_balance']) }} ر.س</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4">
                            <h4 class="font-medium text-gray-700 mb-2">إجمالي المكتسب</h4>
                            <p class="text-2xl font-bold text-blue-600">{{ number_format($performanceData['financial']['total_earned']) }} ر.س</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4">
                            <h4 class="font-medium text-gray-700 mb-2">أرباح الفترة</h4>
                            <p class="text-2xl font-bold text-purple-600">{{ number_format($performanceData['financial']['period_earnings']) }} ر.س</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4">
                            <h4 class="font-medium text-gray-700 mb-2">مسحوبات الفترة</h4>
                            <p class="text-2xl font-bold text-red-600">{{ number_format($performanceData['financial']['period_withdrawals']) }} ر.س</p>
                        </div>
                    </div>

                    <!-- Commission Breakdown -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h4 class="text-lg font-semibold mb-4">تفاصيل العمولات</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <h5 class="font-medium text-blue-800">عمولات الحجوزات</h5>
                                <p class="text-xl font-bold text-blue-900">{{ number_format($performanceData['financial']['commission_breakdown']['booking_commissions']) }} ر.س</p>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <h5 class="font-medium text-green-800">مكافآت الإحالة</h5>
                                <p class="text-xl font-bold text-green-900">{{ number_format($performanceData['financial']['commission_breakdown']['referral_bonuses']) }} ر.س</p>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <h5 class="font-medium text-purple-800">مكافآت الأداء</h5>
                                <p class="text-xl font-bold text-purple-900">{{ number_format($performanceData['financial']['commission_breakdown']['performance_bonuses']) }} ر.س</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <h5 class="font-medium text-gray-800">أخرى</h5>
                                <p class="text-xl font-bold text-gray-900">{{ number_format($performanceData['financial']['commission_breakdown']['other']) }} ر.س</p>
                            </div>
                        </div>
                    </div>

                    <!-- Referrals & Performance -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white rounded-lg shadow p-6">
                            <h4 class="text-lg font-semibold mb-4">إحصائيات الإحالات</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span>إجمالي التجار:</span>
                                    <span class="font-bold">{{ $performanceData['referrals']['total_merchants'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>التجار النشطون:</span>
                                    <span class="font-bold text-green-600">{{ $performanceData['referrals']['active_merchants'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>تجار جدد (الفترة):</span>
                                    <span class="font-bold text-blue-600">{{ $performanceData['referrals']['period_new_merchants'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>معدل القبول:</span>
                                    <span class="font-bold">{{ $performanceData['referrals']['acceptance_rate'] }}%</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow p-6">
                            <h4 class="text-lg font-semibold mb-4">مؤشرات الأداء</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span>إجمالي الحجوزات:</span>
                                    <span class="font-bold">{{ number_format($performanceData['performance']['total_bookings']) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>حجوزات الفترة:</span>
                                    <span class="font-bold text-blue-600">{{ number_format($performanceData['performance']['period_bookings']) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>إجمالي الإيرادات:</span>
                                    <span class="font-bold">{{ number_format($performanceData['performance']['total_revenue_generated']) }} ر.س</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>متوسط قيمة الحجز:</span>
                                    <span class="font-bold">{{ number_format($performanceData['performance']['average_booking_value']) }} ر.س</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Performing Merchants -->
                    @if(isset($performanceData['performance']['top_performing_merchants']) && count($performanceData['performance']['top_performing_merchants']) > 0)
                        <div class="bg-white rounded-lg shadow p-6">
                            <h4 class="text-lg font-semibold mb-4">أفضل التجار أداءً</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاجر</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجمالي الإيرادات</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">عدد الحجوزات</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($performanceData['performance']['top_performing_merchants'] as $merchant)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $merchant['business_name'] }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-bold text-green-600">{{ number_format($merchant['total_revenue']) }} ر.س</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ number_format($merchant['bookings_count']) }}</div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

        @elseif($analysisType === 'trends')
            <!-- Trends Analysis -->
            @if(isset($trendsData))
                <div class="space-y-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-xl font-semibold mb-4">الاتجاهات والتوقعات</h3>
                        
                        <!-- Forecasts -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <h4 class="font-medium text-blue-800 mb-2">توقع إيرادات الشهر القادم</h4>
                                <p class="text-2xl font-bold text-blue-900">{{ number_format($trendsData['forecasts']['next_month_revenue']) }} ر.س</p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <h4 class="font-medium text-green-800 mb-2">توقع نمو الشركاء</h4>
                                <p class="text-2xl font-bold text-green-900">{{ $trendsData['forecasts']['partner_growth_projection']['next_month'] }}</p>
                                <p class="text-sm text-green-600">الثقة: {{ $trendsData['forecasts']['partner_growth_projection']['confidence'] }}</p>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4">
                                <h4 class="font-medium text-purple-800 mb-2">نسبة اختراق السوق</h4>
                                <p class="text-2xl font-bold text-purple-900">{{ $trendsData['forecasts']['market_penetration']['penetration_rate'] }}%</p>
                                <p class="text-sm text-purple-600">{{ number_format($trendsData['forecasts']['market_penetration']['partner_merchants']) }} من {{ number_format($trendsData['forecasts']['market_penetration']['total_merchants']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        @elseif($analysisType === 'roi')
            <!-- ROI Analysis -->
            @if(isset($roiData) && count($roiData) > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-xl font-semibold mb-4">تحليل العائد على الاستثمار</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الشريك</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">العمولات المدفوعة</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإيرادات المحققة</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">العائد %</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التقييم</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach(array_slice($roiData, 0, 10) as $roi)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $roi['partner_name'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ number_format($roi['total_commission_paid']) }} ر.س</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-green-600">{{ number_format($roi['total_revenue_generated']) }} ر.س</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold {{ $roi['roi_percentage'] >= 200 ? 'text-green-600' : ($roi['roi_percentage'] >= 100 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ $roi['roi_percentage'] }}%
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ match($roi['efficiency_rating']) {
                                                    'excellent' => 'bg-green-100 text-green-800',
                                                    'good' => 'bg-blue-100 text-blue-800',
                                                    'average' => 'bg-yellow-100 text-yellow-800',
                                                    'below_average' => 'bg-orange-100 text-orange-800',
                                                    'poor' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                } }}">
                                                {{ match($roi['efficiency_rating']) {
                                                    'excellent' => 'ممتاز',
                                                    'good' => 'جيد',
                                                    'average' => 'متوسط',
                                                    'below_average' => 'دون المتوسط',
                                                    'poor' => 'ضعيف',
                                                    default => 'غير محدد'
                                                } }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        @elseif($analysisType === 'comparative')
            <!-- Comparative Analysis -->
            @if(isset($comparativeData['partners']) && count($comparativeData['partners']) > 0)
                <div class="space-y-6">
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-white rounded-lg shadow p-4">
                            <h4 class="font-medium text-gray-700 mb-2">إجمالي الشركاء</h4>
                            <p class="text-2xl font-bold text-blue-600">{{ $comparativeData['total_partners'] }}</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4">
                            <h4 class="font-medium text-gray-700 mb-2">إجمالي الأرصدة</h4>
                            <p class="text-2xl font-bold text-green-600">{{ number_format($comparativeData['summary']['total_balance']) }} ر.س</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4">
                            <h4 class="font-medium text-gray-700 mb-2">إجمالي التجار</h4>
                            <p class="text-2xl font-bold text-purple-600">{{ number_format($comparativeData['summary']['total_merchants']) }}</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4">
                            <h4 class="font-medium text-gray-700 mb-2">متوسط الأداء</h4>
                            <p class="text-2xl font-bold text-orange-600">{{ number_format($comparativeData['summary']['average_performance_score'], 1) }}</p>
                        </div>
                    </div>

                    <!-- Partners Comparison Table -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-xl font-semibold mb-4">مقارنة أداء الشركاء</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الترتيب</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الشريك</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الفئة</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الرصيد الحالي</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجمالي المكتسب</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التجار</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">نقاط الأداء</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach(array_slice($comparativeData['partners'], 0, 15) as $index => $partner)
                                        <tr class="{{ $index < 3 ? 'bg-yellow-50' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="flex items-center justify-center w-8 h-8 {{ $index === 0 ? 'bg-yellow-500' : ($index === 1 ? 'bg-gray-400' : ($index === 2 ? 'bg-orange-500' : 'bg-gray-300')) }} text-white rounded-full text-sm font-bold">
                                                    {{ $index + 1 }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $partner['partner_name'] }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                    {{ match($partner['tier']) {
                                                        'platinum' => 'bg-purple-100 text-purple-800',
                                                        'gold' => 'bg-yellow-100 text-yellow-800',
                                                        'silver' => 'bg-gray-100 text-gray-800',
                                                        'bronze' => 'bg-orange-100 text-orange-800',
                                                        default => 'bg-gray-100 text-gray-800'
                                                    } }}">
                                                    {{ match($partner['tier']) {
                                                        'platinum' => 'بلاتيني',
                                                        'gold' => 'ذهبي',
                                                        'silver' => 'فضي',
                                                        'bronze' => 'برونزي',
                                                        default => 'غير محدد'
                                                    } }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-bold text-green-600">{{ number_format($partner['current_balance']) }} ر.س</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ number_format($partner['total_earned']) }} ر.س</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $partner['merchants_count'] }} ({{ $partner['active_merchants'] }} نشط)</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-bold text-blue-600">{{ number_format($partner['performance_score'], 1) }}</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</x-filament-panels::page>
