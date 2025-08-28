<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'available_date',
        'start_time',
        'end_time',
        'available_slots',
        'booked_slots',
        'price_override',
        'status',
    ];

    protected $casts = [
        'available_date' => 'date',
        'start_time' => 'time',
        'end_time' => 'time',
        'price_override' => 'decimal:2',
    ];

    /**
     * Service relationship
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Check if slots are available
     */
    public function hasAvailableSlots(): bool
    {
        return $this->available_slots > $this->booked_slots;
    }

    /**
     * Get remaining slots
     */
    public function getRemainingSlots(): int
    {
        return max(0, $this->available_slots - $this->booked_slots);
    }
}
