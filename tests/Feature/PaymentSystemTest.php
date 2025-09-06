<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentSystemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->event = Event::factory()->create();
        $this->ticket = Ticket::factory()->create([
            'event_id' => $this->event->id,
            'price' => 150
        ]);
        
        $this->booking = Booking::factory()->create([
            'user_id' => $this->customer->id,
            'ticket_id' => $this->ticket->id,
            'quantity' => 2,
            'total_amount' => 300,
            'status' => 'pending'
        ]);
    }

    public function test_customer_can_view_payment_page()
    {
        $response = $this->actingAs($this->customer)
                        ->get("/payments/{$this->booking->id}");

        $response->assertStatus(200);
        $response->assertSee('300'); // Total amount
        $response->assertSee($this->event->title);
    }

    public function test_customer_can_process_credit_card_payment()
    {
        $paymentData = [
            'booking_id' => $this->booking->id,
            'payment_method' => 'credit_card',
            'amount' => 300,
            'card_number' => '4111111111111111',
            'card_holder' => 'John Doe',
            'expiry_month' => '12',
            'expiry_year' => '2025',
            'cvv' => '123'
        ];

        $response = $this->actingAs($this->customer)
                        ->post('/payments/process', $paymentData);

        $response->assertRedirect();
        $this->assertDatabaseHas('payments', [
            'booking_id' => $this->booking->id,
            'amount' => 300,
            'payment_method' => 'credit_card',
            'status' => 'completed'
        ]);
    }

    public function test_payment_updates_booking_status()
    {
        $paymentData = [
            'booking_id' => $this->booking->id,
            'payment_method' => 'credit_card',
            'amount' => 300
        ];

        $this->actingAs($this->customer)
             ->post('/payments/process', $paymentData);

        $this->booking->refresh();
        $this->assertEquals('confirmed', $this->booking->status);
    }

    public function test_customer_can_pay_with_bank_transfer()
    {
        $paymentData = [
            'booking_id' => $this->booking->id,
            'payment_method' => 'bank_transfer',
            'amount' => 300,
            'bank_name' => 'Saudi National Bank',
            'account_number' => '1234567890'
        ];

        $response = $this->actingAs($this->customer)
                        ->post('/payments/process', $paymentData);

        $response->assertRedirect();
        $this->assertDatabaseHas('payments', [
            'booking_id' => $this->booking->id,
            'payment_method' => 'bank_transfer',
            'status' => 'pending'
        ]);
    }

    public function test_customer_can_pay_with_mada()
    {
        $paymentData = [
            'booking_id' => $this->booking->id,
            'payment_method' => 'mada',
            'amount' => 300,
            'card_number' => '5123450000000008'
        ];

        $response = $this->actingAs($this->customer)
                        ->post('/payments/process', $paymentData);

        $response->assertRedirect();
        $this->assertDatabaseHas('payments', [
            'booking_id' => $this->booking->id,
            'payment_method' => 'mada'
        ]);
    }

    public function test_payment_validation_for_invalid_amount()
    {
        $paymentData = [
            'booking_id' => $this->booking->id,
            'payment_method' => 'credit_card',
            'amount' => 100, // Wrong amount
        ];

        $response = $this->actingAs($this->customer)
                        ->post('/payments/process', $paymentData);

        $response->assertSessionHasErrors(['amount']);
    }

    public function test_payment_validation_for_invalid_card()
    {
        $paymentData = [
            'booking_id' => $this->booking->id,
            'payment_method' => 'credit_card',
            'amount' => 300,
            'card_number' => '1234', // Invalid card
        ];

        $response = $this->actingAs($this->customer)
                        ->post('/payments/process', $paymentData);

        $response->assertSessionHasErrors(['card_number']);
    }

    public function test_customer_can_view_payment_history()
    {
        $payment = Payment::factory()->create([
            'booking_id' => $this->booking->id,
            'amount' => 300,
            'status' => 'completed'
        ]);

        $response = $this->actingAs($this->customer)
                        ->get('/payments/history');

        $response->assertStatus(200);
        $response->assertSee('300');
        $response->assertSee($payment->transaction_id);
    }

    public function test_refund_processing()
    {
        $payment = Payment::factory()->create([
            'booking_id' => $this->booking->id,
            'amount' => 300,
            'status' => 'completed'
        ]);

        $response = $this->actingAs($this->customer)
                        ->post("/payments/{$payment->id}/refund");

        $response->assertRedirect();
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'refunded'
        ]);
    }

    public function test_partial_refund_processing()
    {
        $payment = Payment::factory()->create([
            'booking_id' => $this->booking->id,
            'amount' => 300,
            'status' => 'completed'
        ]);

        $refundData = [
            'amount' => 150,
            'reason' => 'Customer request'
        ];

        $response = $this->actingAs($this->customer)
                        ->post("/payments/{$payment->id}/partial-refund", $refundData);

        $response->assertRedirect();
        $this->assertDatabaseHas('refunds', [
            'payment_id' => $payment->id,
            'amount' => 150,
            'reason' => 'Customer request'
        ]);
    }

    public function test_payment_receipt_generation()
    {
        $payment = Payment::factory()->create([
            'booking_id' => $this->booking->id,
            'amount' => 300,
            'status' => 'completed'
        ]);

        $response = $this->actingAs($this->customer)
                        ->get("/payments/{$payment->id}/receipt");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_merchant_can_view_payment_reports()
    {
        $merchant = User::factory()->create(['role' => 'merchant']);
        $merchantEvent = Event::factory()->create(['merchant_id' => $merchant->id]);
        
        $payment = Payment::factory()->create([
            'booking_id' => $this->booking->id,
            'amount' => 300,
            'status' => 'completed'
        ]);

        $response = $this->actingAs($merchant)
                        ->get('/merchant/payments/reports');

        $response->assertStatus(200);
    }

    public function test_admin_can_view_all_payments()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $payment = Payment::factory()->create([
            'booking_id' => $this->booking->id,
            'amount' => 300,
            'status' => 'completed'
        ]);

        $response = $this->actingAs($admin)
                        ->get('/admin/payments');

        $response->assertStatus(200);
        $response->assertSee('300');
    }

    public function test_payment_webhook_processing()
    {
        $webhookData = [
            'transaction_id' => 'TXN_12345',
            'booking_id' => $this->booking->id,
            'status' => 'completed',
            'amount' => 300
        ];

        $response = $this->post('/payments/webhook', $webhookData, [
            'X-Webhook-Signature' => 'valid_signature'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('payments', [
            'transaction_id' => 'TXN_12345',
            'status' => 'completed'
        ]);
    }
}
