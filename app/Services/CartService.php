<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Offering;
use App\Models\User;
use Illuminate\Support\Collection;

class CartService
{
    /**
     * Get the user's cart contents.
     */
    public function getCart(User $user): Collection
    {
        return $user->carts()->with('offering')->get();
    }

    /**
     * Add an item to the user's cart.
     * If the item already exists, update the quantity.
     */
    public function addItem(User $user, Offering $offering, int $quantity = 1): Cart
    {
        $cartItem = $user->carts()->where('offering_id', $offering->id)->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);

            return $cartItem->refresh();
        }

        return $user->carts()->create([
            'offering_id' => $offering->id,
            'quantity' => $quantity,
        ]);
    }

    /**
     * Remove an item from the user's cart.
     */
    public function removeItem(int $cartItemId): void
    {
        Cart::destroy($cartItemId);
    }

    /**
     * Clear all items from the user's cart.
     */
    public function clearCart(User $user): void
    {
        $user->carts()->delete();
    }

    /**
     * Get the total price of the items in the cart.
     */
    public function getTotal(User $user): float
    {
        return $user->carts->reduce(function ($total, $cartItem) {
            // Ensure offering is loaded and has a price
            if ($cartItem->offering && isset($cartItem->offering->price)) {
                return $total + ($cartItem->offering->price * $cartItem->quantity);
            }

            return $total;
        }, 0);
    }
}
