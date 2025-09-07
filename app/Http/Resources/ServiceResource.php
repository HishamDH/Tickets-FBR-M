<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'location' => $this->location,
            
            // Pricing information
            'price' => (float) $this->price,
            'base_price' => (float) $this->base_price,
            'price_formatted' => $this->price_formatted,
            'currency' => $this->currency,
            'price_type' => $this->price_type,
            'pricing_model' => $this->pricing_model,
            'pricing_model_name' => $this->pricing_model_name,
            
            // Service details
            'category' => $this->category,
            'category_arabic' => $this->category_arabic,
            'service_type' => $this->service_type,
            'service_type_name' => $this->service_type_name,
            'duration_hours' => $this->duration_hours,
            'capacity' => $this->capacity,
            'max_capacity' => $this->max_capacity,
            'min_capacity' => $this->min_capacity,
            'features' => $this->features,
            
            // Images
            'image' => $this->image ? url('storage/' . $this->image) : null,
            'images' => $this->images ? collect($this->images)->map(fn($img) => url('storage/' . $img))->toArray() : [],
            'gallery' => $this->images ? collect($this->images)->map(fn($img, $index) => [
                'id' => $index,
                'url' => url('storage/' . $img),
                'is_primary' => $index === 0
            ])->values()->toArray() : [],
            
            // Status and availability
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            'is_available' => $this->is_available,
            'online_booking_enabled' => $this->online_booking_enabled,
            'is_bookable' => $this->isBookable(),
            
            // Merchant information
            'merchant_id' => $this->merchant_id,
            'merchant' => $this->when($this->relationLoaded('merchant') && $this->merchant, [
                'id' => $this->merchant->id,
                'name' => $this->merchant->name,
                'business_name' => $this->merchant->business_name,
                'subdomain' => $this->merchant->subdomain,
                'store_url' => $this->merchant->store_url,
                'logo_url' => $this->merchant->logo_url ? url('storage/' . $this->merchant->logo_url) : null,
                'phone' => $this->merchant->phone,
                'city' => $this->merchant->business_city,
                'rating' => 4.5, // TODO: Calculate from reviews
                'total_reviews' => 0, // TODO: Calculate from reviews
            ]),
            
            // Seating and capacity management
            'has_chairs' => $this->has_chairs,
            'chairs_count' => $this->chairs_count,
            'capacity_type' => $this->capacity_type,
            'buffer_capacity' => $this->buffer_capacity,
            'allow_overbooking' => $this->allow_overbooking,
            'overbooking_percentage' => (float) $this->overbooking_percentage,
            
            // Time-based offerings (from merged Offering model)
            'start_time' => $this->start_time?->toISOString(),
            'end_time' => $this->end_time?->toISOString(),
            'additional_data' => $this->additional_data,
            'translations' => $this->translations,
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Relationships (when loaded)
            'bookings_count' => $this->when($this->relationLoaded('bookings'), $this->bookings_count),
            'availability' => $this->when($this->relationLoaded('availability'), ServiceAvailabilityResource::collection($this->availability)),
            'reviews' => $this->when($this->relationLoaded('reviews'), ReviewResource::collection($this->reviews)),
            'reviews_count' => $this->when($this->relationLoaded('reviews'), $this->reviews_count),
            'average_rating' => $this->when($this->relationLoaded('reviews'), $this->reviews->avg('rating')),
            
            // Calculated fields
            'available_spots' => $this->calculateAvailableSpots(),
            'next_available_date' => $this->getNextAvailableDate(),
            'booking_url' => route('services.book', $this->id),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'service_type_display' => match ($this->service_type) {
                    'event' => 'Event',
                    'exhibition' => 'Exhibition',
                    'restaurant' => 'Restaurant',
                    'experience' => 'Experience',
                    default => ucfirst($this->service_type ?? 'Service')
                },
                'category_display' => match ($this->category) {
                    'venues' => 'Venues & Locations',
                    'catering' => 'Catering & Hospitality',
                    'photography' => 'Photography',
                    'beauty' => 'Beauty Services',
                    'entertainment' => 'Entertainment',
                    'transportation' => 'Transportation',
                    'security' => 'Security Services',
                    'flowers_invitations' => 'Flowers & Invitations',
                    default => ucfirst($this->category ?? 'Other')
                },
                'pricing_display' => [
                    'type' => $this->pricing_model,
                    'min_price' => (float) ($this->base_price ?? $this->price),
                    'currency_symbol' => match ($this->currency) {
                        'SAR', 'ريال' => 'ر.س',
                        'USD' => '$',
                        'EUR' => '€',
                        default => $this->currency ?? 'ر.س'
                    },
                    'per_unit_text' => match ($this->pricing_model) {
                        'per_person' => 'per person',
                        'per_hour' => 'per hour',
                        'per_table' => 'per table',
                        'package' => 'package',
                        default => 'fixed price'
                    }
                ],
                'capacity_info' => [
                    'type' => $this->capacity_type ?? 'people',
                    'min' => $this->min_capacity,
                    'max' => $this->max_capacity,
                    'current' => $this->capacity,
                    'available' => $this->calculateAvailableSpots(),
                    'has_flexible_capacity' => $this->allow_overbooking
                ],
                'status_color' => match ($this->status) {
                    'active' => '#28A745',
                    'inactive' => '#6C757D',
                    'draft' => '#FFA500',
                    'archived' => '#DC3545',
                    default => '#6C757D'
                },
                'featured_badge' => $this->is_featured,
                'availability_status' => $this->is_available && $this->is_active ? 'available' : 'unavailable',
            ]
        ];
    }

    /**
     * Calculate available spots for the service
     */
    protected function calculateAvailableSpots(): ?int
    {
        if (!$this->capacity) {
            return null;
        }
        
        // TODO: Calculate based on existing bookings for current period
        // This is a simplified version - in reality, you'd need to check against
        // actual bookings for specific dates/times
        return $this->capacity;
    }

    /**
     * Get the next available date for booking
     */
    protected function getNextAvailableDate(): ?string
    {
        if (!$this->is_available || !$this->is_active || !$this->online_booking_enabled) {
            return null;
        }
        
        // TODO: Calculate based on actual availability and existing bookings
        // For now, return today if available, otherwise tomorrow
        return now()->addDay()->format('Y-m-d');
    }
}