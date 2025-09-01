<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="text-2xl font-bold text-orange-600">Merchant Portal</h2>
        <p class="text-sm text-gray-600">Access your business dashboard</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('merchant.login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Business Email')" />
            <x-text-input id="email" class="block mt-1 w-full border-orange-300 focus:border-orange-500 focus:ring-orange-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full border-orange-300 focus:border-orange-500 focus:ring-orange-500"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-orange-300 text-orange-600 shadow-sm focus:ring-orange-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                {{ __('Sign In to Dashboard') }}
            </button>
        </div>

        <div class="mt-4 text-center">
            <div class="flex justify-between text-sm">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">
                    ← Back to Homepage
                </a>
                <a href="#" class="text-orange-600 hover:text-orange-900">
                    Need Help?
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>