@extends('layouts.app')

@section('title', 'تفاصيل الخدمة')

@section('content')
<div class="min-h-screen bg-gray-50" dir="rtl">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $service->name }}</h1>
                    <p class="text-gray-600 mt-1">تفاصيل الخدمة وإدارتها</p>
                </div>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <a href="{{ route('merchant.services.edit', $service->id) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200">
                        تعديل الخدمة
                    </a>
                    <a href="{{ route('merchant.services.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200">
                        العودة للقائمة
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Service Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">معلومات الخدمة</h2>
                    
                    <!-- Service Image -->
                    @if($service->image)
                        <div class="mb-6">
                            <img src="{{ Storage::url($service->image) }}" 
                                 alt="{{ $service->name }}" 
                                 class="w-full h-64 object-cover rounded-lg">
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">اسم الخدمة</label>
                            <p class="text-lg text-gray-900">{{ $service->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">الوصف</label>
                            <p class="text-gray-700 leading-relaxed">{{ $service->description }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">السعر</label>
                                <p class="text-xl font-bold text-green-600">{{ number_format($service->price) }} ريال</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">التصنيف</label>
                                <p class="text-gray-700">{{ $service->category }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">الموقع</label>
                                <p class="text-gray-700">{{ $service->location ?? 'غير محدد' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">السعة</label>
                                <p class="text-gray-700">{{ $service->capacity ?? 'غير محدد' }} شخص</p>
                            </div>
                        </div>

                        @if($service->features && is_array($service->features))
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">المميزات</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($service->features as $feature)
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">{{ $feature }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">الحجوزات الأخيرة</h2>
                    @if($service->bookings->count() > 0)
                        <div class="space-y-4">
                            @foreach($service->bookings->take(5) as $booking)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $booking->customer_name ?? $booking->user->name ?? 'عميل غير محدد' }}</p>
                                        <p class="text-sm text-gray-600">{{ $booking->created_at->format('Y/m/d') }}</p>
                                    </div>
                                    <div class="text-left">
                                        <p class="font-semibold text-green-600">{{ number_format($booking->total_amount) }} ريال</p>
                                        <span class="px-2 py-1 text-xs rounded-full {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $booking->status === 'confirmed' ? 'مؤكدة' : 'قيد الانتظار' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">لا توجد حجوزات بعد</p>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">حالة الخدمة</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700">الحالة</span>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $service->is_active ? 'نشطة' : 'معطلة' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700">متوفرة للحجز</span>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $service->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $service->is_available ? 'متوفرة' : 'غير متوفرة' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700">خدمة مميزة</span>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $service->is_featured ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $service->is_featured ? 'مميزة' : 'عادية' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">إحصائيات سريعة</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700">إجمالي الحجوزات</span>
                            <span class="text-2xl font-bold text-blue-600">{{ $service->bookings->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700">الحجوزات النشطة</span>
                            <span class="text-2xl font-bold text-green-600">{{ $service->activeBookings->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700">إجمالي الإيرادات</span>
                            <span class="text-xl font-bold text-green-600">{{ number_format($service->bookings->sum('total_amount')) }} ريال</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">إجراءات سريعة</h3>
                    <div class="space-y-3">
                        <form method="POST" action="{{ route('merchant.services.toggle-status', $service->id) }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-2 rounded-lg font-semibold transition duration-200 {{ $service->is_active ? 'bg-red-100 hover:bg-red-200 text-red-700' : 'bg-green-100 hover:bg-green-200 text-green-700' }}">
                                {{ $service->is_active ? 'إيقاف الخدمة' : 'تفعيل الخدمة' }}
                            </button>
                        </form>
                        
                        <a href="{{ route('merchant.bookings.index', ['service' => $service->id]) }}" 
                           class="w-full block text-center bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg font-semibold transition duration-200">
                            عرض الحجوزات
                        </a>
                        
                        <button onclick="if(confirm('هل أنت متأكد من حذف هذه الخدمة؟')) { document.getElementById('delete-form').submit(); }" 
                                class="w-full bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg font-semibold transition duration-200">
                            حذف الخدمة
                        </button>
                        
                        <form id="delete-form" method="POST" action="{{ route('merchant.services.destroy', $service->id) }}" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection