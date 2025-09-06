<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Merchant;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bookingDate = $this->faker->dateTimeBetween('-30 days', '+60 days');
        $bookingTime = $this->faker->time('H:i');

        $guestCount = $this->faker->numberBetween(10, 200);
        $totalAmount = $this->faker->randomFloat(2, 500, 25000);
        $commissionRate = $this->faker->randomFloat(2, 2.0, 8.0);
        $commissionAmount = $totalAmount * ($commissionRate / 100);

        $customerNames = [
            'أحمد محمد السعد',
            'فاطمة علي الزهراني',
            'محمد عبدالله القحطاني',
            'نورا سعد العتيبي',
            'خالد أحمد المطيري',
            'هند محمد الشهري',
            'عبدالرحمن يوسف الغامدي',
            'مريم حسن العمري',
            'فيصل سلمان الدوسري',
            'رهف عبدالعزيز الراشد',
            'ماجد فهد السبيعي',
            'لينا طارق الحربي',
            'سلطان ناصر العجمي',
            'دانة عمر البقمي',
            'وليد صالح الشمري',
        ];

        $phoneNumbers = [
            '0501234567', '0551234567', '0561234567', '0591234567',
            '0502345678', '0552345678', '0562345678', '0592345678',
            '0503456789', '0553456789', '0563456789', '0593456789',
        ];

        $specialRequests = [
            'تنسيق طاولات دائرية',
            'موسيقى هادئة',
            'إضاءة خافتة ورومانسية',
            'تنسيق ورود حمراء',
            'تقديم حلويات شرقية',
            'خدمة فاليه للسيارات',
            'منطقة ألعاب للأطفال',
            'ديكور تراثي سعودي',
            'بوفيه مفتوح',
            'كاميرا مراقبة إضافية',
            'مكان للتدخين منفصل',
            'تكييف إضافي',
            'منطقة استقبال VIP',
        ];

        return [
            'booking_number' => 'TKT-' . $this->faker->unique()->numberBetween(100000, 999999),
            'qr_code' => $this->faker->unique()->uuid(),
            'customer_id' => $this->faker->boolean(70) ? User::factory()->customer() : null,
            'service_id' => Service::factory(),
            'bookable_type' => $this->faker->randomElement(['App\\Models\\Service', 'App\\Models\\Offering']),
            'bookable_id' => function (array $attributes) {
                if ($attributes['bookable_type'] === 'App\\Models\\Service') {
                    return $attributes['service_id'] ?? Service::factory()->create()->id;
                } else {
                    return \App\Models\Offering::factory()->create()->id;
                }
            },
            'merchant_id' => function (array $attributes) {
                // Get merchant from service if service is created, otherwise create new merchant
                if (isset($attributes['service_id']) && is_numeric($attributes['service_id'])) {
                    $service = Service::find($attributes['service_id']);

                    return $service ? $service->merchant_id : Merchant::factory();
                }

                return Merchant::factory();
            },
            'booking_date' => $bookingDate,
            'booking_time' => $bookingTime,
            'guest_count' => $guestCount,
            'total_amount' => $totalAmount,
            'commission_amount' => round($commissionAmount, 2),
            'commission_rate' => $commissionRate,
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed', 'refunded']),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'completed', 'cancelled', 'no_show']),
            'booking_source' => $this->faker->randomElement(['online', 'manual', 'pos']),
            'special_requests' => $this->faker->optional(0.6)->randomElement($specialRequests),
            'cancellation_reason' => $this->faker->optional(0.1)->randomElement([
                'تغيير في الموعد',
                'ظروف طارئة',
                'عدم توفر الميزانية',
                'اختيار مكان آخر',
                'تأجيل الحدث',
            ]),
            'cancelled_at' => function (array $attributes) {
                return $attributes['status'] === 'cancelled' ? $this->faker->dateTimeBetween('-10 days', 'now') : null;
            },
            'cancelled_by' => function (array $attributes) {
                return $attributes['status'] === 'cancelled' ? ($attributes['customer_id'] ?? User::factory()->customer()) : null;
            },

            // For non-registered customers
            'customer_name' => function (array $attributes) use ($customerNames) {
                return $attributes['customer_id'] ? null : $this->faker->randomElement($customerNames);
            },
            'customer_phone' => function (array $attributes) use ($phoneNumbers) {
                return $attributes['customer_id'] ? null : $this->faker->randomElement($phoneNumbers);
            },
            'customer_email' => function (array $attributes) {
                return $attributes['customer_id'] ? null : $this->faker->optional(0.7)->safeEmail();
            },

            'number_of_people' => $guestCount,
            'number_of_tables' => $this->faker->optional(0.5)->numberBetween(5, 25),
            'duration_hours' => $this->faker->optional(0.7)->numberBetween(3, 12),
            'notes' => $this->faker->optional(0.4)->sentence(),
        ];
    }

    /**
     * Indicate that the booking is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Indicate that the booking is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'payment_status' => 'paid',
            'booking_date' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }

    /**
     * Indicate that the booking is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancellation_reason' => $this->faker->randomElement([
                'تغيير في الخطط',
                'ظروف طارئة',
                'مشكلة في التوقيت',
            ]),
            'cancelled_at' => $this->faker->dateTimeBetween('-5 days', 'now'),
        ]);
    }

    /**
     * Indicate that the booking is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
    }

    /**
     * Indicate that the booking is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Indicate that this is an upcoming booking.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_date' => $this->faker->dateTimeBetween('+1 day', '+60 days'),
            'status' => $this->faker->randomElement(['confirmed', 'pending']),
        ]);
    }

    /**
     * Indicate that this is a past booking.
     */
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_date' => $this->faker->dateTimeBetween('-60 days', '-1 day'),
            'status' => $this->faker->randomElement(['completed', 'cancelled']),
        ]);
    }

    /**
     * Set a specific customer for the booking.
     */
    public function forCustomer(User $customer): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_id' => $customer->id,
            'customer_name' => null,
            'customer_phone' => null,
            'customer_email' => null,
        ]);
    }

    /**
     * Set a specific service for the booking.
     */
    public function forService(Service $service): static
    {
        return $this->state(fn (array $attributes) => [
            'service_id' => $service->id,
            'merchant_id' => $service->merchant_id,
        ]);
    }

    /**
     * Create booking for guest (non-registered customer).
     */
    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'customer_id' => null,
        ]);
    }
}
