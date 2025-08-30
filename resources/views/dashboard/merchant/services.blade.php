@extends('layouts.app')

@section('title', 'إدارة الخدمات')

@section('content')
<div class="min-h-screen bg-gray-50" dir="rtl">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">إدارة الخدمات</h1>
                <p class="text-gray-600">إدارة وتنظيم خدماتك المقدمة للعملاء</p>
            </div>
            <button class="btn-primary flex items-center">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                إضافة خدمة جديدة
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="glass-effect rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">إجمالي الخدمات</p>
                        <p class="text-2xl font-bold gradient-text">{{ $services->total() }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-xl">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">الخدمات النشطة</p>
                        <p class="text-2xl font-bold text-green-600">{{ $services->where('is_active', true)->count() }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-xl">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">إجمالي الحجوزات</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $services->sum('bookings_count') }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-xl">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="glass-effect rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">إجمالي الإيرادات</p>
                        <p class="text-2xl font-bold gradient-text">{{ number_format($services->sum('total_revenue') ?? 0) }} ريال</p>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-xl">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="glass-effect rounded-xl p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                    <input type="text" placeholder="ابحث في الخدمات..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">جميع الفئات</option>
                        <option value="venues">قاعات ومواقع</option>
                        <option value="catering">تموين وضيافة</option>
                        <option value="photography">تصوير</option>
                        <option value="entertainment">ترفيه</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">جميع الحالات</option>
                        <option value="active">نشطة</option>
                        <option value="inactive">غير نشطة</option>
                        <option value="draft">مسودة</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="w-full btn-primary">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        بحث
                    </button>
                </div>
            </div>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @forelse($services as $service)
            <div class="glass-effect rounded-xl overflow-hidden card-hover group">
                <!-- Service Image -->
                <div class="h-48 bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center relative">
                    @if($service->images && count($service->images) > 0)
                        <img src="{{ asset('storage/' . $service->images[0]) }}" 
                             alt="{{ $service->name }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="text-6xl text-white/80">🎯</div>
                    @endif
                    
                    <!-- Status Badge -->
                    <div class="absolute top-4 right-4">
                        @if($service->is_active)
                            <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-medium">نشطة</span>
                        @else
                            <span class="bg-gray-500 text-white px-3 py-1 rounded-full text-xs font-medium">غير نشطة</span>
                        @endif
                    </div>

                    <!-- Featured Badge -->
                    @if($service->is_featured)
                        <div class="absolute top-4 left-4">
                            <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-medium">مميزة</span>
                        </div>
                    @endif
                </div>

                <!-- Service Details -->
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2 text-gray-800 group-hover:text-orange-600 transition-colors">
                        {{ $service->name }}
                    </h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $service->description }}</p>
                    
                    <!-- Service Stats -->
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div class="text-center">
                            <p class="text-sm text-gray-500">السعر</p>
                            <p class="font-bold gradient-text">{{ number_format($service->price) }} ريال</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500">الحجوزات</p>
                            <p class="font-bold text-blue-600">{{ $service->bookings_count ?? 0 }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500">الإيرادات</p>
                            <p class="font-bold text-green-600">{{ number_format($service->total_revenue ?? 0) }}</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-2 space-x-reverse">
                        <button class="flex-1 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            تعديل
                        </button>
                        <button class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            عرض
                        </button>
                        <button class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <div class="text-6xl text-gray-300 mb-4">📋</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد خدمات بعد</h3>
                <p class="text-gray-500 mb-6">ابدأ بإضافة خدمتك الأولى لجذب العملاء</p>
                <button class="btn-primary">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    إضافة خدمة جديدة
                </button>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($services->hasPages())
        <div class="flex justify-center">
            {{ $services->links() }}
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endsection
