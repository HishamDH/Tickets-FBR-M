<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Your Shopping Cart</h1>

    @if($cartItems && $cartItems->isNotEmpty())
        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Item
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Price
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantity
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Subtotal
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Remove</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($cartItems as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->offering->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">${{ number_format($item->offering->price ?? 0, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $item->quantity }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">${{ number_format(($item->offering->price ?? 0) * $item->quantity, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button wire:click="removeFromCart({{ $item->id }})" class="text-red-600 hover:text-red-900">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-between items-center">
            <div>
                <button wire:click="clearCart" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Clear Cart
                </button>
            </div>
            <div class="text-right">
                <p class="text-xl font-bold">Total: ${{ number_format($total, 2) }}</p>
                <button class="mt-2 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Proceed to Checkout
                </button>
            </div>
        </div>

    @else
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
            <p class="font-bold">Your cart is empty.</p>
            <p>Looks like you haven't added any items to your cart yet.</p>
        </div>
    @endif
</div>