@extends('layouts.merchant')

@section('title', 'معاينة المقاعد - ' . $venueLayout->name)

@section('content')
<div class="min-h-screen bg-gray-50 py-8" dir="rtl">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">معاينة تخطيط المقاعد</h1>
                    <p class="text-gray-600 mt-1">{{ $venueLayout->name }}</p>
                </div>
                <div class="flex space-x-3 space-x-reverse">
                    <a href="{{ route('merchant.venue-layout.designer', $venueLayout) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        تعديل التخطيط
                    </a>
                </div>
            </div>
        </div>

        <!-- Layout Info -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <h3 class="text-lg font-semibold">إجمالي المقاعد</h3>
                        <p class="text-2xl font-bold text-blue-600">{{ $venueLayout->total_seats }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <h3 class="text-lg font-semibold">المقاعد المتاحة</h3>
                        <p class="text-2xl font-bold text-green-600">{{ $venueLayout->available_seats_count }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <h3 class="text-lg font-semibold">الصفوف</h3>
                        <p class="text-2xl font-bold text-purple-600">{{ $venueLayout->rows }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-100 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                        </svg>
                    </div>
                    <div class="mr-4">
                        <h3 class="text-lg font-semibold">الأعمدة</h3>
                        <p class="text-2xl font-bold text-orange-600">{{ $venueLayout->columns }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Venue Preview -->
        <div class="bg-white rounded-lg shadow-sm border p-8">
            <div class="mb-8">
                <div class="text-center mb-6">
                    <div class="inline-block px-12 py-4 bg-gradient-to-r from-gray-700 to-gray-900 text-white rounded-xl font-bold text-lg shadow-lg">
                        🎭 المسرح / المنصة
                    </div>
                </div>
            </div>

            <!-- Seat Map -->
            <div class="flex justify-center">
                <div class="inline-block">
                    <div id="seat-preview" class="mx-auto" 
                         style="display: grid; grid-template-columns: repeat({{ $venueLayout->columns }}, 1fr); gap: 12px;">
                        @foreach($venueLayout->seats as $seat)
                            @if($seat->category === 'vip')
                                <div class="seat-preview bg-gradient-to-br from-amber-300 to-orange-400 border-2 border-amber-500 text-amber-900 w-10 h-10 rounded-lg flex items-center justify-center text-xs font-bold shadow-md hover:shadow-lg transition-all duration-200 cursor-pointer transform hover:scale-105"
                                     title="مقعد VIP رقم {{ $seat->seat_number }} - الصف {{ $seat->row_number }}">
                                    {{ $seat->seat_number }}
                                </div>
                            @else
                                <div class="seat-preview bg-gradient-to-br from-indigo-300 to-blue-400 border-2 border-indigo-500 text-indigo-900 w-10 h-10 rounded flex items-center justify-center text-xs font-bold shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer"
                                     title="مقعد عادي رقم {{ $seat->seat_number }} - الصف {{ $seat->row_number }}">
                                    {{ $seat->seat_number }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="mt-8 flex justify-center">
                <div class="flex items-center space-x-8 space-x-reverse text-sm">
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <div class="w-6 h-6 bg-gradient-to-br from-indigo-300 to-blue-400 border-2 border-indigo-500 rounded"></div>
                        <span class="text-gray-700 font-medium">مقعد عادي</span>
                    </div>
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <div class="w-6 h-6 bg-gradient-to-br from-amber-300 to-orange-400 border-2 border-amber-500 rounded-lg"></div>
                        <span class="text-gray-700 font-medium">مقعد VIP</span>
                    </div>
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <div class="w-6 h-6 bg-gray-300 border-2 border-gray-400 rounded opacity-50"></div>
                        <span class="text-gray-700 font-medium">غير متاح</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seat Details -->
        <div class="mt-8 bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-xl font-bold mb-4">تفاصيل المقاعد</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم المقعد</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الصف</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العمود</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الفئة</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السعر</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($venueLayout->seats->sortBy(['row_number', 'column_number']) as $seat)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $seat->seat_number }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $seat->row_number }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $seat->column_number }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($seat->category === 'vip')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">
                                        VIP
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        عادي
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($seat->price) }} ريال
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($seat->is_available)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                        متاح
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-rose-100 text-rose-800">
                                        محجوز
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to seats
    const seats = document.querySelectorAll('.seat-preview');
    
    seats.forEach(seat => {
        seat.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
        });
        
        seat.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>
@endpush
@endsection
