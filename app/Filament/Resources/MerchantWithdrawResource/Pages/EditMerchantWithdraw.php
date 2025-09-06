<?php

namespace App\Filament\Resources\MerchantWithdrawResource\Pages;

use App\Filament\Resources\MerchantWithdrawResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMerchantWithdraw extends EditRecord
{
    protected static string $resource = MerchantWithdrawResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
