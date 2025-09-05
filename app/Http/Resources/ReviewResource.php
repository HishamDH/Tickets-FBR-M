<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'rating' => $this->rating,
            'rating_display' => $this->getRatingDisplay(),
            'comment' => $this->comment,
            'is_verified' => $this->is_verified ?? false,
            'is_featured' => $this->is_featured ?? false,
            'helpful_votes' => $this->helpful_votes ?? 0,
            
            // Customer information (limited for privacy)
            'customer_name' => $this->customer?->name ? substr($this->customer->name, 0, 1) . '***' : 'Anonymous',
            'customer_initial' => $this->customer?->name ? strtoupper(substr($this->customer->name, 0, 1)) : 'A',
            
            // Service information
            'service_id' => $this->service_id,
            'service_name' => $this->when($this->relationLoaded('service'), $this->service?->name),
            
            // Booking information (if available)
            'booking_id' => $this->booking_id,
            'booking_verified' => $this->booking_id ? true : false,
            
            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Human-readable time
            'created_at_human' => $this->created_at?->diffForHumans(),
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
                'rating_color' => match (true) {
                    $this->rating >= 4.5 => '#28A745', // Green
                    $this->rating >= 3.5 => '#FFC107', // Yellow
                    $this->rating >= 2.5 => '#FD7E14', // Orange
                    default => '#DC3545' // Red
                },
                'rating_text' => match (true) {
                    $this->rating == 5 => 'Excellent',
                    $this->rating >= 4 => 'Very Good',
                    $this->rating >= 3 => 'Good',
                    $this->rating >= 2 => 'Fair',
                    default => 'Poor'
                },
                'verified_badge' => $this->is_verified,
                'featured_badge' => $this->is_featured,
                'has_booking' => $this->booking_id ? true : false,
            ]
        ];
    }

    /**
     * Get rating display with stars
     */
    private function getRatingDisplay(): string
    {
        $fullStars = floor($this->rating);
        $halfStar = ($this->rating - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
        
        $display = str_repeat('★', $fullStars);
        if ($halfStar) {
            $display .= '☆';
        }
        $display .= str_repeat('☆', $emptyStars);
        
        return $display . ' (' . number_format($this->rating, 1) . ')';
    }
}