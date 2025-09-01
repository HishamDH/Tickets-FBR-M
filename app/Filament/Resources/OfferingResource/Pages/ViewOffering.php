<?php

namespace App\Filament\Resources\OfferingResource\Pages;

use App\Filament\Resources\OfferingResource;
use Filament\Actions;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewOffering extends ViewRecord
{
    protected static string $resource = OfferingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('تعديل العرض'),
        ];
    }

    public function getTitle(): string
    {
        return 'تفاصيل العرض';
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('معلومات أساسية')
                    ->schema([
                        ImageEntry::make('image')
                            ->label('الصورة')
                            ->size(200),
                        TextEntry::make('name')
                            ->label('اسم العرض'),
                        TextEntry::make('description')
                            ->label('الوصف')
                            ->markdown(),
                        TextEntry::make('type')
                            ->label('نوع العرض')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'events' => 'success',
                                'conference' => 'info',
                                'restaurant' => 'warning',
                                'experiences' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'events' => 'فعاليات',
                                'conference' => 'مؤتمرات',
                                'restaurant' => 'مطاعم',
                                'experiences' => 'تجارب',
                                default => $state,
                            }),
                        TextEntry::make('category')
                            ->label('الفئة'),
                        TextEntry::make('location')
                            ->label('الموقع'),
                    ])
                    ->columns(2),

                Section::make('تفاصيل التسعير والتوقيت')
                    ->schema([
                        TextEntry::make('price')
                            ->label('السعر')
                            ->money('SAR'),
                        TextEntry::make('start_time')
                            ->label('وقت البداية')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('end_time')
                            ->label('وقت النهاية')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('status')
                            ->label('الحالة')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'gray',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'active' => 'نشط',
                                'inactive' => 'غير نشط',
                                default => $state,
                            }),
                    ])
                    ->columns(2),

                Section::make('إعدادات المقاعد والمالك')
                    ->schema([
                        IconEntry::make('has_chairs')
                            ->label('يحتوي على مقاعد')
                            ->boolean(),
                        TextEntry::make('chairs_count')
                            ->label('عدد المقاعد')
                            ->placeholder('غير محدد'),
                        TextEntry::make('user.name')
                            ->label('المالك'),
                        TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }
}
