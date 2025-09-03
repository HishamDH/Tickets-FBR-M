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
        $totalAmount = $amount + $gatewayFee + $platformFee;

        $transactionIds = [
            'pi_3M'.$this->faker->numberBetween(10000000, 99999999).'_'.$this->faker->numberBetween(100000, 999999),
            'txn_'.$this->faker->numberBetween(10000000, 99999999),
            'pay_'.$this->faker->numberBetween(1000000000, 9999999999),
            'stripe_'.$this->faker->numberBetween(10000000, 99999999),
            'hyperpay_'.$this->faker->numberBetween(10000000, 99999999),
            'tap_'.$this->faker->numberBetween(10000000, 99999999),
        ];

        $paymentMethods = ['card', 'digital_wallet', 'bank_transfer', 'cash'];

        return [
            'payment_number' => 'PAY-'.date('Y').'-'.str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT),
            'booking_id' => Booking::factory(),
            'merchant_id' => function (array $attributes) {
                if (isset($attributes['booking_id']) && is_numeric($attributes['booking_id'])) {
                    $booking = Booking::find($attributes['booking_id']);

                    return $booking ? $booking->merchant_id : 1;
                }

                return 1; // Default merchant
            },
            'payment_gateway_id' => PaymentGateway::factory(),
            'customer_id' => function (array $attributes) {
                if (isset($attributes['booking_id']) && is_numeric($attributes['booking_id'])) {
                    $booking = Booking::find($attributes['booking_id']);

                    return $booking ? $booking->customer_id : null;
                }

                return null;
            },
            'amount' => $amount,
            'gateway_fee' => round($gatewayFee, 2),
            'platform_fee' => round($platformFee, 2),
            'total_amount' => round($totalAmount, 2),
            'currency' => 'SAR',
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded']),
            'payment_method' => $this->faker->randomElement($paymentMethods),
            'gateway_transaction_id' => $this->faker->randomElement($transactionIds),
            'gateway_reference' => 'REF-'.$this->faker->numberBetween(10000000, 99999999),
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
            'gateway_metadata' => json_encode([
                'ip_address' => $this->faker->ipv4(),
                'user_agent' => $this->faker->userAgent(),
                'platform' => 'web',
                'payment_flow' => $this->faker->randomElement(['direct', 'redirect', 'hosted']),
            ]),
            'initiated_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'completed_at' => function (array $attributes) {
                return in_array($attributes['status'], ['completed', 'refunded'])
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
            'customer_ip' => $this->faker->ipv4(),
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the payment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'completed_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
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
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the payment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'completed_at' => null,
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
            'completed_at' => $this->faker->dateTimeBetween('-15 days', 'now'),
            'notes' => 'Refunded: customer_request',
        ]);
    }

    /**
     * Set specific payment method.
     */
    public function cardPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'card',
        ]);
    }

    /**
     * Set digital wallet payment method.
     */
    public function digitalWallet(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'digital_wallet',
        ]);
    }

    /**
     * Set bank transfer payment method.
     */
    public function bankTransfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'bank_transfer',
        ]);
    }

    /**
     * Set cash payment method.
     */
    public function cash(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'cash',
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
