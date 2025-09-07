<?php

namespace Database\Factories;

use App\Models\Offering;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition()
    {
        return [
            'offering_id' => Offering::factory(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 50, 500),
            'quantity' => $this->faker->numberBetween(100, 500),
            'available_quantity' => function (array $attributes) {
                return $attributes['quantity'];
            },
        ];
    }
}
