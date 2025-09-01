<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Offering;
use App\Models\PaidReservation;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:pos_page')->only(['index']);
        $this->middleware('permission:pos_create')->only(['store', 'processPayment']);
        $this->middleware('permission:pos_view')->only(['show']);
    }

    /**
     * Display POS dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get merchant's offerings for POS
        $offerings = Offering::where('user_id', $user->id)
            ->where('status', 'active')
            ->get();

        // Get today's POS sales
        $todaySales = PaidReservation::whereDate('created_at', today())
            ->whereHas('offering', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereJsonContains('additional_data->source', 'pos')
            ->sum('total_amount');

        // Get recent POS transactions
        $recentTransactions = PaidReservation::whereHas('offering', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereJsonContains('additional_data->source', 'pos')
            ->with(['offering', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('pos.index', compact('offerings', 'todaySales', 'recentTransactions'));
    }

    /**
     * Show POS sale creation form
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        
        $offerings = Offering::where('user_id', $user->id)
            ->where('status', 'active')
            ->get();

        return view('pos.create', compact('offerings'));
    }

    /**
     * Process POS sale
     */
    public function store(Request $request)
    {
        $request->validate([
            'offering_id' => 'required|exists:offerings,id',
            'quantity' => 'required|integer|min:1',
            'customer_phone' => 'nullable|string',
            'customer_name' => 'nullable|string',
            'payment_method' => 'required|in:cash,card,digital',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $offering = Offering::findOrFail($request->offering_id);
            
            // Check if user owns this offering
            if ($offering->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this offering'
                ], 403);
            }

            // Calculate pricing
            $unitPrice = $offering->price;
            $quantity = $request->quantity;
            $subtotal = $unitPrice * $quantity;
            $discount = $request->discount ?? 0;
            $total = $subtotal - $discount;

            // Find or create customer
            $customer = null;
            if ($request->customer_phone) {
                $customer = User::where('phone', $request->customer_phone)->first();
                
                if (!$customer && $request->customer_name) {
                    $customer = User::create([
                        'name' => $request->customer_name,
                        'phone' => $request->customer_phone,
                        'email' => $request->customer_phone . '@pos.local',
                        'password' => bcrypt('temporary_password'),
                        'role' => 'customer',
                        'email_verified_at' => now(),
                    ]);
                }
            }

            // Create reservation record
            $reservation = PaidReservation::create([
                'user_id' => $customer ? $customer->id : null,
                'offering_id' => $offering->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_amount' => $total,
                'discount_amount' => $discount,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'payment_method' => $request->payment_method,
                'additional_data' => [
                    'source' => 'pos',
                    'cashier_id' => Auth::id(),
                    'payment_method' => $request->payment_method,
                    'customer_phone' => $request->customer_phone,
                    'customer_name' => $request->customer_name,
                    'notes' => $request->notes,
                    'processed_at' => now()->toISOString(),
                    'attendance_status' => 'present', // Auto-mark as present for POS sales
                ]
            ]);

            // Generate QR code for the reservation (for verification if needed)
            $qrData = [
                'reservation_id' => $reservation->id,
                'offering_id' => $offering->id,
                'customer_id' => $customer ? $customer->id : null,
                'quantity' => $quantity,
                'verification_code' => $reservation->verification_code ?? uniqid('pos_'),
            ];

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'POS sale processed successfully',
                'data' => [
                    'reservation' => $reservation,
                    'qr_data' => $qrData,
                    'total' => $total,
                    'customer' => $customer,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('POS sale processing error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error processing POS sale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search customer by phone
     */
    public function searchCustomer(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:8'
        ]);

        $customer = User::where('phone', 'like', '%' . $request->phone . '%')
            ->where('role', 'customer')
            ->first();

        if ($customer) {
            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Customer not found'
        ]);
    }

    /**
     * Verify QR code/ticket
     */
    public function verifyTicket(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string'
        ]);

        $reservation = PaidReservation::where('verification_code', $request->verification_code)
            ->orWhere('id', $request->verification_code)
            ->with(['offering', 'user'])
            ->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid ticket or verification code'
            ]);
        }

        // Check if reservation belongs to current merchant
        if ($reservation->offering->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'This ticket does not belong to your business'
            ]);
        }

        // Mark attendance if not already marked
        $additionalData = $reservation->additional_data ?? [];
        if (!isset($additionalData['attendance_verified_at'])) {
            $additionalData['attendance_verified_at'] = now()->toISOString();
            $additionalData['verified_by'] = Auth::id();
            $reservation->update(['additional_data' => $additionalData]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ticket verified successfully',
            'data' => [
                'reservation' => $reservation,
                'customer' => $reservation->user,
                'offering' => $reservation->offering,
                'already_verified' => isset($additionalData['attendance_verified_at']),
                'verified_at' => $additionalData['attendance_verified_at'] ?? null,
            ]
        ]);
    }

    /**
     * Show POS sale details
     */
    public function show(PaidReservation $reservation)
    {
        // Check if reservation belongs to current merchant
        if ($reservation->offering->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $reservation->load(['offering', 'user']);

        return view('pos.show', compact('reservation'));
    }

    /**
     * Get POS analytics
     */
    public function analytics(Request $request)
    {
        $user = Auth::user();
        $period = $request->get('period', 'today'); // today, week, month, year

        $query = PaidReservation::whereHas('offering', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereJsonContains('additional_data->source', 'pos');

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }

        $sales = $query->get();
        
        $analytics = [
            'total_sales' => $sales->sum('total_amount'),
            'total_transactions' => $sales->count(),
            'average_sale' => $sales->count() > 0 ? $sales->avg('total_amount') : 0,
            'total_customers' => $sales->whereNotNull('user_id')->unique('user_id')->count(),
            'payment_methods' => $sales->groupBy('payment_method')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total_amount')
                ];
            }),
            'hourly_sales' => $sales->groupBy(function ($sale) {
                return Carbon::parse($sale->created_at)->format('H');
            })->map(function ($group) {
                return $group->sum('total_amount');
            }),
        ];

        return response()->json([
            'success' => true,
            'analytics' => $analytics,
            'period' => $period
        ]);
    }
}
