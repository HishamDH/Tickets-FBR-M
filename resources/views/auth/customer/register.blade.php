<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="text-2xl font-bold text-blue-600">Create Customer Account</h2>
        <p class="text-sm text-gray-600">Join us to book amazing services</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" class="block mt-1 w-full border-blue-300 focus:border-blue-500 focus:ring-blue-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" class="block mt-1 w-full border-blue-300 focus:border-blue-500 focus:ring-blue-500" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone Number')" />
            <x-text-input id="phone" class="block mt-1 w-full border-blue-300 focus:border-blue-500 focus:ring-blue-500" type="tel" name="phone" :value="old('phone')" required />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full border-blue-300 focus:border-blue-500 focus:ring-blue-500"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full border-blue-300 focus:border-blue-500 focus:ring-blue-500"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Terms Agreement -->
        <div class="mt-4">
            <label for="terms" class="inline-flex items-start">
                <input id="terms" type="checkbox" class="rounded border-blue-300 text-blue-600 shadow-sm focus:ring-blue-500" name="terms" required>
                <span class="ms-2 text-sm text-gray-600">
                    I agree to the 
                    <a href="#" class="text-blue-600 hover:text-blue-900 underline">Terms of Service</a>
                    and 
                    <a href="#" class="text-blue-600 hover:text-blue-900 underline">Privacy Policy</a>
                </span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-6">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                {{ __('Create Account') }}
            </button>
        </div>

        <div class="mt-4 text-center">
            <div class="flex justify-between text-sm">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">
                    ← Back to Homepage
                </a>
                <a href="{{ route('customer.login') }}" class="text-blue-600 hover:text-blue-900">
                    Already have an account?
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>