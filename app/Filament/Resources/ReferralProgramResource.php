<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferralProgramResource\Pages;
use App\Models\ReferralProgram;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;

class ReferralProgramResource extends Resource
{
    protected static ?string $model = ReferralProgram::class;

    protected static ?string $navigationIcon = 'heroicon-o-share';

    protected static ?string $navigationLabel = 'برامج الإحالة';

    protected static ?string $modelLabel = 'برنامج إحالة';

    protected static ?string $pluralModelLabel = 'برامج الإحالة';

    protected static ?string $navigationGroup = 'نظام التسويق';

    protected static ?int $navigationSort = 3;

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
                            ->placeholder('مثل: برنامج إحالة الأصدقاء'),

                        Forms\Components\Textarea::make('description')
                            ->label('وصف البرنامج')
                            ->rows(3)
                            ->placeholder('وصف مفصل لبرنامج الإحالة وآلية عمله'),

                        Forms\Components\Select::make('merchant_id')
                            ->label('التاجر')
                            ->relationship('merchant', 'business_name')
                            ->searchable()
                            ->preload()
                            ->placeholder('جميع التجار (برنامج عام)'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('مكافآت المُحيل (من يرسل الإحالة)')
                    ->schema([
                        Forms\Components\Select::make('referrer_reward_type')
                            ->label('نوع مكافأة المُحيل')
                            ->options([
                                'fixed' => 'مبلغ ثابت',
                                'percentage' => 'نسبة مئوية',
                                'points' => 'نقاط ولاء',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('referrer_reward_value', null)),

                        Forms\Components\TextInput::make('referrer_reward_value')
                            ->label('قيمة مكافأة المُحيل')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix(fn (Forms\Get $get) => $get('referrer_reward_type') === 'fixed' ? 'ر.س' : null)
                            ->suffix(function (Forms\Get $get) {
                                return match ($get('referrer_reward_type')) {
                                    'percentage' => '%',
                                    'points' => 'نقطة',
                                    default => null,
                                };
                            })
                            ->placeholder(function (Forms\Get $get) {
                                return match ($get('referrer_reward_type')) {
                                    'fixed' => 'مثل: 50',
                                    'percentage' => 'مثل: 10',
                                    'points' => 'مثل: 100',
                                    default => '',
                                };
                            }),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('مكافآت المُحال إليه (العميل الجديد)')
                    ->schema([
                        Forms\Components\Select::make('referee_reward_type')
                            ->label('نوع مكافأة المُحال إليه')
                            ->options([
                                'fixed' => 'مبلغ ثابت',
                                'percentage' => 'نسبة مئوية',
                                'points' => 'نقاط ولاء',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('referee_reward_value', null)),

                        Forms\Components\TextInput::make('referee_reward_value')
                            ->label('قيمة مكافأة المُحال إليه')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix(fn (Forms\Get $get) => $get('referee_reward_type') === 'fixed' ? 'ر.س' : null)
                            ->suffix(function (Forms\Get $get) {
                                return match ($get('referee_reward_type')) {
                                    'percentage' => '%',
                                    'points' => 'نقطة',
                                    default => null,
                                };
                            })
                            ->placeholder(function (Forms\Get $get) {
                                return match ($get('referee_reward_type')) {
                                    'fixed' => 'مثل: 25',
                                    'percentage' => 'مثل: 5',
                                    'points' => 'مثل: 50',
                                    default => '',
                                };
                            }),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('شروط وحدود البرنامج')
                    ->schema([
                        Forms\Components\TextInput::make('minimum_order_amount')
                            ->label('الحد الأدنى لمبلغ الطلب')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('ر.س')
                            ->placeholder('اتركه فارغاً لعدم وجود حد أدنى')
                            ->helperText('الحد الأدنى لمبلغ الطلب لاحتساب الإحالة'),

                        Forms\Components\TextInput::make('maximum_uses_per_referrer')
                            ->label('الحد الأقصى للاستخدامات لكل مُحيل')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('غير محدود')
                            ->helperText('أقصى عدد إحالات ناجحة لكل مُحيل'),

                        Forms\Components\TextInput::make('maximum_total_uses')
                            ->label('الحد الأقصى الإجمالي للاستخدامات')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('غير محدود')
                            ->helperText('أقصى عدد إحالات ناجحة للبرنامج كله'),

                        Forms\Components\TextInput::make('validity_days')
                            ->label('فترة صلاحية رمز الإحالة')
                            ->numeric()
                            ->minValue(1)
                            ->suffix('يوم')
                            ->placeholder('لا تنتهي')
                            ->helperText('عدد الأيام قبل انتهاء صلاحية رمز الإحالة'),
                    ])
                    ->columns(2),

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

                Tables\Columns\TextColumn::make('merchant.business_name')
                    ->label('التاجر')
                    ->searchable()
                    ->placeholder('برنامج عام')
                    ->limit(20),

                Tables\Columns\TextColumn::make('referrer_reward_display')
                    ->label('مكافأة المُحيل')
                    ->getStateUsing(function (ReferralProgram $record): string {
                        return match ($record->referrer_reward_type) {
                            'fixed' => number_format($record->referrer_reward_value, 2) . ' ر.س',
                            'percentage' => $record->referrer_reward_value . '%',
                            'points' => $record->referrer_reward_value . ' نقطة',
                            default => $record->referrer_reward_value,
                        };
                    })
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('referee_reward_display')
                    ->label('مكافأة المُحال إليه')
                    ->getStateUsing(function (ReferralProgram $record): string {
                        return match ($record->referee_reward_type) {
                            'fixed' => number_format($record->referee_reward_value, 2) . ' ر.س',
                            'percentage' => $record->referee_reward_value . '%',
                            'points' => $record->referee_reward_value . ' نقطة',
                            default => $record->referee_reward_value,
                        };
                    })
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('total_referrals')
                    ->label('إجمالي الإحالات')
                    ->getStateUsing(function (ReferralProgram $record): int {
                        return $record->referrals()->count();
                    })
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('successful_referrals')
                    ->label('الإحالات الناجحة')
                    ->getStateUsing(function (ReferralProgram $record): int {
                        return $record->referrals()->where('is_successful', true)->count();
                    })
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('conversion_rate')
                    ->label('معدل التحويل')
                    ->getStateUsing(function (ReferralProgram $record): string {
                        $total = $record->referrals()->count();
                        $successful = $record->referrals()->where('is_successful', true)->count();
                        $rate = $total > 0 ? ($successful / $total) * 100 : 0;
                        return number_format($rate, 1) . '%';
                    })
                    ->badge()
                    ->color(function (ReferralProgram $record): string {
                        $total = $record->referrals()->count();
                        $successful = $record->referrals()->where('is_successful', true)->count();
                        $rate = $total > 0 ? ($successful / $total) * 100 : 0;
                        if ($rate >= 50) return 'success';
                        if ($rate >= 25) return 'warning';
                        return 'danger';
                    }),

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

                Tables\Filters\SelectFilter::make('referrer_reward_type')
                    ->label('نوع مكافأة المُحيل')
                    ->options([
                        'fixed' => 'مبلغ ثابت',
                        'percentage' => 'نسبة مئوية',
                        'points' => 'نقاط ولاء',
                    ]),

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
            'index' => Pages\ListReferralPrograms::route('/'),
            'create' => Pages\CreateReferralProgram::route('/create'),
            'edit' => Pages\EditReferralProgram::route('/{record}/edit'),
            'view' => Pages\ViewReferralProgram::route('/{record}'),
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
