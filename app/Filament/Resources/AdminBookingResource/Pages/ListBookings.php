<?php

namespace App\Filament\Resources\AdminBookingResource\Pages;

use App\Filament\Resources\AdminBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBookings extends ListRecords
{
    protected static string $resource = AdminBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
