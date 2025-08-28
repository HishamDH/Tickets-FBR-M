<x-app-layout>
    <div class="min-h-screen bg-gray-50">
        <!-- Breadcrumb -->
        <div class="bg-white border-b shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 lg:py-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3 space-x-reverse">
                        <li class="inline-flex items-center">
                            <a href="{{ route('welcome') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-orange-600 transition duration-300">
                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                الرئيسية
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400 rotate-180" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <a href="{{ route('services.index') }}" class="mr-1 text-sm font-medium text-gray-700 hover:text-orange-600 transition duration-300 md:mr-2">الخدمات</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400 rotate-180" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="mr-1 text-sm font-medium text-gray-500 md:mr-2">{{ $service->name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 lg:gap-12">
                <!-- Main Content -->
                <div class="xl:col-span-2">
                    <!-- Service Header -->
                    <div class="bg-white rounded-2xl shadow-sm border overflow-hidden mb-8">
                        <!-- Service Image -->
                        <div class="h-64 lg:h-80 bg-gradient-to-br from-orange-100 to-red-100 flex items-center justify-center relative">
                            @if($service->is_featured)
                                <div class="absolute top-6 right-6 bg-orange-500 text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg">
                                    ⭐ خدمة مميزة
                                </div>
                            @endif
                            @if($service->is_available)
                                <div class="absolute top-6 left-6 bg-green-500 text-white px-4 py-2 rounded-full text-sm font-bold shadow-lg">
                                    ✅ متاح الآن
                                </div>
                            @endif
                            <div class="text-orange-500 group-hover:text-orange-600 transition-colors duration-300" style="font-size: 6rem;">
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

                        <!-- Service Details -->
                        <div class="p-6 lg:p-10">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between mb-6">
                                <div class="mb-4 lg:mb-0">
                                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-3">{{ $service->name }}</h1>
                                    <div class="flex flex-wrap items-center gap-4">
                                        <span class="bg-orange-100 text-orange-600 px-4 py-2 rounded-full text-sm font-semibold">
                                            {{ $service->category }}
                                        </span>
                                        <div class="flex items-center text-gray-600">
                                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span class="font-medium">{{ $service->location }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-4xl lg:text-5xl font-bold text-orange-500 mb-2">
                                        {{ $service->price_formatted }}
                                    </div>
                                    <p class="text-gray-500 text-lg">شامل الضريبة</p>
                                </div>
                            </div>

                            <!-- Service Description -->
                            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">وصف الخدمة</h3>
                                <p class="text-lg leading-8">{{ $service->description }}</p>
                            </div>

                            <!-- Service Features -->
                            <div class="mt-8 pt-8 border-t border-gray-200">
                                <h3 class="text-2xl font-bold text-gray-900 mb-6">مميزات الخدمة</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @if($service->is_featured)
                                        <div class="flex items-center text-orange-600">
                                            <svg class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                            </svg>
                                            <span class="font-semibold text-lg">خدمة مميزة</span>
                                        </div>
                                    @endif
                                    @if($service->is_available)
                                        <div class="flex items-center text-green-600">
                                            <svg class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="font-semibold text-lg">متاح للحجز فوراً</span>
                                        </div>
                                    @endif
                                    <div class="flex items-center text-blue-600">
                                        <svg class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                        <span class="font-semibold text-lg">ضمان الجودة</span>
                                    </div>
                                    <div class="flex items-center text-purple-600">
                                        <svg class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        <span class="font-semibold text-lg">دعم فني على مدار الساعة</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="xl:col-span-1">
                    <!-- Booking Card -->
                    <div class="bg-white rounded-2xl shadow-sm border p-6 lg:p-8 mb-8 sticky top-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">احجز الآن</h3>
                        
                        <div class="mb-6 p-4 bg-orange-50 rounded-xl border border-orange-200">
                            <div class="flex justify-between items-center text-lg">
                                <span class="text-gray-700 font-medium">السعر الإجمالي:</span>
                                <span class="text-2xl font-bold text-orange-500">{{ $service->price_formatted }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">* شامل الضريبة والرسوم</p>
                        </div>

                        <!-- Contact Form -->
                        <form action="{{ route('booking.create', $service->id) }}" method="GET" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الحدث المطلوب</label>
                                <input type="date" 
                                       name="event_date" 
                                       required 
                                       min="{{ date('Y-m-d') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-lg">
                            </div>

                            @if($service->price_type == 'per_person')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">عدد الأشخاص</label>
                                    <input type="number" 
                                           name="guests" 
                                           min="1" 
                                           value="50"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-lg">
                                </div>
                            @endif
                            
                            <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-4 px-6 rounded-lg font-bold text-lg transition duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                🎯 احجز الآن - {{ $service->price_formatted }}
                            </button>
                        </form>

                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-center text-sm text-gray-600">
                                <svg class="w-5 h-5 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium">إلغاء مجاني خلال 24 ساعة</span>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <h4 class="font-bold text-lg mb-4 text-gray-900">تحتاج مساعدة؟</h4>
                            <div class="space-y-3">
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 ml-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span class="font-medium">+966 50 123 4567</span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 ml-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="font-medium">info@shubaktickets.com</span>
                                </div>
                                <button class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 px-4 rounded-lg font-semibold transition duration-300 mt-4">
                                    💬 تواصل معنا مباشرة
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Services -->
            @if($relatedServices->count() > 0)
                <div class="mt-16">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">خدمات مشابهة</h2>
                        <p class="text-lg text-gray-600 max-w-2xl mx-auto">اكتشف المزيد من الخدمات المميزة في نفس الفئة</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                        @foreach($relatedServices as $relatedService)
                            <div class="bg-white rounded-xl shadow-sm border overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 group">
                                <!-- Service Image -->
                                <div class="h-48 lg:h-56 bg-gradient-to-br from-orange-100 to-red-100 flex items-center justify-center relative">
                                    @if($relatedService->is_featured)
                                        <div class="absolute top-4 right-4 bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                            مميز
                                        </div>
                                    @endif
                                    <div class="text-orange-500 group-hover:text-orange-600 transition-colors duration-300" style="font-size: 3.5rem;">
                                        @if($relatedService->category == 'Venues')
                                            🏰
                                        @elseif($relatedService->category == 'Catering')
                                            🍽️
                                        @elseif($relatedService->category == 'Photography')
                                            📸
                                        @elseif($relatedService->category == 'Entertainment')
                                            🎵
                                        @elseif($relatedService->category == 'Planning')
                                            📋
                                        @else
                                            ⭐
                                        @endif
                                    </div>
                                </div>

                                <!-- Service Content -->
                                <div class="p-6">
                                    <h3 class="font-bold text-xl text-gray-900 mb-2 group-hover:text-orange-600 transition-colors duration-300">{{ $relatedService->name }}</h3>
                                    
                                    <div class="flex items-center text-gray-500 mb-3">
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="text-sm">{{ $relatedService->location }}</span>
                                    </div>
                                    
                                    <p class="text-gray-600 mb-4 line-clamp-2">
                                        {{ Str::limit($relatedService->description, 80) }}
                                    </p>
                                    
                                    <div class="flex justify-between items-center">
                                        <div class="font-bold text-orange-500 text-lg">
                                            {{ $relatedService->price_formatted }}
                                        </div>
                                        <a href="{{ route('services.show', $relatedService->id) }}" 
                                           class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-lg font-semibold transition duration-300 text-sm">
                                            عرض التفاصيل
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
