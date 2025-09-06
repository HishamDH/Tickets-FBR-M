<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoyaltyProgramResource\Pages;
use App\Models\LoyaltyProgram;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;

class LoyaltyProgramResource extends Resource
{
    protected static ?string $model = LoyaltyProgram::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationLabel = 'برامج الولاء';

    protected static ?string $modelLabel = 'برنامج ولاء';

    protected static ?string $pluralModelLabel = 'برامج الولاء';

    protected static ?string $navigationGroup = 'نظام التسويق';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات البرنامج الأساسية')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم البرنامج')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('مثل: برنامج النقاط الذهبية'),

                        Forms\Components\Textarea::make('description')
                            ->label('وصف البرنامج')
                            ->rows(3)
                            ->placeholder('وصف مفصل لبرنامج الولاء ومزاياه'),

                        Forms\Components\Select::make('type')
                            ->label('نوع البرنامج')
                            ->options([
                                'points' => 'نقاط',
                                'cashback' => 'استرداد نقدي',
                                'tier' => 'مستويات',
                            ])
                            ->default('points')
                            ->required(),

                        Forms\Components\Select::make('merchant_id')
                            ->label('التاجر')
                            ->relationship('merchant', 'business_name')
                            ->searchable()
                            ->preload()
                            ->placeholder('جميع التجار (برنامج عام)'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('إعدادات النقاط')
                    ->schema([
                        Forms\Components\TextInput::make('points_per_amount')
                            ->label('النقاط لكل ريال')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->default(1.00)
                            ->suffix('نقطة/ر.س')
                            ->helperText('عدد النقاط الممنوحة لكل ريال سعودي يتم إنفاقه'),

                        Forms\Components\TextInput::make('redemption_rate')
                            ->label('معدل الاسترداد')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->default(0.01)
                            ->suffix('ر.س/نقطة')
                            ->helperText('قيمة النقطة الواحدة بالريال السعودي'),

                        Forms\Components\TextInput::make('minimum_points')
                            ->label('الحد الأدنى للاسترداد')
                            ->numeric()
                            ->minValue(0)
                            ->default(100)
                            ->suffix('نقطة')
                            ->helperText('أقل عدد نقاط يمكن استردادها'),

                        Forms\Components\TextInput::make('maximum_points_per_transaction')
                            ->label('الحد الأقصى لكل معاملة')
                            ->numeric()
                            ->minValue(0)
                            ->suffix('نقطة')
                            ->placeholder('غير محدود')
                            ->helperText('أقصى عدد نقاط يمكن كسبها في معاملة واحدة'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('صلاحية النقاط')
                    ->schema([
                        Forms\Components\TextInput::make('expiry_months')
                            ->label('فترة انتهاء النقاط')
                            ->numeric()
                            ->minValue(1)
                            ->suffix('شهر')
                            ->placeholder('لا تنتهي')
                            ->helperText('عدد الشهور قبل انتهاء صلاحية النقاط'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true)
                            ->helperText('تفعيل أو إلغاء تفعيل البرنامج'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('مستويات الولاء')
                    ->schema([
                        Forms\Components\Repeater::make('tier_benefits')
                            ->label('مستويات الولاء')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('اسم المستوى')
                                    ->required()
                                    ->placeholder('مثل: برونزي، فضي، ذهبي'),

                                Forms\Components\TextInput::make('required_points')
                                    ->label('النقاط المطلوبة')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0),

                                Forms\Components\TextInput::make('bonus_multiplier')
                                    ->label('مضاعف المكافآت')
                                    ->numeric()
                                    ->step(0.1)
                                    ->default(1.0)
                                    ->minValue(1.0),

                                Forms\Components\TagsInput::make('benefits')
                                    ->label('المزايا')
                                    ->placeholder('اكتب المزايا واضغط Enter'),
                            ])
                            ->columns(2)
                            ->addActionLabel('إضافة مستوى جديد')
                            ->deleteActionLabel('حذف المستوى')
                            ->collapsible()
                            ->collapsed()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('إعدادات إضافية')
                    ->schema([
                        Forms\Components\KeyValue::make('settings')
                            ->label('إعدادات إضافية')
                            ->keyLabel('المفتاح')
                            ->valueLabel('القيمة')
                            ->addActionLabel('إضافة إعداد')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم البرنامج')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->color('primary'),

                Tables\Columns\TextColumn::make('type')
                    ->label('النوع')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'points' => 'نقاط',
                        'cashback' => 'استرداد نقدي',
                        'tier' => 'مستويات',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'points' => 'info',
                        'cashback' => 'success',
                        'tier' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('merchant.business_name')
                    ->label('التاجر')
                    ->searchable()
                    ->placeholder('برنامج عام')
                    ->limit(20),

                Tables\Columns\TextColumn::make('points_per_amount')
                    ->label('النقاط/ر.س')
                    ->sortable()
                    ->suffix(' نقطة'),

                Tables\Columns\TextColumn::make('redemption_rate')
                    ->label('قيمة النقطة')
                    ->sortable()
                    ->money('SAR'),

                Tables\Columns\TextColumn::make('total_points_awarded')
                    ->label('النقاط الممنوحة')
                    ->getStateUsing(function (LoyaltyProgram $record): int {
                        return $record->points()->sum('points');
                    })
                    ->formatStateUsing(fn (int $state): string => number_format($state))
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('active_users')
                    ->label('المستخدمون النشطون')
                    ->getStateUsing(function (LoyaltyProgram $record): int {
                        return $record->points()
                                     ->where('points', '>', 0)
                                     ->whereNull('used_at')
                                     ->distinct('user_id')
                                     ->count('user_id');
                    })
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('نوع البرنامج')
                    ->options([
                        'points' => 'نقاط',
                        'cashback' => 'استرداد نقدي',
                        'tier' => 'مستويات',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                    ->trueLabel('نشط')
                    ->falseLabel('غير نشط')
                    ->placeholder('جميع البرامج'),

                Tables\Filters\SelectFilter::make('merchant_id')
                    ->label('التاجر')
                    ->relationship('merchant', 'business_name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TrashedFilter::make()
                    ->label('المحذوفة'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('عرض'),
                Tables\Actions\EditAction::make()
                    ->label('تعديل'),
                Tables\Actions\DeleteAction::make()
                    ->label('حذف'),
                Tables\Actions\RestoreAction::make()
                    ->label('استعادة'),
                Tables\Actions\ForceDeleteAction::make()
                    ->label('حذف نهائي'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف المحدد'),
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('استعادة المحدد'),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('حذف نهائي للمحدد'),
                    
                    Tables\Actions\BulkAction::make('activate')
                        ->label('تفعيل المحدد')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (array $records) {
                            foreach ($records as $record) {
                                $record->update(['is_active' => true]);
                            }
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('إلغاء تفعيل المحدد')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function (array $records) {
                            foreach ($records as $record) {
                                $record->update(['is_active' => false]);
                            }
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession();
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
            'index' => Pages\ListLoyaltyPrograms::route('/'),
            'create' => Pages\CreateLoyaltyProgram::route('/create'),
            'edit' => Pages\EditLoyaltyProgram::route('/{record}/edit'),
            'view' => Pages\ViewLoyaltyProgram::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
