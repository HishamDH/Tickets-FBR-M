<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition()
    {
        return [
            'customer_id' => User::factory()->customer(),
            'merchant_id' => User::factory()->merchant(),
            'title' => $this->faker->sentence,
            'type' => 'merchant_customer',
            'status' => 'active',
        ];
    }
}
