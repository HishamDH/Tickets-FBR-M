<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Offering;
use App\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateOfferingsToServices extends Command
{
    protected $signature = 'app:migrate-offerings';
    protected $description = 'Migrate data from the old offerings table to the services table and update bookings.';

    public function handle()
    {
        $this->info('Starting offering to service data migration...');

        DB::transaction(function () {
            $offerings = Offering::all();
            $bar = $this->output->createProgressBar($offerings->count());
            $bar->start();

            foreach ($offerings as $offering) {
                // Create a new Service from the Offering
                $service = Service::create([
                    'merchant_id' => $offering->user_id,
                    'name' => $offering->name,
                    'description' => $offering->description,
                    'location' => $offering->location,
                    'price' => $offering->price,
                    'category' => $offering->category,
                    'service_type' => $offering->type, // Map type to service_type
                    'features' => $offering->features,
                    'status' => $offering->status,
                    'is_active' => $offering->status === 'active',
                    'is_available' => $offering->status === 'active',
                    'online_booking_enabled' => true, // Default
                    // Merged fields
                    'start_time' => $offering->start_time,
                    'end_time' => $offering->end_time,
                    'additional_data' => $offering->additional_data,
                    'translations' => $offering->translations,
                    'has_chairs' => $offering->has_chairs,
                    'chairs_count' => $offering->chairs_count,
                    'max_capacity' => $offering->max_capacity,
                    'min_capacity' => $offering->min_capacity,
                    'allow_overbooking' => $offering->allow_overbooking,
                    'overbooking_percentage' => $offering->overbooking_percentage,
                    'capacity_type' => $offering->capacity_type,
                    'buffer_capacity' => $offering->buffer_capacity,
                    'created_at' => $offering->created_at,
                    'updated_at' => $offering->updated_at,
                ]);

                // Update bookings that were pointing to this Offering
                Booking::where('bookable_type', Offering::class)
                       ->where('bookable_id', $offering->id)
                       ->update([
                           'bookable_type' => Service::class,
                           'bookable_id' => $service->id,
                           'service_id' => $service->id, // Also update the old service_id for now
                       ]);
                
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);
        });

        $this->info('Data migration from offerings to services completed successfully!');
    }
}
