<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_has_tickets_relationship()
    {
        $event = Event::factory()->create();
        $tickets = Ticket::factory()->count(3)->create(['event_id' => $event->id]);

        $this->assertCount(3, $event->tickets);
        $this->assertInstanceOf(Ticket::class, $event->tickets->first());
    }

    public function test_event_has_merchant_relationship()
    {
        $merchant = User::factory()->create(['role' => 'merchant']);
        $event = Event::factory()->create(['merchant_id' => $merchant->id]);

        $this->assertInstanceOf(User::class, $event->merchant);
        $this->assertEquals($merchant->id, $event->merchant->id);
    }

    public function test_event_scope_active()
    {
        Event::factory()->create(['status' => 'active']);
        Event::factory()->create(['status' => 'inactive']);
        Event::factory()->create(['status' => 'cancelled']);

        $activeEvents = Event::active()->get();
        
        $this->assertCount(1, $activeEvents);
        $this->assertEquals('active', $activeEvents->first()->status);
    }

    public function test_event_scope_upcoming()
    {
        Event::factory()->create(['date' => now()->addDays(10)]);
        Event::factory()->create(['date' => now()->subDays(10)]);

        $upcomingEvents = Event::upcoming()->get();
        
        $this->assertCount(1, $upcomingEvents);
    }

    public function test_event_total_tickets_available()
    {
        $event = Event::factory()->create();
        Ticket::factory()->create(['event_id' => $event->id, 'quantity' => 50]);
        Ticket::factory()->create(['event_id' => $event->id, 'quantity' => 30]);

        $this->assertEquals(80, $event->totalTicketsAvailable());
    }

    public function test_event_is_sold_out()
    {
        $event = Event::factory()->create();
        $ticket = Ticket::factory()->create([
            'event_id' => $event->id, 
            'quantity' => 0
        ]);

        $this->assertTrue($event->isSoldOut());
    }

    public function test_event_has_available_tickets()
    {
        $event = Event::factory()->create();
        Ticket::factory()->create([
            'event_id' => $event->id, 
            'quantity' => 10
        ]);

        $this->assertTrue($event->hasAvailableTickets());
    }

    public function test_event_minimum_price()
    {
        $event = Event::factory()->create();
        Ticket::factory()->create(['event_id' => $event->id, 'price' => 100]);
        Ticket::factory()->create(['event_id' => $event->id, 'price' => 50]);
        Ticket::factory()->create(['event_id' => $event->id, 'price' => 200]);

        $this->assertEquals(50, $event->minimumPrice());
    }

    public function test_event_maximum_price()
    {
        $event = Event::factory()->create();
        Ticket::factory()->create(['event_id' => $event->id, 'price' => 100]);
        Ticket::factory()->create(['event_id' => $event->id, 'price' => 50]);
        Ticket::factory()->create(['event_id' => $event->id, 'price' => 200]);

        $this->assertEquals(200, $event->maximumPrice());
    }

    public function test_event_can_be_booked()
    {
        $futureEvent = Event::factory()->create([
            'date' => now()->addDays(10),
            'status' => 'active'
        ]);
        Ticket::factory()->create([
            'event_id' => $futureEvent->id, 
            'quantity' => 10
        ]);

        $this->assertTrue($futureEvent->canBeBooked());
    }

    public function test_past_event_cannot_be_booked()
    {
        $pastEvent = Event::factory()->create([
            'date' => now()->subDays(10),
            'status' => 'active'
        ]);

        $this->assertFalse($pastEvent->canBeBooked());
    }

    public function test_event_days_until_event()
    {
        $event = Event::factory()->create([
            'date' => now()->addDays(5)
        ]);

        $this->assertEquals(5, $event->daysUntilEvent());
    }

    public function test_event_is_today()
    {
        $todayEvent = Event::factory()->create([
            'date' => now()
        ]);

        $this->assertTrue($todayEvent->isToday());
    }

    public function test_event_format_date()
    {
        $event = Event::factory()->create([
            'date' => '2024-12-25 15:30:00'
        ]);

        $formattedDate = $event->formatDate();
        $this->assertStringContainsString('2024', $formattedDate);
    }
}
