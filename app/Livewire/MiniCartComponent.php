<?php

namespace App\Livewire;

use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MiniCartComponent extends Component
{
    public int $itemCount = 0;

    protected $listeners = ['cart-updated' => 'updateCartCount'];

    public function mount(CartService $cartService)
    {
        $this->updateCartCount($cartService);
    }

    public function updateCartCount(CartService $cartService)
    {
        $user = Auth::user();
        if ($user) {
            $this->itemCount = $cartService->getCart($user)->sum('quantity');
        } else {
            $this->itemCount = 0;
        }
    }

    public function render()
    {
        return view('livewire.mini-cart-component');
    }
}
