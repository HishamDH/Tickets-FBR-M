<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateBookingsServiceIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing bookings to set service_id from polymorphic relationship
        $bookings = Booking::where('bookable_type', Service::class)
                          ->whereNull('service_id')
                          ->get();

        $this->command->info("Found {$bookings->count()} bookings to update...");

        foreach ($bookings as $booking) {
            $booking->update(['service_id' => $booking->bookable_id]);
            $this->command->info("Updated booking #{$booking->id}");
        }

        $this->command->info("Successfully updated {$bookings->count()} bookings!");
    }
}
