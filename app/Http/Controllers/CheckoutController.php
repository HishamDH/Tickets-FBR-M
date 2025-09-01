<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmation;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('message', 'Please login to proceed with checkout');
        }

        $userId = Auth::id();
        $sessionId = $request->session()->getId();
        
        $cartData = Cart::getCartTotal($userId, $sessionId);
        
        if (empty($cartData['items']) || $cartData['count'] === 0) {
            return redirect()->route('services.index')->with('error', 'Your cart is empty');
        }

        // Validate cart items availability
        $issues = [];
        foreach ($cartData['items'] as $cartItem) {
            if (!$cartItem->isAvailable()) {
                $issues[] = "Item '{$cartItem->getItemName()}' is no longer available";
            }
        }

        if (!empty($issues)) {
            return redirect()->route('services.index')->with('error', implode(', ', $issues));
        }

        $user = Auth::user();
        
        return view('checkout.index', compact('cartData', 'user'));
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $validator = Validator::make($request->all(), [
            'billing_first_name' => 'required|string|max:255',
            'billing_last_name' => 'required|string|max:255',
            'billing_email' => 'required|email|max:255',
            'billing_phone' => 'required|string|max:20',
            'billing_address' => 'required|string|max:500',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'required|string|max:100',
            'billing_postal_code' => 'required|string|max:20',
            'payment_method' => 'required|in:stripe,paypal,bank_transfer,cash_on_delivery',
            'terms_accepted' => 'accepted'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $userId = Auth::id();
        $sessionId = $request->session()->getId();
        
        $cartData = Cart::getCartTotal($userId, $sessionId);
        
        if (empty($cartData['items']) || $cartData['count'] === 0) {
            return redirect()->route('services.index')->with('error', 'Your cart is empty');
        }

        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'user_id' => $userId,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'subtotal' => $cartData['subtotal'],
                'discount' => $cartData['discount'],
                'total' => $cartData['total'],
                'currency' => 'SAR',
                'billing_details' => [
                    'first_name' => $request->billing_first_name,
                    'last_name' => $request->billing_last_name,
                    'email' => $request->billing_email,
                    'phone' => $request->billing_phone,
                    'address' => $request->billing_address,
                    'city' => $request->billing_city,
                    'state' => $request->billing_state,
                    'postal_code' => $request->billing_postal_code,
                ],
                'notes' => $request->notes,
            ]);

            // Create order items from cart
            foreach ($cartData['items'] as $cartItem) {
                $order->items()->create([
                    'item_id' => $cartItem->item_id,
                    'item_type' => $cartItem->item_type,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'discount' => $cartItem->discount,
                    'total' => $cartItem->subtotal,
                    'item_data' => [
                        'name' => $cartItem->getItemName(),
                        'additional_data' => $cartItem->additional_data,
                    ],
                ]);
            }

            // Clear cart
            Cart::clearCart($userId, $sessionId);

            // Send order confirmation email
            Mail::send(new OrderConfirmation($order));

            DB::commit();

            // Redirect based on payment method
            switch ($request->payment_method) {
                case 'stripe':
                    return redirect()->route('checkout.payment.stripe', $order);
                case 'paypal':
                    return redirect()->route('checkout.payment.paypal', $order);
                case 'bank_transfer':
                    return redirect()->route('checkout.confirmation', $order)->with('success', 'Order created successfully. Please complete the bank transfer.');
                case 'cash_on_delivery':
                    $order->update(['payment_status' => 'pending_cod']);
                    return redirect()->route('checkout.confirmation', $order)->with('success', 'Order created successfully. Payment will be collected on delivery.');
                default:
                    return redirect()->route('checkout.confirmation', $order);
            }

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Checkout error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred during checkout. Please try again.')->withInput();
        }
    }

    public function confirmation(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.confirmation', compact('order'));
    }

    public function stripePayment(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->payment_status !== 'pending') {
            return redirect()->route('checkout.confirmation', $order);
        }

        // Initialize Stripe payment
        return view('checkout.payment.stripe', compact('order'));
    }

    public function paypalPayment(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->payment_status !== 'pending') {
            return redirect()->route('checkout.confirmation', $order);
        }

        // Initialize PayPal payment
        return view('checkout.payment.paypal', compact('order'));
    }

    public function processStripePayment(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'stripeToken' => 'required'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            // Process Stripe payment
            // This would integrate with Stripe API
            
            $order->update([
                'payment_status' => 'completed',
                'status' => 'confirmed',
                'payment_details' => [
                    'stripe_charge_id' => 'ch_test_' . Str::random(24),
                    'paid_at' => now(),
                ]
            ]);

            return redirect()->route('checkout.confirmation', $order)
                ->with('success', 'Payment successful! Your order has been confirmed.');

        } catch (\Exception $e) {
            \Log::error('Stripe payment error: ' . $e->getMessage());
            return back()->with('error', 'Payment failed. Please try again.');
        }
    }

    public function processPaypalPayment(Request $request, Order $order)
    {
        try {
            // Process PayPal payment
            // This would integrate with PayPal API
            
            $order->update([
                'payment_status' => 'completed',
                'status' => 'confirmed',
                'payment_details' => [
                    'paypal_transaction_id' => 'PAYPAL_' . Str::random(16),
                    'paid_at' => now(),
                ]
            ]);

            return redirect()->route('checkout.confirmation', $order)
                ->with('success', 'Payment successful! Your order has been confirmed.');

        } catch (\Exception $e) {
            \Log::error('PayPal payment error: ' . $e->getMessage());
            return back()->with('error', 'Payment failed. Please try again.');
        }
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $timestamp = now()->format('Ymd');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return $prefix . $timestamp . $random;
    }
}