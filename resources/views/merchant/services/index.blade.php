@extends('layouts.app')

@section('title', 'إدارة الخدمات')

@section('content')
<div class="min-h-screen bg-gray-50" dir="rtl">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">إدارة الخدمات</h1>
                    <p class="text-gray-600 mt-1">إدارة وتنظيم جميع خدماتك المقدمة</p>
                </div>
                <a href="{{ route('merchant.services.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200 flex items-center">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    إضافة خدمة جديدة
                </a>
            </div>

            <!-- Filter Tabs -->
            <div class="bg-white rounded-lg p-4 shadow-sm">
                <div class="flex space-x-4 space-x-reverse">
                    <a href="{{ route('merchant.services.index') }}" 
                       class="px-4 py-2 rounded-lg font-medium {{ !request('status') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-blue-600' }}">
                        جميع الخدمات
                    </a>
                    <a href="{{ route('merchant.services.index', ['status' => 'active']) }}" 
                       class="px-4 py-2 rounded-lg font-medium {{ request('status') === 'active' ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:text-green-600' }}">
                        الخدمات النشطة
                    </a>
                    <a href="{{ route('merchant.services.index', ['status' => 'inactive']) }}" 
                       class="px-4 py-2 rounded-lg font-medium {{ request('status') === 'inactive' ? 'bg-red-100 text-red-700' : 'text-gray-600 hover:text-red-600' }}">
                        الخدمات المعطلة
                    </a>
                </div>
            </div>
        </div>

        <!-- Services Grid -->
        @if($services->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($services as $service)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                        <!-- Service Image -->
                        <div class="h-48 bg-gradient-to-br from-blue-500 to-blue-600 relative">
                            @if($service->image)
                                <img src="{{ Storage::url($service->image) }}" 
                                     alt="{{ $service->name }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Status Badge -->
                            <div class="absolute top-4 right-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $service->is_active ? 'نشطة' : 'معطلة' }}
                                </span>
                            </div>
                        </div>

                        <!-- Service Content -->
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $service->name }}</h3>
                                    <p class="text-gray-600 text-sm line-clamp-2">{{ $service->description }}</p>
                                </div>
                            </div>

                            <!-- Service Details -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                    السعر: {{ number_format($service->price) }} ريال
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 ml-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    الموقع: {{ $service->location ?? 'غير محدد' }}
                                </div>
                                @if($service->category)
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 ml-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    التصنيف: {{ $service->category }}
                                </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <a href="{{ route('merchant.services.show', $service->id) }}" 
                                   class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold text-center transition duration-200">
                                    عرض التفاصيل
                                </a>
                                <a href="{{ route('merchant.services.edit', $service->id) }}" 
                                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                
                                <!-- Toggle Status -->
                                <form method="POST" action="{{ route('merchant.services.toggle-status', $service->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="px-4 py-2 rounded-lg transition duration-200 {{ $service->is_active ? 'bg-red-100 hover:bg-red-200 text-red-700' : 'bg-green-100 hover:bg-green-200 text-green-700' }}">
                                        @if($service->is_active)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $services->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">لا توجد خدمات بعد</h3>
                <p class="text-gray-600 mb-6">ابدأ بإضافة خدماتك الأولى لتتمكن من استقبال الحجوزات</p>
                <a href="{{ route('merchant.services.create') }}" 
                   class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-200">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    إضافة خدمة جديدة
                </a>
            </div>
        @endif
    </div>
</div>
@endsection