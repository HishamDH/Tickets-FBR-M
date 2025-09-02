@extends('layouts.app')

@section('title', 'تاريخ المبيعات - POS Sales History')
@section('description', 'سجل تفصيلي لجميع مبيعات نقاط البيع')

@push('styles')
<style>
    .sale-card {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .sale-card:hover {
        border-color: #3B82F6;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .payment-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .payment-cash {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .payment-card {
        background-color: #dbeafe;
        color: #1e40af;
    }
    
    .payment-mixed {
        background-color: #f3e8ff;
        color: #7c3aed;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
    <!-- Header -->
    <div class="bg-white shadow-lg border-b-4 border-indigo-500 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl flex items-center justify-center">
                        <span class="text-white text-2xl">📋</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">تاريخ المبيعات</h1>
                        <p class="text-sm text-gray-600">سجل شامل لجميع معاملات نقاط البيع</p>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center space-x-3 space-x-reverse">
                    <a href="{{ route('pos.reports') }}" 
                       class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 space-x-reverse transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>التقارير</span>
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
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <span class="text-green-600 text-xl">💰</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">إجمالي المبيعات</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($sales->sum('total_amount'), 2) }}</p>
                        <p class="text-xs text-gray-500">ريال سعودي</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <span class="text-blue-600 text-xl">🛒</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">عدد المعاملات</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $sales->total() }}</p>
                        <p class="text-xs text-gray-500">معاملة</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <span class="text-purple-600 text-xl">📊</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">متوسط البيع</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $sales->count() > 0 ? number_format($sales->avg('total_amount'), 2) : '0.00' }}</p>
                        <p class="text-xs text-gray-500">ريال سعودي</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                        <span class="text-orange-600 text-xl">👥</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">عدد العملاء</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $sales->whereNotNull('user_id')->unique('user_id')->count() }}</p>
                        <p class="text-xs text-gray-500">عميل مختلف</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales List -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <span class="text-2xl mr-3">🛍️</span>
                    سجل المبيعات
                </h2>
            </div>

            <div class="p-6">
                @forelse($sales as $sale)
                <div class="sale-card bg-gray-50 rounded-xl p-6 mb-4">
                    <div class="flex items-center justify-between">
                        <!-- Sale Info -->
                        <div class="flex-1">
                            <div class="flex items-start space-x-4 space-x-reverse">
                                <!-- Service Icon -->
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <span class="text-white text-xl">🎯</span>
                                </div>
                                
                                <!-- Details -->
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="text-lg font-bold text-gray-800">{{ $sale->offering->title ?? 'خدمة متعددة' }}</h3>
                                        
                                        <!-- Receipt Number -->
                                        @if(isset($sale->additional_data['receipt_number']))
                                        <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-xs font-mono">
                                            {{ $sale->additional_data['receipt_number'] }}
                                        </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Customer Info -->
                                    <div class="text-sm text-gray-600 mb-3">
                                        @if($sale->user)
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                {{ $sale->user->name }}
                                                @if($sale->user->phone)
                                                    - {{ $sale->user->phone }}
                                                @endif
                                            </span>
                                        @else
                                            <span class="flex items-center text-gray-500">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                                عميل غير مسجل
                                                @if(isset($sale->additional_data['customer_info']['name']))
                                                    - {{ $sale->additional_data['customer_info']['name'] }}
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Sale Details -->
                                    <div class="flex items-center space-x-6 space-x-reverse text-sm text-gray-600">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                            الكمية: {{ $sale->quantity }}
                                        </span>
                                        
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $sale->created_at->format('Y-m-d H:i') }}
                                        </span>
                                        
                                        @if($sale->discount_amount > 0)
                                        <span class="flex items-center text-orange-600">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                            خصم: {{ number_format($sale->discount_amount, 2) }} ريال
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Amount & Payment -->
                        <div class="text-right space-y-2">
                            <div class="text-2xl font-bold text-green-600">
                                {{ number_format($sale->total_amount, 2) }}
                                <span class="text-sm text-gray-500">ريال</span>
                            </div>
                            
                            <!-- Payment Method Badge -->
                            @php
                                $paymentMethod = $sale->payment_method;
                                $badgeClass = 'payment-' . $paymentMethod;
                                $methodName = match($paymentMethod) {
                                    'cash' => '💵 نقدي',
                                    'card' => '💳 بطاقة',
                                    'mixed' => '💰 مختلط',
                                    default => '💸 ' . $paymentMethod
                                };
                            @endphp
                            <div>
                                <span class="payment-badge {{ $badgeClass }}">{{ $methodName }}</span>
                            </div>
                            
                            <!-- Notes if any -->
                            @if(isset($sale->additional_data['notes']) && $sale->additional_data['notes'])
                            <div class="text-xs text-gray-500 max-w-48">
                                <strong>ملاحظة:</strong> {{ Str::limit($sale->additional_data['notes'], 50) }}
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Items Details (if multiple items) -->
                    @if(isset($sale->additional_data['items']) && count($sale->additional_data['items']) > 1)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600 mb-2"><strong>تفاصيل الخدمات:</strong></p>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach($sale->additional_data['items'] as $item)
                            <div class="bg-white rounded-lg p-3 text-sm">
                                <div class="font-medium text-gray-800">خدمة #{{ $item['id'] }}</div>
                                <div class="text-gray-600">الكمية: {{ $item['quantity'] }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @empty
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                        <span class="text-4xl text-gray-400">📋</span>
                    </div>
                    <h3 class="text-xl font-medium text-gray-600 mb-2">لا توجد مبيعات</h3>
                    <p class="text-gray-500">لم يتم تسجيل أي مبيعات حتى الآن</p>
                    <a href="{{ route('pos.dashboard') }}" 
                       class="inline-flex items-center mt-4 bg-indigo-500 hover:bg-indigo-600 text-white px-6 py-3 rounded-lg font-medium transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        بدء البيع الآن
                    </a>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($sales->hasPages())
            <div class="p-6 border-t border-gray-200">
                {{ $sales->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
