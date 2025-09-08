@extends('layouts.app')

@section('title', 'الملف الشخصي')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-purple-100" dir="rtl">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white py-16 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 right-10 w-32 h-32 bg-white rounded-full animate-pulse"></div>
            <div class="absolute bottom-10 left-10 w-24 h-24 bg-white rounded-full animate-pulse" style="animation-delay: 1s;"></div>
        </div>
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">👤 الملف الشخصي</h1>
                <p class="text-xl opacity-90 max-w-2xl mx-auto">إدارة معلوماتك الشخصية وتفضيلاتك</p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-6 py-8">
        @auth('customer')
            <div class="max-w-4xl mx-auto">
                <!-- Profile Card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-8 text-white">
                        <div class="flex flex-col md:flex-row items-center gap-6">
                            <!-- Avatar -->
                            <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center text-4xl">
                                @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full rounded-full object-cover">
                                @else
                                    👤
                                @endif
                            </div>
                            
                            <!-- User Info -->
                            <div class="text-center md:text-right">
                                <h2 class="text-3xl font-bold mb-2">{{ $user->name }}</h2>
                                <p class="text-xl opacity-90">{{ $user->email }}</p>
                                @if($user->phone)
                                    <p class="text-lg opacity-80 mt-1">{{ $user->phone }}</p>
                                @endif
                                <div class="mt-3">
                                    <span class="bg-white/20 px-3 py-1 rounded-full text-sm">
                                        عضو منذ {{ $user->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profile Details -->
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Personal Information -->
                            <div class="space-y-4">
                                <h3 class="text-xl font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">المعلومات الشخصية</h3>
                                
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <div>
                                        <label class="text-sm text-gray-600">الاسم الكامل</label>
                                        <p class="font-semibold">{{ $user->name }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <div>
                                        <label class="text-sm text-gray-600">البريد الإلكتروني</label>
                                        <p class="font-semibold">{{ $user->email }}</p>
                                        @if($user->email_verified_at)
                                            <span class="text-xs text-green-600">✓ مؤكد</span>
                                        @else
                                            <span class="text-xs text-red-600">✗ غير مؤكد</span>
                                        @endif
                                    </div>
                                </div>

                                @if($user->phone)
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <div>
                                        <label class="text-sm text-gray-600">رقم الهاتف</label>
                                        <p class="font-semibold">{{ $user->phone }}</p>
                                    </div>
                                </div>
                                @endif

                                @if($user->address)
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <div>
                                        <label class="text-sm text-gray-600">العنوان</label>
                                        <p class="font-semibold">{{ $user->address }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Account Statistics -->
                            <div class="space-y-4">
                                <h3 class="text-xl font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">إحصائيات الحساب</h3>
                                
                                @php
                                    $bookingsCount = $user->bookings ? $user->bookings->count() : 0;
                                    $favoritesCount = method_exists($user, 'favoriteServices') ? $user->favoriteServices->count() : 0;
                                    $completedBookings = $user->bookings ? $user->bookings->where('status', 'completed')->count() : 0;
                                @endphp

                                <div class="grid grid-cols-1 gap-4">
                                    <div class="bg-blue-50 p-4 rounded-lg text-center">
                                        <div class="text-3xl text-blue-600 mb-2">{{ $bookingsCount }}</div>
                                        <div class="text-sm text-blue-800 font-semibold">إجمالي الحجوزات</div>
                                    </div>

                                    <div class="bg-green-50 p-4 rounded-lg text-center">
                                        <div class="text-3xl text-green-600 mb-2">{{ $completedBookings }}</div>
                                        <div class="text-sm text-green-800 font-semibold">الحجوزات المكتملة</div>
                                    </div>

                                    <div class="bg-pink-50 p-4 rounded-lg text-center">
                                        <div class="text-3xl text-pink-600 mb-2">{{ $favoritesCount }}</div>
                                        <div class="text-sm text-pink-800 font-semibold">الخدمات المفضلة</div>
                                    </div>

                                    <div class="bg-purple-50 p-4 rounded-lg text-center">
                                        <div class="text-3xl text-purple-600 mb-2">{{ $user->created_at->diffInDays(now()) }}</div>
                                        <div class="text-sm text-purple-800 font-semibold">يوم كعضو</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <h3 class="text-xl font-bold text-gray-800 mb-4">الإجراءات السريعة</h3>
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('customer.profile.edit') }}" 
                                   class="bg-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-purple-700 transition duration-200 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    تعديل الملف الشخصي
                                </a>
                                
                                <a href="{{ route('customer.bookings.index') }}" 
                                   class="bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition duration-200 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    عرض الحجوزات
                                </a>
                                
                                <a href="{{ route('customer.favorites.index') }}" 
                                   class="bg-pink-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-pink-700 transition duration-200 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                                    </svg>
                                    الخدمات المفضلة
                                </a>
                                
                                <a href="{{ route('customer.services.index') }}" 
                                   class="bg-green-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-green-700 transition duration-200 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    تصفح الخدمات
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Not Authenticated -->
            <div class="text-center py-16">
                <div class="text-8xl mb-6">🔒</div>
                <h3 class="text-3xl font-bold text-gray-800 mb-4">تحتاج إلى تسجيل الدخول</h3>
                <p class="text-xl text-gray-600 mb-8">سجل دخولك لعرض ملفك الشخصي</p>
                <a href="{{ route('customer.login') }}" 
                   class="inline-flex items-center bg-purple-600 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:bg-purple-700 transition duration-200">
                    تسجيل الدخول
                </a>
            </div>
        @endauth
    </div>
</div>

@endsection