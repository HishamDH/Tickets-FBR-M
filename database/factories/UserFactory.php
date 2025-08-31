<?php

namespace Database\Factories;

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
        $arabicNames = [
            'أحمد محمد العلي',
            'فاطمة سعد الأحمد',
            'محمد عبدالله القحطاني',
            'نورا علي العتيبي',
            'خالد أحمد المطيري',
            'هند محمد الشهري',
            'عبدالرحمن يوسف الغامدي',
            'مريم حسن العمري',
            'فيصل سلمان الدوسري',
            'رهف عبدالعزيز الراشد'
        ];

        $saudiPhones = [
            '0501234567', '0551234567', '0561234567', '0591234567',
            '0502345678', '0552345678', '0562345678', '0592345678',
            '0503456789', '0553456789', '0563456789', '0593456789'
        ];

        return [
            'name' => $this->faker->randomElement($arabicNames),
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
            'name' => $this->faker->randomElement([
                'أحمد محمد الإدارة',
                'فاطمة علي التطوير',
                'محمد سعد التقنية',
                'نورا خالد العمليات'
            ]),
        ]);
    }

    /**
     * Create a merchant user.
     */
    public function merchant(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'merchant',
            'name' => $this->faker->randomElement([
                'أحمد محمد التجاري',
                'فاطمة علي الأعمال',
                'محمد سعد الخدمات',
                'نورا خالد المناسبات',
                'خالد أحمد الضيافة'
            ]),
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
            'name' => $this->faker->randomElement([
                'أحمد محمد الشراكات',
                'فاطمة علي التسويق',
                'محمد سعد المبيعات',
                'نورا خالد التطوير'
            ]),
        ]);
    }
}
