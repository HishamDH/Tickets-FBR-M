<?php

namespace App\Livewire;

use App\Models\Offering;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AddToCartButton extends Component
{
    public Offering $offering;

    public function mount(Offering $offering)
    {
        $this->offering = $offering;
    }

    public function addToCart(CartService $cartService)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->redirect(route('login'));
        }

        $cartService->addItem($user, $this->offering);

        $this->dispatch('cart-updated');
        
        // Optionally, add a success message
        session()->flash('message', 'Item added to cart successfully!');
    }

    public function render()
    {
        return view('livewire.add-to-cart-button');
    }
}