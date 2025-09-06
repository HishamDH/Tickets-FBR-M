<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات أساسية')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم الفرع')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->rows(3),
                        Forms\Components\TextInput::make('capacity')
                            ->label('السعة')
                            ->numeric()
                            ->suffix('شخص'),
                    ])->columns(2),

                Forms\Components\Section::make('معلومات الموقع')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('العنوان')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->label('المدينة')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('state')
                            ->label('المنطقة/الولاية')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('الرمز البريدي')
                            ->maxLength(20),
                        Forms\Components\TextInput::make('country')
                            ->label('الدولة')
                            ->default('السعودية')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('coordinates.lat')
                            ->label('خط العرض')
                            ->numeric()
                            ->step(0.000001),
                        Forms\Components\TextInput::make('coordinates.lng')
                            ->label('خط الطول')
                            ->numeric()
                            ->step(0.000001),
                    ])->columns(2),

                Forms\Components\Section::make('معلومات الاتصال')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('manager_name')
                            ->label('اسم المدير')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('manager_phone')
                            ->label('هاتف المدير')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('manager_email')
                            ->label('بريد المدير')
                            ->email()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('ساعات العمل')
                    ->schema([
                        Forms\Components\Repeater::make('opening_hours')
                            ->label('ساعات العمل')
                            ->schema([
                                Forms\Components\Select::make('day')
                                    ->label('اليوم')
                                    ->options([
                                        'sunday' => 'الأحد',
                                        'monday' => 'الاثنين',
                                        'tuesday' => 'الثلاثاء',
                                        'wednesday' => 'الأربعاء',
                                        'thursday' => 'الخميس',
                                        'friday' => 'الجمعة',
                                        'saturday' => 'السبت',
                                    ])
                                    ->required(),
                                Forms\Components\TimePicker::make('open')
                                    ->label('ساعة الفتح'),
                                Forms\Components\TimePicker::make('close')
                                    ->label('ساعة الإغلاق'),
                                Forms\Components\Toggle::make('is_closed')
                                    ->label('مغلق')
                                    ->default(false),
                            ])
                            ->columns(4)
                            ->defaultItems(0),
                    ]),

                Forms\Components\Section::make('الخدمات والصور')
                    ->schema([
                        Forms\Components\TagsInput::make('services_offered')
                            ->label('الخدمات المقدمة')
                            ->placeholder('أدخل الخدمات...'),
                        Forms\Components\FileUpload::make('images')
                            ->label('صور الفرع')
                            ->image()
                            ->multiple()
                            ->maxFiles(10)
                            ->directory('branches'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الفرع')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('city')
                    ->label('المدينة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('manager_name')
                    ->label('المدير')
                    ->searchable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->label('السعة')
                    ->suffix(' شخص')
                    ->sortable(),
                Tables\Columns\TextColumn::make('active_services_count')
                    ->label('الخدمات النشطة')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('this_month_bookings')
                    ->label('حجوزات الشهر')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('حالة الفرع')
                    ->boolean()
                    ->trueLabel('نشط')
                    ->falseLabel('غير نشط')
                    ->placeholder('جميع الفروع'),
                Tables\Filters\SelectFilter::make('city')
                    ->label('المدينة')
                    ->options(function () {
                        return Branch::distinct('city')
                            ->whereNotNull('city')
                            ->pluck('city', 'city')
                            ->toArray();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('analytics')
                    ->label('التحليلات')
                    ->icon('heroicon-o-chart-bar')
                    ->color('info')
                    ->url(fn (Branch $record): string => static::getUrl('analytics', ['record' => $record])),
                Tables\Actions\Action::make('toggle_status')
                    ->label(fn (Branch $record) => $record->is_active ? 'إلغاء التفعيل' : 'تفعيل')
                    ->icon(fn (Branch $record) => $record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn (Branch $record) => $record->is_active ? 'warning' : 'success')
                    ->action(fn (Branch $record) => $record->update(['is_active' => !$record->is_active]))
                    ->requiresConfirmation(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('تفعيل المحدد')
                        ->icon('heroicon-o-play')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('إلغاء تفعيل المحدد')
                        ->icon('heroicon-o-pause')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
            'analytics' => Pages\BranchAnalytics::route('/{record}/analytics'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function performCreate(array $data): Branch
    {
        $data['user_id'] = auth()->id();

        return static::getModel()::create($data);
    }
}
