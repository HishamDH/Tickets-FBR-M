@extends('layouts.app')

@section('title', 'إدارة الحجوزات')

@section('content')
<div class="min-h-screen bg-gray-50" dir="rtl">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">إدارة الحجوزات</h1>
                    <p class="text-gray-600 mt-1">متابعة وإدارة جميع حجوزات عملائك</p>
                </div>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-200">
                        تصدير التقرير
                    </button>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex space-x-4 space-x-reverse">
                    <a href="{{ route('merchant.bookings.index') }}" 
                       class="px-4 py-2 rounded-lg font-medium {{ !request('status') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-blue-600' }}">
                        جميع الحجوزات
                    </a>
                    <a href="{{ route('merchant.bookings.index', ['status' => 'pending']) }}" 
                       class="px-4 py-2 rounded-lg font-medium {{ request('status') === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'text-gray-600 hover:text-yellow-600' }}">
                        قيد الانتظار
                    </a>
                    <a href="{{ route('merchant.bookings.index', ['status' => 'confirmed']) }}" 
                       class="px-4 py-2 rounded-lg font-medium {{ request('status') === 'confirmed' ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:text-green-600' }}">
                        مؤكدة
                    </a>
                    <a href="{{ route('merchant.bookings.index', ['status' => 'completed']) }}" 
                       class="px-4 py-2 rounded-lg font-medium {{ request('status') === 'completed' ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-blue-600' }}">
                        مكتملة
                    </a>
                    <a href="{{ route('merchant.bookings.index', ['status' => 'cancelled']) }}" 
                       class="px-4 py-2 rounded-lg font-medium {{ request('status') === 'cancelled' ? 'bg-red-100 text-red-700' : 'text-gray-600 hover:text-red-600' }}">
                        ملغاة
                    </a>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg alert flex items-center">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg alert flex items-center">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="mb-6 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg alert flex items-center">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('info') }}
            </div>
        @endif

        <!-- Bookings Table -->
        @if($bookings->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">رقم الحجز</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">العميل</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الخدمة</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">التاريخ</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">المبلغ</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">الحالة</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($bookings as $booking)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">#{{ $booking->booking_number ?? 'BOK-' . $booking->id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center ml-3">
                                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $booking->customer_name ?? $booking->user->name ?? 'غير محدد' }}</div>
                                                <div class="text-sm text-gray-500">{{ $booking->customer_email ?? $booking->user->email ?? '' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $booking->service->name ?? 'خدمة محذوفة' }}</div>
                                        @if($booking->service)
                                            <div class="text-sm text-gray-500">{{ Str::limit($booking->service->description, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $booking->booking_date ? $booking->booking_date->format('Y/m/d') : 'غير محدد' }}</div>
                                        <div class="text-sm text-gray-500">{{ $booking->booking_time ?? 'غير محدد' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900">{{ number_format($booking->total_amount ?? 0) }} ريال</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'confirmed' => 'bg-green-100 text-green-800',
                                                'completed' => 'bg-blue-100 text-blue-800',
                                                'cancelled' => 'bg-red-100 text-red-800'
                                            ];
                                            $statusNames = [
                                                'pending' => 'قيد الانتظار',
                                                'confirmed' => 'مؤكدة',
                                                'completed' => 'مكتملة',
                                                'cancelled' => 'ملغاة'
                                            ];
                                        @endphp
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $statusNames[$booking->status] ?? $booking->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            <a href="{{ route('merchant.bookings.show', $booking->id) }}" 
                                               class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-lg transition-colors">
                                                عرض
                                            </a>
                                            
                                            @if($booking->status === 'pending')
                                                <form method="POST" action="{{ route('merchant.bookings.confirm', $booking->id) }}" class="inline" onsubmit="return confirmAction('confirm')">
                                                    @csrf
                                                    <button type="submit" class="confirm-btn text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100 px-3 py-1 rounded-lg transition-colors">
                                                        <span class="text">تأكيد</span>
                                                        <span class="loading hidden">جاري التأكيد...</span>
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('merchant.bookings.cancel', $booking->id) }}" class="inline" onsubmit="return confirmAction('cancel')">
                                                    @csrf
                                                    <button type="submit" class="cancel-btn text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-lg transition-colors">
                                                        <span class="text">إلغاء</span>
                                                        <span class="loading hidden">جاري الإلغاء...</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-white px-4 py-3 border-t border-gray-200">
                    {{ $bookings->links() }}
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 6l4-4m4 4l-4-4m0 8h.01"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">لا توجد حجوزات بعد</h3>
                <p class="text-gray-600 mb-6">ستظهر هنا جميع الحجوزات المستلمة من العملاء</p>
                <a href="{{ route('merchant.services.index') }}" 
                   class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    إدارة الخدمات
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function confirmAction(actionType) {
    const messages = {
        'confirm': 'هل أنت متأكد من تأكيد هذا الحجز؟',
        'cancel': 'هل أنت متأكد من إلغاء هذا الحجز؟'
    };
    
    const result = confirm(messages[actionType]);
    
    if (result) {
        // Show loading state
        const form = event.target.closest('form');
        const button = form.querySelector('button[type="submit"]');
        const textSpan = button.querySelector('.text');
        const loadingSpan = button.querySelector('.loading');
        
        textSpan.classList.add('hidden');
        loadingSpan.classList.remove('hidden');
        button.disabled = true;
        
        // Set timeout to reset button state if form submission fails
        setTimeout(() => {
            textSpan.classList.remove('hidden');
            loadingSpan.classList.add('hidden');
            button.disabled = false;
        }, 10000); // Reset after 10 seconds
    }
    
    return result;
}

// Add general error handling for failed requests
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert, .notification');
        alerts.forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
});
</script>

@endsection