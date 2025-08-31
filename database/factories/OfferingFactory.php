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
        $offeringTypes = ['events', 'conference', 'restaurant', 'experiences'];
        $statuses = ['active', 'inactive'];

        return [
            'name' => $this->faker->bs,
            'location' => $this->faker->address,
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10, 500),
            'start_time' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'end_time' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'status' => $this->faker->randomElement($statuses),
            'type' => $this->faker->randomElement($offeringTypes),
            'category' => Category::inRandomOrder()->first()->name ?? 'Default Category',
            'user_id' => User::where('user_type', 'merchant')->inRandomOrder()->first()->id,
            'has_chairs' => $this->faker->boolean,
            'chairs_count' => $this->faker->numberBetween(50, 500),
        ];
    }
}