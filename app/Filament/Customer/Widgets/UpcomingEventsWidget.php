<?php

namespace App\Filament\Customer\Widgets;

use App\Models\Offering;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class UpcomingEventsWidget extends Widget
{
    protected static string $view = 'filament.customer.widgets.upcoming-events';

    protected int | string | array $columnSpan = [
        'default' => 1,
        'sm' => 2,
        'lg' => 2,
    ];

    public function getUpcomingEvents(): Collection
    {
        return Offering::where('type', 'event')
            ->where('status', 'active')
            ->where('start_date', '>=', now())
            ->with(['user', 'category'])
            ->withAvg('reviews', 'rating')
            ->orderBy('start_date')
            ->limit(6)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'image' => $event->image ? asset('storage/' . $event->image) : asset('images/placeholder-event.jpg'),
                    'start_date' => $event->start_date,
                    'end_date' => $event->end_date,
                    'location' => $event->location ?? 'Online Event',
                    'price' => $event->price,
                    'merchant' => $event->user->name ?? 'Unknown Merchant',
                    'category' => $event->category->name ?? 'General',
                    'rating' => round($event->reviews_avg_rating ?? 0, 1),
                    'available_quantity' => $event->available_quantity,
                    'booking_url' => route('services.show', $event->id),
                ];
            });
    }

    public function getViewData(): array
    {
        return [
            'events' => $this->getUpcomingEvents(),
            'title' => 'Upcoming Events',
            'subtitle' => 'Don\'t miss these amazing events',
        ];
    }
}
