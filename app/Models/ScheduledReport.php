<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'report_type',
        'frequency',
        'options',
        'next_run_at',
        'last_run_at',
        'run_count',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'options' => 'array',
        'next_run_at' => 'datetime',
        'last_run_at' => 'datetime',
        'is_active' => 'boolean',
        'run_count' => 'integer',
    ];

    /**
     * Partner relationship
     */
    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Creator relationship
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if report is due for execution
     */
    public function isDue(): bool
    {
        return $this->is_active && 
               $this->next_run_at && 
               $this->next_run_at->isPast();
    }

    /**
     * Get frequency display name
     */
    public function getFrequencyNameAttribute(): string
    {
        return match($this->frequency) {
            'daily' => 'يومي',
            'weekly' => 'أسبوعي',
            'monthly' => 'شهري',
            'quarterly' => 'ربع سنوي',
            'yearly' => 'سنوي',
            default => 'غير محدد',
        };
    }

    /**
     * Get report type display name
     */
    public function getReportTypeNameAttribute(): string
    {
        return match($this->report_type) {
            'comprehensive' => 'تقرير شامل',
            'financial' => 'تقرير مالي',
            'performance' => 'تقرير أداء',
            'merchants' => 'تقرير التجار',
            'commission' => 'تقرير العمولات',
            default => 'تقرير عام',
        };
    }

    /**
     * Scope for active reports
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for due reports
     */
    public function scopeDue($query)
    {
        return $query->where('is_active', true)
                    ->where('next_run_at', '<=', now());
    }

    /**
     * Scope for specific frequency
     */
    public function scopeFrequency($query, string $frequency)
    {
        return $query->where('frequency', $frequency);
    }
}
