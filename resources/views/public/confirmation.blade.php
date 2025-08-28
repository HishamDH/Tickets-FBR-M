@extends('layouts.public')

@section('title', 'تأكيد الحجز - ' . $booking->service->name)

@section('content')
<div class="min-h-screen bg-slate-50" dir="rtl">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Success Message -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 mx-auto mb-6 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-green-600 mb-2">تم تأكيد الحجز بنجاح!</h1>
                <p class="text-slate-600">تم إنشاء حجزك وإرسال التفاصيل إليك</p>
            </div>

            <!-- Booking Details Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-primary to-primary/80 text-white p-6">
                    <h2 class="text-xl font-bold mb-2">تفاصيل الحجز</h2>
                    <p class="text-primary-100">رقم الحجز: <span class="font-bold">{{ $booking->booking_number }}</span></p>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Service Information -->
                    <div class="flex items-start space-x-4 space-x-reverse pb-6 border-b">
                        @if($booking->service->images && is_array($booking->service->images) && count($booking->service->images) > 0)
                            <img src="{{ Storage::url($booking->service->images[0]) }}" 
                                 alt="{{ $booking->service->name }}" 
                                 class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
                        @else
                            <div class="w-20 h-20 bg-primary/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-8 h-8 text-primary/60" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-slate-800 mb-1">{{ $booking->service->name }}</h3>
                                                        <p class="text-slate-600 mb-2">{{ $booking->service->merchant->business_name }}</p>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                @switch($booking->service->service_type)
                                    @case('event')
                                        فعالية
                                        @break
                                    @case('exhibition')
                                        معرض
                                        @break
                                    @case('restaurant')
                                        مطعم
                                        @break
                                    @case('experience')
                                        تجربة
                                        @break
                                    @default
                                        {{ $booking->service->service_type }}
                                @endswitch
                            </span>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div>
                        <h4 class="text-lg font-semibold text-slate-800 mb-3">معلومات العميل</h4>
                        <div class="grid md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-slate-500">الاسم:</span>
                                <span class="font-medium mr-2">{{ $booking->customer_name }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500">الهاتف:</span>
                                <span class="font-medium mr-2">{{ $booking->customer_phone }}</span>
                            </div>
                            @if($booking->customer_email)
                                <div class="md:col-span-2">
                                    <span class="text-slate-500">البريد الإلكتروني:</span>
                                    <span class="font-medium mr-2">{{ $booking->customer_email }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Booking Information -->
                    <div>
                        <h4 class="text-lg font-semibold text-slate-800 mb-3">تفاصيل الحجز</h4>
                        <div class="grid md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-slate-500">التاريخ:</span>
                                <span class="font-medium mr-2">{{ \Carbon\Carbon::parse($booking->booking_date)->format('Y/m/d') }}</span>
                            </div>
                            <div>
                                <span class="text-slate-500">الوقت:</span>
                                <span class="font-medium mr-2">{{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}</span>
                            </div>
                            @if($booking->number_of_people)
                                <div>
                                    <span class="text-slate-500">عدد الأشخاص:</span>
                                    <span class="font-medium mr-2">{{ $booking->number_of_people }} شخص</span>
                                </div>
                            @endif
                            @if($booking->number_of_tables)
                                <div>
                                    <span class="text-slate-500">عدد الطاولات:</span>
                                    <span class="font-medium mr-2">{{ $booking->number_of_tables }} طاولة</span>
                                </div>
                            @endif
                            @if($booking->duration_hours)
                                <div>
                                    <span class="text-slate-500">المدة:</span>
                                    <span class="font-medium mr-2">{{ $booking->duration_hours }} ساعة</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($booking->notes)
                        <div>
                            <h4 class="text-lg font-semibold text-slate-800 mb-3">ملاحظات خاصة</h4>
                            <p class="text-slate-600 bg-slate-50 p-3 rounded-lg">{{ $booking->notes }}</p>
                        </div>
                    @endif

                    <!-- Financial Information -->
                    <div class="bg-slate-50 rounded-lg p-4">
                        <h4 class="text-lg font-semibold text-slate-800 mb-3">المعلومات المالية</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-slate-600">المبلغ الإجمالي:</span>
                                <span class="font-bold text-slate-800">{{ number_format($booking->total_amount) }} ريال</span>
                            </div>
                            @if($booking->commission_amount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500">عمولة المنصة:</span>
                                    <span class="text-slate-600">{{ number_format($booking->commission_amount) }} ريال</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-500">مبلغ التاجر:</span>
                                    <span class="text-slate-600">{{ number_format($booking->merchant_amount) }} ريال</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center pt-2 border-t">
                                <span class="text-slate-600">حالة الدفع:</span>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    @if($booking->payment_status === 'paid') bg-green-100 text-green-800
                                    @elseif($booking->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    @switch($booking->payment_status)
                                        @case('paid')
                                            مدفوع
                                            @break
                                        @case('pending')
                                            في انتظار الدفع
                                            @break
                                        @case('failed')
                                            فشل الدفع
                                            @break
                                        @default
                                            {{ $booking->payment_status }}
                                    @endswitch
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code -->
                    @if($booking->qr_code)
                        <div class="text-center">
                            <h4 class="text-lg font-semibold text-slate-800 mb-3">رمز الاستجابة السريعة</h4>
                            <div class="inline-block p-4 bg-white border-2 border-slate-200 rounded-lg">
                                <img src="data:image/png;base64,{{ $booking->qr_code }}" 
                                     alt="QR Code" 
                                     class="w-32 h-32 mx-auto">
                            </div>
                            <p class="text-sm text-slate-500 mt-2">اعرض هذا الرمز عند الوصول</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
                <button onclick="window.print()" 
                        class="flex-1 bg-slate-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-slate-700 transition-colors duration-200 flex items-center justify-center">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    طباعة الحجز
                </button>

                <a href="{{ route('merchant.booking', $booking->service->merchant->id) }}" 
                   class="flex-1 bg-primary text-white py-3 px-6 rounded-lg font-semibold hover:bg-primary/90 transition-colors duration-200 text-center">
                    العودة للخدمات
                </a>
            </div>

            <!-- Contact Information -->
            <div class="mt-8 p-6 bg-blue-50 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800 mb-3">معلومات التواصل</h3>
                <div class="space-y-2 text-sm text-blue-700">
                    <p><strong>{{ $booking->service->merchant->business_name }}</strong></p>
                    @if($booking->service->merchant->user && $booking->service->merchant->user->phone)
                        <p>📞 {{ $booking->service->merchant->user->phone }}</p>
                    @endif
                    @if($booking->service->merchant->user && $booking->service->merchant->user->email)
                        <p>📧 {{ $booking->service->merchant->user->email }}</p>
                    @endif
                    @if($booking->service->location)
                        <p>📍 {{ $booking->service->location }}</p>
                    @endif
                </div>
                <div class="mt-4 p-3 bg-blue-100 rounded-lg">
                    <p class="text-xs text-blue-600">
                        في حالة وجود أي استفسار أو تغيير في الحجز، يرجى التواصل مع التاجر مباشرة أو خدمة العملاء.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .bg-slate-50 {
        background: white !important;
    }
    
    button, .no-print {
        display: none !important;
    }
    
    .shadow-lg {
        box-shadow: none !important;
        border: 1px solid #e2e8f0 !important;
    }
}
</style>
@endsection
