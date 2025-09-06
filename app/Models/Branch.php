<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $location
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Branch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Branch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Branch query()
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Branch whereUserId($value)
 * @mixin \Eloquent
 * @mixin IdeHelperBranch
 */
class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'phone',
        'location',
        'city',
        'state',
        'postal_code',
        'country',
        'is_active',
        'manager_name',
        'manager_phone',
        'manager_email',
        'opening_hours',
        'capacity',
        'services_offered',
        'description',
        'images',
        'coordinates',
        'additional_data',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_hours' => 'array',
        'services_offered' => 'array',
        'images' => 'array',
        'coordinates' => 'array',
        'additional_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function offerings()
    {
        return $this->belongsToMany(Offering::class, 'branch_offerings');
    }

    public function bookings()
    {
        return $this->hasMany(PaidReservation::class, 'branch_id');
    }

    public function employees()
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForMerchant($query, $merchantId)
    {
        return $query->where('user_id', $merchantId);
    }

    // Helper methods
    public function isOpen(): bool
    {
        if (!$this->is_active || !$this->opening_hours) {
            return false;
        }

        $today = strtolower(now()->format('l')); // monday, tuesday, etc.
        $currentTime = now()->format('H:i');
        
        $todayHours = $this->opening_hours[$today] ?? null;
        
        if (!$todayHours || empty($todayHours)) {
            return false;
        }

        foreach ($todayHours as $timeSlot) {
            if (isset($timeSlot['open']) && isset($timeSlot['close'])) {
                if ($currentTime >= $timeSlot['open'] && $currentTime <= $timeSlot['close']) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    public function getActiveServicesCountAttribute(): int
    {
        return $this->offerings()->where('status', 'active')->count();
    }

    public function getTotalBookingsAttribute(): int
    {
        return $this->bookings()->count();
    }

    public function getThisMonthBookingsAttribute(): int
    {
        return $this->bookings()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }
}
