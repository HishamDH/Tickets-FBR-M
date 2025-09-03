<?php

namespace App\Livewire\Cart;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class CartComponent extends Component
{
    public $cartItems = [];

    public $subtotal = 0;

    public $discount = 0;

    public $total = 0;

    public $count = 0;

    public $showCart = false;

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $userId = Auth::id();
        $sessionId = session()->getId();

        $cartData = Cart::getCartTotal($userId, $sessionId);

        $this->cartItems = $cartData['items'];
        $this->subtotal = $cartData['subtotal'];
        $this->discount = $cartData['discount'];
        $this->total = $cartData['total'];
        $this->count = $cartData['count'];
    }

    public function addToCart($itemId, $itemType, $quantity = 1, $additionalData = [])
    {
        try {
            $userId = Auth::id();
            $sessionId = session()->getId();

            // Validate item exists
            $item = $itemType::find($itemId);
            if (! $item) {
                $this->dispatch('cart-error', message: 'Item not found');

                return;
            }

            // Check availability
            if (method_exists($item, 'isAvailable') && ! $item->isAvailable($quantity)) {
                $this->dispatch('cart-error', message: 'Item is not available');

                return;
            }

            $price = $item->price ?? $item->amount ?? 0;

            // Add to cart
            Cart::addItem($userId, $sessionId, $itemId, $itemType, $quantity, $price, $additionalData);

            // Reload cart
            $this->loadCart();

            // Dispatch success event
            $this->dispatch('cart-updated', count: $this->count);
            $this->dispatch('cart-success', message: 'Item added to cart!');

        } catch (\Exception $e) {
            $this->dispatch('cart-error', message: 'Failed to add item to cart');
        }
    }

    public function updateQuantity($cartItemId, $quantity)
    {
        if ($quantity < 1) {
            $this->removeItem($cartItemId);

            return;
        }

        try {
            $userId = Auth::id();
            $sessionId = session()->getId();

            $cartItem = Cart::where('id', $cartItemId)
                ->where(function ($query) use ($userId, $sessionId) {
                    if ($userId) {
                        $query->where('user_id', $userId);
                    } else {
                        $query->where('session_id', $sessionId)->whereNull('user_id');
                    }
                })
                ->first();

            if (! $cartItem) {
                $this->dispatch('cart-error', message: 'Cart item not found');

                return;
            }

            // Check availability
            if ($cartItem->item && method_exists($cartItem->item, 'isAvailable') && ! $cartItem->item->isAvailable($quantity)) {
                $this->dispatch('cart-error', message: 'Requested quantity is not available');

                return;
            }

            $cartItem->update(['quantity' => $quantity]);
            $this->loadCart();

            $this->dispatch('cart-updated', count: $this->count);

        } catch (\Exception $e) {
            $this->dispatch('cart-error', message: 'Failed to update cart');
        }
    }

    public function removeItem($cartItemId)
    {
        try {
            $userId = Auth::id();
            $sessionId = session()->getId();

            $deleted = Cart::where('id', $cartItemId)
                ->where(function ($query) use ($userId, $sessionId) {
                    if ($userId) {
                        $query->where('user_id', $userId);
                    } else {
                        $query->where('session_id', $sessionId)->whereNull('user_id');
                    }
                })
                ->delete();

            if ($deleted) {
                $this->loadCart();
                $this->dispatch('cart-updated', count: $this->count);
                $this->dispatch('cart-success', message: 'Item removed from cart');
            }

        } catch (\Exception $e) {
            $this->dispatch('cart-error', message: 'Failed to remove item');
        }
    }

    public function clearCart()
    {
        try {
            $userId = Auth::id();
            $sessionId = session()->getId();

            Cart::clearCart($userId, $sessionId);
            $this->loadCart();

            $this->dispatch('cart-updated', count: $this->count);
            $this->dispatch('cart-success', message: 'Cart cleared');

        } catch (\Exception $e) {
            $this->dispatch('cart-error', message: 'Failed to clear cart');
        }
    }

    public function toggleCart()
    {
        $this->showCart = ! $this->showCart;
    }

    #[On('cart-refresh')]
    public function refreshCart()
    {
        $this->loadCart();
    }

    #[On('user-logged-in')]
    public function mergeGuestCart()
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $sessionId = session()->getId();

            Cart::mergeGuestCart($sessionId, $userId);
            $this->loadCart();

            $this->dispatch('cart-updated', count: $this->count);
        }
    }

    public function checkout()
    {
        if ($this->count === 0) {
            $this->dispatch('cart-error', message: 'Cart is empty');

            return;
        }

        if (! Auth::check()) {
            $this->dispatch('show-login-modal');

            return;
        }

        // Validate cart before checkout
        $issues = [];
        foreach ($this->cartItems as $cartItem) {
            if (! $cartItem->isAvailable()) {
                $issues[] = "Item '{$cartItem->getItemName()}' is no longer available";
            }
        }

        if (! empty($issues)) {
            $this->dispatch('cart-error', message: implode(', ', $issues));

            return;
        }

        // Redirect to checkout
        return redirect()->route('checkout.index');
    }

    public function render()
    {
        return view('livewire.cart.cart-component');
    }
}
