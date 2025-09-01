<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Tickets Platform') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Custom CSS -->
    <style>
        .brand-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .bg-brand {
            background-color: #667eea;
        }
        .text-brand {
            color: #667eea;
        }
        .border-brand {
            border-color: #667eea;
        }
        .hover\:bg-brand:hover {
            background-color: #5a6fd8;
        }
        .btn-brand {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-brand:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <a href="{{ route('home') }}" class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 brand-gradient rounded-lg flex items-center justify-center mr-3">
                                <span class="text-white font-bold text-xl">🎟️</span>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-800">{{ config('app.name') }}</h1>
                            <p class="text-xs text-gray-500">Dashboard</p>
                        </div>
                    </a>
                </div>
                
                <!-- Right Side -->
                <div class="flex items-center space-x-4">
                    @auth
                        <div class="flex items-center space-x-2">
                            <span class="text-gray-600">Welcome, {{ auth()->user()->name }}</span>
                            
                            <a href="{{ route('home') }}" class="text-gray-600 hover:text-brand px-3 py-2 rounded-md text-sm font-medium transition">
                                🏠 Home
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-500 hover:text-gray-700 transition">
                                    🚪 Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('home') }}" class="text-gray-600 hover:text-brand px-3 py-2 rounded-md text-sm font-medium transition">
                            🏠 Home
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Heading -->
    @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    <!-- Main Content -->
    <main>
        @if(isset($slot))
            {{ $slot }}
        @else
            @yield('content')
        @endif
    </main>

    @livewireScripts
</body>
</html>
