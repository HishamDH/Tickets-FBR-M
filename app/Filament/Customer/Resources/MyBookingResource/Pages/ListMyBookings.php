<?php

namespace App\Filament\Customer\Resources\MyBookingResource\Pages;

use App\Filament\Customer\Resources\MyBookingResource;
use Filament\Resources\Pages\ListRecords;

class ListMyBookings extends ListRecords
{
    protected static string $resource = MyBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action since bookings are made through the booking form
        ];
    }

    public function getTitle(): string
    {
        return 'My Bookings';
    }
}
