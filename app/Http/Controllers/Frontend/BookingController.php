<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Offering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('customer.role');
    }

    public function show(Offering $offering)
    {
        $offering->load(['merchant.user']);

        return view('frontend.booking-form', compact('offering'));
    }

    public function store(Request $request, Offering $offering)
    {
        $request->validate([
            'booking_date' => 'required|date|after:today',
            'booking_time' => 'required',
            'quantity' => 'required|integer|min:1',
            'customer_notes' => 'nullable|string|max:500',
        ]);

        // Calculate total amount
        $totalAmount = $offering->price * $request->quantity;

        // Create booking
        $booking = Booking::create([
            'booking_reference' => 'BK-'.Str::upper(Str::random(8)),
            'customer_id' => Auth::id(),
            'service_id' => $offering->id,
            'merchant_id' => $offering->merchant_id,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'quantity' => $request->quantity,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'customer_notes' => $request->customer_notes,
            'booking_details' => [
                'offering_title' => $offering->title,
                'offering_price' => $offering->price,
                'merchant_name' => $offering->merchant->business_name ?? $offering->merchant->user->name,
            ],
        ]);

        return redirect()->route('booking.confirmation', $booking)
            ->with('success', 'Your booking has been submitted successfully!');
    }

    public function confirmation(Booking $booking)
    {
        // Ensure user can only view their own bookings
        if ($booking->customer_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['service', 'merchant.user']);

        return view('frontend.booking-confirmation', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        // Ensure user can only cancel their own bookings
        if ($booking->customer_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->status === 'pending') {
            $booking->update(['status' => 'cancelled']);

            return back()->with('success', 'Booking cancelled successfully.');
        }

        return back()->with('error', 'This booking cannot be cancelled.');
    }
}
