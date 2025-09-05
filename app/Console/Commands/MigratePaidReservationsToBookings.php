<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\PaidReservation;
use App\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigratePaidReservationsToBookings extends Command
{
    protected $signature = 'app:migrate-paid-reservations';
    protected $description = 'Migrate data from the old paid_reservations table to the bookings table and update existing bookings.';

    public function handle()
    {
        $this->info('Starting data migration...');

        DB::transaction(function () {
            $this->migratePaidReservations();
            $this->updateExistingBookings();
        });

        $this->info('Data migration completed successfully!');
    }

    private function migratePaidReservations()
    {
        $this->line('Migrating paid reservations...');
        $paidReservations = PaidReservation::all();
        $bar = $this->output->createProgressBar($paidReservations->count());
        $bar->start();

        foreach ($paidReservations as $pr) {
            Booking::create([
                'bookable_id' => $pr->item_id,
                'bookable_type' => $pr->item_type,
                'customer_id' => $pr->user_id,
                'merchant_id' => $pr->item ? $pr->item->user_id : null, // Assuming item has a user_id (merchant)
                'booking_date' => $pr->booking_date,
                'booking_time' => $pr->booking_time,
                'guest_count' => $pr->guest_count,
                'total_amount' => $pr->total_amount,
                'commission_amount' => 0, // Default value
                'commission_rate' => 0, // Default value
                'payment_status' => $pr->payment_status,
                'status' => 'confirmed', // Paid reservations are likely confirmed
                'booking_source' => 'migrated', // Default value
                'reservation_status' => $pr->reservation_status,
                'special_requests' => $pr->special_requests,
                'qr_code' => $pr->qr_code,
                'discount' => $pr->discount,
                'code' => $pr->code,
                'pos_terminal_id' => $pr->pos_terminal_id,
                'pos_data' => $pr->pos_data,
                'printed_at' => $pr->printed_at,
                'print_count' => $pr->print_count,
                'is_offline_transaction' => $pr->is_offline_transaction,
                'offline_transaction_id' => $pr->offline_transaction_id,
                'synced_at' => $pr->synced_at,
                'created_at' => $pr->created_at,
                'updated_at' => $pr->updated_at,
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }

    private function updateExistingBookings()
    {
        $this->line('Updating existing bookings with polymorphic data...');
        $bookingsToUpdate = Booking::whereNotNull('service_id')->get();
        $bar = $this->output->createProgressBar($bookingsToUpdate->count());
        $bar->start();

        foreach ($bookingsToUpdate as $booking) {
            $booking->bookable_id = $booking->service_id;
            $booking->bookable_type = Service::class;
            $booking->save();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
    }
}
