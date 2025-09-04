<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\MerchantEmployee;
use App\Models\EmployeeShift;
use App\Models\EmployeeActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'merchant']);
    }

    /**
     * Display staff management dashboard
     */
    public function index()
    {
        $merchant = Auth::user();
        
        $staff = MerchantEmployee::with(['employee', 'shifts' => function($query) {
            $query->where('shift_date', today());
        }])
        ->where('merchant_id', $merchant->id)
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        $stats = [
            'total_staff' => MerchantEmployee::where('merchant_id', $merchant->id)->count(),
            'active_staff' => MerchantEmployee::where('merchant_id', $merchant->id)
                ->where('status', 'active')->count(),
            'staff_on_duty' => MerchantEmployee::where('merchant_id', $merchant->id)
                ->whereHas('shifts', function($query) {
                    $query->where('shift_date', today())
                          ->where('status', 'in_progress');
                })->count(),
            'monthly_hours' => EmployeeShift::whereHas('merchantEmployee', function($query) use ($merchant) {
                $query->where('merchant_id', $merchant->id);
            })->where('shift_date', '>=', now()->startOfMonth())
            ->where('status', 'completed')
            ->sum('total_hours') ?? 0,
        ];

        return view('merchant.staff.index', compact('staff', 'stats'));
    }

    /**
     * Show form to add new staff member
     */
    public function create()
    {
        $roles = MerchantEmployee::getRoles();
        return view('merchant.staff.create', compact('roles'));
    }

    /**
     * Store new staff member
     */
    public function store(Request $request)
    {
        $merchant = Auth::user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|max:20',
            'role' => 'required|in:' . implode(',', array_keys(MerchantEmployee::getRoles())),
            'hourly_rate' => 'nullable|numeric|min:0|max:999.99',
            'hire_date' => 'required|date',
            'password' => 'required|string|min:8|confirmed',
            'can_process_payments' => 'boolean',
            'can_manage_bookings' => 'boolean',
            'can_view_reports' => 'boolean',
            'can_manage_services' => 'boolean',
            'can_manage_inventory' => 'boolean',
        ]);

        DB::transaction(function () use ($request, $merchant) {
            // Create user account for employee
            $employee = User::create([
                'f_name' => $request->first_name,
                'l_name' => $request->last_name,
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'user_type' => 'employee',
                'status' => 'active',
            ]);

            // Create merchant employee record
            MerchantEmployee::create([
                'merchant_id' => $merchant->id,
                'employee_id' => $employee->id,
                'role' => $request->role,
                'employee_code' => MerchantEmployee::generateEmployeeCode($merchant->id),
                'hourly_rate' => $request->hourly_rate,
                'hire_date' => $request->hire_date,
                'can_process_payments' => $request->boolean('can_process_payments'),
                'can_manage_bookings' => $request->boolean('can_manage_bookings'),
                'can_view_reports' => $request->boolean('can_view_reports'),
                'can_manage_services' => $request->boolean('can_manage_services'),
                'can_manage_inventory' => $request->boolean('can_manage_inventory'),
            ]);
        });

        return redirect()->route('merchant.staff.index')
            ->with('success', 'Staff member added successfully!');
    }

    /**
     * Show staff member details
     */
    public function show(MerchantEmployee $staff)
    {
        $this->authorize('view', $staff);
        
        $staff->load(['employee', 'shifts' => function($query) {
            $query->orderBy('shift_date', 'desc')->take(10);
        }, 'activities' => function($query) {
            $query->orderBy('activity_time', 'desc')->take(20);
        }]);

        $metrics = $staff->getPerformanceMetrics();
        
        return view('merchant.staff.show', compact('staff', 'metrics'));
    }

    /**
     * Show form to edit staff member
     */
    public function edit(MerchantEmployee $staff)
    {
        $this->authorize('update', $staff);
        
        $staff->load('employee');
        $roles = MerchantEmployee::getRoles();
        $statuses = MerchantEmployee::getStatuses();
        
        return view('merchant.staff.edit', compact('staff', 'roles', 'statuses'));
    }

    /**
     * Update staff member
     */
    public function update(Request $request, MerchantEmployee $staff)
    {
        $this->authorize('update', $staff);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($staff->employee_id)],
            'phone_number' => 'required|string|max:20',
            'role' => 'required|in:' . implode(',', array_keys(MerchantEmployee::getRoles())),
            'status' => 'required|in:' . implode(',', array_keys(MerchantEmployee::getStatuses())),
            'hourly_rate' => 'nullable|numeric|min:0|max:999.99',
            'termination_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'can_process_payments' => 'boolean',
            'can_manage_bookings' => 'boolean',
            'can_view_reports' => 'boolean',
            'can_manage_services' => 'boolean',
            'can_manage_inventory' => 'boolean',
        ]);

        DB::transaction(function () use ($request, $staff) {
            // Update employee user account
            $staff->employee->update([
                'f_name' => $request->first_name,
                'l_name' => $request->last_name,
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
            ]);

            // Update merchant employee record
            $staff->update([
                'role' => $request->role,
                'status' => $request->status,
                'hourly_rate' => $request->hourly_rate,
                'termination_date' => $request->termination_date,
                'notes' => $request->notes,
                'can_process_payments' => $request->boolean('can_process_payments'),
                'can_manage_bookings' => $request->boolean('can_manage_bookings'),
                'can_view_reports' => $request->boolean('can_view_reports'),
                'can_manage_services' => $request->boolean('can_manage_services'),
                'can_manage_inventory' => $request->boolean('can_manage_inventory'),
            ]);
        });

        return redirect()->route('merchant.staff.show', $staff)
            ->with('success', 'Staff member updated successfully!');
    }

    /**
     * Delete staff member
     */
    public function destroy(MerchantEmployee $staff)
    {
        $this->authorize('delete', $staff);

        $staff->delete();

        return redirect()->route('merchant.staff.index')
            ->with('success', 'Staff member removed successfully!');
    }

    /**
     * Manage staff schedules
     */
    public function schedules()
    {
        $merchant = Auth::user();
        
        $staff = MerchantEmployee::with(['employee', 'shifts' => function($query) {
            $query->whereBetween('shift_date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->orderBy('shift_date');
        }])
        ->where('merchant_id', $merchant->id)
        ->where('status', 'active')
        ->get();

        return view('merchant.staff.schedules', compact('staff'));
    }

    /**
     * Store new shift
     */
    public function storeShift(Request $request)
    {
        $request->validate([
            'merchant_employee_id' => 'required|exists:merchant_employees,id',
            'shift_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string|max:500',
        ]);

        // Verify the employee belongs to this merchant
        $merchantEmployee = MerchantEmployee::where('id', $request->merchant_employee_id)
            ->where('merchant_id', Auth::user()->id)
            ->firstOrFail();

        EmployeeShift::create([
            'merchant_employee_id' => $request->merchant_employee_id,
            'shift_date' => $request->shift_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Shift scheduled successfully!');
    }

    /**
     * Clock in/out functionality
     */
    public function clockIn(MerchantEmployee $staff)
    {
        $this->authorize('update', $staff);

        $todayShift = $staff->shifts()
            ->where('shift_date', today())
            ->where('status', 'scheduled')
            ->first();

        if ($todayShift) {
            $todayShift->start();
            $staff->logActivity('login', 'Clocked in for shift');
        } else {
            // Create an unscheduled shift
            $shift = EmployeeShift::create([
                'merchant_employee_id' => $staff->id,
                'shift_date' => today(),
                'start_time' => now()->format('H:i'),
                'end_time' => now()->addHours(8)->format('H:i'),
                'actual_start_time' => now()->format('H:i'),
                'status' => 'in_progress',
            ]);
            $staff->logActivity('login', 'Clocked in (unscheduled shift)');
        }

        return back()->with('success', 'Clocked in successfully!');
    }

    public function clockOut(MerchantEmployee $staff)
    {
        $this->authorize('update', $staff);

        $currentShift = $staff->getCurrentShift();
        
        if ($currentShift) {
            $currentShift->end();
            $staff->logActivity('logout', 'Clocked out from shift');
            
            return back()->with('success', 'Clocked out successfully!');
        }

        return back()->with('error', 'No active shift found!');
    }

    /**
     * View staff performance reports
     */
    public function reports()
    {
        $merchant = Auth::user();
        
        $staff = MerchantEmployee::with('employee')
            ->where('merchant_id', $merchant->id)
            ->get()
            ->map(function ($employee) {
                $metrics = $employee->getPerformanceMetrics();
                return [
                    'employee' => $employee,
                    'metrics' => $metrics,
                ];
            });

        return view('merchant.staff.reports', compact('staff'));
    }

    /**
     * Export staff report
     */
    public function exportReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:csv,excel',
        ]);

        $merchant = Auth::user();
        
        $data = MerchantEmployee::with(['employee', 'shifts', 'activities'])
            ->where('merchant_id', $merchant->id)
            ->get();

        // Export logic here (you can use Laravel Excel or similar)
        // For now, return JSON
        return response()->json($data);
    }

    /**
     * Search staff members
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $merchant = Auth::user();
        
        $staff = MerchantEmployee::with('employee')
            ->where('merchant_id', $merchant->id)
            ->whereHas('employee', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->orWhere('employee_code', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json($staff);
    }
}
