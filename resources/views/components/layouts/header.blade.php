<header class="bg-white shadow-md" x-data="{ open: false }">
    <nav class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="text-2xl font-bold text-gray-800">
                <a href="/">Shubak Tickets</a>
            </div>
            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ route('services.index') }}" class="text-gray-600 hover:text-orange-500">Services</a>
                @auth
                    <a href="{{ dashboard_route() }}" class="text-gray-600 hover:text-orange-500">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); this.closest('form').submit();" 
                           class="text-gray-600 hover:text-orange-500">Logout</a>
                    </form>
                @else
                    <a href="{{ route('customer.login') }}" class="bg-orange-500 text-white px-4 py-2 rounded-full hover:bg-orange-600">Login</a>
                @endauth
            </div>
            <div class="md:hidden flex items-center">
                <button @click="open = !open" class="text-gray-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div x-show="open" @click.away="open = false" class="md:hidden mt-4">
            <a href="{{ route('services.index') }}" class="block py-2 px-4 text-sm text-gray-600 hover:bg-gray-100">Services</a>
            @auth
                <a href="{{ dashboard_route() }}" class="block py-2 px-4 text-sm text-gray-600 hover:bg-gray-100">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" 
                       onclick="event.preventDefault(); this.closest('form').submit();" 
                       class="block py-2 px-4 text-sm text-gray-600 hover:bg-gray-100">Logout</a>
                </form>
            @else
                <a href="{{ route('customer.login') }}" class="block py-2 px-4 text-sm text-white bg-orange-500 rounded-md hover:bg-orange-600">Login</a>
            @endauth
        </div>
    </nav>
</header>