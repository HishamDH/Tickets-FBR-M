<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة تحكم الإدارة') - شباك التذاكر</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Admin Navigation -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left side - Logo & Nav -->
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center ml-3">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900">شباك التذاكر</span>
                        <span class="mr-3 px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">إدارة</span>
                    </div>
                    
                    <!-- Navigation Links -->
                    <div class="hidden md:flex mr-8 space-x-reverse space-x-8">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'text-red-600 bg-red-50' : '' }}">
                            <i class="fas fa-tachometer-alt ml-2"></i>
                            لوحة التحكم
                        </a>
                        <a href="{{ route('admin.users.pending') }}" 
                           class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.users.pending') ? 'text-red-600 bg-red-50' : '' }}">
                            <i class="fas fa-user-clock ml-2"></i>
                            التفعيلات المنتظرة
                            @php
                                $pendingCount = \App\Models\User::where('user_type', 'merchant')
                                    ->where('merchant_status', 'pending')
                                    ->count() + 
                                \App\Models\User::where('user_type', 'partner')
                                    ->where('partner_status', 'pending')
                                    ->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="mr-1 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $pendingCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.merchants-report') }}" 
                           class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-store ml-2"></i>
                            إدارة التجار
                        </a>
                        <a href="{{ route('admin.partners-report') }}" 
                           class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-handshake ml-2"></i>
                            إدارة الشركاء
                        </a>
                        <a href="{{ route('admin.revenue-report') }}" 
                           class="text-gray-700 hover:text-red-600 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-chart-line ml-2"></i>
                            التقارير
                        </a>
                    </div>
                </div>
                
                <!-- Right side - User Menu -->
                <div class="flex items-center">
                    <!-- Admin Info -->
                    <div class="flex items-center ml-4">
                        <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="mr-3">
                            <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-gray-500">مدير النظام</div>
                        </div>
                    </div>
                    
                    <!-- Dropdown Menu -->
                    <div class="relative mr-3" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-cog w-5 h-5"></i>
                        </button>
                        
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="origin-top-right absolute left-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                            
                            <a href="{{ route('profile.edit') }}" 
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user-edit ml-3 text-gray-400"></i>
                                الملف الشخصي
                            </a>
                            
                            <a href="{{ route('home') }}" 
                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-home ml-3 text-gray-400"></i>
                                العودة للموقع
                            </a>
                            
                            <hr class="my-1">
                            
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt ml-3 text-red-500"></i>
                                    تسجيل الخروج
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div class="md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="{{ route('admin.dashboard') }}" 
                   class="text-gray-700 hover:text-red-600 block px-3 py-2 rounded-md text-base font-medium">
                    لوحة التحكم
                </a>
                <a href="{{ route('admin.users.pending') }}" 
                   class="text-gray-700 hover:text-red-600 block px-3 py-2 rounded-md text-base font-medium">
                    التفعيلات المنتظرة
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Page Content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
