@extends('layouts.public')

@section('title', 'حجز الخدمات - ' . $merchant->business_name)

@section('content')
<div class="min-h-screen bg-slate-50" dir="rtl">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 space-x-reverse">
                    @if($merchant->logo)
                        <img src="{{ Storage::url($merchant->logo) }}" alt="{{ $merchant->business_name }}" class="w-16 h-16 rounded-lg object-cover">
                    @else
                        <div class="w-16 h-16 bg-primary/10 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">{{ $merchant->business_name }}</h1>
                        <p class="text-slate-600">{{ $merchant->business_type }}</p>
                        @if($merchant->city)
                            <p class="text-sm text-slate-500 flex items-center mt-1">
                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                </svg>
                                {{ $merchant->city }}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="text-left">
                    @if($merchant->verification_status === 'approved')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            تاجر معتمد
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <div class="container mx-auto px-4 py-8">
        @if($services->count() > 0)
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-slate-800 mb-2">الخدمات المتاحة</h2>
                <p class="text-slate-600">اختر الخدمة التي تريد حجزها من الخيارات أدناه</p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($services as $service)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 group">
                        @if($service->images && is_array($service->images) && count($service->images) > 0)
                            <div class="aspect-video overflow-hidden">
                                <img src="{{ Storage::url($service->images[0]) }}" 
                                     alt="{{ $service->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                        @else
                            <div class="aspect-video bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center">
                                <svg class="w-16 h-16 text-primary/40" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                                </svg>
                            </div>
                        @endif

                        <div class="p-6">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="text-xl font-bold text-slate-800 mb-1">{{ $service->name }}</h3>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                        @switch($service->service_type)
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
                                                {{ $service->service_type }}
                                        @endswitch
                                    </span>
                                </div>
                                @if($service->is_featured)
                                    <span class="text-yellow-500">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </span>
                                @endif
                            </div>

                            <p class="text-slate-600 text-sm mb-4 line-clamp-2">{{ $service->description }}</p>

                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <span class="text-2xl font-bold text-slate-800">{{ number_format($service->base_price ?? $service->price) }}</span>
                                    <span class="text-slate-500 text-sm">ريال</span>
                                    @if($service->pricing_model !== 'fixed' && $service->price_type !== 'fixed')
                                        <span class="text-xs text-slate-400 block">
                                            @switch($service->pricing_model ?? $service->price_type)
                                                @case('per_person')
                                                    لكل شخص
                                                    @break
                                                @case('per_table')
                                                    لكل طاولة
                                                    @break
                                                @case('hourly')
                                                @case('per_hour')
                                                    بالساعة
                                                    @break
                                                @case('package')
                                                    باقة
                                                    @break
                                                @default
                                                    {{ $service->pricing_model ?? $service->price_type }}
                                            @endswitch
                                        </span>
                                    @endif
                                </div>
                                @if($service->capacity)
                                    <div class="text-left">
                                        <span class="text-sm text-slate-500">السعة</span>
                                        <div class="font-semibold text-slate-700">{{ $service->capacity }} شخص</div>
                                    </div>
                                @endif
                            </div>

                            @if($service->location)
                                <div class="flex items-center text-sm text-slate-500 mb-4">
                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $service->location }}
                                </div>
                            @endif

                            <a href="{{ route('merchant.service.booking', ['merchant' => $merchant->id, 'service' => $service->id]) }}" 
                               class="block w-full bg-primary text-white text-center py-3 rounded-lg font-semibold hover:bg-primary/90 transition-colors duration-200">
                                احجز الآن
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-slate-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m0 0V9a2 2 0 012-2h2m0 0V6a2 2 0 012-2h2.5"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-slate-700 mb-3">لا توجد خدمات متاحة</h3>
                <p class="text-slate-500">لم يقم هذا التاجر بإضافة أي خدمات متاحة للحجز الإلكتروني بعد.</p>
            </div>
        @endif
    </div>
</div>
@endsection
