@extends('subdomain.layout')

@section('title', $currentMerchant->business_name . ' - الرئيسية')
@section('description', 'اكتشف خدمات ' . $currentMerchant->business_name . ' واحجز تجربتك المميزة')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
        <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
            مرحباً بك في {{ $currentMerchant->business_name }}
        </h1>
        <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto">
            @if($currentMerchant->business_type)
                {{ $currentMerchant->business_type }} - 
            @endif
            تجربة استثنائية تنتظرك
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('subdomain.services') }}" 
               class="bg-white text-gray-900 hover:bg-gray-100 px-8 py-3 rounded-lg font-semibold transition-colors">
                استعرض خدماتنا
            </a>
            <a href="{{ route('subdomain.contact') }}" 
               class="border-2 border-white text-white hover:bg-white hover:text-gray-900 px-8 py-3 rounded-lg font-semibold transition-all">
                اتصل بنا
            </a>
        </div>
    </div>
</section>

<!-- Featured Services -->
@if($featuredServices->count() > 0)
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">خدماتنا المميزة</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                اختر من بين مجموعة متنوعة من الخدمات المصممة خصيصاً لتلبية احتياجاتك
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredServices as $service)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow group">
                    @if($service->images && count($service->images) > 0)
                        <img src="{{ asset('storage/' . $service->images[0]) }}" 
                             alt="{{ $service->name }}" 
                             class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-48 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif

                    <div class="p-6">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-xl font-semibold text-gray-900 group-hover:text-primary-600 transition-colors">
                                {{ $service->name }}
                            </h3>
                            @if($service->is_featured)
                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">مميز</span>
                            @endif
                        </div>

                        <p class="text-gray-600 mb-4 line-clamp-2">
                            {{ Str::limit($service->description, 100) }}
                        </p>

                        <div class="flex justify-between items-center">
                            <div class="text-2xl font-bold" style="color: var(--primary-color);">
                                {{ $service->price_formatted }}
                            </div>
                            <a href="{{ route('subdomain.service', $service) }}" 
                               class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                               style="background-color: var(--primary-color);">
                                عرض التفاصيل
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('subdomain.services') }}" 
               class="inline-flex items-center px-8 py-3 border border-transparent text-lg font-medium rounded-lg text-white hover:bg-opacity-90 transition-all"
               style="background-color: var(--primary-color);">
                عرض جميع الخدمات
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Why Choose Us -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">لماذا تختارنا؟</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                نقدم لك تجربة متميزة مع أعلى معايير الجودة والخدمة
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                     style="background-color: var(--primary-color); opacity: 0.1;">
                    <svg class="w-8 h-8" style="color: var(--primary-color);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">جودة عالية</h3>
                <p class="text-gray-600">نضمن لك أفضل مستوى من الخدمة والجودة في كل تجربة</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                     style="background-color: var(--primary-color); opacity: 0.1;">
                    <svg class="w-8 h-8" style="color: var(--primary-color);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">سرعة في الاستجابة</h3>
                <p class="text-gray-600">نلتزم بالمواعيد ونقدم خدمة سريعة وفعالة</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                     style="background-color: var(--primary-color); opacity: 0.1;">
                    <svg class="w-8 h-8" style="color: var(--primary-color);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">رضا العملاء</h3>
                <p class="text-gray-600">رضاكم هو هدفنا الأول ونسعى لتحقيق توقعاتكم</p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-16" style="background-color: var(--primary-color);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
            جاهز لبدء تجربتك معنا؟
        </h2>
        <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
            احجز الآن واستمتع بخدمة متميزة تفوق توقعاتك
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('subdomain.services') }}" 
               class="bg-white text-gray-900 hover:bg-gray-100 px-8 py-3 rounded-lg font-semibold transition-colors">
                احجز الآن
            </a>
            <a href="{{ route('subdomain.contact') }}" 
               class="border-2 border-white text-white hover:bg-white hover:text-gray-900 px-8 py-3 rounded-lg font-semibold transition-all">
                تواصل معنا
            </a>
        </div>
    </div>
</section>
@endsection

@push('head')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush