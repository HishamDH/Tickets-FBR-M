<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <!-- Page Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-2">استكشف خدماتنا</h1>
                        <p class="text-gray-600 text-lg">{{ $services->total() }} خدمة متاحة في جميع أنحاء المملكة</p>
                    </div>
                    <div class="mt-4 lg:mt-0">
                        <div class="flex items-center space-x-reverse space-x-4">
                            <span class="text-sm text-gray-500">مرتبة حسب الأحدث</span>
                            <div class="flex space-x-reverse space-x-2">
                                <button class="p-2 text-gray-400 hover:text-gray-600 border rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                    </svg>
                                </button>
                                <button class="p-2 text-gray-400 hover:text-gray-600 border rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Search and Filters Bar -->
            <div class="bg-white rounded-xl shadow-sm border p-6 lg:p-8 mb-8">
                <form method="GET" action="{{ route('services.index') }}" class="space-y-6 lg:space-y-0">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <!-- Search Input -->
                        <div class="lg:col-span-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">البحث في الخدمات</label>
                            <div class="relative">
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="ابحث عن اسم الخدمة أو الموقع..."
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-lg">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Category Filter -->
                        <div class="lg:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
                            <select name="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-lg">
                                <option value="">جميع الفئات</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="lg:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">نطاق السعر (ريال)</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" 
                                       name="min_price" 
                                       value="{{ request('min_price') }}"
                                       placeholder="من"
                                       class="px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                                <input type="number" 
                                       name="max_price" 
                                       value="{{ request('max_price') }}"
                                       placeholder="إلى"
                                       class="px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="lg:col-span-2 flex flex-col justify-end">
                            <div class="flex space-x-reverse space-x-3">
                                <button type="submit" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-300 shadow-sm hover:shadow-md">
                                    بحث
                                </button>
                                @if(request()->hasAny(['search', 'category', 'min_price', 'max_price']))
                                    <a href="{{ route('services.index') }}" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-300 text-center">
                                        مسح
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="flex flex-col xl:flex-row gap-8">
                {{-- Quick Filters Sidebar --}}
                <aside class="w-full xl:w-1/4">
                    <div class="bg-white rounded-xl shadow-sm border p-6 sticky top-6">
                        <h3 class="font-bold text-xl mb-6 text-gray-900">فئات الخدمات</h3>

                        <ul class="space-y-3">
                            <li>
                                <a href="{{ route('services.index') }}" 
                                   class="block px-4 py-3 rounded-lg transition duration-300 {{ !request('category') ? 'bg-orange-500 text-white font-semibold shadow-sm' : 'text-gray-600 hover:bg-orange-50 hover:text-orange-600' }}">
                                    <div class="flex justify-between items-center">
                                        <span>جميع الخدمات</span>
                                        <span class="text-sm {{ !request('category') ? 'text-orange-100' : 'text-gray-400' }}">{{ $services->total() }}</span>
                                    </div>
                                </a>
                            </li>
                            @foreach($categories as $category)
                                @php
                                    $categoryCount = \App\Models\Service::where('category', $category)->where('is_active', true)->count();
                                @endphp
                                <li>
                                    <a href="{{ route('services.index', ['category' => $category]) }}" 
                                       class="block px-4 py-3 rounded-lg transition duration-300 {{ request('category') == $category ? 'bg-orange-500 text-white font-semibold shadow-sm' : 'text-gray-600 hover:bg-orange-50 hover:text-orange-600' }}">
                                        <div class="flex justify-between items-center">
                                            <span>{{ $category }}</span>
                                            <span class="text-sm {{ request('category') == $category ? 'text-orange-100' : 'text-gray-400' }}">{{ $categoryCount }}</span>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Price Ranges -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <h4 class="font-bold text-lg mb-4 text-gray-900">نطاقات الأسعار</h4>
                            <ul class="space-y-2">
                                <li><a href="{{ route('services.index', ['max_price' => 1000]) }}" class="text-gray-600 hover:text-orange-600 transition duration-300">أقل من 1,000 ريال</a></li>
                                <li><a href="{{ route('services.index', ['min_price' => 1000, 'max_price' => 5000]) }}" class="text-gray-600 hover:text-orange-600 transition duration-300">1,000 - 5,000 ريال</a></li>
                                <li><a href="{{ route('services.index', ['min_price' => 5000, 'max_price' => 15000]) }}" class="text-gray-600 hover:text-orange-600 transition duration-300">5,000 - 15,000 ريال</a></li>
                                <li><a href="{{ route('services.index', ['min_price' => 15000]) }}" class="text-gray-600 hover:text-orange-600 transition duration-300">أكثر من 15,000 ريال</a></li>
                            </ul>
                        </div>
                    </div>
                </aside>

                {{-- Services Grid --}}
                <main class="w-full xl:w-3/4">
                    @if($services->count() > 0)
                        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 lg:gap-8">
                            @foreach($services as $service)
                                <div class="bg-white rounded-xl shadow-sm border overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 group">
                                    <!-- Service Image -->
                                    <div class="h-56 lg:h-64 bg-gradient-to-br from-orange-100 to-red-100 flex items-center justify-center relative overflow-hidden">
                                        @if($service->is_featured)
                                            <div class="absolute top-4 right-4 bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-semibold shadow-sm">
                                                مميز
                                            </div>
                                        @endif
                                        <div class="text-orange-500 group-hover:text-orange-600 transition-colors duration-300" style="font-size: 4rem;">
                                            @if($service->category == 'Venues')
                                                🏰
                                            @elseif($service->category == 'Catering')
                                                🍽️
                                            @elseif($service->category == 'Photography')
                                                📸
                                            @elseif($service->category == 'Entertainment')
                                                🎵
                                            @elseif($service->category == 'Planning')
                                                📋
                                            @else
                                                ⭐
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Service Content -->
                                    <div class="p-6 lg:p-8">
                                        <div class="flex justify-between items-start mb-3">
                                            <h3 class="font-bold text-xl lg:text-2xl text-gray-900 group-hover:text-orange-600 transition-colors duration-300">{{ $service->name }}</h3>
                                            <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap mr-3">
                                                {{ $service->category }}
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center text-gray-500 mb-4">
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span class="text-sm">{{ $service->location }}</span>
                                        </div>
                                        
                                        <p class="text-gray-600 mb-6 leading-relaxed line-clamp-3">
                                            {{ Str::limit($service->description, 120) }}
                                        </p>
                                        
                                        <div class="flex justify-between items-center">
                                            <div class="font-bold text-orange-500 text-xl lg:text-2xl">
                                                {{ $service->price_formatted }}
                                            </div>
                                            <a href="{{ route('services.show', $service->id) }}" 
                                               class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition duration-300 shadow-sm hover:shadow-md">
                                                عرض التفاصيل
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-12">
                            {{ $services->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-16 lg:py-24">
                            <div class="text-gray-300 text-8xl lg:text-9xl mb-6">🔍</div>
                            <h3 class="text-2xl lg:text-3xl font-bold text-gray-600 mb-4">لا توجد خدمات مطابقة للبحث</h3>
                            <p class="text-lg text-gray-500 mb-8 max-w-md mx-auto">جرب تغيير معايير البحث أو الفلاتر للعثور على الخدمة المناسبة</p>
                            <a href="{{ route('services.index') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-4 rounded-lg font-semibold transition duration-300 text-lg">
                                عرض جميع الخدمات
                            </a>
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </div>
</x-app-layout>
