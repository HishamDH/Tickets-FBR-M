<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MerchantResource\Pages;
use App\Models\Merchant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class MerchantResource extends Resource
{
    protected static ?string $model = Merchant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('business_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('business_type')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('cr_number')
                    ->required()
                    ->maxLength(50),
                Forms\Components\Textarea::make('business_address')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('city')
                    ->required()
                    ->maxLength(100),
                Forms\Components\Select::make('verification_status')
                    ->label('Verification Status')
                    ->required()
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->native(false),
                Forms\Components\TextInput::make('commission_rate')
                    ->required()
                    ->numeric()
                    ->default(3.00),
                Forms\Components\Select::make('partner_id')
                    ->relationship('partner', 'id'),
                Forms\Components\Select::make('account_manager_id')
                    ->relationship('accountManager', 'name'),

                // Branding Section
                Forms\Components\Section::make('Merchant Branding')
                    ->description('Customize the merchant\'s storefront appearance')
                    ->schema([
                        Forms\Components\TextInput::make('subdomain')
                            ->label('Subdomain')
                            ->placeholder('merchant-name')
                            ->helperText('Will be accessible at: subdomain.domain.com')
                            ->unique(ignoreRecord: true)
                            ->regex('/^[a-z0-9-]+$/')
                            ->validationMessages([
                                'regex' => 'Subdomain can only contain lowercase letters, numbers, and hyphens.',
                                'unique' => 'This subdomain is already taken.',
                            ]),

                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Merchant Logo')
                            ->image()
                            ->directory('merchant-logos')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\ColorPicker::make('primary_color')
                                    ->label('Primary Color')
                                    ->default('#3B82F6'),

                                Forms\Components\ColorPicker::make('secondary_color')
                                    ->label('Secondary Color')
                                    ->default('#1E40AF'),

                                Forms\Components\ColorPicker::make('accent_color')
                                    ->label('Accent Color')
                                    ->default('#EF4444'),
                            ]),

                        Forms\Components\Textarea::make('custom_css')
                            ->label('Custom CSS')
                            ->helperText('Additional CSS to customize the storefront appearance')
                            ->rows(10),

                        Forms\Components\TextInput::make('custom_domain')
                            ->label('Custom Domain')
                            ->placeholder('merchant.com')
                            ->helperText('Optional: Use your own domain instead of subdomain'),

                        Forms\Components\Toggle::make('subdomain_enabled')
                            ->label('Enable Subdomain Storefront')
                            ->default(true)
                            ->helperText('Allow customers to access storefront via subdomain'),
                    ]),

                Forms\Components\KeyValue::make('settings')
                    ->label('Additional Settings'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('business_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('business_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cr_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subdomain')
                    ->searchable()
                    ->copyable()
                    ->url(fn (Merchant $record) => $record->subdomain_url)
                    ->openUrlInNewTab(),
                Tables\Columns\ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->size(40),
                Tables\Columns\ToggleColumn::make('subdomain_enabled')
                    ->label('Storefront Active'),
                Tables\Columns\TextColumn::make('verification_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('commission_rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('partner.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('accountManager.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('verification_status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Merchant $record) {
                        $record->update(['verification_status' => 'approved']);

                        Notification::make()
                            ->title('Merchant Approved')
                            ->body("Merchant '{$record->business_name}' has been approved.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Merchant $record) => $record->verification_status === 'pending'),

                Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Merchant $record) {
                        $record->update(['verification_status' => 'rejected']);

                        Notification::make()
                            ->title('Merchant Rejected')
                            ->body("Merchant '{$record->business_name}' has been rejected.")
                            ->warning()
                            ->send();
                    })
                    ->visible(fn (Merchant $record) => $record->verification_status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListMerchants::route('/'),
            'create' => Pages\CreateMerchant::route('/create'),
            'edit' => Pages\EditMerchant::route('/{record}/edit'),
        ];
    }
}
