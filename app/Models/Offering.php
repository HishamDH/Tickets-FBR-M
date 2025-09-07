<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offering extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'category',
        'status',
        'location',
        'image',
        'start_time',
        'end_time',
        'type',
        'additional_data',
        'translations',
        'has_chairs',
        'chairs_count',
        'features',
        'max_capacity',
        'min_capacity',
        'allow_overbooking',
        'overbooking_percentage',
        'capacity_type',
        'buffer_capacity'
    ];

    protected $casts = [
        'has_chairs' => 'boolean',
        'allow_overbooking' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'price' => 'decimal:2',
        'additional_data' => 'array',
        'translations' => 'array',
        'features' => 'array',
        'chairs_count' => 'integer',
        'max_capacity' => 'integer',
        'min_capacity' => 'integer',
        'overbooking_percentage' => 'decimal:2',
        'buffer_capacity' => 'integer',
    ];

    protected $dates = [
        'start_time',
        'end_time',
        'deleted_at'
    ];

    /**
     * Get is_active attribute based on status field
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get the user that owns the offering.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the merchant that owns the offering.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the category of the offering.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the bookings for the offering.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the reviews for the offering.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Scope a query to only include active offerings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured offerings.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope a query to only include available offerings.
     */
    public function scopeAvailable($query)
    {
        return $query->where('available_slots', '>', 0)
                    ->where('start_date', '>', now())
                    ->where('is_active', true);
    }

    /**
     * Get the average rating of the offering.
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Get the total number of reviews.
     */
    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->count();
    }

    /**
     * Check if the offering has available slots.
     */
    public function hasAvailableSlots(): bool
    {
        return $this->available_slots > 0;
    }

    /**
     * Check if the offering is bookable.
     */
    public function isBookable(): bool
    {
        return $this->is_active && 
               $this->hasAvailableSlots() && 
               $this->start_date > now();
    }

    /**
     * Decrease available slots when booking is made.
     */
    public function decreaseSlots(int $quantity = 1): bool
    {
        if ($this->available_slots >= $quantity) {
            $this->decrement('available_slots', $quantity);
            return true;
        }
        return false;
    }

    /**
     * Increase available slots when booking is cancelled.
     */
    public function increaseSlots(int $quantity = 1): void
    {
        $this->increment('available_slots', $quantity);
    }
}
