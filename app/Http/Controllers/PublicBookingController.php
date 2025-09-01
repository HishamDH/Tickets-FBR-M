<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Merchant;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicBookingController extends Controller
{
    /**
     * عرض صفحة حجز التاجر
     */
    public function show($merchantId): View
    {
        $merchant = Merchant::where('verification_status', 'approved')
            ->findOrFail($merchantId);

        $services = Service::where('merchant_id', $merchantId)
            ->where('is_active', true)
            ->where('is_available', true)
            ->where('online_booking_enabled', true)
            ->orderBy('is_featured', 'desc')
            ->orderBy('name')
            ->get();

        return view('public.booking', compact('merchant', 'services'));
    }

    /**
     * عرض خدمة محددة للحجز
     */
    public function service($merchantId, $serviceId): View
    {
        $merchant = Merchant::where('verification_status', 'approved')
            ->findOrFail($merchantId);

        $service = Service::where('merchant_id', $merchantId)
            ->where('is_active', true)
            ->where('is_available', true)
            ->where('online_booking_enabled', true)
            ->findOrFail($serviceId);

        return view('public.service-booking', compact('merchant', 'service'));
    }

    /**
     * معالجة طلب الحجز
     */
    public function book(Request $request, $merchantId, $serviceId)
    {
        $merchant = Merchant::where('verification_status', 'approved')
            ->findOrFail($merchantId);

        $service = Service::where('merchant_id', $merchantId)
            ->where('is_active', true)
            ->where('is_available', true)
            ->where('online_booking_enabled', true)
            ->findOrFail($serviceId);

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
            'number_of_people' => 'nullable|integer|min:1'.($service->capacity ? '|max:'.$service->capacity : ''),
            'number_of_tables' => 'nullable|integer|min:1',
            'duration_hours' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Calculate total amount based on pricing model
        $totalAmount = $this->calculateTotalAmount($service, $request);

        // Calculate commission using merchant's commission rate
        $commissionRate = $merchant->commission_rate ?? 5.0;
        $commissionAmount = $totalAmount * ($commissionRate / 100);

        // Create or find customer
        $customer = null;
        if ($request->customer_email) {
            $customer = User::where('email', $request->customer_email)
                ->where('user_type', 'customer')
                ->first();

            if (! $customer) {
                $customer = User::create([
                    'name' => $request->customer_name,
                    'email' => $request->customer_email,
                    'phone' => $request->customer_phone,
                    'user_type' => 'customer',
                    'password' => bcrypt(str()->random(12)),
                    'email_verified_at' => now(),
                ]);
            }
        }

        // Create booking
        $booking = Booking::create([
            'customer_id' => $customer?->id,
            'service_id' => $service->id,
            'merchant_id' => $merchant->id,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'guest_count' => $request->number_of_people ?? 1,
            'total_amount' => $totalAmount,
            'commission_amount' => $commissionAmount,
            'commission_rate' => $commissionRate,
            'payment_status' => 'pending',
            'booking_status' => 'pending',
            'booking_source' => 'online',
            'special_requests' => $request->notes,
            // Store customer info for non-registered customers
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
        ]);

        return redirect()->route('merchant.booking.confirmation', [
            'merchant' => $merchant->id,
            'booking' => $booking->id,
        ]);
    }

    /**
     * عرض صفحة تأكيد الحجز
     */
    public function confirmation($merchantId, $bookingId): View
    {
        $merchant = Merchant::findOrFail($merchantId);

        $booking = Booking::with(['customer', 'service'])
            ->where('merchant_id', $merchantId)
            ->findOrFail($bookingId);

        // Generate QR code for the booking
        $booking->qr_code = $booking->generateQrCode();

        return view('public.confirmation', compact('booking', 'merchant'));
    }

    /**
     * Calculate total amount based on service pricing model
     */
    private function calculateTotalAmount(Service $service, Request $request): float
    {
        $basePrice = $service->base_price ?? $service->price ?? 0;
        $pricingModel = $service->pricing_model ?? $service->price_type ?? 'fixed';

        switch ($pricingModel) {
            case 'per_person':
                return $basePrice * ($request->number_of_people ?? 1);

            case 'per_table':
                return $basePrice * ($request->number_of_tables ?? 1);

            case 'hourly':
            case 'per_hour':
                return $basePrice * ($request->duration_hours ?? 1);

            case 'package':
            case 'fixed':
            default:
                return $basePrice;
        }
    }
}
