<?php

namespace App\Filament\Resources;

use App\Filament\Exports\EventItemDonationExporter;
use App\Filament\Imports\EventItemDonationImporter;
use App\Filament\Resources\EventItemDonationResource\Pages\ListEventItemDonations;
use App\Models\Event;
use App\Models\EventItemDonation;
use App\Models\House;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventItemDonationResource extends Resource
{
    protected static ?string $model = EventItemDonation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static ?string $recordTitleAttribute = 'item_name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_id')
                    ->label('Event')
                    ->required()
                    ->columnSpanFull()
                    ->options(Event::pluck('name', 'id')),

                TextInput::make('item_name')
                    ->label('Item Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1),

                TextInput::make('unit')
                    ->label('Unit')
                    ->maxLength(255)
                    ->placeholder('pcs, kg, dus, ...'),

                TextInput::make('price')
                    ->label('Harga')
                    ->numeric()
                    ->prefix('Rp'),

                TextInput::make('donor_name')
                    ->label('Donor Name')
                    ->maxLength(255)
                    ->reactive()
                    ->required(fn (Get $get): bool => blank($get('house_id'))),

                Toggle::make('is_anonymous')
                    ->label('Anonymous Donor'),

                Select::make('house_id')
                    ->label('House')
                    ->reactive()
                    ->options(House::pluck('code', 'id'))
                    ->nullable()
                    ->required(fn (Get $get): bool => blank($get('donor_name')))
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
                    ->maxLength(255)
                    ->columnSpanFull(),

                FileUpload::make('attachment')
                    ->directory('event-item-donations/attachments')
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('event.name')
                    ->label('Event'),
                TextEntry::make('item_name')
                    ->label('Item Name'),
                TextEntry::make('quantity'),
                TextEntry::make('unit'),
                TextEntry::make('price')
                    ->label('Harga')
                    ->money('IDR'),
                TextEntry::make('house.code')
                    ->label('House'),
                TextEntry::make('donor_name')
                    ->label('Donor Name'),
                IconEntry::make('is_anonymous')
                    ->label('Anonymous Donor')
                    ->boolean(),
                TextEntry::make('description'),
                TextEntry::make('created_at')
                    ->label('Donation Date')
                    ->dateTime(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('item_name')
            ->columns([
                TextColumn::make('event.name')
                    ->label('Event')
                    ->searchable(),

                TextColumn::make('item_name')
                    ->label('Item Name')
                    ->searchable(),

                TextColumn::make('quantity'),

                TextColumn::make('unit'),

                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('house.code')
                    ->label('House')
                    ->searchable(),

                TextColumn::make('donor_name')
                    ->label('Donor Name')
                    ->searchable()
                    ->limit(25),

                IconColumn::make('is_anonymous')
                    ->label('Anonymous')
                    ->boolean(),

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
                        'is_anonymous',
                        'house_id',
                    ]),
                ImportAction::make()
                    ->importer(EventItemDonationImporter::class),
                ExportAction::make()
                    ->exporter(EventItemDonationExporter::class),
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
            'index' => ListEventItemDonations::route('/'),
        ];
    }
}
