<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MerchantEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'employee_id',
        'role',
        'permissions',
        'employee_code',
        'hourly_rate',
        'status',
        'hire_date',
        'termination_date',
        'notes',
        'schedule',
        'can_process_payments',
        'can_manage_bookings',
        'can_view_reports',
        'can_manage_services',
        'can_manage_inventory',
    ];

    protected $casts = [
        'permissions' => 'array',
        'schedule' => 'array',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'hourly_rate' => 'decimal:2',
        'can_process_payments' => 'boolean',
        'can_manage_bookings' => 'boolean',
        'can_view_reports' => 'boolean',
        'can_manage_services' => 'boolean',
        'can_manage_inventory' => 'boolean',
    ];

    // Role constants
    const ROLE_MANAGER = 'manager';
    const ROLE_SUPERVISOR = 'supervisor';
    const ROLE_STAFF = 'staff';
    const ROLE_CASHIER = 'cashier';

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';

    /**
     * Get the merchant that owns this employee
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    /**
     * Get the user who is the employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Get the shifts for this employee
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(EmployeeShift::class);
    }

    /**
     * Get the activities for this employee
     */
    public function activities(): HasMany
    {
        return $this->hasMany(EmployeeActivity::class);
    }

    /**
     * Get available roles
     */
    public static function getRoles(): array
    {
        return [
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_SUPERVISOR => 'Supervisor',
            self::ROLE_STAFF => 'Staff',
            self::ROLE_CASHIER => 'Cashier',
        ];
    }

    /**
     * Get available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_SUSPENDED => 'Suspended',
        ];
    }

    /**
     * Check if employee has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions);
    }

    /**
     * Get role-based permissions
     */
    public function getRolePermissions(): array
    {
        return match($this->role) {
            self::ROLE_MANAGER => [
                'can_process_payments',
                'can_manage_bookings',
                'can_view_reports',
                'can_manage_services',
                'can_manage_inventory',
                'can_manage_staff',
                'can_access_pos',
                'can_issue_refunds',
            ],
            self::ROLE_SUPERVISOR => [
                'can_process_payments',
                'can_manage_bookings',
                'can_view_reports',
                'can_access_pos',
                'can_issue_refunds',
            ],
            self::ROLE_CASHIER => [
                'can_process_payments',
                'can_access_pos',
            ],
            self::ROLE_STAFF => [
                'can_manage_bookings',
            ],
            default => [],
        };
    }

    /**
     * Check if employee can perform an action
     */
    public function canPerform(string $action): bool
    {
        // Check role-based permissions first
        $rolePermissions = $this->getRolePermissions();
        if (in_array($action, $rolePermissions)) {
            return true;
        }

        // Check custom permissions
        return $this->hasPermission($action);
    }

    /**
     * Generate unique employee code
     */
    public static function generateEmployeeCode(int $merchantId): string
    {
        do {
            $code = 'EMP' . str_pad($merchantId, 3, '0', STR_PAD_LEFT) . mt_rand(1000, 9999);
        } while (self::where('employee_code', $code)->exists());

        return $code;
    }

    /**
     * Get employee's current shift
     */
    public function getCurrentShift()
    {
        return $this->shifts()
            ->where('shift_date', now()->toDateString())
            ->where('status', 'in_progress')
            ->first();
    }

    /**
     * Check if employee is currently working
     */
    public function isWorking(): bool
    {
        return $this->getCurrentShift() !== null;
    }

    /**
     * Get total hours worked this month
     */
    public function getMonthlyHours(): float
    {
        return $this->shifts()
            ->where('shift_date', '>=', now()->startOfMonth())
            ->where('shift_date', '<=', now()->endOfMonth())
            ->where('status', 'completed')
            ->sum('total_hours') ?? 0;
    }

    /**
     * Get employee performance metrics
     */
    public function getPerformanceMetrics(): array
    {
        $now = now();
        $startOfMonth = $now->startOfMonth();
        
        return [
            'monthly_hours' => $this->getMonthlyHours(),
            'total_sales' => $this->activities()
                ->where('activity_type', 'sale')
                ->where('activity_time', '>=', $startOfMonth)
                ->sum('amount') ?? 0,
            'total_bookings' => $this->activities()
                ->where('activity_type', 'booking')
                ->where('activity_time', '>=', $startOfMonth)
                ->count(),
            'total_refunds' => $this->activities()
                ->where('activity_type', 'refund')
                ->where('activity_time', '>=', $startOfMonth)
                ->sum('amount') ?? 0,
        ];
    }

    /**
     * Log an activity for this employee
     */
    public function logActivity(string $type, string $description, array $metadata = [], ?float $amount = null): void
    {
        $this->activities()->create([
            'activity_type' => $type,
            'description' => $description,
            'metadata' => $metadata,
            'amount' => $amount,
            'activity_time' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
