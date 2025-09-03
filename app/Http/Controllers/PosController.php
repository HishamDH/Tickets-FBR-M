<?php

namespace App\Http\Controllers;

use App\Models\Offering;
use App\Models\PaidReservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        // Get merchant's offerings/services for POS
        $services = Offering::where('user_id', $user->id)
            ->where('status', 'active')
            ->select('id', 'title as name', 'price', 'description', 'category')
            ->get()
            ->map(function ($service) {
                $service->icon = $this->getCategoryIcon($service->category);

                return $service;
            });

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

        return view('pos.dashboard', compact('services', 'todaySales', 'recentTransactions'));
    }

    /**
     * Get category icon for service display
     */
    private function getCategoryIcon($category)
    {
        $icons = [
            'venues' => '🏛️',
            'catering' => '🍽️',
            'photography' => '📸',
            'entertainment' => '🎭',
            'planning' => '📋',
            'decoration' => '🎨',
            'audio_visual' => '🔊',
            'transportation' => '🚗',
            'security' => '🛡️',
            'cleaning' => '🧹',
        ];

        return $icons[$category] ?? '🎯';
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
     * Process direct POS sale
     */
    public function processDirectSale(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:offerings,id',
            'items.*.quantity' => 'required|integer|min:1',
            'customer' => 'nullable|array',
            'customer.name' => 'nullable|string',
            'customer.phone' => 'nullable|string',
            'payment_method' => 'required|in:cash,card,mixed',
            'discount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:amount,percentage',
            'notes' => 'nullable|string|max:500',
            'cash_received' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $items = $request->items;
            $subtotal = 0;

            // Calculate subtotal
            foreach ($items as $item) {
                $offering = Offering::where('id', $item['id'])
                    ->where('user_id', $user->id)
                    ->firstOrFail();

                $subtotal += $offering->price * $item['quantity'];
            }

            // Calculate discount
            $discount = $request->discount ?? 0;
            if ($request->discount_type === 'percentage') {
                $discount = ($subtotal * $discount) / 100;
            }

            $total = $subtotal - $discount;

            // Find or create customer
            $customer = null;
            if (! empty($request->customer['phone'])) {
                $customer = User::where('phone', $request->customer['phone'])->first();

                if (! $customer && ! empty($request->customer['name'])) {
                    $customer = User::create([
                        'name' => $request->customer['name'],
                        'phone' => $request->customer['phone'],
                        'email' => $request->customer['phone'].'@pos.local',
                        'password' => bcrypt('temporary_password'),
                        'role' => 'customer',
                        'email_verified_at' => now(),
                    ]);
                }
            }

            // Create main reservation record
            $reservation = PaidReservation::create([
                'user_id' => $customer ? $customer->id : null,
                'offering_id' => $items[0]['id'], // Main item
                'quantity' => array_sum(array_column($items, 'quantity')),
                'unit_price' => $subtotal / array_sum(array_column($items, 'quantity')),
                'total_amount' => $total,
                'discount_amount' => $discount,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'payment_method' => $request->payment_method,
                'additional_data' => [
                    'source' => 'pos',
                    'cashier_id' => $user->id,
                    'items' => $items,
                    'subtotal' => $subtotal,
                    'discount' => [
                        'amount' => $discount,
                        'type' => $request->discount_type,
                        'original_value' => $request->discount,
                    ],
                    'payment' => [
                        'method' => $request->payment_method,
                        'cash_received' => $request->cash_received,
                        'change' => max(0, ($request->cash_received ?? 0) - $total),
                    ],
                    'customer_info' => $request->customer,
                    'notes' => $request->notes,
                    'processed_at' => now()->toISOString(),
                    'receipt_number' => 'POS-'.now()->format('Ymd').'-'.str_pad($user->id, 3, '0', STR_PAD_LEFT).'-'.time(),
                ],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale processed successfully',
                'data' => [
                    'reservation_id' => $reservation->id,
                    'receipt_number' => $reservation->additional_data['receipt_number'],
                    'total' => $total,
                    'change' => $reservation->additional_data['payment']['change'] ?? 0,
                    'customer' => $customer ? $customer->only(['id', 'name', 'phone']) : null,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('POS sale processing error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error processing sale: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Customer lookup by phone or name
     */
    public function customerLookup(Request $request)
    {
        $search = $request->get('search');

        if (strlen($search) < 3) {
            return response()->json([
                'success' => false,
                'message' => 'Search term must be at least 3 characters',
            ]);
        }

        $customers = User::where('role', 'customer')
            ->where(function ($query) use ($search) {
                $query->where('phone', 'like', '%'.$search.'%')
                    ->orWhere('name', 'like', '%'.$search.'%');
            })
            ->select('id', 'name', 'phone', 'email')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'customers' => $customers,
        ]);
    }

    /**
     * Check attendance with QR code
     */
    public function attendanceCheck(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        try {
            // Try to decode QR data
            $qrData = json_decode($request->qr_code, true);

            if (! $qrData || ! isset($qrData['reservation_id'])) {
                // Fallback: treat as reservation ID
                $reservation = PaidReservation::where('id', $request->qr_code)
                    ->orWhere('verification_code', $request->qr_code)
                    ->first();
            } else {
                $reservation = PaidReservation::find($qrData['reservation_id']);
            }

            if (! $reservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code or reservation not found',
                ]);
            }

            // Check if reservation belongs to current merchant
            if ($reservation->offering->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This reservation does not belong to your business',
                ]);
            }

            // Mark attendance
            $additionalData = $reservation->additional_data ?? [];
            $isAlreadyChecked = isset($additionalData['attendance_checked_at']);

            if (! $isAlreadyChecked) {
                $additionalData['attendance_checked_at'] = now()->toISOString();
                $additionalData['checked_by'] = Auth::id();
                $reservation->update(['additional_data' => $additionalData]);
            }

            $reservation->load(['offering', 'user']);

            return response()->json([
                'success' => true,
                'message' => $isAlreadyChecked ? 'Attendance already verified' : 'Attendance verified successfully',
                'data' => [
                    'reservation' => $reservation,
                    'customer' => $reservation->user,
                    'offering' => $reservation->offering,
                    'already_checked' => $isAlreadyChecked,
                    'checked_at' => $additionalData['attendance_checked_at'],
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('QR attendance check error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error processing QR code',
            ], 500);
        }
    }

    /**
     * Get services for POS (API endpoint)
     */
    public function getServices(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $category = $request->get('category');

        $query = Offering::where('user_id', $user->id)
            ->where('status', 'active');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        $services = $query->select('id', 'title as name', 'price', 'description', 'category')
            ->get()
            ->map(function ($service) {
                $service->icon = $this->getCategoryIcon($service->category);

                return $service;
            });

        return response()->json([
            'success' => true,
            'services' => $services,
        ]);
    }

    /**
     * Validate QR code (API endpoint)
     */
    public function validateQr(Request $request)
    {
        return $this->attendanceCheck($request);
    }

    /**
     * Search customers (API endpoint)
     */
    public function searchCustomers(Request $request)
    {
        return $this->customerLookup($request);
    }

    /**
     * Create new customer (API endpoint)
     */
    public function createCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
        ]);

        try {
            $customer = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->phone.'@pos.local',
                'password' => bcrypt('temporary_password'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'customer' => $customer->only(['id', 'name', 'phone']),
            ]);

        } catch (\Exception $e) {
            Log::error('Customer creation error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error creating customer: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * POS Reports
     */
    public function reports(Request $request)
    {
        $user = Auth::user();
        $period = $request->get('period', 'today');

        $analytics = $this->getPosAnalytics($user, $period);

        return view('pos.reports', compact('analytics', 'period'));
    }

    /**
     * Sales History
     */
    public function salesHistory(Request $request)
    {
        $user = Auth::user();

        $sales = PaidReservation::whereHas('offering', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->whereJsonContains('additional_data->source', 'pos')
            ->with(['offering', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pos.sales-history', compact('sales'));
    }

    /**
     * Daily Summary
     */
    public function dailySummary(Request $request)
    {
        $user = Auth::user();
        $date = $request->get('date', today()->toDateString());

        $analytics = $this->getPosAnalytics($user, 'custom', $date);

        return view('pos.daily-summary', compact('analytics', 'date'));
    }

    /**
     * Get POS analytics helper method
     */
    private function getPosAnalytics($user, $period, $customDate = null)
    {
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
            case 'custom':
                if ($customDate) {
                    $query->whereDate('created_at', $customDate);
                }
                break;
        }

        $sales = $query->get();

        return [
            'total_sales' => $sales->sum('total_amount'),
            'total_transactions' => $sales->count(),
            'average_sale' => $sales->count() > 0 ? $sales->avg('total_amount') : 0,
            'total_customers' => $sales->whereNotNull('user_id')->unique('user_id')->count(),
            'payment_methods' => $sales->groupBy('payment_method')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('total_amount'),
                ];
            }),
            'hourly_sales' => $sales->groupBy(function ($sale) {
                return Carbon::parse($sale->created_at)->format('H');
            })->map(function ($group) {
                return $group->sum('total_amount');
            }),
            'period' => $period,
            'sales' => $sales,
        ];
    }

    /**
     * Legacy store method (kept for compatibility)
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
                    'message' => 'Unauthorized access to this offering',
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

                if (! $customer && $request->customer_name) {
                    $customer = User::create([
                        'name' => $request->customer_name,
                        'phone' => $request->customer_phone,
                        'email' => $request->customer_phone.'@pos.local',
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
                ],
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
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('POS sale processing error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error processing POS sale: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search customer by phone
     */
    public function searchCustomer(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:8',
        ]);

        $customer = User::where('phone', 'like', '%'.$request->phone.'%')
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
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Customer not found',
        ]);
    }

    /**
     * Verify QR code/ticket
     */
    public function verifyTicket(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string',
        ]);

        $reservation = PaidReservation::where('verification_code', $request->verification_code)
            ->orWhere('id', $request->verification_code)
            ->with(['offering', 'user'])
            ->first();

        if (! $reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid ticket or verification code',
            ]);
        }

        // Check if reservation belongs to current merchant
        if ($reservation->offering->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'This ticket does not belong to your business',
            ]);
        }

        // Mark attendance if not already marked
        $additionalData = $reservation->additional_data ?? [];
        if (! isset($additionalData['attendance_verified_at'])) {
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
            ],
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
                    'total' => $group->sum('total_amount'),
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
            'period' => $period,
        ]);
    }
}
