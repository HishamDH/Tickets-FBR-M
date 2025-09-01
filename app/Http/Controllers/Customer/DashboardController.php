<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get customer's bookings statistics
        $bookingsStats = [
            'total' => $user->bookings()->count(),
            'pending' => $user->bookings()->where('booking_status', 'pending')->count(),
            'confirmed' => $user->bookings()->where('booking_status', 'confirmed')->count(),
            'completed' => $user->bookings()->where('booking_status', 'completed')->count(),
            'cancelled' => $user->bookings()->where('booking_status', 'cancelled')->count(),
        ];

        // Get recent bookings
        $recentBookings = $user->bookings()
            ->with(['service', 'merchant'])
            ->latest()
            ->limit(5)
            ->get();

        // Get upcoming bookings
        $upcomingBookings = $user->bookings()
            ->with(['service', 'merchant'])
            ->where('booking_date', '>=', now())
            ->where('booking_status', '!=', 'cancelled')
            ->orderBy('booking_date')
            ->limit(3)
            ->get();

        // Get total spent
        $totalSpent = $user->bookings()
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Get favorite services (most booked)
        $favoriteServices = Service::select('services.*')
            ->join('bookings', 'services.id', '=', 'bookings.service_id')
            ->where('bookings.customer_id', $user->id)
            ->groupBy('services.id')
            ->orderByRaw('COUNT(bookings.id) DESC')
            ->limit(3)
            ->get();

        return view('customer.dashboard', compact(
            'bookingsStats',
            'recentBookings',
            'upcomingBookings',
            'totalSpent',
            'favoriteServices'
        ));
    }
}
