@extends('layouts.app')

@section('title', 'تفاصيل الحجز')

@section('content')
<div class="min-h-screen bg-gray-50" dir="rtl">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">تفاصيل الحجز #{{ $booking->booking_number }}</h1>
                <p class="text-gray-600">إدارة ومتابعة تفاصيل الحجز</p>
            </div>
            <div class="flex space-x-3 space-x-reverse">
                <a href="{{ route('merchant.dashboard.bookings') }}" 
                   class="flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    العودة للحجوزات
                </a>
                
                @if($booking->booking_status == 'pending')
                <button onclick="updateBookingStatus('confirmed')" 
                        class="flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    تأكيد الحجز
                </button>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Booking Info -->
                <div class="glass-effect rounded-xl p-6">
                    <h2 class="text-xl font-bold mb-6 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        معلومات الحجز
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">رقم الحجز</label>
                            <p class="text-lg font-bold gradient-text">{{ $booking->booking_number }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الإنشاء</label>
                            <p class="text-gray-900">{{ $booking->created_at->format('Y/m/d - H:i') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الخدمة</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $booking->service->name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
                            <p class="text-gray-900">{{ $booking->service->category_arabic }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الحجز</label>
                            <p class="text-lg font-semibold text-blue-600">{{ \Carbon\Carbon::parse($booking->booking_date)->format('Y/m/d') }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">وقت الحجز</label>
                            <p class="text-lg font-semibold text-blue-600">{{ $booking->booking_time }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">عدد الضيوف</label>
                            <p class="text-lg font-semibold text-purple-600">{{ $booking->guest_count }} ضيف</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ الإجمالي</label>
                            <p class="text-2xl font-bold gradient-text">{{ number_format($booking->total_amount) }} ريال</p>
                        </div>
                    </div>
                    
                    @if($booking->special_requests)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">الطلبات الخاصة</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-900">{{ $booking->special_requests }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Customer Details -->
                <div class="glass-effect rounded-xl p-6">
                    <h2 class="text-xl font-bold mb-6 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        بيانات العميل
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الكامل</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $booking->customer_name }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                            <p class="text-gray-900">
                                <a href="mailto:{{ $booking->customer_email }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $booking->customer_email }}
                                </a>
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                            <p class="text-gray-900">
                                <a href="tel:{{ $booking->customer_phone }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $booking->customer_phone }}
                                </a>
                            </p>
                        </div>
                        
                        @if($booking->customer_address)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-900">{{ $booking->customer_address }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Merchant Notes -->
                <div class="glass-effect rounded-xl p-6">
                    <h2 class="text-xl font-bold mb-6 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        ملاحظات التاجر
                    </h2>
                    
                    <form id="notesForm" onsubmit="updateNotes(event)">
                        <textarea name="merchant_notes" rows="4" 
                                  placeholder="أضف ملاحظاتك حول هذا الحجز..."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 resize-none">{{ $booking->merchant_notes }}</textarea>
                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="btn-primary">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                </svg>
                                حفظ الملاحظات
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="glass-effect rounded-xl p-6">
                    <h3 class="text-lg font-bold mb-4">حالة الحجز</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">حالة الحجز:</span>
                            @switch($booking->booking_status)
                                @case('pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        ⏳ في الانتظار
                                    </span>
                                    @break
                                @case('confirmed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        ✓ مؤكد
                                    </span>
                                    @break
                                @case('completed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        ✅ مكتمل
                                    </span>
                                    @break
                                @case('cancelled')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        ✗ ملغي
                                    </span>
                                    @break
                            @endswitch
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">حالة الدفع:</span>
                            @switch($booking->payment_status)
                                @case('paid')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        ✓ مدفوع
                                    </span>
                                    @break
                                @case('pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        ⏳ معلق
                                    </span>
                                    @break
                                @default
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        ✗ غير مدفوع
                                    </span>
                            @endswitch
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="mt-6 space-y-3">
                        @if($booking->booking_status == 'pending')
                            <button onclick="updateBookingStatus('confirmed')" 
                                    class="w-full btn-primary bg-green-500 hover:bg-green-600">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                تأكيد الحجز
                            </button>
                            <button onclick="updateBookingStatus('cancelled')" 
                                    class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                رفض الحجز
                            </button>
                        @endif

                        @if($booking->booking_status == 'confirmed')
                            <button onclick="updateBookingStatus('completed')" 
                                    class="w-full btn-primary bg-blue-500 hover:bg-blue-600">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                إكمال الحجز
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="glass-effect rounded-xl p-6">
                    <h3 class="text-lg font-bold mb-4">تفاصيل الدفع</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">سعر الخدمة:</span>
                            <span class="font-semibold">{{ number_format($booking->service->price) }} ريال</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">عدد الضيوف:</span>
                            <span class="font-semibold">{{ $booking->guest_count }} ضيف</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">المبلغ الأساسي:</span>
                            <span class="font-semibold">{{ number_format($booking->service->price * $booking->guest_count) }} ريال</span>
                        </div>
                        
                        @if($booking->guest_count > 100)
                        <div class="flex justify-between">
                            <span class="text-gray-600">رسوم الفعاليات الكبيرة:</span>
                            <span class="font-semibold">500 ريال</span>
                        </div>
                        @endif
                        
                        <hr class="border-gray-200">
                        
                        <div class="flex justify-between text-lg">
                            <span class="font-bold">المجموع:</span>
                            <span class="font-bold gradient-text">{{ number_format($booking->total_amount) }} ريال</span>
                        </div>
                    </div>
                </div>

                <!-- Contact Actions -->
                <div class="glass-effect rounded-xl p-6">
                    <h3 class="text-lg font-bold mb-4">التواصل مع العميل</h3>
                    
                    <div class="space-y-3">
                        <a href="mailto:{{ $booking->customer_email }}" 
                           class="w-full flex items-center justify-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            إرسال بريد إلكتروني
                        </a>
                        
                        <a href="tel:{{ $booking->customer_phone }}" 
                           class="w-full flex items-center justify-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium transition-colors">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            اتصال هاتفي
                        </a>
                        
                        <a href="https://wa.me/{{ str_replace(['+', ' ', '-'], '', $booking->customer_phone) }}" 
                           target="_blank"
                           class="w-full flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                            <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                            </svg>
                            واتساب
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateBookingStatus(status) {
    if (!confirm('هل أنت متأكد من تغيير حالة الحجز؟')) {
        return;
    }
    
    fetch(`{{ route('merchant.dashboard.update-booking-status', $booking->id) }}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            booking_status: status
        })
    })
    .then(response => {
        if (response.ok) {
            location.reload();
        } else {
            alert('حدث خطأ في تحديث الحالة');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ في تحديث الحالة');
    });
}

function updateNotes(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    fetch(`{{ route('merchant.dashboard.update-booking-status', $booking->id) }}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            booking_status: '{{ $booking->booking_status }}',
            notes: formData.get('merchant_notes')
        })
    })
    .then(response => {
        if (response.ok) {
            alert('تم حفظ الملاحظات بنجاح');
        } else {
            alert('حدث خطأ في حفظ الملاحظات');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ في حفظ الملاحظات');
    });
}
</script>
@endpush
@endsection
