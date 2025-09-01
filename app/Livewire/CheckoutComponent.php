<?php

namespace App\Livewire;

use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CheckoutComponent extends Component
{
    public $cartItems;

    public float $total = 0;

    // Form properties
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
    ];

    public function mount(CartService $cartService)
    {
        $user = Auth::user();
        if (! $user) {
            // This should ideally be handled by middleware, but as a safeguard:
            return $this->redirect(route('login'));
        }

        $this->cartItems = $cartService->getCart($user);
        $this->total = $cartService->getTotal($user);

        if ($this->cartItems->isEmpty()) {
            // Redirect to cart page if it's empty, with a message
            session()->flash('error', 'Your cart is empty. Cannot proceed to checkout.');

            return $this->redirect(route('cart.index'));
        }

        // Pre-fill form with authenticated user's data
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
    }

    public function placeOrder(CheckoutService $checkoutService)
    {
        $this->validate();

        $user = Auth::user();
        $customerDetails = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];

        try {
            // 1. Create the pending reservation
            $reservation = $checkoutService->placeOrder($user, $customerDetails);

            // 2. Get the payment gateway URL
            $paymentUrl = $checkoutService->initiatePayment($reservation);

            // 3. Redirect the user to the payment gateway
            return $this->redirect($paymentUrl);

        } catch (\Exception $e) {
            // Log the error and show a user-friendly message
            session()->flash('error', 'There was an issue placing your order. Please try again.');

            return null; // Prevent further execution
        }
    }

    public function render()
    {
        return view('livewire.checkout-component');
    }
}
