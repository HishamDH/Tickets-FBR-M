<?php

namespace App\Livewire;

use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CartComponent extends Component
{
    public $cartItems;

    public float $total = 0;

    protected CartService $cartService;

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $user = Auth::user();
        if ($user) {
            $this->cartItems = $this->cartService->getCart($user);
            $this->total = $this->cartService->getTotal($user);
        } else {
            $this->cartItems = collect();
            $this->total = 0;
        }
    }

    public function removeFromCart($cartItemId)
    {
        $this->cartService->removeItem($cartItemId);
        $this->loadCart();
        $this->dispatch('cart-updated'); // Dispatch event for other components
    }

    public function clearCart()
    {
        $user = Auth::user();
        if ($user) {
            $this->cartService->clearCart($user);
            $this->loadCart();
            $this->dispatch('cart-updated');
        }
    }

    public function render()
    {
        return view('livewire.cart-component');
    }
}
