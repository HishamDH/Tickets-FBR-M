<?php

namespace Database\Factories;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Merchant>
 */
class MerchantFactory extends Factory
{
    protected $model = Merchant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $businessTypes = [
            'شركة تنظيم فعاليات',
            'مؤسسة خدمات ضيافة',
            'شركة تموين وطعام',
            'استوديو تصوير',
            'شركة تأجير قاعات',
            'مكتب تنسيق أفراح',
            'مؤسسة فنون وترفيه',
            'شركة خدمات لوجستية',
            'مكتب تنظيم مؤتمرات',
            'شركة ديكور ومناسبات'
        ];

        $businessNames = [
            'مؤسسة الأحلام الذهبية',
            'شركة النجوم المتميزة',
            'مجموعة الإبداع اللامحدود',
            'مؤسسة الفخامة والأناقة',
            'شركة اللمسة السحرية',
            'مكتب الأحداث الراقية',
            'مؤسسة البريق الماسي',
            'شركة الذوق الرفيع',
            'مجموعة الإتقان والجودة',
            'مؤسسة الإبداع الفني',
            'شركة المناسبات الذهبية',
            'مكتب الفعاليات المتميزة',
            'مؤسسة الضيافة الراقية',
            'شركة الأحلام المحققة',
            'مجموعة التميز والإبداع'
        ];

        $cities = [
            'الرياض', 'جدة', 'الدمام', 'مكة المكرمة', 'المدينة المنورة', 
            'الطائف', 'أبها', 'تبوك', 'الخبر', 'القطيف', 'حائل', 'الجبيل',
            'بريدة', 'خميس مشيط', 'الهفوف', 'المبرز', 'نجران', 'ينبع',
            'عنيزة', 'الباحة', 'سكاكا', 'جازان', 'عرعر', 'القريات'
        ];

        $businessAddresses = [
            'شارع الملك فهد، حي العليا',
            'طريق الملك عبدالعزيز، حي السلامة',
            'شارع الأمير محمد بن عبدالعزيز',
            'طريق الملك فيصل، حي النزهة',
            'شارع التحلية، حي الحمراء',
            'طريق الدمام السريع',
            'شارع الملك خالد، حي المروج',
            'طريق الرياض - الخرج',
            'شارع العروبة، حي الياسمين',
            'طريق الملك فهد، حي الورود'
        ];

        return [
            'user_id' => User::factory()->merchant(),
            'business_name' => $this->faker->randomElement($businessNames),
            'business_type' => $this->faker->randomElement($businessTypes),
            'cr_number' => $this->faker->unique()->numerify('##########'),
            'business_address' => $this->faker->randomElement($businessAddresses) . '، ' . $this->faker->streetAddress(),
            'city' => $this->faker->randomElement($cities),
            'verification_status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'commission_rate' => $this->faker->randomFloat(2, 2.0, 10.0),
            'partner_id' => null, // Will be set later if needed
            'account_manager_id' => null, // Will be set later if needed
            'settings' => json_encode([
                'notification_preferences' => [
                    'email_notifications' => true,
                    'sms_notifications' => true,
                    'booking_alerts' => true,
                    'payment_alerts' => true,
                ],
                'business_hours' => [
                    'sunday' => ['open' => '09:00', 'close' => '22:00'],
                    'monday' => ['open' => '09:00', 'close' => '22:00'],
                    'tuesday' => ['open' => '09:00', 'close' => '22:00'],
                    'wednesday' => ['open' => '09:00', 'close' => '22:00'],
                    'thursday' => ['open' => '09:00', 'close' => '22:00'],
                    'friday' => ['open' => '14:00', 'close' => '23:00'],
                    'saturday' => ['open' => '09:00', 'close' => '22:00'],
                ],
                'auto_confirm_bookings' => $this->faker->boolean(60),
                'advance_booking_days' => $this->faker->numberBetween(1, 90),
                'cancellation_policy' => $this->faker->randomElement([
                    'flexible', 'moderate', 'strict'
                ]),
                'languages' => ['ar', 'en'],
            ]),
        ];
    }

    /**
     * Indicate that the merchant is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'approved',
        ]);
    }

    /**
     * Indicate that the merchant is pending approval.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'pending',
        ]);
    }

    /**
     * Indicate that the merchant is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => 'rejected',
        ]);
    }

    /**
     * Set a specific commission rate.
     */
    public function withCommissionRate(float $rate): static
    {
        return $this->state(fn (array $attributes) => [
            'commission_rate' => $rate,
        ]);
    }

    /**
     * Set a specific city.
     */
    public function inCity(string $city): static
    {
        return $this->state(fn (array $attributes) => [
            'city' => $city,
        ]);
    }

    /**
     * Associate merchant with a specific user.
     */
    public function forUser($user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => is_object($user) ? $user->id : $user,
        ]);
    }
}
