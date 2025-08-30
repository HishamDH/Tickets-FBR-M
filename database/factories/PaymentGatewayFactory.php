<?php

namespace Database\Factories;

use App\Models\PaymentGateway;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentGateway>
 */
class PaymentGatewayFactory extends Factory
{
    protected $model = PaymentGateway::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gateways = [
            'stripe' => [
                'name' => 'Stripe',
                'display_name' => 'Stripe (بطاقات ائتمانية)',
                'type' => 'card',
                'fee_percentage' => 2.9,
                'fee_fixed' => 0.30,
                'currencies' => ['SAR', 'USD', 'EUR'],
                'payment_methods' => ['card', 'apple_pay', 'google_pay'],
                'config' => [
                    'public_key' => 'pk_test_' . $this->faker->bothify('?????????????????????'),
                    'secret_key' => 'sk_test_' . $this->faker->bothify('?????????????????????'),
                    'webhook_secret' => 'whsec_' . $this->faker->bothify('????????????????'),
                    'api_version' => '2022-11-15',
                    'capture_method' => 'automatic',
                    'payment_methods' => ['card', 'apple_pay']
                ]
            ],
            'hyperpay' => [
                'name' => 'HyperPay',
                'display_name' => 'HyperPay (MADA & فيزا)',
                'type' => 'card',
                'fee_percentage' => 2.75,
                'fee_fixed' => 0.00,
                'currencies' => ['SAR'],
                'payment_methods' => ['mada', 'visa', 'mastercard'],
                'config' => [
                    'entity_id' => $this->faker->bothify('?.##############'),
                    'access_token' => $this->faker->bothify('??????????????????????????'),
                    'test_mode' => true,
                    'webhook_secret' => $this->faker->bothify('????????????????'),
                    'payment_brands' => ['MADA', 'VISA', 'MASTER'],
                    'checkout_mode' => 'redirect'
                ]
            ],
            'tap' => [
                'name' => 'Tap Payments',
                'display_name' => 'Tap (جميع البطاقات)',
                'type' => 'multi',
                'fee_percentage' => 2.85,
                'fee_fixed' => 0.00,
                'currencies' => ['SAR', 'KWD', 'AED'],
                'payment_methods' => ['knet', 'benefit', 'mada', 'visa', 'mastercard'],
                'config' => [
                    'api_key' => 'sk_test_' . $this->faker->bothify('????????????????'),
                    'public_key' => 'pk_test_' . $this->faker->bothify('????????????????'),
                    'secret_key' => $this->faker->bothify('????????????????'),
                    'webhook_secret' => $this->faker->bothify('????????????????'),
                    'payment_methods' => ['src_kw.knet', 'src_bh.benefit', 'src_sa.mada'],
                    'save_card' => true
                ]
            ],
            'paypal' => [
                'name' => 'PayPal',
                'display_name' => 'PayPal (حساب PayPal)',
                'type' => 'wallet',
                'fee_percentage' => 3.4,
                'fee_fixed' => 0.35,
                'currencies' => ['USD', 'EUR', 'SAR'],
                'payment_methods' => ['paypal', 'credit', 'debit'],
                'config' => [
                    'client_id' => $this->faker->bothify('???????????????????????????????'),
                    'client_secret' => $this->faker->bothify('???????????????????????????????'),
                    'webhook_id' => $this->faker->bothify('??????????????'),
                    'mode' => 'sandbox',
                    'currency' => 'SAR',
                    'locale' => 'ar_SA'
                ]
            ],
            'stc_pay' => [
                'name' => 'STC Pay',
                'display_name' => 'STC Pay (محفظة STC)',
                'type' => 'wallet',
                'fee_percentage' => 0.0,
                'fee_fixed' => 0.0,
                'currencies' => ['SAR'],
                'payment_methods' => ['stc_pay'],
                'config' => [
                    'merchant_id' => $this->faker->numerify('########'),
                    'api_key' => $this->faker->bothify('????????????????'),
                    'secret_key' => $this->faker->bothify('????????????????'),
                    'webhook_secret' => $this->faker->bothify('????????????????'),
                    'environment' => 'sandbox'
                ]
            ]
        ];

