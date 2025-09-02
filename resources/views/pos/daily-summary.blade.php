@extends('layouts.app')

@section('title', 'ملخص يومي - Daily Summary')
@section('description', 'ملخص مفصل لمبيعات اليوم')

@push('styles')
<style>
    .summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .summary-card:hover {
        transform: translateY(-5px);
    }
    
    .time-slot {
        background: linear-gradient(45deg, #f0f9ff, #e0f2fe);
        border: 2px solid #e1f5fe;
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.3s ease;
    }
    
    .time-slot:hover {
        border-color: #0284c7;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.15);
    }
    
    .progress-bar {
        background: linear-gradient(90deg, #3b82f6, #1d4ed8);
        height: 8px;
        border-radius: 4px;
        transition: width 0.5s ease;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Header -->
    <div class="bg-white shadow-lg border-b-4 border-purple-500 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                        <span class="text-white text-2xl">📅</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">الملخص اليومي</h1>
                        <p class="text-sm text-gray-600">تفاصيل مبيعات يوم {{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}</p>
                    </div>
                </div>
                
                <!-- Date Picker & Actions -->
                <div class="flex items-center space-x-3 space-x-reverse">
                    <input type="date" 
                           value="{{ $date }}" 
                           onchange="location.href='?date='+this.value"
                           class="bg-white border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    
                    <a href="{{ route('pos.sales.history') }}" 
                       class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 space-x-reverse transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 00-2-2M9 5v4"/>
                        </svg>
                        <span>السجل الكامل</span>
                    </a>
                    
                    <a href="{{ route('pos.dashboard') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 space-x-reverse transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span>العودة للنظام</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Sales -->
            <div class="summary-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">إجمالي المبيعات</p>
                        <p class="text-3xl font-bold">{{ number_format($analytics['total_sales'], 2) }}</p>
                        <p class="text-blue-100 text-xs">ريال سعودي</p>
                    </div>
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <span class="text-2xl">💰</span>
                    </div>
                </div>
            </div>

            <!-- Total Transactions -->
            <div class="summary-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-pink-100 text-sm font-medium">عدد المعاملات</p>
                        <p class="text-3xl font-bold">{{ number_format($analytics['total_transactions']) }}</p>
                        <p class="text-pink-100 text-xs">معاملة</p>
                    </div>
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <span class="text-2xl">🛒</span>
                    </div>
                </div>
            </div>

            <!-- Average Sale -->
            <div class="summary-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">متوسط البيع</p>
                        <p class="text-3xl font-bold">{{ number_format($analytics['average_sale'], 2) }}</p>
                        <p class="text-blue-100 text-xs">ريال سعودي</p>
                    </div>
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <span class="text-2xl">📈</span>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="summary-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">عدد العملاء</p>
                        <p class="text-3xl font-bold">{{ number_format($analytics['total_customers']) }}</p>
                        <p class="text-green-100 text-xs">عميل</p>
                    </div>
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <span class="text-2xl">👥</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Hourly Breakdown -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                    <span class="text-2xl mr-3">⏰</span>
                    المبيعات حسب الساعة
                </h3>
                
                <div class="space-y-4">
                    @php
                        $maxSales = collect($analytics['hourly_sales'])->max() ?: 1;
                    @endphp
                    
                    @for($hour = 6; $hour <= 23; $hour++)
                        @php
                            $hourStr = str_pad($hour, 2, '0', STR_PAD_LEFT);
                            $sales = $analytics['hourly_sales'][$hourStr] ?? 0;
                            $percentage = $maxSales > 0 ? ($sales / $maxSales) * 100 : 0;
                        @endphp
                        
                        <div class="time-slot">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium text-gray-700">{{ $hourStr }}:00 - {{ str_pad($hour + 1, 2, '0', STR_PAD_LEFT) }}:00</span>
                                <span class="font-bold text-blue-600">{{ number_format($sales, 2) }} ريال</span>
                            </div>
                            
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Payment Methods & Top Sales -->
            <div class="space-y-8">
                
                <!-- Payment Methods -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <span class="text-2xl mr-3">💳</span>
                        طرق الدفع
                    </h3>
                    
                    <div class="space-y-4">
                        @forelse($analytics['payment_methods'] as $method => $data)
                        @php
                            $methodName = match($method) {
                                'cash' => ['name' => 'نقدي', 'icon' => '💵', 'color' => 'green'],
                                'card' => ['name' => 'بطاقة', 'icon' => '💳', 'color' => 'blue'],
                                'mixed' => ['name' => 'مختلط', 'icon' => '💰', 'color' => 'purple'],
                                default => ['name' => $method, 'icon' => '💸', 'color' => 'gray']
                            };
                            $percentage = $analytics['total_sales'] > 0 ? ($data['total'] / $analytics['total_sales']) * 100 : 0;
                        @endphp
                        
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">{{ $methodName['icon'] }}</span>
                                    <span class="font-medium text-gray-700">{{ $methodName['name'] }}</span>
                                </div>
                                <span class="font-bold text-{{ $methodName['color'] }}-600">{{ number_format($data['total'], 2) }} ريال</span>
                            </div>
                            
                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <span>{{ $data['count'] }} معاملة</span>
                                <span>{{ number_format($percentage, 1) }}%</span>
                            </div>
                            
                            <div class="mt-2 w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-{{ $methodName['color'] }}-500 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">لا توجد مبيعات لهذا اليوم</p>
                        @endforelse
                    </div>
                </div>

                <!-- Today's Highlights -->
                @if($analytics['total_transactions'] > 0)
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <span class="text-2xl mr-3">🌟</span>
                        أبرز إحصائيات اليوم
                    </h3>
                    
                    <div class="space-y-4">
                        <!-- Best Hour -->
                        @php
                            $bestHour = collect($analytics['hourly_sales'])->sortDesc()->keys()->first();
                            $bestHourSales = $analytics['hourly_sales'][$bestHour] ?? 0;
                        @endphp
                        @if($bestHour && $bestHourSales > 0)
                        <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center">
                                <span class="text-green-600 text-2xl mr-3">🏆</span>
                                <div>
                                    <p class="font-medium text-green-800">أفضل ساعة مبيعات</p>
                                    <p class="text-sm text-green-600">{{ $bestHour }}:00</p>
                                </div>
                            </div>
                            <span class="font-bold text-green-600">{{ number_format($bestHourSales, 2) }} ريال</span>
                        </div>
                        @endif
                        
                        <!-- Peak Performance -->
                        @if($analytics['total_transactions'] >= 10)
                        <div class="flex items-center justify-between p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center">
                                <span class="text-blue-600 text-2xl mr-3">🚀</span>
                                <div>
                                    <p class="font-medium text-blue-800">يوم نشط</p>
                                    <p class="text-sm text-blue-600">{{ $analytics['total_transactions'] }} معاملة</p>
                                </div>
                            </div>
                            <span class="text-blue-600 font-medium">أداء ممتاز</span>
                        </div>
                        @endif
                        
                        <!-- Good Customer Service -->
                        @if($analytics['total_customers'] >= 5)
                        <div class="flex items-center justify-between p-4 bg-purple-50 border border-purple-200 rounded-lg">
                            <div class="flex items-center">
                                <span class="text-purple-600 text-2xl mr-3">👥</span>
                                <div>
                                    <p class="font-medium text-purple-800">خدمة عملاء ممتازة</p>
                                    <p class="text-sm text-purple-600">{{ $analytics['total_customers'] }} عميل جديد</p>
                                </div>
                            </div>
                            <span class="text-purple-600 font-medium">تنوع جيد</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Recent Sales Today -->
        @if(isset($analytics['sales']) && $analytics['sales']->count() > 0)
        <div class="mt-8 bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <span class="text-2xl mr-3">📋</span>
                مبيعات اليوم ({{ $analytics['sales']->count() }} معاملة)
            </h3>
            
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الوقت</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الخدمة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">العميل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">طريقة الدفع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المبلغ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($analytics['sales']->sortByDesc('created_at') as $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $sale->created_at->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $sale->offering->title ?? 'خدمة متعددة' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $sale->user ? $sale->user->name : 'عميل غير مسجل' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @php
                                    $methodDisplay = match($sale->payment_method) {
                                        'cash' => '💵 نقدي',
                                        'card' => '💳 بطاقة',
                                        'mixed' => '💰 مختلط',
                                        default => '💸 ' . $sale->payment_method
                                    };
                                @endphp
                                {{ $methodDisplay }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                {{ number_format($sale->total_amount, 2) }} ريال
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
