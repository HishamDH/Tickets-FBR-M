<?php

namespace App\Filament\Customer\Resources\BookingResource\Pages;

use App\Filament\Customer\Resources\BookingResource;
use Filament\Resources\Pages\ListRecords;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Remove create action for customers - they book through the frontend
        ];
    }
}
