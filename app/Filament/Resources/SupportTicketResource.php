<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupportTicketResource\Pages;
use App\Models\SupportTicket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Support & Communication';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Support Tickets';

    protected static ?string $modelLabel = 'Support Ticket';

    protected static ?string $pluralModelLabel = 'Support Tickets';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ticket Information')
                    ->schema([
                        Forms\Components\TextInput::make('ticket_number')
                            ->label('Ticket Number')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto-generated'),

                        Forms\Components\Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name.' ('.$record->email.')'
                            ),

                        Forms\Components\Select::make('assigned_to')
                            ->label('Assigned To')
                            ->relationship('assignedTo', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Unassigned'),

                        Forms\Components\Select::make('booking_id')
                            ->label('Related Booking')
                            ->relationship('booking', 'reference_number')
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->reference_number.' - '.$record->service_name
                            )
                            ->placeholder('No related booking'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Ticket Details')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->label('Subject')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('description')
                            ->label('Description')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('category')
                            ->label('Category')
                            ->options([
                                'general' => 'General',
                                'booking' => 'Booking',
                                'payment' => 'Payment',
                                'refund' => 'Refund',
                                'technical' => 'Technical',
                                'complaint' => 'Complaint',
                                'feature_request' => 'Feature Request',
                            ])
                            ->required()
                            ->default('general'),

                        Forms\Components\Select::make('priority')
                            ->label('Priority')
                            ->options([
                                'low' => 'Low',
                                'normal' => 'Normal',
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->required()
                            ->default('normal'),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'open' => 'Open',
                                'in_progress' => 'In Progress',
                                'waiting_response' => 'Waiting Response',
                                'resolved' => 'Resolved',
                                'closed' => 'Closed',
                            ])
                            ->required()
                            ->default('open'),

                        Forms\Components\Select::make('source')
                            ->label('Source')
                            ->options([
                                'email' => 'Email',
                                'website' => 'Website',
                                'phone' => 'Phone',
                                'chat' => 'Chat',
                                'admin' => 'Admin Created',
                            ])
                            ->default('admin'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label('Tags')
                            ->placeholder('Add tags...')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('resolution_notes')
                            ->label('Resolution Notes')
                            ->placeholder('Add notes when resolving the ticket...')
                            ->visible(fn ($record) => $record && in_array($record->status, ['resolved', 'closed']))
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),

                Forms\Components\Section::make('Timestamps')
                    ->schema([
                        Forms\Components\DateTimePicker::make('first_response_at')
                            ->label('First Response At')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('resolved_at')
                            ->label('Resolved At')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('closed_at')
                            ->label('Closed At')
                            ->disabled(),
                    ])
                    ->collapsed()
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('Ticket #')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'secondary',
                        'normal' => 'primary',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'danger',
                        'in_progress' => 'warning',
                        'waiting_response' => 'info',
                        'resolved' => 'success',
                        'closed' => 'secondary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'waiting_response' => 'Waiting Response',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 40) {
                            return null;
                        }

                        return $state;
                    }),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),

                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->placeholder('Unassigned')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'general' => 'primary',
                        'booking' => 'success',
                        'payment' => 'warning',
                        'refund' => 'danger',
                        'technical' => 'info',
                        'complaint' => 'gray',
                        'feature_request' => 'purple',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'general' => 'General',
                        'booking' => 'Booking',
                        'payment' => 'Payment',
                        'refund' => 'Refund',
                        'technical' => 'Technical',
                        'complaint' => 'Complaint',
                        'feature_request' => 'Feature Request',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('booking.reference_number')
                    ->label('Booking')
                    ->placeholder('No booking')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('source')
                    ->badge()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('first_response_at')
                    ->label('First Response')
                    ->dateTime()
                    ->toggleable()
                    ->placeholder('No response'),

                Tables\Columns\TextColumn::make('resolved_at')
                    ->label('Resolved')
                    ->dateTime()
                    ->toggleable()
                    ->placeholder('Not resolved'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'waiting_response' => 'Waiting Response',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                    ]),

                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'normal' => 'Normal',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ]),

                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'general' => 'General',
                        'booking' => 'Booking',
                        'payment' => 'Payment',
                        'refund' => 'Refund',
                        'technical' => 'Technical',
                        'complaint' => 'Complaint',
                        'feature_request' => 'Feature Request',
                    ]),

                Tables\Filters\SelectFilter::make('assigned_to')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('has_booking')
                    ->label('Has Related Booking')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('booking_id'),
                        false: fn (Builder $query) => $query->whereNull('booking_id'),
                    ),

                Tables\Filters\Filter::make('response_time')
                    ->label('Response Time')
                    ->form([
                        Forms\Components\Select::make('response_status')
                            ->options([
                                'no_response' => 'No Response',
                                'has_response' => 'Has Response',
                                'overdue' => 'Overdue (>24h)',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['response_status'] === 'no_response',
                            fn (Builder $query): Builder => $query->whereNull('first_response_at'),
                        )->when(
                            $data['response_status'] === 'has_response',
                            fn (Builder $query): Builder => $query->whereNotNull('first_response_at'),
                        )->when(
                            $data['response_status'] === 'overdue',
                            fn (Builder $query): Builder => $query->where('created_at', '<', now()->subHours(24))
                                ->whereNull('first_response_at'),
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('assign')
                        ->label('Assign')
                        ->icon('heroicon-o-user-plus')
                        ->color('info')
                        ->form([
                            Forms\Components\Select::make('assigned_to')
                                ->label('Assign To')
                                ->relationship('assignedTo', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update($data);

                            \Filament\Notifications\Notification::make()
                                ->title('Ticket Assigned')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('respond')
                        ->label('Add Response')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('primary')
                        ->url(fn ($record) => route('filament.admin.resources.support-tickets.view', $record).'#responses'),

                    Tables\Actions\Action::make('resolve')
                        ->label('Mark as Resolved')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn ($record): bool => ! in_array($record->status, ['resolved', 'closed']))
                        ->form([
                            Forms\Components\Textarea::make('resolution_notes')
                                ->label('Resolution Notes')
                                ->required()
                                ->placeholder('Describe how this ticket was resolved...'),
                        ])
                        ->action(function ($record, array $data) {
                            $record->markAsResolved($data['resolution_notes']);

                            \Filament\Notifications\Notification::make()
                                ->title('Ticket Resolved')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('close')
                        ->label('Close Ticket')
                        ->icon('heroicon-o-lock-closed')
                        ->color('warning')
                        ->visible(fn ($record): bool => $record->status === 'resolved')
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $record->markAsClosed();

                            \Filament\Notifications\Notification::make()
                                ->title('Ticket Closed')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('reopen')
                        ->label('Reopen Ticket')
                        ->icon('heroicon-o-lock-open')
                        ->color('danger')
                        ->visible(fn ($record): bool => $record->canBeReopened())
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $record->update([
                                'status' => 'open',
                                'closed_at' => null,
                                'resolved_at' => null,
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->title('Ticket Reopened')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('assign_bulk')
                        ->label('Assign Selected')
                        ->icon('heroicon-o-user-plus')
                        ->color('info')
                        ->form([
                            Forms\Components\Select::make('assigned_to')
                                ->label('Assign To')
                                ->relationship('assignedTo', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                $record->update($data);
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Tickets Assigned')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('change_status')
                        ->label('Change Status')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->options([
                                    'open' => 'Open',
                                    'in_progress' => 'In Progress',
                                    'waiting_response' => 'Waiting Response',
                                    'resolved' => 'Resolved',
                                    'closed' => 'Closed',
                                ])
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                $record->update($data);
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Status Updated')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('export')
                        ->label('Export Tickets')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('secondary')
                        ->action(function ($records) {
                            return response()->streamDownload(function () use ($records) {
                                $csv = fopen('php://output', 'w');

                                // Headers
                                fputcsv($csv, [
                                    'Ticket Number',
                                    'Subject',
                                    'Customer',
                                    'Assigned To',
                                    'Status',
                                    'Priority',
                                    'Category',
                                    'Source',
                                    'Created At',
                                    'First Response',
                                    'Resolved At',
                                ]);

                                // Data
                                foreach ($records as $ticket) {
                                    fputcsv($csv, [
                                        $ticket->ticket_number,
                                        $ticket->subject,
                                        $ticket->user->name ?? '',
                                        $ticket->assignedTo->name ?? 'Unassigned',
                                        $ticket->status_label,
                                        $ticket->priority_label,
                                        $ticket->category_label,
                                        $ticket->source,
                                        $ticket->created_at,
                                        $ticket->first_response_at,
                                        $ticket->resolved_at,
                                    ]);
                                }

                                fclose($csv);
                            }, 'support-tickets-export-'.now()->format('Y-m-d-H-i-s').'.csv');
                        }),

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
            'index' => Pages\ListSupportTickets::route('/'),
            'create' => Pages\CreateSupportTicket::route('/create'),
            'view' => Pages\ViewSupportTicket::route('/{record}'),
            'edit' => Pages\EditSupportTicket::route('/{record}/edit'),
        ];
    }
}
