<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 100, 15000);
        $gatewayFee = $amount * 0.029; // 2.9% gateway fee
        $platformFee = $amount * 0.025; // 2.5% platform fee

        $transactionIds = [
            'pi_3M'.$this->faker->randomNumber(8).'_'.$this->faker->randomNumber(6),
            'txn_'.$this->faker->randomNumber(8),
            'pay_'.$this->faker->randomNumber(10),
            'stripe_'.$this->faker->randomNumber(8),
            'hyperpay_'.$this->faker->randomNumber(8),
            'tap_'.$this->faker->randomNumber(8),
        ];

        $paymentMethods = ['card', 'bank_transfer', 'apple_pay', 'stc_pay', 'mada'];
        $cardBrands = ['visa', 'mastercard', 'amex', 'mada'];
        $bankNames = [
            'البنك الأهلي التجاري',
            'بنك الراجحي',
            'بنك الرياض',
            'البنك السعودي للاستثمار',
            'البنك السعودي الفرنسي',
            'بنك ساب',
            'البنك الأول',
        ];

        return [
            'booking_id' => Booking::factory(),
            'payment_gateway_id' => PaymentGateway::factory(),
            'transaction_id' => $this->faker->randomElement($transactionIds),
            'amount' => $amount,
            'currency' => 'SAR',
            'payment_method' => $this->faker->randomElement($paymentMethods),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed', 'cancelled', 'refunded']),
            'gateway_response' => json_encode([
                'id' => $this->faker->uuid(),
                'status' => 'succeeded',
                'amount' => $amount * 100, // Amount in halalas for Stripe
                'currency' => 'sar',
                'payment_method' => $this->faker->randomElement($paymentMethods),
                'created' => now()->timestamp,
                'metadata' => [
                    'booking_id' => null, // Will be set when booking is created
                    'platform' => 'shubak_tickets',
                ],
            ]),
            'processed_at' => function (array $attributes) {
                return in_array($attributes['status'], ['completed', 'failed'])
                    ? $this->faker->dateTimeBetween('-30 days', 'now')
                    : null;
            },
            'failed_at' => function (array $attributes) {
                return $attributes['status'] === 'failed'
                    ? $this->faker->dateTimeBetween('-30 days', 'now')
                    : null;
            },
            'failure_reason' => function (array $attributes) {
                if ($attributes['status'] === 'failed') {
                    return $this->faker->randomElement([
                        'insufficient_funds',
                        'card_declined',
                        'expired_card',
                        'invalid_cvc',
                        'network_error',
                        'processing_error',
                    ]);
                }

                return null;
            },
            'refunded_at' => function (array $attributes) {
                return $attributes['status'] === 'refunded'
                    ? $this->faker->dateTimeBetween('-15 days', 'now')
                    : null;
            },
            'refund_amount' => function (array $attributes) {
                return $attributes['status'] === 'refunded'
                    ? $this->faker->randomFloat(2, $attributes['amount'] * 0.5, $attributes['amount'])
                    : null;
            },
            'refund_reason' => function (array $attributes) {
                if ($attributes['status'] === 'refunded') {
                    return $this->faker->randomElement([
                        'customer_request',
                        'cancelled_booking',
                        'duplicate_charge',
                        'merchant_request',
                        'technical_issue',
                    ]);
                }

                return null;
            },
            'gateway_fee' => round($gatewayFee, 2),
            'platform_fee' => round($platformFee, 2),
            'net_amount' => round($amount - $gatewayFee - $platformFee, 2),

            // Payment method specific details
            'card_last4' => function (array $attributes) {
                return in_array($attributes['payment_method'], ['card', 'apple_pay', 'mada'])
                    ? $this->faker->numerify('####')
                    : null;
            },
            'card_brand' => function (array $attributes) {
                return in_array($attributes['payment_method'], ['card', 'apple_pay', 'mada'])
                    ? $this->faker->randomElement($cardBrands)
                    : null;
            },
            'bank_name' => function (array $attributes) {
                return $attributes['payment_method'] === 'bank_transfer'
                    ? $this->faker->randomElement($bankNames)
                    : null;
            },

            'receipt_url' => function () {
                return $this->faker->optional(0.8)->url();
            },
            'receipt_number' => $this->faker->unique()->numerify('RCP-########'),

            'metadata' => json_encode([
                'ip_address' => $this->faker->ipv4(),
                'user_agent' => $this->faker->userAgent(),
                'platform' => 'web',
                'payment_flow' => $this->faker->randomElement(['direct', 'redirect', 'hosted']),
            ]),
        ];
    }

    /**
     * Indicate that the payment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'processed_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'failed_at' => null,
            'failure_reason' => null,
        ]);
    }

    /**
     * Indicate that the payment failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'failed_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'failure_reason' => $this->faker->randomElement([
                'insufficient_funds',
                'card_declined',
                'expired_card',
                'invalid_cvc',
            ]),
            'processed_at' => null,
        ]);
    }

    /**
     * Indicate that the payment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'processed_at' => null,
            'failed_at' => null,
            'failure_reason' => null,
        ]);
    }

    /**
     * Indicate that the payment is refunded.
     */
    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'refunded',
            'refunded_at' => $this->faker->dateTimeBetween('-15 days', 'now'),
            'refund_amount' => $attributes['amount'] ?? $this->faker->randomFloat(2, 100, 1000),
            'refund_reason' => 'customer_request',
        ]);
    }

    /**
     * Set specific payment method.
     */
    public function cardPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'card',
            'card_last4' => $this->faker->numerify('####'),
            'card_brand' => $this->faker->randomElement(['visa', 'mastercard', 'amex']),
            'bank_name' => null,
        ]);
    }

    /**
     * Set MADA payment method.
     */
    public function madaPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'mada',
            'card_last4' => $this->faker->numerify('####'),
            'card_brand' => 'mada',
            'bank_name' => null,
        ]);
    }

    /**
     * Set bank transfer payment method.
     */
    public function bankTransfer(): static
    {
        $bankNames = [
            'البنك الأهلي التجاري',
            'بنك الراجحي',
            'بنك الرياض',
            'البنك السعودي للاستثمار',
        ];

        return $this->state(fn (array $attributes) => [
            'payment_method' => 'bank_transfer',
            'bank_name' => $this->faker->randomElement($bankNames),
            'card_last4' => null,
            'card_brand' => null,
        ]);
    }

    /**
     * Set STC Pay payment method.
     */
    public function stcPay(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'stc_pay',
            'card_last4' => null,
            'card_brand' => null,
            'bank_name' => null,
        ]);
    }

    /**
     * Create payment for specific booking.
     */
    public function forBooking(Booking $booking): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_id' => $booking->id,
            'amount' => $booking->total_amount,
        ]);
    }

    /**
     * Create payment with specific gateway.
     */
    public function withGateway(PaymentGateway $gateway): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_gateway_id' => $gateway->id,
        ]);
    }
}
