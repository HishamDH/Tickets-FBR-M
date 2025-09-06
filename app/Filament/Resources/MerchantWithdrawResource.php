<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MerchantWithdrawResource\Pages;
use App\Filament\Resources\MerchantWithdrawResource\RelationManagers;
use App\Models\MerchantWithdraw;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MerchantWithdrawResource extends Resource
{
    protected static ?string $model = MerchantWithdraw::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $navigationLabel = 'طلبات السحب';
    
    protected static ?string $modelLabel = 'طلب سحب';
    
    protected static ?string $pluralModelLabel = 'طلبات السحب';

    protected static ?string $navigationGroup = 'إدارة المدفوعات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الطلب')
                    ->schema([
                        Forms\Components\Select::make('merchant_id')
                            ->label('التاجر')
                            ->relationship('merchant', 'name')
                            ->searchable()
                            ->required()
                            ->disabled(fn (string $operation) => $operation === 'edit'),
                        Forms\Components\TextInput::make('amount')
                            ->label('المبلغ')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->disabled(fn (string $operation) => $operation === 'edit'),
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options(MerchantWithdraw::getStatuses())
                            ->required()
                            ->reactive(),
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('رقم المعاملة')
                            ->maxLength(255)
                            ->visible(fn (callable $get) => in_array($get('status'), ['processing', 'completed'])),
                    ])->columns(2),

                Forms\Components\Section::make('التفاصيل المصرفية')
                    ->schema([
                        Forms\Components\TextInput::make('bank_details.bank_name')
                            ->label('اسم البنك')
                            ->disabled(),
                        Forms\Components\TextInput::make('bank_details.account_number')
                            ->label('رقم الحساب')
                            ->disabled(),
                        Forms\Components\TextInput::make('bank_details.account_holder_name')
                            ->label('اسم صاحب الحساب')
                            ->disabled(),
                        Forms\Components\TextInput::make('bank_details.iban')
                            ->label('IBAN')
                            ->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('الملاحظات')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('ملاحظات التاجر')
                            ->disabled()
                            ->rows(2),
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('ملاحظات الإدارة')
                            ->rows(3)
                            ->placeholder('أضف ملاحظات حول هذا الطلب...'),
                    ]),

                Forms\Components\Section::make('التواريخ')
                    ->schema([
                        Forms\Components\DateTimePicker::make('requested_at')
                            ->label('تاريخ الطلب')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('processed_at')
                            ->label('تاريخ المعالجة')
                            ->visible(fn (callable $get) => in_array($get('status'), ['approved', 'processing', 'completed'])),
                        Forms\Components\DateTimePicker::make('cancelled_at')
                            ->label('تاريخ الإلغاء')
                            ->visible(fn (callable $get) => in_array($get('status'), ['rejected', 'cancelled'])),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('merchant.name')
                    ->label('التاجر')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'في الانتظار',
                        'approved' => 'معتمد',
                        'processing' => 'قيد المعالجة',
                        'completed' => 'مكتمل',
                        'rejected' => 'مرفوض',
                        'cancelled' => 'ملغي',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'info',
                        'processing' => 'primary',
                        'completed' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'secondary',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('bank_name')
                    ->label('البنك')
                    ->getStateUsing(fn (MerchantWithdraw $record): ?string => $record->bank_details['bank_name'] ?? null),
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('رقم المعاملة')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('requested_at')
                    ->label('تاريخ الطلب')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('processed_at')
                    ->label('تاريخ المعالجة')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('days_ago')
                    ->label('منذ')
                    ->suffix(' يوم')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options(MerchantWithdraw::getStatuses()),
                Tables\Filters\Filter::make('amount_range')
                    ->label('نطاق المبلغ')
                    ->form([
                        Forms\Components\TextInput::make('amount_from')
                            ->label('من')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('amount_to')
                            ->label('إلى')
                            ->numeric()
                            ->prefix('$'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '>=', $amount),
                            )
                            ->when(
                                $data['amount_to'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '<=', $amount),
                            );
                    }),
                Tables\Filters\Filter::make('requested_at')
                    ->label('تاريخ الطلب')
                    ->form([
                        Forms\Components\DatePicker::make('requested_from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('requested_until')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['requested_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('requested_at', '>=', $date),
                            )
                            ->when(
                                $data['requested_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('requested_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('الموافقة')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (MerchantWithdraw $record) {
                        $record->update([
                            'status' => MerchantWithdraw::STATUS_APPROVED,
                            'processed_at' => now(),
                        ]);
                    })
                    ->visible(fn (MerchantWithdraw $record) => $record->canBeApproved())
                    ->requiresConfirmation()
                    ->modalHeading('الموافقة على طلب السحب')
                    ->modalDescription('هل أنت متأكد من الموافقة على هذا الطلب؟'),

                Tables\Actions\Action::make('reject')
                    ->label('الرفض')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('سبب الرفض')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (MerchantWithdraw $record, array $data) {
                        $record->update([
                            'status' => MerchantWithdraw::STATUS_REJECTED,
                            'admin_notes' => $data['admin_notes'],
                            'processed_at' => now(),
                        ]);
                    })
                    ->visible(fn (MerchantWithdraw $record) => $record->canBeRejected()),

                Tables\Actions\Action::make('mark_processing')
                    ->label('قيد المعالجة')
                    ->icon('heroicon-o-clock')
                    ->color('primary')
                    ->form([
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('رقم المعاملة')
                            ->required(),
                    ])
                    ->action(function (MerchantWithdraw $record, array $data) {
                        $record->update([
                            'status' => MerchantWithdraw::STATUS_PROCESSING,
                            'transaction_id' => $data['transaction_id'],
                        ]);
                    })
                    ->visible(fn (MerchantWithdraw $record) => $record->canBeProcessed()),

                Tables\Actions\Action::make('mark_completed')
                    ->label('إكمال')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (MerchantWithdraw $record) {
                        $record->update([
                            'status' => MerchantWithdraw::STATUS_COMPLETED,
                            'processed_at' => now(),
                        ]);
                    })
                    ->visible(fn (MerchantWithdraw $record) => $record->canBeCompleted())
                    ->requiresConfirmation(),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('الموافقة على المحدد')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function (MerchantWithdraw $record) {
                                if ($record->canBeApproved()) {
                                    $record->update([
                                        'status' => MerchantWithdraw::STATUS_APPROVED,
                                        'processed_at' => now(),
                                    ]);
                                }
                            });
                        })
                        ->requiresConfirmation(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('requested_at', 'desc');
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
            'index' => Pages\ListMerchantWithdraws::route('/'),
            'create' => Pages\CreateMerchantWithdraw::route('/create'),
            'edit' => Pages\EditMerchantWithdraw::route('/{record}/edit'),
        ];
    }
}
