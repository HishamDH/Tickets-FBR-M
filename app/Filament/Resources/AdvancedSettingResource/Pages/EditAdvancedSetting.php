<?php

namespace App\Filament\Resources\AdvancedSettingResource\Pages;

use App\Filament\Resources\AdvancedSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdvancedSetting extends EditRecord
{
    protected static string $resource = AdvancedSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
