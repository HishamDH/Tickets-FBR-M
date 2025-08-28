<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'name',
        'description',
        'location',
        'price',
        'price_type',
        'category',
        'service_type',
        'pricing_model',
        'base_price',
        'currency',
        'duration_hours',
        'capacity',
        'features',
        'images',
        'status',
        'online_booking_enabled',
        'is_featured',
        'is_active',
        'is_available',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'base_price' => 'decimal:2',
        'features' => 'array',
        'images' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'is_available' => 'boolean',
        'online_booking_enabled' => 'boolean',
    ];

    /**
     * Merchant relationship
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Bookings relationship
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Service availability relationship
     */
    public function availability(): HasMany
    {
        return $this->hasMany(ServiceAvailability::class);
    }

    /**
     * Get formatted price
     */
    public function getPriceFormattedAttribute()
    {
        $price = $this->base_price ?? $this->price;
        $formattedPrice = number_format($price, 0) . ' ' . ($this->currency ?? 'ريال');
        
        if ($this->pricing_model === 'per_person' || $this->price_type === 'per_person') {
            return $formattedPrice . ' / شخص';
        } elseif ($this->pricing_model === 'per_hour' || $this->price_type === 'per_hour') {
            return $formattedPrice . ' / ساعة';
        }
        
        return $formattedPrice;
    }

    /**
     * Check if service is bookable
     */
    public function isBookable(): bool
    {
        return $this->is_active && 
               $this->is_available && 
               $this->online_booking_enabled &&
               $this->merchant->isVerified();
    }

    /**
     * Get category in Arabic
     */
    public function getCategoryArabicAttribute(): string
    {
        $categories = [
            'venues' => 'قاعات ومواقع',
            'catering' => 'تموين وضيافة',
            'photography' => 'تصوير',
            'beauty' => 'تجميل',
            'entertainment' => 'ترفيه',
            'transportation' => 'نقل ومواصلات',
            'security' => 'أمن وحراسة',
            'flowers_invitations' => 'ورود ودعوات',
        ];

        return $categories[$this->category] ?? $this->category;
    }

    /**
     * Get service type in Arabic
     */
    public function getServiceTypeNameAttribute(): string
    {
        return match($this->service_type) {
            'event' => 'فعالية',
            'exhibition' => 'معرض', 
            'restaurant' => 'مطعم',
            'experience' => 'تجربة',
            default => $this->service_type
        };
    }

    /**
     * Get pricing model in Arabic
     */
    public function getPricingModelNameAttribute(): string
    {
        return match($this->pricing_model) {
            'fixed' => 'سعر ثابت',
            'per_person' => 'لكل شخص',
            'per_table' => 'لكل طاولة', 
            'hourly' => 'بالساعة',
            'package' => 'باقة',
            default => $this->pricing_model
        };
    }

    /**
     * Scope queries to active services
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope queries to featured services
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope queries to services of specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('service_type', $type);
    }
}
