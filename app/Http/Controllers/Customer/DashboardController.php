<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get customer's bookings statistics
        $bookingsStats = [
            'total' => $user->bookings()->count(),
            'pending' => $user->bookings()->where('status', 'pending')->count(),
            'confirmed' => $user->bookings()->where('status', 'confirmed')->count(),
            'completed' => $user->bookings()->where('status', 'completed')->count(),
            'cancelled' => $user->bookings()->where('status', 'cancelled')->count(),
        ];

        // Get recent bookings
        $recentBookings = $user->bookings()
            ->with(['bookable', 'merchant'])
            ->latest()
            ->limit(5)
            ->get();

        // Get upcoming bookings
        $upcomingBookings = $user->bookings()
            ->with(['bookable', 'merchant'])
            ->where('booking_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('booking_date')
            ->limit(3)
            ->get();

        // Get total spent (from bookings)
        $totalSpentBookings = $user->bookings()
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Get favorite services (most booked)
        $favoriteServices = Service::select('services.*')
            ->join('bookings', 'services.id', '=', 'bookings.bookable_id') // Updated join
            ->where('bookings.bookable_type', Service::class) // Ensure it's a service
            ->where('bookings.customer_id', $user->id)
            ->groupBy('services.id')
            ->orderByRaw('COUNT(bookings.id) DESC')
            ->limit(3)
            ->get();

        // Order statistics (from UserDashboardController)
        $orderStats = [
            'total' => Order::where('user_id', $user->id)->count(),
            'completed' => Order::where('user_id', $user->id)->where('status', 'delivered')->count(),
            'pending' => Order::where('user_id', $user->id)->whereIn('status', ['pending', 'confirmed', 'processing'])->count(),
            'cancelled' => Order::where('user_id', $user->id)->where('status', 'cancelled')->count(),
        ];

        // Total spending (from orders)
        $totalSpentOrders = Order::where('user_id', $user->id)
            ->where('payment_status', 'completed')
            ->sum('total');

        // Combine total spent
        $totalSpent = $totalSpentBookings + $totalSpentOrders;

        // Recent activity
        $recentActivity = $this->getRecentActivity($user);

        return view('customer.dashboard', compact(
            'user',
            'bookingsStats',
            'recentBookings',
            'upcomingBookings',
            'totalSpent',
            'favoriteServices',
            'orderStats',
            'recentActivity'
        ));
    }

    public function orders(Request $request)
    {
        $user = Auth::user();

        $query = Order::where('user_id', $user->id)->with(['items']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by order number
        if ($request->has('search') && $request->search) {
            $query->where('order_number', 'like', '%'.$request->search.'%');
        }

        $orders = $query->latest()->paginate(10);

        // Get filter options
        $statusOptions = [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];

        $paymentStatusOptions = [
            'pending' => 'Pending Payment',
            'completed' => 'Paid',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
            'pending_cod' => 'Cash on Delivery',
        ];

        return view('customer.orders', compact(
            'orders',
            'statusOptions',
            'paymentStatusOptions'
        ));
    }

    public function orderShow(Order $order)
    {
        // Ensure user can only view their own orders
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items']);

        return view('customer.order-details', compact('order'));
    }

    public function profile()
    {
        $user = Auth::user();

        return view('customer.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
        ]);

        // Update user profile
        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'name' => $validated['first_name'].' '.$validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'date_of_birth' => $validated['date_of_birth'],
            'gender' => $validated['gender'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'state' => $validated['state'],
            'postal_code' => $validated['postal_code'],
            'country' => $validated['country'],
        ]);

        return redirect()->route('customer.profile')
            ->with('success', 'Profile updated successfully!');
    }

    public function cancelOrder(Request $request, Order $order)
    {
        // Ensure user can only cancel their own orders
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if order can be cancelled
        if (! $order->canBeCancelled()) {
            return redirect()->back()
                ->with('error', 'This order cannot be cancelled at this time.');
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        // Update order status
        $order->update([
            'status' => 'cancelled',
            'notes' => ($order->notes ? $order->notes."\n\n" : '').
                      'Cancelled by customer: '.$validated['cancellation_reason'],
        ]);

        // TODO: Process refund if payment was completed
        // TODO: Send cancellation notification email

        return redirect()->route('customer.orders')
            ->with('success', 'Order cancelled successfully. Refund will be processed within 5-7 business days.');
    }

    private function getRecentActivity($user)
    {
        $activities = collect();

        // Recent orders
        $recentOrders = Order::where('user_id', $user->id)
            ->latest()
            ->limit(3)
            ->get();

        foreach ($recentOrders as $order) {
            $activities->push([
                'type' => 'order',
                'title' => 'Order #'.$order->order_number,
                'description' => 'Order '.strtolower($order->status_label),
                'date' => $order->updated_at,
                'icon' => 'shopping-cart',
                'color' => $order->status_color,
                'url' => route('customer.orders.show', $order),
            ]);
        }

        // TODO: Add more activity types like profile updates, reviews, etc.

        return $activities->sortByDesc('date')->take(5);
    }
}
