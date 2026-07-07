<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventMoneyTransactionResource\Pages\BatchCreateEventMoneyTransactions;
use App\Filament\Resources\EventMoneyTransactionResource\Pages\ListEventMoneyTransactions;
use App\Models\Event;
use App\Models\EventMoneyTransaction;
use App\Models\House;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventMoneyTransactionResource extends Resource
{
    protected static ?string $model = EventMoneyTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_id')
                    ->label('Event')
                    ->required()
                    ->columnSpanFull()
                    ->options(Event::pluck('name', 'id')),

                TextInput::make('donor_name')
                    ->label('Donor Name')
                    ->maxLength(255)
                    ->reactive()
                    ->required(fn(Get $get): bool => blank($get('house_id'))),

                Select::make('house_id')
                    ->label('House')
                    ->reactive()
                    ->options(House::pluck('code', 'id'))
                    ->nullable()
                    ->required(fn(Get $get): bool => blank($get('donor_name')) || $get('category') === 'contribution')
                    ->createOptionForm([
                        TextInput::make('code')
                            ->label('House Code')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->createOptionUsing(function (array $data): mixed {
                        return House::create($data)->getKey();
                    }),

                TextInput::make('description')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                ToggleButtons::make('type')
                    ->required()
                    ->live()
                    ->inline()
                    ->options([
                        'in' => 'Income',
                        'out' => 'Expense',
                    ])
                    ->colors([
                        'in' => 'success',
                        'out' => 'danger',
                    ])
                    ->icons([
                        'in' => 'heroicon-o-arrow-trending-up',
                        'out' => 'heroicon-o-arrow-trending-down',
                    ])
                    ->default('in'),

                Select::make('category')
                    ->required()
                    ->live()
                    ->options(fn(Get $get): array => match ($get('type')) {
                        'in' => [
                            'donation' => 'Donation',
                            'contribution' => 'Contribution',
                            'sponsorship' => 'Sponsorship',
                            'ticket_sales' => 'Ticket Sales',
                            'merchandise' => 'Merchandise',
                            'others' => 'Others',
                        ],
                        'out' => [
                            'consumption' => 'Consumption',
                            'administration' => 'Administration',
                            'decoration' => 'Decoration',
                            'documentation' => 'Documentation',
                            'transport' => 'Transport',
                            'venue_rental' => 'Venue Rental',
                            'sound_system' => 'Sound System',
                            'printing' => 'Printing',
                            'others' => 'Others',
                        ],
                        default => [],
                    }),

                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->columnSpanFull()
                    ->prefix('Rp'),

                FileUpload::make('attachment')
                    ->directory('event-money-transactions/attachments')
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('event.name')
                    ->label('Event'),
                TextEntry::make('house.code')
                    ->label('House'),
                TextEntry::make('donor_name')
                    ->label('Donor Name'),
                TextEntry::make('description'),
                TextEntry::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'in' => 'Income',
                        'out' => 'Expense',
                    }),
                TextEntry::make('category'),
                TextEntry::make('amount')
                    ->money('IDR'),
                TextEntry::make('created_at')
                    ->label('Transaction Date')
                    ->dateTime(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('event.name')
                    ->label('Event')
                    ->searchable(),

                TextColumn::make('house.code')
                    ->label('House')
                    ->searchable(),

                TextColumn::make('donor_name')
                    ->label('Donor Name')
                    ->searchable()
                    ->limit(25),

                TextColumn::make('description')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'in' => 'Income',
                        'out' => 'Expense',
                    }),

                TextColumn::make('category')
                    ->searchable(),

                TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalWidth('xl')
                    ->preserveFormDataWhenCreatingAnother([
                        'event_id',
                        'donor_name',
                        'house_id',
                        'description',
                        'type',
                        'category',
                        'amount',
                        'attachment',
                    ]),
            ])
            ->actions([
                EditAction::make()
                    ->modalWidth('xl'),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEventMoneyTransactions::route('/'),
            'batch-create' => BatchCreateEventMoneyTransactions::route('/batch-create'),
        ];
    }
}
