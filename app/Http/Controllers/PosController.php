<?php

namespace App\Http\Controllers;

use App\Models\Offering;
use App\Models\PaidReservation;
use App\Models\User;
use App\Services\ThermalPrinterService;
use App\Services\OfflinePosService;
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

    /**
     * Print ticket receipt
     */
    public function printTicket(Request $request, PaidReservation $reservation)
    {
        // Check if reservation belongs to current merchant
        if (!$reservation->offering || $reservation->offering->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to reservation'
            ], 403);
        }

        try {
            $printerService = app(ThermalPrinterService::class);
            $printed = $printerService->printTicket($reservation);

            if ($printed) {
                // Log print activity
                Log::info('Ticket printed', [
                    'reservation_id' => $reservation->id,
                    'merchant_id' => Auth::id(),
                    'printed_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Ticket printed successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to print ticket'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Print ticket error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Print error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print sales receipt
     */
    public function printReceipt(Request $request, PaidReservation $reservation)
    {
        // Check if reservation belongs to current merchant
        if (!$reservation->offering || $reservation->offering->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to reservation'
            ], 403);
        }

        try {
            $printerService = app(ThermalPrinterService::class);
            $printed = $printerService->printSalesReceipt($reservation);

            if ($printed) {
                return response()->json([
                    'success' => true,
                    'message' => 'Receipt printed successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to print receipt'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Print receipt error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Print error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batch print multiple tickets
     */
    public function batchPrint(Request $request)
    {
        $request->validate([
            'reservation_ids' => 'required|array|min:1|max:50',
            'reservation_ids.*' => 'exists:paid_reservations,id',
            'print_type' => 'required|in:ticket,receipt',
        ]);

        try {
            $reservations = PaidReservation::whereIn('id', $request->reservation_ids)
                ->whereHas('offering', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->get();

            if ($reservations->count() !== count($request->reservation_ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some reservations not found or unauthorized'
                ], 403);
            }

            $printerService = app(ThermalPrinterService::class);
            $results = [];

            foreach ($reservations as $reservation) {
                if ($request->print_type === 'ticket') {
                    $printed = $printerService->printTicket($reservation);
                } else {
                    $printed = $printerService->printSalesReceipt($reservation);
                }

                $results[] = [
                    'reservation_id' => $reservation->id,
                    'printed' => $printed,
                ];
            }

            $successCount = collect($results)->where('printed', true)->count();
            $failedCount = collect($results)->where('printed', false)->count();

            return response()->json([
                'success' => true,
                'message' => sprintf('Batch print completed: %d successful, %d failed', $successCount, $failedCount),
                'results' => $results,
                'stats' => [
                    'total' => count($results),
                    'successful' => $successCount,
                    'failed' => $failedCount,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Batch print error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Batch print error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print daily report
     */
    public function printDailyReport(Request $request)
    {
        try {
            $user = Auth::user();
            $date = $request->input('date', now()->toDateString());

            // Get sales data for the day
            $sales = PaidReservation::whereHas('offering', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->whereJsonContains('additional_data->source', 'pos')
                ->whereDate('created_at', $date)
                ->get();

            $salesData = [
                'date' => $date,
                'total_transactions' => $sales->count(),
                'total_sales' => $sales->sum('total_amount'),
                'cash_sales' => $sales->filter(function ($sale) {
                    $paymentMethod = $sale->additional_data['payment']['method'] ?? 'unknown';
                    return $paymentMethod === 'cash';
                })->sum('total_amount'),
                'card_sales' => $sales->filter(function ($sale) {
                    $paymentMethod = $sale->additional_data['payment']['method'] ?? 'unknown';
                    return in_array($paymentMethod, ['card', 'credit', 'debit']);
                })->sum('total_amount'),
                'payment_methods' => $sales->groupBy(function ($sale) {
                    return $sale->additional_data['payment']['method'] ?? 'unknown';
                })->map(function ($group) {
                    return $group->sum('total_amount');
                })->toArray(),
            ];

            $printerService = app(ThermalPrinterService::class);
            $printed = $printerService->printDailyReport($user, $salesData);

            if ($printed) {
                Log::info('Daily report printed', [
                    'merchant_id' => $user->id,
                    'date' => $date,
                    'total_sales' => $salesData['total_sales'],
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Daily report printed successfully',
                    'sales_data' => $salesData,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to print daily report'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Print daily report error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Print error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test thermal printer
     */
    public function testPrinter()
    {
        try {
            $printerService = app(ThermalPrinterService::class);
            $tested = $printerService->testPrinter();

            if ($tested) {
                return response()->json([
                    'success' => true,
                    'message' => 'Printer test successful'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Printer test failed'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Printer test error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Printer test error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Open cash drawer
     */
    public function openCashDrawer()
    {
        try {
            $printerService = app(ThermalPrinterService::class);
            
            // Send cash drawer open command using print method
            $drawerCommand = "\x1B\x70\x00\x32\xC8"; // ESC p 0 50 200
            $opened = $printerService->printRaw($drawerCommand);
            
            if ($opened) {
                Log::info('Cash drawer opened', [
                    'merchant_id' => Auth::id(),
                    'opened_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Cash drawer opened'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to open cash drawer'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Cash drawer error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Cash drawer error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store transaction offline
     */
    public function storeOfflineTransaction(Request $request)
    {
        $request->validate([
            'offering_id' => 'required|exists:offerings,id',
            'customer.name' => 'required|string|max:255',
            'customer.email' => 'nullable|email',
            'customer.phone' => 'nullable|string|max:20',
            'num_people' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'payment.method' => 'required|string',
            'payment.amount_paid' => 'required|numeric|min:0',
            'seats' => 'nullable|array',
        ]);

        try {
            $offlineService = app(OfflinePosService::class);

            // Check if offline mode is enabled
            if (!$offlineService->isOfflineModeEnabled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Offline mode is not enabled'
                ], 400);
            }

            // Verify offering belongs to current merchant
            $offering = Offering::where('id', $request->offering_id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$offering) {
                return response()->json([
                    'success' => false,
                    'message' => 'Offering not found or unauthorized'
                ], 404);
            }

            // Prepare transaction data
            $transactionData = [
                'offering_id' => $request->offering_id,
                'customer' => $request->customer,
                'num_people' => $request->num_people,
                'total_amount' => $request->total_amount,
                'seats' => $request->seats ?? [],
                'additional_data' => [
                    'source' => 'pos',
                    'payment' => $request->payment,
                    'pos_terminal' => gethostname(),
                    'offline_mode' => true,
                ],
            ];

            $result = $offlineService->storeOfflineTransaction($transactionData);

            if ($result['success']) {
                // Try to print ticket if printer is available
                try {
                    if (config('pos.ticket.auto_print', true)) {
                        $printerService = app(ThermalPrinterService::class);
                        
                        // Create a temporary reservation-like object for printing
                        $tempReservation = (object) [
                            'id' => $result['offline_id'],
                            'offering' => $offering,
                            'user_name' => $request->customer['name'],
                            'user_phone' => $request->customer['phone'] ?? '',
                            'num_people' => $request->num_people,
                            'total_amount' => $request->total_amount,
                            'created_at' => now(),
                            'additional_data' => $transactionData['additional_data'],
                        ];
                        
                        $printerService->printTicket($tempReservation);
                    }
                } catch (\Exception $printError) {
                    Log::warning('Offline print failed: ' . $printError->getMessage());
                }

                return response()->json([
                    'success' => true,
                    'offline_id' => $result['offline_id'],
                    'message' => 'Transaction stored offline successfully',
                    'data' => [
                        'transaction_id' => $result['offline_id'],
                        'status' => 'offline',
                        'will_sync_when_online' => true,
                    ]
                ]);
            } else {
                return response()->json($result, 500);
            }

        } catch (\Exception $e) {
            Log::error('Store offline transaction error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to store offline transaction: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync offline transactions
     */
    public function syncOfflineTransactions()
    {
        try {
            $offlineService = app(OfflinePosService::class);
            $result = $offlineService->syncOfflineTransactions();

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Sync offline transactions error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get offline statistics
     */
    public function getOfflineStats()
    {
        try {
            $offlineService = app(OfflinePosService::class);
            $stats = $offlineService->getOfflineStats();

            return response()->json([
                'success' => true,
                'stats' => $stats,
            ]);

        } catch (\Exception $e) {
            Log::error('Get offline stats error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get offline transactions
     */
    public function getOfflineTransactions(Request $request)
    {
        try {
            $offlineService = app(OfflinePosService::class);
            $status = $request->query('status');
            $transactions = $offlineService->getOfflineTransactions($status);

            return response()->json([
                'success' => true,
                'transactions' => $transactions,
                'count' => count($transactions),
            ]);

        } catch (\Exception $e) {
            Log::error('Get offline transactions error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get transactions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear synced offline transactions
     */
    public function clearSyncedTransactions()
    {
        try {
            $offlineService = app(OfflinePosService::class);
            $result = $offlineService->clearSyncedTransactions();

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Clear synced transactions error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export offline data
     */
    public function exportOfflineData()
    {
        try {
            $offlineService = app(OfflinePosService::class);
            $result = $offlineService->exportOfflineData();

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'download_url' => route('merchant.pos.download-offline-export', [
                        'filename' => $result['file_name']
                    ]),
                    'file_info' => [
                        'name' => $result['file_name'],
                        'size' => $result['file_size'],
                        'transactions_count' => $result['transactions_count'],
                    ]
                ]);
            } else {
                return response()->json($result, 500);
            }

        } catch (\Exception $e) {
            Log::error('Export offline data error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download offline data export
     */
    public function downloadOfflineExport($filename)
    {
        try {
            $merchantId = Auth::id();
            $filePath = "offline/pos/{$merchantId}/exports/{$filename}";

            if (!Storage::exists($filePath)) {
                abort(404, 'Export file not found');
            }

            return Storage::download($filePath);

        } catch (\Exception $e) {
            Log::error('Download offline export error: ' . $e->getMessage());
            abort(500, 'Download failed');
        }
    }

    /**
     * Check connection status
     */
    public function checkConnectionStatus()
    {
        try {
            // Simple connectivity check
            $onlineStatus = $this->isOnline();
            $offlineService = app(OfflinePosService::class);
            
            return response()->json([
                'success' => true,
                'status' => [
                    'online' => $onlineStatus,
                    'offline_mode_enabled' => $offlineService->isOfflineModeEnabled(),
                    'pending_sync_count' => count($offlineService->getOfflineTransactions('pending')),
                    'last_check' => now()->toISOString(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Check connection status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if system is online
     */
    private function isOnline()
    {
        try {
            // Try to connect to a reliable server
            $connection = @fsockopen('www.google.com', 80, $errno, $errstr, 5);
            if ($connection) {
                fclose($connection);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
