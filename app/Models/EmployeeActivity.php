<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_employee_id',
        'activity_type',
        'description',
        'metadata',
        'amount',
        'activity_time',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
        'amount' => 'decimal:2',
        'activity_time' => 'datetime',
    ];

    // Activity type constants
    const TYPE_LOGIN = 'login';
    const TYPE_LOGOUT = 'logout';
    const TYPE_SALE = 'sale';
    const TYPE_BOOKING = 'booking';
    const TYPE_REFUND = 'refund';
    const TYPE_PAYMENT = 'payment';
    const TYPE_VOID = 'void';
    const TYPE_DISCOUNT = 'discount';
    const TYPE_INVENTORY = 'inventory';
    const TYPE_SERVICE = 'service';

    /**
     * Get the merchant employee that owns this activity
     */
    public function merchantEmployee(): BelongsTo
    {
        return $this->belongsTo(MerchantEmployee::class);
    }

    /**
     * Get available activity types
     */
    public static function getActivityTypes(): array
    {
        return [
            self::TYPE_LOGIN => 'Login',
            self::TYPE_LOGOUT => 'Logout',
            self::TYPE_SALE => 'Sale',
            self::TYPE_BOOKING => 'Booking',
            self::TYPE_REFUND => 'Refund',
            self::TYPE_PAYMENT => 'Payment',
            self::TYPE_VOID => 'Void',
            self::TYPE_DISCOUNT => 'Discount',
            self::TYPE_INVENTORY => 'Inventory',
            self::TYPE_SERVICE => 'Service',
        ];
    }

    /**
     * Log a new activity
     */
    public static function log(
        int $merchantEmployeeId,
        string $type,
        string $description,
        array $metadata = [],
        ?float $amount = null
    ): self {
        return self::create([
            'merchant_employee_id' => $merchantEmployeeId,
            'activity_type' => $type,
            'description' => $description,
            'metadata' => $metadata,
            'amount' => $amount,
            'activity_time' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        if (!$this->amount) {
            return 'N/A';
        }
        
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Get activity icon based on type
     */
    public function getIconAttribute(): string
    {
        return match($this->activity_type) {
            self::TYPE_LOGIN => 'fas fa-sign-in-alt',
            self::TYPE_LOGOUT => 'fas fa-sign-out-alt',
            self::TYPE_SALE => 'fas fa-cash-register',
            self::TYPE_BOOKING => 'fas fa-calendar-check',
            self::TYPE_REFUND => 'fas fa-undo',
            self::TYPE_PAYMENT => 'fas fa-credit-card',
            self::TYPE_VOID => 'fas fa-ban',
            self::TYPE_DISCOUNT => 'fas fa-percentage',
            self::TYPE_INVENTORY => 'fas fa-boxes',
            self::TYPE_SERVICE => 'fas fa-cogs',
            default => 'fas fa-circle',
        };
    }

    /**
     * Get activity color based on type
     */
    public function getColorAttribute(): string
    {
        return match($this->activity_type) {
            self::TYPE_LOGIN => 'success',
            self::TYPE_LOGOUT => 'warning',
            self::TYPE_SALE => 'primary',
            self::TYPE_BOOKING => 'info',
            self::TYPE_REFUND => 'danger',
            self::TYPE_PAYMENT => 'success',
            self::TYPE_VOID => 'danger',
            self::TYPE_DISCOUNT => 'warning',
            self::TYPE_INVENTORY => 'secondary',
            self::TYPE_SERVICE => 'info',
            default => 'light',
        };
    }
}
