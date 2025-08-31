<div>
    <button wire:click="addToCart" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Add to Cart
    </button>

    @if (session()->has('message'))
        <div class="mt-2 text-green-600">
            {{ session('message') }}
        </div>
    @endif
</div>