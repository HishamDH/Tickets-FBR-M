@extends('layouts.app')

@section('title', 'إيصال الحجز')

@section('content')
<div class="min-h-screen bg-gray-50" dir="rtl">
    <div class="container mx-auto px-6 py-8">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">إيصال الحجز</h1>
            <p class="text-gray-600">رقم الحجز: {{ $booking->booking_number ?? '#' . $booking->id }}</p>
        </div>

        <!-- Receipt Card -->
        <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <!-- Receipt Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6">
                <div class="text-center">
                    <h2 class="text-2xl font-bold mb-2">نافذة التذاكر</h2>
                    <p class="text-blue-100">إيصال رسمي</p>
                </div>
            </div>

            <!-- Receipt Body -->
            <div class="p-6 space-y-6">
                <!-- Booking Information -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">معلومات الحجز</h3>
                        <div class="space-y-1 text-sm">
                            <p><span class="text-gray-500">رقم الحجز:</span> <span class="font-medium">{{ $booking->booking_number ?? '#' . $booking->id }}</span></p>
                            <p><span class="text-gray-500">التاريخ:</span> <span class="font-medium">{{ $booking->booking_date ? $booking->booking_date->format('Y/m/d') : 'غير محدد' }}</span></p>
                            @if($booking->booking_time)
                            <p><span class="text-gray-500">الوقت:</span> <span class="font-medium">{{ $booking->booking_time }}</span></p>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">معلومات العميل</h3>
                        <div class="space-y-1 text-sm">
                            <p><span class="text-gray-500">الاسم:</span> <span class="font-medium">{{ $booking->customer_name ?? auth()->user()->name }}</span></p>
                            <p><span class="text-gray-500">البريد:</span> <span class="font-medium">{{ $booking->customer_email ?? auth()->user()->email }}</span></p>
                            @if($booking->customer_phone)
                            <p><span class="text-gray-500">الهاتف:</span> <span class="font-medium">{{ $booking->customer_phone }}</span></p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Service Details -->
                @if($booking->service)
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="font-semibold text-gray-700 mb-4">تفاصيل الخدمة</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium text-gray-900">{{ $booking->service->name }}</h4>
                            <span class="text-lg font-bold text-gray-900">{{ number_format($booking->total_amount ?? $booking->service->price, 2) }} ريال</span>
                        </div>
                        @if($booking->service->description)
                        <p class="text-gray-600 text-sm mb-2">{{ $booking->service->description }}</p>
                        @endif
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            @if($booking->guest_count || $booking->number_of_people)
                            <p><span class="text-gray-500">عدد الأشخاص:</span> {{ $booking->guest_count ?? $booking->number_of_people }} شخص</p>
                            @endif
                            @if($booking->duration_hours)
                            <p><span class="text-gray-500">المدة:</span> {{ $booking->duration_hours }} ساعة</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Merchant Information -->
                @if($booking->merchant)
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="font-semibold text-gray-700 mb-4">معلومات التاجر</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">{{ $booking->merchant->business_name ?? $booking->merchant->name }}</h4>
                        @if($booking->merchant->email)
                        <p class="text-sm text-gray-600"><span class="text-gray-500">البريد:</span> {{ $booking->merchant->email }}</p>
                        @endif
                        @if($booking->merchant->phone)
                        <p class="text-sm text-gray-600"><span class="text-gray-500">الهاتف:</span> {{ $booking->merchant->phone }}</p>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Payment Summary -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="font-semibold text-gray-700 mb-4">ملخص الدفع</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">المبلغ الأساسي:</span>
                            <span class="font-medium">{{ number_format($booking->total_amount ?? 0, 2) }} ريال</span>
                        </div>
                        @if($booking->discount)
                        <div class="flex justify-between text-green-600">
                            <span>الخصم:</span>
                            <span class="font-medium">-{{ number_format($booking->discount, 2) }} ريال</span>
                        </div>
                        @endif
                        <div class="border-t border-gray-200 pt-2">
                            <div class="flex justify-between text-lg font-bold">
                                <span>إجمالي المبلغ:</span>
                                <span class="text-blue-600">{{ number_format(($booking->total_amount ?? 0) - ($booking->discount ?? 0), 2) }} ريال</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Status -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-700">حالة الدفع:</span>
                        @if($booking->payment_status === 'paid')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            ✅ مدفوع
                        </span>
                        @elseif($booking->payment_status === 'pending')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            ⏳ في الانتظار
                        </span>
                        @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            ❌ غير مدفوع
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Special Requests -->
                @if($booking->special_requests || $booking->notes)
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="font-semibold text-gray-700 mb-2">ملاحظات خاصة</h3>
                    <p class="text-gray-600 bg-gray-50 rounded-lg p-3">{{ $booking->special_requests ?? $booking->notes }}</p>
                </div>
                @endif

                <!-- Footer -->
                <div class="border-t border-gray-200 pt-6 text-center text-sm text-gray-500">
                    <p>تاريخ الإصدار: {{ now()->format('Y/m/d H:i') }}</p>
                    <p class="mt-1">شكراً لاستخدام نافذة التذاكر</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="max-w-2xl mx-auto mt-6 flex gap-4 justify-center">
            <a href="{{ route('customer.bookings.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                العودة للحجوزات
            </a>
            <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition duration-200">
                طباعة الإيصال
            </button>
        </div>
    </div>
</div>

<style>
@media print {
    .container {
        margin: 0 !important;
        padding: 0 !important;
        max-width: none !important;
    }
    .bg-gray-50 {
        background: white !important;
    }
    button, .bg-gray-500 {
        display: none !important;
    }
}
</style>
@endsection