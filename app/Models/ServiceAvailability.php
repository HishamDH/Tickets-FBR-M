<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $service_id
 * @property string $availability_date
 * @property mixed|null $start_time
 * @property mixed|null $end_time
 * @property int|null $available_slots
 * @property int $booked_slots
 * @property int $is_available
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Service $service
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereAvailabilityDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereAvailableSlots($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereBookedSlots($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereIsAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAvailability whereUpdatedAt($value)
 * @mixin \Eloquent
 * @mixin IdeHelperServiceAvailability
 */
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
