<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Cart;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('customer.login')->with('message', 'يرجى تسجيل الدخول لإكمال الطلب');
        }

        $userId = Auth::guard('customer')->id();
        $sessionId = $request->session()->getId();

        $cartData = Cart::getCartTotal($userId, $sessionId);

        if (empty($cartData['items']) || $cartData['count'] === 0) {
            return redirect()->route('customer.services.index')->with('error', 'العربة فارغة');
        }

        // Validate cart items availability
        $issues = [];
        foreach ($cartData['items'] as $cartItem) {
            $service = Service::find($cartItem->item_id);
            if (!$service || !$service->is_active) {
                $issues[] = "الخدمة '{$cartItem->getItemName()}' لم تعد متاحة";
            }
        }

        if (!empty($issues)) {
            return redirect()->route('customer.services.index')->with('error', implode(', ', $issues));
        }

        $user = Auth::guard('customer')->user();

        return view('customer.checkout.index', compact('cartData', 'user'));
    }

    public function store(Request $request)
    {
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('customer.login');
        }

        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255', 
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string|max:500',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|string',
            'payment_method' => 'required|in:pay_at_location,pay_when_visit,bank_transfer',
            'notes' => 'nullable|string|max:1000',
            'terms_accepted' => 'accepted',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $userId = Auth::guard('customer')->id();
        $sessionId = $request->session()->getId();

        $cartData = Cart::getCartTotal($userId, $sessionId);

        if (empty($cartData['items']) || $cartData['count'] === 0) {
            return redirect()->route('customer.services.index')->with('error', 'العربة فارغة');
        }

        try {
            DB::beginTransaction();

            $bookingNumber = $this->generateBookingNumber();
            $bookings = [];

            // Create individual bookings for each cart item
            foreach ($cartData['items'] as $cartItem) {
                $service = Service::find($cartItem->item_id);
                
                if (!$service) {
                    throw new \Exception("الخدمة غير موجودة");
                }

                $totalAmount = $cartItem->price * $cartItem->quantity;

                $booking = Booking::create([
                    'customer_id' => $userId,
                    'service_id' => $service->id,
                    'bookable_id' => $service->id,
                    'bookable_type' => Service::class,
                    'merchant_id' => $service->merchant_id,
                    'booking_number' => $bookingNumber . '-' . (count($bookings) + 1),
                    'guest_count' => $cartItem->quantity,
                    'booking_date' => $request->booking_date,
                    'booking_time' => $request->booking_time,
                    'total_amount' => $totalAmount,
                    'payment_status' => in_array($request->payment_method, ['pay_at_location', 'pay_when_visit']) ? 'pending' : 'pending',
                    'payment_method' => $request->payment_method,
                    'status' => 'pending',
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'notes' => $request->notes,
                    'special_requests' => $request->notes,
                ]);

                $bookings[] = $booking;
            }

            // Clear cart
            Cart::clearCart($userId, $sessionId);

            DB::commit();

            // Redirect to confirmation with all booking IDs
            $bookingIds = collect($bookings)->pluck('id')->toArray();
            return redirect()->route('customer.checkout.confirmation')
                ->with('booking_ids', $bookingIds)
                ->with('success', 'تم إرسال طلبات الحجز بنجاح!');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Customer checkout error: ' . $e->getMessage());

            return back()->with('error', 'حدث خطأ أثناء معالجة الطلب. يرجى المحاولة مرة أخرى.')->withInput();
        }
    }

    public function confirmation(Request $request)
    {
        $bookingIds = session('booking_ids');
        
        if (!$bookingIds) {
            return redirect()->route('customer.services.index');
        }

        $bookings = Booking::whereIn('id', $bookingIds)
            ->where('customer_id', Auth::guard('customer')->id())
            ->with(['service', 'service.merchant'])
            ->get();

        if ($bookings->isEmpty()) {
            return redirect()->route('customer.services.index');
        }

        return view('customer.checkout.confirmation', compact('bookings'));
    }

    private function generateBookingNumber(): string
    {
        $prefix = 'BK';
        $timestamp = now()->format('Ymd');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return $prefix . $timestamp . $random;
    }
}