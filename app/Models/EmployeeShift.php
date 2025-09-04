<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_employee_id',
        'shift_date',
        'start_time',
        'end_time',
        'actual_start_time',
        'actual_end_time',
        'break_duration',
        'notes',
        'status',
        'total_hours',
        'overtime_hours',
    ];

    protected $casts = [
        'shift_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'actual_start_time' => 'datetime:H:i',
        'actual_end_time' => 'datetime:H:i',
        'break_duration' => 'decimal:2',
        'total_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
    ];

    // Status constants
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the merchant employee that owns this shift
     */
    public function merchantEmployee(): BelongsTo
    {
        return $this->belongsTo(MerchantEmployee::class);
    }

    /**
     * Calculate total hours worked
     */
    public function calculateTotalHours(): void
    {
        if ($this->actual_start_time && $this->actual_end_time) {
            $start = \Carbon\Carbon::parse($this->actual_start_time);
            $end = \Carbon\Carbon::parse($this->actual_end_time);
            
            $totalMinutes = $end->diffInMinutes($start);
            $breakMinutes = ($this->break_duration ?? 0) * 60;
            
            $workedMinutes = $totalMinutes - $breakMinutes;
            $this->total_hours = round($workedMinutes / 60, 2);
            
            // Calculate overtime (assuming 8 hours is standard)
            $standardHours = 8;
            $this->overtime_hours = max(0, $this->total_hours - $standardHours);
            
            $this->save();
        }
    }

    /**
     * Start the shift
     */
    public function start(): void
    {
        $this->update([
            'actual_start_time' => now()->format('H:i'),
            'status' => self::STATUS_IN_PROGRESS,
        ]);
    }

    /**
     * End the shift
     */
    public function end(): void
    {
        $this->update([
            'actual_end_time' => now()->format('H:i'),
            'status' => self::STATUS_COMPLETED,
        ]);
        
        $this->calculateTotalHours();
    }

    /**
     * Get available statuses
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    /**
     * Check if shift is late
     */
    public function isLate(): bool
    {
        if (!$this->actual_start_time || !$this->start_time) {
            return false;
        }
        
        $scheduled = \Carbon\Carbon::parse($this->start_time);
        $actual = \Carbon\Carbon::parse($this->actual_start_time);
        
        return $actual->gt($scheduled->addMinutes(5)); // 5 minute grace period
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDuration(): string
    {
        if (!$this->total_hours) {
            return 'N/A';
        }
        
        $hours = floor($this->total_hours);
        $minutes = ($this->total_hours - $hours) * 60;
        
        return sprintf('%dh %dm', $hours, round($minutes));
    }
}
