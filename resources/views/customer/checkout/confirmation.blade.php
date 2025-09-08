@extends('layouts.app')

@section('title', 'تأكيد الطلب')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-green-100" dir="rtl">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white py-16 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 right-10 w-32 h-32 bg-white rounded-full animate-pulse"></div>
            <div class="absolute bottom-10 left-10 w-24 h-24 bg-white rounded-full animate-pulse" style="animation-delay: 1s;"></div>
        </div>
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center">
                <div class="text-8xl mb-6 animate-bounce">🎉</div>
                <h1 class="text-4xl md:text-5xl font-bold mb-4">تم تأكيد طلبك بنجاح!</h1>
                <p class="text-xl opacity-90 max-w-2xl mx-auto">شكراً لك، تم إرسال طلبات الحجز إلى مقدمي الخدمة</p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 py-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-xl mb-8 text-center">
                <div class="flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="max-w-4xl mx-auto">
            <!-- Booking Summary -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6">
                    <h2 class="text-2xl font-bold">ملخص الحجوزات</h2>
                    <p class="opacity-90">تم إنشاء {{ $bookings->count() }} حجز جديد</p>
                </div>

                <div class="p-6">
                    <div class="space-y-6">
                        @foreach($bookings as $booking)
                            <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition duration-200">
                                <div class="flex flex-col lg:flex-row gap-6">
                                    <!-- Service Info -->
                                    <div class="flex-1">
                                        <div class="flex items-center gap-4 mb-4">
                                            <div class="w-16 h-16 bg-gradient-to-br from-green-100 to-green-200 rounded-lg flex items-center justify-center text-2xl">
                                                @if($booking->service->image)
                                                    <img src="{{ Storage::url($booking->service->image) }}" 
                                                         alt="{{ $booking->service->name }}" 
                                                         class="w-full h-full object-cover rounded-lg">
                                                @else
                                                    🎯
                                                @endif
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-bold text-gray-800">{{ $booking->service->name }}</h3>
                                                <p class="text-gray-600">{{ $booking->service->merchant->business_name ?? $booking->service->merchant->name }}</p>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                            <div class="flex items-center gap-2 text-gray-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                                </svg>
                                                <span>رقم الحجز: {{ $booking->booking_number }}</span>
                                            </div>
                                            
                                            <div class="flex items-center gap-2 text-gray-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <span>التاريخ المطلوب: {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</span>
                                            </div>

                                            <div class="flex items-center gap-2 text-gray-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>الوقت: 
                                                    @switch($booking->booking_time)
                                                        @case('morning') صباحاً @break
                                                        @case('afternoon') بعد الظهر @break  
                                                        @case('evening') مساءً @break
                                                        @case('night') ليلاً @break
                                                        @case('flexible') مرن @break
                                                        @default {{ $booking->booking_time }}
                                                    @endswitch
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-2 text-gray-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10l4 12H5l2-12z"></path>
                                                </svg>
                                                <span>الكمية: {{ $booking->quantity }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status & Payment -->
                                    <div class="text-center lg:text-right">
                                        <div class="mb-4">
                                            @php
                                                $statusClasses = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'confirmed' => 'bg-green-100 text-green-800',
                                                    'cancelled' => 'bg-red-100 text-red-800'
                                                ];
                                                $statusLabels = [
                                                    'pending' => 'في الانتظار',
                                                    'confirmed' => 'مؤكد', 
                                                    'cancelled' => 'ملغي'
                                                ];
                                            @endphp
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$booking->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $statusLabels[$booking->status] ?? $booking->status }}
                                            </span>
                                        </div>

                                        <div class="text-2xl font-bold text-green-600 mb-2">
                                            {{ number_format($booking->total_amount, 0) }} ريال
                                        </div>

                                        @if($booking->payment_method === 'pay_at_location')
                                            <div class="bg-orange-100 text-orange-800 px-3 py-1 rounded-lg text-sm">
                                                💰 دفع في الموقع
                                            </div>
                                        @elseif($booking->payment_method === 'pay_when_visit')
                                            <div class="bg-green-100 text-green-800 px-3 py-1 rounded-lg text-sm">
                                                📍 دفع عند الزيارة
                                            </div>
                                        @elseif($booking->payment_method === 'bank_transfer')
                                            <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-lg text-sm">
                                                🏦 تحويل بنكي
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    الخطوات التالية
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-sm">1</div>
                        <div>
                            <h4 class="font-semibold text-gray-800">مراجعة الطلب</h4>
                            <p class="text-gray-600 text-sm">سيقوم مقدم الخدمة بمراجعة طلب الحجز والرد عليك خلال 24 ساعة</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-sm">2</div>
                        <div>
                            <h4 class="font-semibold text-gray-800">التواصل</h4>
                            <p class="text-gray-600 text-sm">سيتم التواصل معك لتأكيد التفاصيل والموعد النهائي</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-sm">3</div>
                        <div>
                            <h4 class="font-semibold text-gray-800">
                                @if($bookings->whereIn('payment_method', ['pay_at_location', 'pay_when_visit'])->count() > 0)
                                    الدفع عند اللقاء
                                @else
                                    إتمام الدفع
                                @endif
                            </h4>
                            <p class="text-gray-600 text-sm">
                                @if($bookings->whereIn('payment_method', ['pay_at_location', 'pay_when_visit'])->count() > 0)
                                    قم بدفع المبلغ مباشرة لمقدم الخدمة عند تلقي الخدمة أو الزيارة
                                @else
                                    أكمل عملية الدفع حسب الطريقة المختارة
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-sm">4</div>
                        <div>
                            <h4 class="font-semibold text-gray-800">الاستمتاع بالخدمة</h4>
                            <p class="text-gray-600 text-sm">احضر في الموعد المحدد واستمتع بالخدمة المطلوبة</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="text-center space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('customer.bookings.index') }}" 
                       class="bg-blue-600 text-white px-6 py-4 rounded-xl font-bold hover:bg-blue-700 transition duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        عرض جميع الحجوزات
                    </a>

                    <a href="{{ route('customer.services.index') }}" 
                       class="bg-green-600 text-white px-6 py-4 rounded-xl font-bold hover:bg-green-700 transition duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        تصفح خدمات أخرى
                    </a>

                    <a href="{{ route('customer.dashboard') }}" 
                       class="bg-gray-600 text-white px-6 py-4 rounded-xl font-bold hover:bg-gray-700 transition duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        الذهاب للوحة التحكم
                    </a>
                </div>

                <div class="pt-8 border-t border-gray-200">
                    <p class="text-gray-600 text-sm">
                        في حالة وجود أي استفسار، يمكنك التواصل معنا على:
                        <a href="mailto:support@example.com" class="text-blue-600 hover:underline">support@example.com</a>
                        أو الهاتف: <a href="tel:+966123456789" class="text-blue-600 hover:underline">+966 12 345 6789</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection