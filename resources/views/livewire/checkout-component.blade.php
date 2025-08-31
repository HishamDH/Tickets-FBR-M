<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-8">Checkout</h1>

    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p class="font-bold">Error</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Customer Information Form -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Your Details</h2>
            <form wire:submit.prevent="placeOrder" class="bg-white p-6 rounded-lg shadow-md">
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" id="name" wire:model.defer="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" id="email" wire:model.defer="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="tel" id="phone" wire:model.defer="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg">
                        Place Order & Proceed to Payment
                    </button>
                </div>
            </form>
        </div>

        <!-- Order Summary -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="space-y-4">
                    @foreach($cartItems as $item)
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium">{{ $item->offering->name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                            </div>
                            <p class="font-medium">${{ number_format(($item->offering->price ?? 0) * $item->quantity, 2) }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-gray-200 mt-4 pt-4">
                    <div class="flex justify-between items-center font-bold text-lg">
                        <p>Total</p>
                        <p>${{ number_format($total, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>