<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $merchant_id
 * @property string $name
 * @property string $description
 * @property string $location
 * @property string $price
 * @property string|null $base_price
 * @property string $currency
 * @property int|null $duration_hours
 * @property int|null $capacity
 * @property array|null $features
 * @property string $price_type
 * @property string $pricing_model
 * @property string $category
 * @property string $service_type
 * @property string|null $image
 * @property array|null $images
 * @property bool $is_featured
 * @property bool $is_available
 * @property bool $is_active
 * @property string $status
 * @property bool $online_booking_enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ServiceAvailability> $availability
 * @property-read int|null $availability_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read string $category_arabic
 * @property-read mixed $price_formatted
 * @property-read string $pricing_model_name
 * @property-read string $service_type_name
 * @property-read \App\Models\User|null $merchant
 * @method static \Illuminate\Database\Eloquent\Builder|Service active()
 * @method static \Database\Factories\ServiceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Service featured()
 * @method static \Illuminate\Database\Eloquent\Builder|Service newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Service newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Service ofType($type)
 * @method static \Illuminate\Database\Eloquent\Builder|Service query()
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereBasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereDurationHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereFeatures($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereIsAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereOnlineBookingEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service wherePriceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service wherePricingModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereServiceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Service whereUpdatedAt($value)
 * @mixin \Eloquent
 * @mixin IdeHelperService
 */
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
        // Merged from Offering
        'start_time',
        'end_time',
        'additional_data',
        'translations',
        'has_chairs',
        'chairs_count',
        'max_capacity',
        'min_capacity',
        'allow_overbooking',
        'overbooking_percentage',
        'capacity_type',
        'buffer_capacity',
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
        // Merged from Offering
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'additional_data' => 'array',
        'translations' => 'array',
        'has_chairs' => 'boolean',
        'allow_overbooking' => 'boolean',
        'overbooking_percentage' => 'decimal:2',
    ];

    /**
     * Merchant relationship
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    /**
     * Bookings relationship - using direct service_id for better performance
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'service_id');
    }

    /**
     * Service availability relationship
     */
    public function availability(): HasMany
    {
        return $this->hasMany(ServiceAvailability::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function activeBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'service_id')->whereIn('status', ['pending', 'confirmed']);
    }

    /**
     * Users who favorited this service
     */
    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_favorite_services')
            ->withTimestamps();
    }

    /**
     * Get formatted price
     */
    public function getPriceFormattedAttribute()
    {
        $price = $this->base_price ?? $this->price;
        $formattedPrice = number_format($price, 0).' '.($this->currency ?? 'ريال');

        if ($this->pricing_model === 'per_person' || $this->price_type === 'per_person') {
            return $formattedPrice.' / شخص';
        } elseif ($this->pricing_model === 'per_hour' || $this->price_type === 'per_hour') {
            return $formattedPrice.' / ساعة';
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
               $this->merchant && 
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
        return match ($this->service_type) {
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
        return match ($this->pricing_model) {
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
