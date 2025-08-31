<?php

namespace App\Services;

use App\Models\User;
use App\Models\PaidReservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Create a reservation record from the cart.
     * This is the first step before payment.
     *
     * @param User $user
     * @param array $customerDetails
     * @return PaidReservation
     * @throws \Exception
     */
    public function placeOrder(User $user, array $customerDetails): PaidReservation
    {
        $cartItems = $this->cartService->getCart($user);

        if ($cartItems->isEmpty()) {
            throw new \Exception("Cannot place an order with an empty cart.");
        }

        $total = $this->cartService->getTotal($user);

        $itemsData = $cartItems->map(function ($item) {
            return [
                'offering_id' => $item->offering->id,
                'name' => $item->offering->name,
                'price' => $item->offering->price,
                'quantity' => $item->quantity,
            ];
        });

        // Create the reservation with a 'pending' status
        $reservation = PaidReservation::create([
            'user_id' => $user->id,
            'price' => $total,
            'code' => Str::uuid()->toString(),
            'status' => 'pending', // Add a status field
            'additional_data' => [
                'customer_details' => $customerDetails,
                'items' => $itemsData,
            ],
            'item_id' => null,
            'item_type' => null,
            'quantity' => $cartItems->sum('quantity'),
            'discount' => 0,
        ]);

        return $reservation;
    }

    /**
     * Generate the payment gateway URL.
     *
     * @param PaidReservation $reservation
     * @return string
     */
    public function initiatePayment(PaidReservation $reservation): string
    {
        // --- Placeholder for Ottu Payment Gateway Integration ---
        // This is where you would build the request to the payment gateway API.
        // The following is a dummy implementation.
        
        $gatewayUrl = 'https://ticket-window.ottu.com/payment';
        
        $paymentData = [
            'merchant_id' => env('OTTU_MERCHANT_ID', 'dummy_merchant_id'),
            'order_id' => $reservation->code,
            'amount' => $reservation->price,
            'currency' => 'KWD', // Or your desired currency
            'redirect_url' => route('payment.callback'),
            'customer_name' => $reservation->additional_data['customer_details']['name'],
            'customer_email' => $reservation->additional_data['customer_details']['email'],
        ];

        // In a real scenario, you would generate a secure hash/signature
        // $paymentData['signature'] = hash_hmac('sha256', implode('|', $paymentData), env('OTTU_SECRET_KEY'));

        return $gatewayUrl . '?' . http_build_query($paymentData);
    }

    /**
     * Complete the order after a successful payment.
     *
     * @param string $orderCode The unique code of the reservation.
     * @param string $paymentStatus The status from the payment gateway.
     * @return PaidReservation
     */
    public function completeOrder(string $orderCode, string $paymentStatus): PaidReservation
    {
        $reservation = PaidReservation::where('code', $orderCode)->firstOrFail();

        if ($reservation->status === 'completed') {
            return $reservation; // Already completed
        }

        if ($paymentStatus === 'success') {
            $reservation->status = 'completed';
            $reservation->save();

            // Clear the user's cart
            $this->cartService->clearCart($reservation->user);
        } else {
            $reservation->status = 'failed';
            $reservation->save();
        }

        return $reservation;
    }
}