@extends('layouts.app')

@section('title', 'حجوزاتي')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100" dir="rtl">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white py-16 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 right-10 w-32 h-32 bg-white rounded-full animate-pulse"></div>
            <div class="absolute bottom-10 left-10 w-24 h-24 bg-white rounded-full animate-pulse" style="animation-delay: 1s;"></div>
        </div>
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">📅 حجوزاتي</h1>
                <p class="text-xl opacity-90 max-w-2xl mx-auto">إدارة جميع حجوزاتك وتتبع حالتها</p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 py-8">
        @auth('customer')
            @php
                $bookings = Auth::guard('customer')->user()->bookings()
                    ->with(['service', 'service.merchant'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            @endphp

            @if($bookings->count() > 0)
                <!-- Bookings List -->
                <div class="space-y-6">
                    @foreach($bookings as $booking)
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                            <div class="p-6">
                                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                    <!-- Booking Info -->
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-3">
                                            <h3 class="text-xl font-bold text-gray-800">{{ $booking->service->name }}</h3>
                                            @php
                                                $statusClasses = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'confirmed' => 'bg-green-100 text-green-800',
                                                    'cancelled' => 'bg-red-100 text-red-800',
                                                    'completed' => 'bg-blue-100 text-blue-800'
                                                ];
                                                $statusLabels = [
                                                    'pending' => 'في الانتظار',
                                                    'confirmed' => 'مؤكد',
                                                    'cancelled' => 'ملغي',
                                                    'completed' => 'مكتمل'
                                                ];
                                            @endphp
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClasses[$booking->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $statusLabels[$booking->status] ?? $booking->status }}
                                            </span>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m0 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8h1m4 0h1"></path>
                                                </svg>
                                                <span>مقدم الخدمة: {{ $booking->service->merchant->business_name ?? $booking->service->merchant->name }}</span>
                                            </div>
                                            
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>تاريخ الحجز: {{ $booking->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            
                                            @if($booking->booking_date)
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <span>تاريخ الخدمة: {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</span>
                                            </div>
                                            @endif
                                            
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                </svg>
                                                <span>المبلغ: {{ number_format($booking->total_amount ?? $booking->service->price, 0) }} ريال</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex gap-2 lg:flex-col">
                                        <a href="{{ route('customer.bookings.show', $booking) }}" 
                                           class="bg-blue-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-blue-700 transition duration-200 text-center whitespace-nowrap">
                                            عرض التفاصيل
                                        </a>
                                        
                                        @if($booking->status === 'pending' || $booking->status === 'confirmed')
                                            <button onclick="cancelBooking({{ $booking->id }})" 
                                                    class="bg-red-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-red-700 transition duration-200 whitespace-nowrap">
                                                إلغاء الحجز
                                            </button>
                                        @endif
                                        
                                        @if($booking->status === 'completed')
                                            <a href="{{ route('customer.reviews.create', $booking->service) }}" 
                                               class="bg-green-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-green-700 transition duration-200 text-center whitespace-nowrap">
                                                تقييم الخدمة
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination if needed -->
                @if($bookings instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-8">
                        {{ $bookings->links() }}
                    </div>
                @endif

            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="text-8xl mb-6">📋</div>
                    <h3 class="text-3xl font-bold text-gray-800 mb-4">لا توجد حجوزات</h3>
                    <p class="text-xl text-gray-600 mb-8">لم تقم بأي حجوزات بعد، ابدأ بتصفح الخدمات المتاحة</p>
                    <a href="{{ route('customer.services.index') }}" 
                       class="inline-flex items-center bg-blue-600 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:bg-blue-700 transition duration-200">
                        <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        تصفح الخدمات
                    </a>
                </div>
            @endif
        @else
            <!-- Not Authenticated -->
            <div class="text-center py-16">
                <div class="text-8xl mb-6">🔒</div>
                <h3 class="text-3xl font-bold text-gray-800 mb-4">تحتاج إلى تسجيل الدخول</h3>
                <p class="text-xl text-gray-600 mb-8">سجل دخولك لعرض حجوزاتك</p>
                <a href="{{ route('customer.login') }}" 
                   class="inline-flex items-center bg-blue-600 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:bg-blue-700 transition duration-200">
                    تسجيل الدخول
                </a>
            </div>
        @endauth
    </div>
</div>

<script>
function cancelBooking(bookingId) {
    if (confirm('هل أنت متأكد من إلغاء هذا الحجز؟')) {
        fetch('/customer/bookings/' + bookingId + '/cancel', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload page to update booking status
                location.reload();
            } else {
                alert('حدث خطأ أثناء إلغاء الحجز: ' + (data.message || 'خطأ غير معروف'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في الاتصال');
        });
    }
}
</script>

@endsection