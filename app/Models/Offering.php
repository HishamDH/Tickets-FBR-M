<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offering extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'description',
        'image',
        'price',
        'start_time',
        'end_time',
        'status',
        'type',
        'category',
        'additional_data',
        'translations',
        'has_chairs',
        'chairs_count',
        'user_id',
        'features',
    ];

    protected $casts = [
        'additional_data' => 'array',
        'translations' => 'array',
        'features' => 'array',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'has_chairs' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * User relationship - the merchant who owns this offering
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Reservations relationship - bookings for this offering
     */
    public function reservations()
    {
        return $this->hasMany(PaidReservation::class, 'item_id');
    }

    /**
     * Reviews relationship - customer ratings for this offering
     */
    public function reviews()
    {
        return $this->hasMany(CustomerRating::class, 'service_id');
    }

    /**
     * Check if offering is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if offering is published
     */
    public function isPublished(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get merchant/owner of this offering
     */
    public function merchant()
    {
        return $this->user();
    }
}
