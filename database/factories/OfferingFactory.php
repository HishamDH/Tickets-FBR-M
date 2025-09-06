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
        $categories = ['حفلات', 'مؤتمرات', 'مطاعم', 'تجارب', 'ورش عمل', 'رياضة', 'ثقافة'];

        $startTime = $this->faker->dateTimeBetween('+1 week', '+1 month');
        $endTime = $this->faker->dateTimeBetween($startTime, '+2 months');

        return [
            'name' => $this->faker->randomElement([
                'حفل موسيقي للفنان محمد عبده',
                'مؤتمر التكنولوجيا والابتكار',
                'ورشة تطوير الذات',
                'معرض الفنون التشكيلية',
                'بطولة كرة القدم',
                'ندوة ريادة الأعمال',
                'مهرجان الطعام السعودي',
                'عرض مسرحي كوميدي'
            ]),
            'location' => $this->faker->randomElement([
                'مركز الملك فهد الثقافي - الرياض',
                'قاعة الأمير سلطان - جدة',
                'مركز الرياض الدولي للمؤتمرات',
                'مدينة الملك عبدالعزيز للعلوم والتقنية',
                'جامعة الملك سعود',
                'مركز حي الملك سلمان',
                'فندق الفيصلية',
                'برج المملكة'
            ]),
            'description' => $this->faker->paragraph(3),
            'image' => $this->faker->imageUrl(800, 600, 'events'),
            'price' => $this->faker->randomFloat(2, 50, 1000),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => $this->faker->randomElement($statuses),
            'type' => $this->faker->randomElement($offeringTypes),
            'category' => $this->faker->randomElement($categories),
            'user_id' => User::where('user_type', 'merchant')->inRandomOrder()->first()?->id ?? User::factory(),
            'has_chairs' => $this->faker->boolean(80),
            'chairs_count' => $this->faker->numberBetween(50, 500),
            'additional_data' => json_encode([
                'tags' => $this->faker->words(3),
                'difficulty_level' => $this->faker->randomElement(['مبتدئ', 'متوسط', 'متقدم']),
                'language' => 'العربية',
                'includes_materials' => $this->faker->boolean(),
                'includes_certificate' => $this->faker->boolean(),
                'min_age' => $this->faker->numberBetween(5, 18),
                'max_age' => $this->faker->numberBetween(16, 65)
            ]),
            'translations' => json_encode([
                'ar' => [
                    'name' => $this->faker->sentence(3),
                    'description' => $this->faker->paragraph()
                ],
                'en' => [
                    'name' => $this->faker->sentence(3),
                    'description' => $this->faker->paragraph()
                ]
            ]),
            'features' => json_encode([
                'parking' => $this->faker->boolean(),
                'wifi' => $this->faker->boolean(),
                'refreshments' => $this->faker->boolean(),
                'air_conditioning' => $this->faker->boolean(),
                'wheelchair_accessible' => $this->faker->boolean()
            ])
        ];
    }
}
