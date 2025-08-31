<?php

namespace App\Filament\Resources\AdvancedSettingResource\Pages;

use App\Filament\Resources\AdvancedSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdvancedSettings extends ListRecords
{
    protected static string $resource = AdvancedSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
