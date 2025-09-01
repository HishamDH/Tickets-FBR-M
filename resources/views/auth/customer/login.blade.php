<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="text-2xl font-bold text-blue-600">Customer Login</h2>
        <p class="text-sm text-gray-600">Access your bookings and account</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('customer.login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" class="block mt-1 w-full border-blue-300 focus:border-blue-500 focus:ring-blue-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full border-blue-300 focus:border-blue-500 focus:ring-blue-500"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-blue-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                {{ __('Sign In') }}
            </button>
        </div>

        <div class="mt-4 text-center">
            <div class="flex justify-between text-sm">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">
                    ← Back to Homepage
                </a>
                <a href="{{ route('customer.register') }}" class="text-blue-600 hover:text-blue-900">
                    Create Account
                </a>
            </div>
        </div>

        <div class="mt-4 text-center border-t pt-4">
            <p class="text-xs text-gray-500">
                Don't have an account? 
                <a href="{{ route('customer.register') }}" class="text-blue-600 hover:text-blue-900 font-semibold">
                    Sign up now
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>