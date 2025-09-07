<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Offering>
 */
class OfferingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1, // Use existing user
            'name' => 'Test Offering',
            'description' => 'Test Description',
            'price' => 100.00,
            'category' => 'events',
            'status' => 'active',
            'max_capacity' => 100,
            'start_time' => now()->addWeek(),
            'end_time' => now()->addMonth(),
            'location' => 'Test Location',
            'image' => 'test.jpg',
        ];
    }
}