        $gatewayKey = $this->faker->randomElement(array_keys($gateways));
        $gatewayData = $gateways[$gatewayKey];

        return [
            'name' => $gatewayData['name'],
            'slug' => $gatewayKey,
            'display_name' => $gatewayData['display_name'],
            'description' => $this->getGatewayDescription($gatewayKey),
            'type' => $gatewayData['type'],
            'is_active' => $this->faker->boolean(80),
            'is_test_mode' => $this->faker->boolean(70),
            'fee_percentage' => $gatewayData['fee_percentage'],
            'fee_fixed' => $gatewayData['fee_fixed'],
            'min_amount' => $this->faker->randomFloat(2, 1, 50),
            'max_amount' => $this->faker->randomFloat(2, 10000, 100000),
            'supported_currencies' => $gatewayData['currencies'],
            'supported_payment_methods' => $gatewayData['payment_methods'],
            'config' => $gatewayData['config'],
            'webhook_url' => route('webhooks.payment', ['gateway' => $gatewayKey], false),
            'webhook_secret' => $gatewayData['config']['webhook_secret'] ?? null,
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }

    /**
     * Get gateway description based on gateway key.
     */
    private function getGatewayDescription(string $gateway): string
    {
        $descriptions = [
            'stripe' => 'بوابة دفع عالمية تدعم جميع البطاقات الائتمانية والمحافظ الرقمية',
            'hyperpay' => 'بوابة دفع سعودية متخصصة في MADA والبطاقات المحلية',
            'tap' => 'بوابة دفع خليجية تدعم جميع وسائل الدفع في المنطقة',
            'paypal' => 'محفظة رقمية عالمية للدفع الآمن عبر الإنترنت',
            'stc_pay' => 'محفظة STC الرقمية للعملاء في المملكة العربية السعودية'
        ];

        return $descriptions[$gateway] ?? 'بوابة دفع إلكتروني';
    }

    /**
     * Indicate that the gateway is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the gateway is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the gateway is in test mode.
     */
    public function testMode(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_test_mode' => true,
        ]);
    }

    /**
     * Indicate that the gateway is in production mode.
     */
    public function production(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_test_mode' => false,
        ]);
    }

    /**
     * Create Stripe gateway.
     */
    public function stripe(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Stripe',
            'slug' => 'stripe',
            'display_name' => 'Stripe (بطاقات ائتمانية)',
            'type' => 'card',
            'fee_percentage' => 2.9,
            'fee_fixed' => 0.30,
            'supported_currencies' => ['SAR', 'USD', 'EUR'],
            'supported_payment_methods' => ['card', 'apple_pay', 'google_pay'],
        ]);
    }

    /**
     * Create HyperPay gateway.
     */
    public function hyperpay(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'HyperPay',
            'slug' => 'hyperpay',
            'display_name' => 'HyperPay (MADA & فيزا)',
            'type' => 'card',
            'fee_percentage' => 2.75,
            'fee_fixed' => 0.00,
            'supported_currencies' => ['SAR'],
            'supported_payment_methods' => ['mada', 'visa', 'mastercard'],
        ]);
    }

    /**
     * Create Tap Payments gateway.
     */
    public function tap(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Tap Payments',
            'slug' => 'tap',
            'display_name' => 'Tap (جميع البطاقات)',
            'type' => 'multi',
            'fee_percentage' => 2.85,
            'fee_fixed' => 0.00,
            'supported_currencies' => ['SAR', 'KWD', 'AED'],
            'supported_payment_methods' => ['knet', 'benefit', 'mada', 'visa', 'mastercard'],
        ]);
    }

    /**
     * Create STC Pay gateway.
     */
    public function stcPay(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'STC Pay',
            'slug' => 'stc_pay',
            'display_name' => 'STC Pay (محفظة STC)',
            'type' => 'wallet',
            'fee_percentage' => 0.0,
            'fee_fixed' => 0.0,
            'supported_currencies' => ['SAR'],
            'supported_payment_methods' => ['stc_pay'],
        ]);
    }
}
