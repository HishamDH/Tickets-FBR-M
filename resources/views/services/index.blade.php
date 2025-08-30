@extends('layouts.public')

@section('title', 'اكتشف الخدمات - شباك التذاكر')
@section('description', 'استكشف مجموعة واسعة من الخدمات المتميزة في جميع أنحاء المملكة العربية السعودية')

@section('content')
<!-- Page Header -->
<section class="bg-gradient-to-br from-orange-50 to-red-50 py-16">
    <div class="container mx-auto px-4">
        <div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 200)">
            <div x-show="loaded" 
                 x-transition:enter="transition ease-out duration-800"
                 x-transition:enter-start="opacity-0 transform translate-y-8"
                 x-transition:enter-end="opacity-1 transform translate-y-0"
                 class="text-center mb-12">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    <span class="gradient-text">اكتشف الخدمات</span>
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    استكشف مجموعة واسعة من الخدمات المتميزة المقدمة من شركائنا المعتمدين
                </p>
                <div class="mt-6 flex items-center justify-center space-x-6 space-x-reverse">
                    <div class="flex items-center text-gray-600">
                        <span class="text-2xl font-bold gradient-text ml-2">{{ $services->total() }}</span>
                        <span>خدمة متاحة</span>
                    </div>
                    <div class="w-2 h-2 bg-orange-300 rounded-full"></div>
                    <div class="flex items-center text-gray-600">
                        <span class="text-2xl font-bold gradient-text ml-2">{{ $categories->count() }}</span>
                        <span>فئة مختلفة</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search and Filters Section -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <div class="glass-effect rounded-2xl p-8 shadow-lg">
            <form method="GET" action="{{ route('services.index') }}" x-data="{ 
                category: '{{ request('category') }}',
                minPrice: '{{ request('min_price') }}',
                maxPrice: '{{ request('max_price') }}'
            }">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Search Input -->
                    <div class="lg:col-span-5">
                        <label class="block text-sm font-bold text-gray-700 mb-3">🔍 البحث في الخدمات</label>
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="ابحث عن اسم الخدمة، الموقع، أو الوصف..."
                                   class="w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-lg transition-all">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Category Filter -->
                    <div class="lg:col-span-3">
                        <label class="block text-sm font-bold text-gray-700 mb-3">🏷️ الفئة</label>
                        <select name="category" x-model="category" 
                                class="w-full px-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-lg transition-all bg-white">
                            <option value="">جميع الفئات</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-3">💰 نطاق السعر</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" 
                                   name="min_price" 
                                   x-model="minPrice"
                                   placeholder="من"
                                   class="px-3 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                            <input type="number" 
                                   name="max_price" 
                                   x-model="maxPrice"
                                   placeholder="إلى"
                                   class="px-3 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="lg:col-span-2 flex flex-col justify-end">
                        <div class="flex space-x-reverse space-x-3">
                            <button type="submit" 
                                    class="flex-1 btn-primary py-4 text-lg font-bold shadow-lg hover:shadow-xl pulse-glow">
                                بحث
                            </button>
                            @if(request()->hasAny(['search', 'category', 'min_price', 'max_price']))
                                <a href="{{ route('services.index') }}" 
                                   class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-4 rounded-xl font-bold transition-all text-center">
                                    مسح
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="flex flex-col xl:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="w-full xl:w-1/4">
                <div class="glass-effect rounded-2xl p-6 sticky top-28">
                    <h3 class="font-bold text-xl mb-6 gradient-text">🎯 فئات الخدمات</h3>

                    <div class="space-y-3">
                        <a href="{{ route('services.index') }}" 
                           class="block px-4 py-3 rounded-xl transition-all duration-300 card-hover {{ !request('category') ? 'gradient-bg text-white font-bold shadow-lg' : 'text-gray-600 hover:bg-orange-50 hover:text-orange-600 bg-white' }}">
                            <div class="flex justify-between items-center">
                                <span>🌟 جميع الخدمات</span>
                                <span class="text-sm {{ !request('category') ? 'text-orange-100' : 'text-gray-400' }} bg-black/10 px-2 py-1 rounded-full">
                                    {{ $services->total() }}
                                </span>
                            </div>
                        </a>
                        
                        @foreach($categories as $category)
                            @php
                                $categoryCount = \App\Models\Service::where('category', $category)->where('is_active', true)->count();
                                $categoryIcons = [
                                    'Venues' => '🏰',
                                    'Catering' => '🍽️', 
                                    'Photography' => '📸',
                                    'Entertainment' => '🎵',
                                    'Planning' => '📋',
                                    'Transportation' => '🚗',
                                    'Decoration' => '🎨'
                                ];
                                $icon = $categoryIcons[$category] ?? '⭐';
                            @endphp
                            <a href="{{ route('services.index', ['category' => $category]) }}" 
                               class="block px-4 py-3 rounded-xl transition-all duration-300 card-hover {{ request('category') == $category ? 'gradient-bg text-white font-bold shadow-lg' : 'text-gray-600 hover:bg-orange-50 hover:text-orange-600 bg-white' }}">
                                <div class="flex justify-between items-center">
                                    <span>{{ $icon }} {{ $category }}</span>
                                    <span class="text-sm {{ request('category') == $category ? 'text-orange-100' : 'text-gray-400' }} bg-black/10 px-2 py-1 rounded-full">
                                        {{ $categoryCount }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Popular Price Ranges -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h4 class="font-bold text-lg mb-4 gradient-text">💎 نطاقات الأسعار الشائعة</h4>
                        <div class="space-y-2">
                            @php
                                $priceRanges = [
                                    ['max' => 1000, 'label' => 'أقل من 1,000 ريال', 'icon' => '💚'],
                                    ['min' => 1000, 'max' => 5000, 'label' => '1,000 - 5,000 ريال', 'icon' => '💙'],
                                    ['min' => 5000, 'max' => 15000, 'label' => '5,000 - 15,000 ريال', 'icon' => '💜'],
                                    ['min' => 15000, 'label' => 'أكثر من 15,000 ريال', 'icon' => '🔥']
                                ];
                            @endphp
                            @foreach($priceRanges as $range)
                                <a href="{{ route('services.index', array_filter($range, function($key) { return in_array($key, ['min', 'max']); }, ARRAY_FILTER_USE_KEY)) }}" 
                                   class="flex items-center text-gray-600 hover:text-orange-600 transition-colors duration-300 py-2 px-3 rounded-lg hover:bg-orange-50">
                                    <span class="ml-2">{{ $range['icon'] }}</span>
                                    <span>{{ $range['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Services Grid -->
            <main class="w-full xl:w-3/4">
                @if($services->count() > 0)
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                        @foreach($services as $index => $service)
                            <div x-data="{ inView: false }" 
                                 x-intersect="inView = true"
                                 x-show="inView"
                                 x-transition:enter="transition ease-out duration-700"
                                 x-transition:enter-start="opacity-0 transform translate-y-8"
                                 x-transition:enter-end="opacity-1 transform translate-y-0"
                                 style="transition-delay: {{ ($index % 6) * 100 }}ms"
                                 class="glass-effect rounded-2xl overflow-hidden card-hover group shadow-lg">
                                
                                <!-- Service Image/Header -->
                                <div class="h-48 bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center relative overflow-hidden">
                                    @if($service->is_featured)
                                        <div class="absolute top-4 right-4 bg-white/20 backdrop-blur-sm text-white px-3 py-1 rounded-full text-sm font-bold pulse-glow">
                                            ⭐ مميز
                                        </div>
                                    @endif
                                    
                                    <div class="text-white/80 group-hover:text-white transition-colors duration-300 text-6xl">
                                        @php
                                            $categoryIcons = [
                                                'Venues' => '🏰',
                                                'Catering' => '🍽️', 
                                                'Photography' => '📸',
                                                'Entertainment' => '🎵',
                                                'Planning' => '📋',
                                                'Transportation' => '🚗',
                                                'Decoration' => '🎨'
                                            ];
                                            echo $categoryIcons[$service->category] ?? '⭐';
                                        @endphp
                                    </div>

                                    <!-- Floating Category Badge -->
                                    <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur-sm text-orange-600 px-3 py-1 rounded-full text-sm font-bold">
                                        {{ $service->category }}
                                    </div>
                                </div>

                                <!-- Service Content -->
                                <div class="p-6">
                                    <div class="mb-4">
                                        <h3 class="font-bold text-xl text-gray-800 group-hover:text-orange-600 transition-colors duration-300 mb-2">
                                            {{ $service->name }}
                                        </h3>
                                        
                                        <div class="flex items-center text-gray-500 text-sm">
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span>📍 {{ $service->location }}</span>
                                        </div>
                                    </div>
                                    
                                    <p class="text-gray-600 mb-6 leading-relaxed">
                                        {{ Str::limit($service->description, 120) }}
                                    </p>
                                    
                                    <!-- Service Features -->
                                    @if($service->features)
                                        <div class="mb-6">
                                            <div class="flex flex-wrap gap-2">
                                                @foreach(json_decode($service->features, true) ?? [] as $feature)
                                                    <span class="bg-orange-100 text-orange-600 px-2 py-1 rounded-full text-xs font-medium">
                                                        ✓ {{ $feature }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Price and CTA -->
                                    <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                                        <div>
                                            <div class="text-2xl font-bold gradient-text">
                                                {{ number_format($service->price) }}
                                            </div>
                                            <div class="text-sm text-gray-500">ريال سعودي</div>
                                        </div>
                                        <a href="{{ route('services.show', $service->id) }}" 
                                           class="btn-primary px-6 py-3 font-bold shadow-lg hover:shadow-xl">
                                            عرض التفاصيل
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-center">
                        <div class="bg-white rounded-2xl shadow-lg p-4">
                            {{ $services->appends(request()->query())->links() }}
                        </div>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-20">
                        <div class="text-8xl mb-6">🔍</div>
                        <h3 class="text-3xl font-bold gradient-text mb-4">لا توجد خدمات مطابقة للبحث</h3>
                        <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto leading-relaxed">
                            جرب تغيير معايير البحث أو الفلاتر للعثور على الخدمة المناسبة
                        </p>
                        <div class="space-y-4">
                            <a href="{{ route('services.index') }}" 
                               class="btn-primary px-8 py-4 text-lg font-bold inline-block">
                                عرض جميع الخدمات
                            </a>
                            <div class="text-sm text-gray-500">
                                أو تواصل معنا للمساعدة في العثور على ما تبحث عنه
                            </div>
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </div>
</section>
@endsection
