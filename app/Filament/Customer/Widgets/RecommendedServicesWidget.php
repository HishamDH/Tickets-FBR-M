<?php

namespace App\Filament\Customer\Widgets;

use App\Models\Booking;
use App\Models\Offering;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class RecommendedServicesWidget extends Widget
{
    protected static string $view = 'filament.customer.widgets.recommended-services';

    protected int | string | array $columnSpan = [
        'default' => 1,
        'sm' => 2,
        'lg' => 2,
    ];

    public function getRecommendedServices(): Collection
    {
        $user = Auth::user();
        
        // Get user's booking history to recommend similar services
        $bookedServices = Booking::where('customer_id', $user->id)
            ->with('service.category')
            ->get()
            ->pluck('service.category.id')
            ->filter()
            ->unique();

        $query = Offering::where('type', 'service')
            ->where('status', 'active')
            ->with(['user', 'category'])
            ->withAvg('reviews', 'rating')
            ->withCount('bookings');

        // If user has booking history, prioritize similar categories
        if ($bookedServices->isNotEmpty()) {
            $query->whereIn('category_id', $bookedServices->toArray());
        }

        return $query->orderByDesc('bookings_count')
            ->orderByDesc('reviews_avg_rating')
            ->limit(4)
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'title' => $service->title,
                    'description' => $service->description,
                    'image' => $service->image ? asset('storage/' . $service->image) : asset('images/placeholder-service.jpg'),
                    'price' => $service->price,
                    'merchant' => $service->user->name ?? 'Unknown Merchant',
                    'merchant_logo' => $service->user->avatar ? asset('storage/' . $service->user->avatar) : null,
                    'category' => $service->category->name ?? 'General',
                    'rating' => round($service->reviews_avg_rating ?? 0, 1),
                    'bookings_count' => $service->bookings_count ?? 0,
                    'available' => $service->available_quantity > 0,
                    'booking_url' => route('services.show', $service->id),
                    'features' => $this->getServiceFeatures($service),
                ];
            });
    }

    protected function getServiceFeatures($service): array
    {
        $features = [];
        
        if ($service->available_quantity > 0) {
            $features[] = 'Available Now';
        }
        
        if ($service->reviews_avg_rating >= 4.5) {
            $features[] = 'Highly Rated';
        }
        
        if ($service->bookings_count >= 50) {
            $features[] = 'Popular Choice';
        }
        
        if ($service->price <= 50) {
            $features[] = 'Budget Friendly';
        }
        
        return array_slice($features, 0, 2); // Limit to 2 features
    }

    public function getViewData(): array
    {
        return [
            'services' => $this->getRecommendedServices(),
            'title' => 'Recommended for You',
            'subtitle' => 'Based on your preferences and activity',
        ];
    }
}
