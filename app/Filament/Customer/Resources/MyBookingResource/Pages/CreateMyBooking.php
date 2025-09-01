<?php

namespace App\Filament\Customer\Resources\MyBookingResource\Pages;

use App\Filament\Customer\Resources\MyBookingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMyBooking extends CreateRecord
{
    protected static string $resource = MyBookingResource::class;
}
