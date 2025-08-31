<?php

namespace App\Http\Controllers;

use App\Services\CheckoutService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    /**
     * Handle the payment gateway callback.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleCallback(Request $request)
    {
        // Retrieve parameters from the payment gateway's redirect
        $orderCode = $request->query('order_id');
        $paymentStatus = $request->query('status', 'failed'); // Assume 'failed' if status is not present

        try {
            // Complete the order using the CheckoutService
            $reservation = $this->checkoutService->completeOrder($orderCode, $paymentStatus);

            // Redirect to the confirmation page
            return redirect()->route('checkout.confirmation', ['reservation' => $reservation->id])
                ->with('status', 'Payment ' . $paymentStatus);

        } catch (\Exception $e) {
            // Handle cases where the reservation is not found or another error occurs
            return redirect()->route('cart.index')->with('error', 'Invalid payment callback or order not found.');
        }
    }
}