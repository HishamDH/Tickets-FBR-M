<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $saudiPhones = [
            '0501234567', '0551234567', '0561234567', '0591234567',
            '0502345678', '0552345678', '0562345678', '0592345678',
            '0503456789', '0553456789', '0563456789', '0593456789',
        ];

        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;

        return [
            'f_name' => $firstName,
            'l_name' => $lastName,
            'name' => $firstName.' '.$lastName, // Set name directly here
            'email' => fake()->unique()->safeEmail(),
            'phone' => $this->faker->randomElement($saudiPhones),
            'user_type' => 'customer',
            'status' => 'active',
            'language' => 'ar',
            'timezone' => 'Asia/Riyadh',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'push_notifications_enabled' => $this->faker->boolean(80),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (User $user) {
            //
        })->afterCreating(function (User $user) {
            // No longer needed to set name here
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create an admin user.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'admin',
        ]);
    }

    /**
     * Create a merchant user.
     */
    public function merchant(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'merchant',
        ]);
    }

    /**
     * Create a customer user.
     */
    public function customer(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'customer',
        ]);
    }

    /**
     * Create a partner user.
     */
    public function partner(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'partner',
        ]);
    }
}
