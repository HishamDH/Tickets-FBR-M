<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use App\Models\Merchant;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'الكوبونات';

    protected static ?string $modelLabel = 'كوبون';

    protected static ?string $pluralModelLabel = 'الكوبونات';

    protected static ?string $navigationGroup = 'نظام التسويق';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الكوبون الأساسية')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم الكوبون')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('مثل: خصم العيد'),

                        Forms\Components\TextInput::make('code')
                            ->label('كود الكوبون')
                            ->required()
                            ->unique(Coupon::class, 'code', ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('مثل: EID2025')
                            ->alphaNum()
                            ->uppercase(),

                        Forms\Components\Textarea::make('description')
                            ->label('وصف الكوبون')
                            ->rows(3)
                            ->placeholder('وصف مفصل للكوبون والشروط'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('إعدادات الخصم')
                    ->schema([
                        Forms\Components\Select::make('discount_type')
                            ->label('نوع الخصم')
                            ->options([
                                'fixed' => 'مبلغ ثابت',
                                'percentage' => 'نسبة مئوية',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('discount_value', null)),

                        Forms\Components\TextInput::make('discount_value')
                            ->label('قيمة الخصم')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix(fn (Forms\Get $get) => $get('discount_type') === 'fixed' ? 'ر.س' : null)
                            ->suffix(fn (Forms\Get $get) => $get('discount_type') === 'percentage' ? '%' : null)
                            ->placeholder(fn (Forms\Get $get) => 
                                $get('discount_type') === 'fixed' ? 'مثل: 50' : 'مثل: 10'
                            ),

                        Forms\Components\TextInput::make('minimum_amount')
                            ->label('الحد الأدنى للمبلغ')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('ر.س')
                            ->placeholder('اتركه فارغاً لعدم وجود حد أدنى'),

                        Forms\Components\TextInput::make('maximum_discount')
                            ->label('الحد الأقصى للخصم')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('ر.س')
                            ->placeholder('اتركه فارغاً لعدم وجود حد أقصى')
                            ->visible(fn (Forms\Get $get) => $get('discount_type') === 'percentage'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('حدود الاستخدام')
                    ->schema([
                        Forms\Components\TextInput::make('usage_limit')
                            ->label('العدد الإجمالي للاستخدامات')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('اتركه فارغاً للاستخدام اللامحدود'),

                        Forms\Components\TextInput::make('usage_limit_per_user')
                            ->label('عدد الاستخدامات لكل مستخدم')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->placeholder('الافتراضي: 1'),

                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('تاريخ البداية')
                            ->placeholder('اتركه فارغاً للتفعيل الفوري'),

                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('تاريخ الانتهاء')
                            ->placeholder('اتركه فارغاً لعدم انتهاء الصلاحية'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('النطاق والاستهداف')
                    ->schema([
                        Forms\Components\Select::make('merchant_id')
                            ->label('التاجر')
                            ->relationship('merchant', 'business_name')
                            ->searchable()
                            ->preload()
                            ->placeholder('جميع التجار'),

                        Forms\Components\Select::make('service_id')
                            ->label('الخدمة')
                            ->relationship('service', 'title')
                            ->searchable()
                            ->preload()
                            ->placeholder('جميع الخدمات'),

                        Forms\Components\Select::make('user_id')
                            ->label('مستخدم محدد')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('جميع المستخدمين'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('نشط')
                            ->default(true),
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
                    ->label('اسم الكوبون')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->color('primary'),

                Tables\Columns\TextColumn::make('code')
                    ->label('الكود')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم نسخ الكود!')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('discount_display')
                    ->label('الخصم')
                    ->getStateUsing(function (Coupon $record): string {
                        if ($record->discount_type === 'fixed') {
                            return number_format($record->discount_value, 2) . ' ر.س';
                        }
                        return $record->discount_value . '%';
                    })
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('usage_stats')
                    ->label('الاستخدام')
                    ->getStateUsing(function (Coupon $record): string {
                        $used = $record->usage_count;
                        $limit = $record->usage_limit ?: '∞';
                        return "{$used} / {$limit}";
                    })
                    ->badge()
                    ->color(function (Coupon $record): string {
                        if (!$record->usage_limit) return 'gray';
                        $percentage = ($record->usage_count / $record->usage_limit) * 100;
                        if ($percentage >= 90) return 'danger';
                        if ($percentage >= 70) return 'warning';
                        return 'success';
                    }),

                Tables\Columns\TextColumn::make('merchant.business_name')
                    ->label('التاجر')
                    ->searchable()
                    ->placeholder('جميع التجار')
                    ->limit(20),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('تاريخ الانتهاء')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('لا ينتهي')
                    ->color(function (?string $state): string {
                        if (!$state) return 'gray';
                        $expiresAt = \Carbon\Carbon::parse($state);
                        if ($expiresAt->isPast()) return 'danger';
                        if ($expiresAt->diffInDays() <= 7) return 'warning';
                        return 'success';
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('discount_type')
                    ->label('نوع الخصم')
                    ->options([
                        'fixed' => 'مبلغ ثابت',
                        'percentage' => 'نسبة مئوية',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('الحالة')
                    ->boolean()
                    ->trueLabel('نشط')
                    ->falseLabel('غير نشط')
                    ->placeholder('جميع الكوبونات'),

                Tables\Filters\SelectFilter::make('merchant_id')
                    ->label('التاجر')
                    ->relationship('merchant', 'business_name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('expires_soon')
                    ->label('ينتهي قريباً')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('expires_at', '>', now())
                              ->where('expires_at', '<=', now()->addDays(7))
                    ),

                Tables\Filters\Filter::make('expired')
                    ->label('منتهي الصلاحية')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('expires_at', '<', now())
                    ),

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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
            'view' => Pages\ViewCoupon::route('/{record}'),
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
