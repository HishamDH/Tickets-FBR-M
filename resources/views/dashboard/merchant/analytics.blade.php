@extends('layouts.app')

@section('title', 'التحليلات والتقارير')

@section('content')
<div class="min-h-screen bg-gray-50" dir="rtl">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">التحليلات والتقارير</h1>
            <p class="text-gray-600">تحليل شامل لأداء خدماتك ونمو أعمالك</p>
        </div>

        <!-- Performance Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="glass-effect rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">متوسط قيمة الحجز</p>
                        <p class="text-2xl font-bold gradient-text">{{ number_format($averageBookingValue) }} ريال</p>
                        <p class="text-xs text-green-600">+15% من الشهر الماضي</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-xl">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">معدل الإلغاء</p>
                        <p class="text-2xl font-bold text-red-600">{{ number_format($cancellationRate, 1) }}%</p>
                        <p class="text-xs text-red-600">{{ $cancellationRate > 10 ? 'يحتاج تحسين' : 'جيد' }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-xl">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">نمو الحجوزات</p>
                        <p class="text-2xl font-bold {{ $growthRate >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $growthRate >= 0 ? '+' : '' }}{{ number_format($growthRate, 1) }}%
                        </p>
                        <p class="text-xs text-gray-500">مقارنة بالشهر الماضي</p>
                    </div>
                    <div class="p-3 {{ $growthRate >= 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-xl">
                        <svg class="w-6 h-6 {{ $growthRate >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($growthRate >= 0)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                            @endif
                        </svg>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">حجوزات هذا الشهر</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $currentMonthBookings }}</p>
                        <p class="text-xs text-gray-500">{{ $lastMonthBookings }} الشهر الماضي</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-xl">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Peak Hours Chart -->
            <div class="glass-effect rounded-xl p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    أوقات الذروة للحجوزات
                </h2>
                
                <div class="space-y-3">
                    @foreach($peakHours->take(5) as $hour)
                    @php
                        $hourDisplay = $hour->hour == 0 ? '12 ص' : 
                                      ($hour->hour < 12 ? $hour->hour . ' ص' : 
                                      ($hour->hour == 12 ? '12 م' : ($hour->hour - 12) . ' م'));
                        $percentage = $peakHours->max('bookings_count') > 0 ? 
                                     ($hour->bookings_count / $peakHours->max('bookings_count')) * 100 : 0;
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="w-16 text-sm font-medium">{{ $hourDisplay }}</span>
                            <div class="flex-1 mx-4 bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-blue-600">{{ $hour->bookings_count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Peak Days Chart -->
            <div class="glass-effect rounded-xl p-6">
                <h2 class="text-xl font-bold mb-6 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    أيام الأسبوع الأكثر حجزاً
                </h2>
                
                <div class="space-y-3">
                    @foreach($peakDays as $day)
                    @php
                        $dayNames = [
                            1 => 'الأحد', 2 => 'الاثنين', 3 => 'الثلاثاء', 4 => 'الأربعاء',
                            5 => 'الخميس', 6 => 'الجمعة', 7 => 'السبت'
                        ];
                        $dayName = $dayNames[$day->day_of_week] ?? 'غير محدد';
                        $percentage = $peakDays->max('bookings_count') > 0 ? 
                                     ($day->bookings_count / $peakDays->max('bookings_count')) * 100 : 0;
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="w-16 text-sm font-medium">{{ $dayName }}</span>
                            <div class="flex-1 mx-4 bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-green-600">{{ $day->bookings_count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Insights and Recommendations -->
        <div class="glass-effect rounded-xl p-6 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center">
                <svg class="w-6 h-6 ml-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                رؤى وتوصيات
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Performance Insight -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <div class="p-2 bg-blue-100 rounded-lg ml-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-blue-900">الأداء</h3>
                    </div>
                    <p class="text-sm text-blue-800">
                        @if($growthRate > 0)
                            أداء ممتاز! نمو إيجابي بنسبة {{ number_format($growthRate, 1) }}% مقارنة بالشهر الماضي.
                        @else
                            يمكن تحسين الأداء. انخفاض بنسبة {{ number_format(abs($growthRate), 1) }}% عن الشهر الماضي.
                        @endif
                    </p>
                </div>

                <!-- Peak Time Recommendation -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <div class="p-2 bg-green-100 rounded-lg ml-3">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-green-900">أوقات الذروة</h3>
                    </div>
                    <p class="text-sm text-green-800">
                        @if($peakHours->isNotEmpty())
                            @php
                                $topHour = $peakHours->first();
                                $hourDisplay = $topHour->hour == 0 ? '12 ص' : 
                                              ($topHour->hour < 12 ? $topHour->hour . ' ص' : 
                                              ($topHour->hour == 12 ? '12 م' : ($topHour->hour - 12) . ' م'));
                            @endphp
                            أكثر الأوقات حجزاً: {{ $hourDisplay }}. استغل هذا الوقت لعروض خاصة.
                        @else
                            لا توجد بيانات كافية لتحديد أوقات الذروة.
                        @endif
                    </p>
                </div>

                <!-- Cancellation Rate Insight -->
                <div class="bg-{{ $cancellationRate > 15 ? 'red' : ($cancellationRate > 10 ? 'yellow' : 'green') }}-50 border border-{{ $cancellationRate > 15 ? 'red' : ($cancellationRate > 10 ? 'yellow' : 'green') }}-200 rounded-lg p-4">
                    <div class="flex items-center mb-3">
                        <div class="p-2 bg-{{ $cancellationRate > 15 ? 'red' : ($cancellationRate > 10 ? 'yellow' : 'green') }}-100 rounded-lg ml-3">
                            <svg class="w-5 h-5 text-{{ $cancellationRate > 15 ? 'red' : ($cancellationRate > 10 ? 'yellow' : 'green') }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($cancellationRate > 15)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.072 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                @elseif($cancellationRate > 10)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                @endif
                            </svg>
                        </div>
                        <h3 class="font-semibold text-{{ $cancellationRate > 15 ? 'red' : ($cancellationRate > 10 ? 'yellow' : 'green') }}-900">معدل الإلغاء</h3>
                    </div>
                    <p class="text-sm text-{{ $cancellationRate > 15 ? 'red' : ($cancellationRate > 10 ? 'yellow' : 'green') }}-800">
                        @if($cancellationRate > 15)
                            معدل إلغاء عالي ({{ number_format($cancellationRate, 1) }}%). راجع سياسات الإلغاء وجودة الخدمة.
                        @elseif($cancellationRate > 10)
                            معدل إلغاء متوسط ({{ number_format($cancellationRate, 1) }}%). يمكن التحسين قليلاً.
                        @else
                            معدل إلغاء ممتاز ({{ number_format($cancellationRate, 1) }}%). استمر على هذا الأداء!
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Action Items -->
        <div class="glass-effect rounded-xl p-6">
            <h2 class="text-xl font-bold mb-6 flex items-center">
                <svg class="w-6 h-6 ml-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                خطة العمل المقترحة
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @if($growthRate < 0)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h3 class="font-semibold text-red-900 mb-2">تحسين الأداء</h3>
                    <ul class="text-sm text-red-800 space-y-1">
                        <li>• مراجعة استراتيجية التسويق</li>
                        <li>• تحسين جودة الخدمات</li>
                        <li>• مراجعة الأسعار</li>
                    </ul>
                </div>
                @endif

                @if($cancellationRate > 10)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h3 class="font-semibold text-yellow-900 mb-2">تقليل الإلغاءات</h3>
                    <ul class="text-sm text-yellow-800 space-y-1">
                        <li>• تحسين التواصل مع العملاء</li>
                        <li>• مراجعة سياسة الإلغاء</li>
                        <li>• تأكيد الحجوزات مسبقاً</li>
                    </ul>
                </div>
                @endif

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="font-semibold text-blue-900 mb-2">زيادة المبيعات</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• إضافة خدمات جديدة</li>
                        <li>• عروض في أوقات الذروة</li>
                        <li>• برنامج ولاء للعملاء</li>
                    </ul>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h3 class="font-semibold text-green-900 mb-2">تحسين التجربة</h3>
                    <ul class="text-sm text-green-800 space-y-1">
                        <li>• جمع آراء العملاء</li>
                        <li>• تطوير الخدمات الحالية</li>
                        <li>• تحسين عملية الحجز</li>
                    </ul>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <h3 class="font-semibold text-purple-900 mb-2">التسويق الذكي</h3>
                    <ul class="text-sm text-purple-800 space-y-1">
                        <li>• استهداف أوقات الذروة</li>
                        <li>• حملات للأيام الهادئة</li>
                        <li>• تسويق موسمي</li>
                    </ul>
                </div>

                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                    <h3 class="font-semibold text-indigo-900 mb-2">تطوير الأعمال</h3>
                    <ul class="text-sm text-indigo-800 space-y-1">
                        <li>• شراكات جديدة</li>
                        <li>• توسيع نطاق الخدمة</li>
                        <li>• الاستثمار في التكنولوجيا</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
