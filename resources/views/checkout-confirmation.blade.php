<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Confirmation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 text-center">
                    <h3 class="text-2xl font-bold text-green-600 mb-4">Thank you for your order!</h3>
                    <p class="text-gray-700 mb-2">Your reservation has been placed successfully.</p>
                    <p class="text-gray-700 mb-6">Your reservation code is: <span class="font-mono bg-gray-200 px-2 py-1 rounded">{{ $reservation->code }}</span></p>
                    
                    <div class="text-left my-8 p-6 border rounded-lg">
                        <h4 class="text-xl font-semibold mb-4">Order Summary</h4>
                        @if(isset($reservation->additional_data['items']))
                            @foreach($reservation->additional_data['items'] as $item)
                                <div class="flex justify-between items-center py-2 border-b">
                                    <div>
                                        <p class="font-medium">{{ $item['name'] }}</p>
                                        <p class="text-sm text-gray-600">Quantity: {{ $item['quantity'] }}</p>
                                    </div>
                                    <p class="font-medium">${{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                                </div>
                            @endforeach
                        @endif
                        <div class="flex justify-between items-center font-bold text-lg mt-4">
                            <p>Total</p>
                            <p>${{ number_format($reservation->price, 2) }}</p>
                        </div>
                    </div>

                    <a href="{{ dashboard_route() }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
