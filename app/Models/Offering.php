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
        'category_id',
        'is_active',
        'capacity',
        'available_slots',
        'start_date',
        'end_date',
        'location',
        'image_url',
        'terms_conditions',
        'cancellation_policy',
        'min_booking_time',
        'max_booking_time',
        'auto_accept',
        'featured',
        'sort_order',
        'meta_data'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'auto_accept' => 'boolean',
        'featured' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'price' => 'decimal:2',
        'meta_data' => 'array',
        'min_booking_time' => 'integer',
        'max_booking_time' => 'integer',
        'capacity' => 'integer',
        'available_slots' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'deleted_at'
    ];

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
