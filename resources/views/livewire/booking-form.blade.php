<div>
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-3xl mx-auto">

        {{-- Progress Bar --}}
        <div class="mb-8">
            <div class="flex items-center">
                <div class="w-1/3 text-center">
                    <div class="{{ $step >= 1 ? 'bg-orange-500 text-white' : 'bg-gray-200' }} rounded-full h-8 w-8 flex items-center justify-center mx-auto">1</div>
                    <p class="mt-2 text-sm {{ $step >= 1 ? 'text-gray-800' : 'text-gray-500' }}">Details</p>
                </div>
                <div class="w-1/3 border-t-2 {{ $step >= 2 ? 'border-orange-500' : 'border-gray-200' }}"></div>
                <div class="w-1/3 text-center">
                    <div class="{{ $step >= 2 ? 'bg-orange-500 text-white' : 'bg-gray-200' }} rounded-full h-8 w-8 flex items-center justify-center mx-auto">2</div>
                    <p class="mt-2 text-sm {{ $step >= 2 ? 'text-gray-800' : 'text-gray-500' }}">Your Info</p>
                </div>
                <div class="w-1/3 border-t-2 {{ $step >= 3 ? 'border-orange-500' : 'border-gray-200' }}"></div>
                <div class="w-1/3 text-center">
                    <div class="{{ $step >= 3 ? 'bg-orange-500 text-white' : 'bg-gray-200' }} rounded-full h-8 w-8 flex items-center justify-center mx-auto">3</div>
                    <p class="mt-2 text-sm {{ $step >= 3 ? 'text-gray-800' : 'text-gray-500' }}">Confirm</p>
                </div>
            </div>
        </div>

        {{-- Step 1: Details --}}
        <div x-data="{}" x-show="{{ $step == 1 }}">
            <h2 class="text-2xl font-bold mb-6">Booking Details</h2>
            <div class="mb-4">
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" id="date" wire:model="date" class="w-full p-2 border border-gray-300 rounded-md">
            </div>
            <div class="mb-4">
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity / Guests</label>
                <input type="number" id="quantity" wire:model="quantity" min="1" class="w-full p-2 border border-gray-300 rounded-md">
            </div>
            <button wire:click="nextStep" class="w-full bg-orange-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-orange-600">Next</button>
        </div>

        {{-- Step 2: Your Info --}}
        <div x-data="{}" x-show="{{ $step == 2 }}">
            <h2 class="text-2xl font-bold mb-6">Your Information</h2>
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" id="name" wire:model="name" class="w-full p-2 border border-gray-300 rounded-md">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" id="email" wire:model="email" class="w-full p-2 border border-gray-300 rounded-md">
            </div>
            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="tel" id="phone" wire:model="phone" class="w-full p-2 border border-gray-300 rounded-md">
            </div>
            <div class="flex justify-between">
                <button wire:click="previousStep" class="bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded-lg hover:bg-gray-400">Back</button>
                <button wire:click="nextStep" class="bg-orange-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-orange-600">Next</button>
            </div>
        </div>

        {{-- Step 3: Confirmation --}}
        <div x-data="{}" x-show="{{ $step == 3 }}">
            <h2 class="text-2xl font-bold mb-6">Confirm Booking</h2>
            <div class="bg-gray-100 p-4 rounded-lg mb-6">
                <p><strong>Service ID:</strong> {{ $service_id }}</p>
                <p><strong>Date:</strong> {{ $date }}</p>
                <p><strong>Guests:</strong> {{ $quantity }}</p>
                <hr class="my-2">
                <p><strong>Name:</strong> {{ $name }}</p>
                <p><strong>Email:</strong> {{ $email }}</p>
                <p><strong>Phone:</strong> {{ $phone }}</p>
            </div>
            <div class="flex justify-between">
                <button wire:click="previousStep" class="bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded-lg hover:bg-gray-400">Back</button>
                <button wire:click="submit" class="bg-green-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-green-600">Confirm & Pay</button>
            </div>
        </div>

        {{-- Success Message --}}
        <div x-data="{}" x-show="{{ $step == 'success' }}">
            <h2 class="text-2xl font-bold mb-6 text-center text-green-500">Booking Confirmed!</h2>
            <p class="text-center">Thank you for your booking. A confirmation has been sent to your email.</p>
            <div class="text-center mt-6">
                <a href="/" class="bg-orange-500 text-white font-bold py-3 px-6 rounded-lg hover:bg-orange-600">Back to Home</a>
            </div>
        </div>

    </div>
</div>