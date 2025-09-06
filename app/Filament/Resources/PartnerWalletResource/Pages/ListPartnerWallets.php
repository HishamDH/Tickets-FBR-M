<?php

namespace App\Filament\Resources\PartnerWalletResource\Pages;

use App\Filament\Resources\PartnerWalletResource;
use App\Services\PartnerCommissionService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Concerns\ExposesTableToWidgets;

class ListPartnerWallets extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = PartnerWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            
            Actions\Action::make('process_auto_withdrawals')
                ->label('معالجة جميع السحوبات التلقائية')
                ->icon('heroicon-o-banknotes')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('تأكيد معالجة السحوبات التلقائية')
                ->modalDescription('سيتم معالجة جميع المحافظ المؤهلة للسحب التلقائي')
                ->action(function () {
                    $service = app(PartnerCommissionService::class);
                    $processedCount = 0;
                    
                    $eligibleWallets = static::getResource()::getEloquentQuery()
                        ->where('auto_withdraw', true)
                        ->whereNotNull('auto_withdraw_threshold')
                        ->whereColumn('balance', '>=', 'auto_withdraw_threshold')
                        ->get();
                    
                    foreach ($eligibleWallets as $wallet) {
                        if ($service->processAutoWithdrawal($wallet)) {
                            $processedCount++;
                        }
                    }
                    
                    \Filament\Notifications\Notification::make()
                        ->title("تم معالجة {$processedCount} طلب سحب تلقائي")
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PartnerWalletResource\Widgets\PartnerWalletStatsWidget::class,
        ];
    }
}
