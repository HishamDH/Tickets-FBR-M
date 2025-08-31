<?php

namespace App\Filament\Resources\DynamicSettingResource\Pages;

use App\Filament\Resources\DynamicSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDynamicSetting extends EditRecord
{
    protected static string $resource = DynamicSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
