@extends('layouts.app')

@section('title', 'تفاصيل الحجز')

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
                <h1 class="text-4xl md:text-5xl font-bold mb-4">📋 تفاصيل الحجز</h1>
                <p class="text-xl opacity-90 max-w-2xl mx-auto">رقم الحجز: {{ $booking->booking_number ?? '#' . $booking->id }}</p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 py-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('customer.bookings.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50 transition duration-200 shadow-md">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                العودة للحجوزات
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Service Details -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <span class="text-2xl ml-2">🎯</span>
                            تفاصيل الخدمة
                        </h2>
                    </div>
                    <div class="p-6">
                        @if($booking->service)
                            <div class="space-y-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-20 h-20 bg-gradient-to-r from-blue-400 to-blue-500 rounded-xl flex items-center justify-center text-white text-2xl">
                                        🎉
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $booking->service->name }}</h3>
                                        @if($booking->service->description)
                                            <p class="text-gray-600 leading-relaxed">{{ $booking->service->description }}</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                                    <div class="flex items-center gap-3">
                                        <span class="text-blue-500 text-lg">💰</span>
                                        <div>
                                            <span class="text-sm text-gray-500">السعر</span>
                                            <div class="font-bold text-gray-800">{{ number_format($booking->total_amount ?? $booking->service->price, 2) }} ريال</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-blue-500 text-lg">⏱️</span>
                                        <div>
                                            <span class="text-sm text-gray-500">المدة</span>
                                            <div class="font-bold text-gray-800">{{ $booking->duration_hours ?? $booking->service->duration ?? '2' }} ساعة</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500">تفاصيل الخدمة غير متاحة</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Booking Information -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <span class="text-2xl ml-2">📅</span>
                            معلومات الحجز
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-green-500 text-lg">📅</span>
                                    <div>
                                        <span class="text-sm text-gray-500">تاريخ الحجز</span>
                                        <div class="font-bold text-gray-800">
                                            {{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('Y/m/d') : 'غير محدد' }}
                                        </div>
                                    </div>
                                </div>
                                
                                @if($booking->booking_time)
                                <div class="flex items-center gap-3">
                                    <span class="text-green-500 text-lg">🕐</span>
                                    <div>
                                        <span class="text-sm text-gray-500">وقت الحجز</span>
                                        <div class="font-bold text-gray-800">{{ $booking->booking_time }}</div>
                                    </div>
                                </div>
                                @endif
                                
                                @if($booking->guest_count || $booking->number_of_people)
                                <div class="flex items-center gap-3">
                                    <span class="text-green-500 text-lg">👥</span>
                                    <div>
                                        <span class="text-sm text-gray-500">عدد الأشخاص</span>
                                        <div class="font-bold text-gray-800">{{ $booking->guest_count ?? $booking->number_of_people }} شخص</div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <span class="text-green-500 text-lg">📍</span>
                                    <div>
                                        <span class="text-sm text-gray-500">التاجر</span>
                                        <div class="font-bold text-gray-800">
                                            @if($booking->merchant)
                                                {{ $booking->merchant->business_name ?? $booking->merchant->name }}
                                            @else
                                                غير محدد
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                @if($booking->special_requests || $booking->notes)
                                <div class="flex items-start gap-3">
                                    <span class="text-green-500 text-lg">📝</span>
                                    <div>
                                        <span class="text-sm text-gray-500">ملاحظات خاصة</span>
                                        <div class="font-bold text-gray-800">{{ $booking->special_requests ?? $booking->notes }}</div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                @if($booking->payments && $booking->payments->count() > 0)
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <span class="text-2xl ml-2">💳</span>
                            معلومات الدفع
                        </h2>
                    </div>
                    <div class="p-6">
                        @foreach($booking->payments as $payment)
                        <div class="border-l-4 border-purple-400 pl-4 py-2">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <span class="text-sm text-gray-500">المبلغ</span>
                                    <div class="font-bold text-gray-800">{{ number_format($payment->amount, 2) }} ريال</div>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">طريقة الدفع</span>
                                    <div class="font-bold text-gray-800">{{ $payment->payment_method ?? 'غير محدد' }}</div>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">الحالة</span>
                                    <div class="font-bold text-gray-800">
                                        @if($payment->status === 'completed')
                                            <span class="text-green-600">✅ مكتمل</span>
                                        @elseif($payment->status === 'pending')
                                            <span class="text-yellow-600">⏳ في الانتظار</span>
                                        @else
                                            <span class="text-red-600">❌ فشل</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800">حالة الحجز</h3>
                    </div>
                    <div class="p-6">
                        @php
                            $statusClasses = [
                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'confirmed' => 'bg-green-100 text-green-800 border-green-200',
                                'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                                'completed' => 'bg-blue-100 text-blue-800 border-blue-200'
                            ];
                            $statusLabels = [
                                'pending' => '⏳ في الانتظار',
                                'confirmed' => '✅ مؤكد',
                                'cancelled' => '❌ ملغي',
                                'completed' => '🎉 مكتمل'
                            ];
                            $currentStatus = $booking->status ?? 'pending';
                        @endphp
                        
                        <div class="text-center">
                            <div class="inline-flex items-center px-4 py-2 rounded-full border {{ $statusClasses[$currentStatus] ?? $statusClasses['pending'] }}">
                                <span class="font-bold">{{ $statusLabels[$currentStatus] ?? $statusLabels['pending'] }}</span>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">آخر تحديث: {{ $booking->updated_at->format('Y/m/d H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800">الإجراءات</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if(in_array($currentStatus, ['pending', 'confirmed']))
                            <button onclick="showCancelModal()" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition duration-200">
                                ❌ إلغاء الحجز
                            </button>
                        @endif
                        
                        @if($booking->payment_status === 'paid')
                            <a href="{{ route('customer.bookings.receipt', $booking) }}" class="block w-full bg-blue-500 hover:bg-blue-600 text-white text-center px-4 py-2 rounded-lg transition duration-200">
                                📄 عرض الإيصال
                            </a>
                        @endif
                        
                        <a href="tel:{{ $booking->merchant->phone ?? '' }}" class="block w-full bg-green-500 hover:bg-green-600 text-white text-center px-4 py-2 rounded-lg transition duration-200">
                            📞 اتصال بالتاجر
                        </a>
                    </div>
                </div>

                <!-- Quick Info -->
                <div class="bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl p-6">
                    <h3 class="text-lg font-bold text-blue-800 mb-3">🎯 معلومات سريعة</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-blue-700">رقم الحجز:</span>
                            <span class="font-bold text-blue-900">#{{ $booking->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-blue-700">تاريخ الإنشاء:</span>
                            <span class="font-bold text-blue-900">{{ $booking->created_at->format('Y/m/d') }}</span>
                        </div>
                        @if($booking->total_amount)
                        <div class="flex justify-between">
                            <span class="text-blue-700">إجمالي المبلغ:</span>
                            <span class="font-bold text-blue-900">{{ number_format($booking->total_amount, 2) }} ريال</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" onclick="hideCancelModal()">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4" onclick="event.stopPropagation()">
        <h3 class="text-xl font-bold text-gray-800 mb-4">تأكيد إلغاء الحجز</h3>
        <p class="text-gray-600 mb-6">هل أنت متأكد من رغبتك في إلغاء هذا الحجز؟ هذا الإجراء لا يمكن التراجع عنه.</p>
        
        <form method="POST" action="{{ route('customer.bookings.cancel', $booking) }}">
            @csrf
            <div class="mb-4">
                <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">سبب الإلغاء</label>
                <textarea 
                    id="cancellation_reason" 
                    name="cancellation_reason" 
                    rows="3" 
                    class="w-full border-2 border-gray-200 rounded-lg px-3 py-2 focus:border-blue-500 focus:outline-none"
                    placeholder="يرجى ذكر سبب إلغاء الحجز..."
                    required
                ></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="hideCancelModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    إلغاء
                </button>
                <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition duration-200">
                    تأكيد الإلغاء
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
    document.getElementById('cancelModal').classList.add('flex');
}

function hideCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
    document.getElementById('cancelModal').classList.remove('flex');
}
</script>
@endsection