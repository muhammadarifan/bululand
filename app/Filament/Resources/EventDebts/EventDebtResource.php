<?php

namespace App\Filament\Resources\EventDebts;

use App\Filament\Resources\EventDebts\Pages\ManageEventDebts;
use App\Models\Event;
use App\Models\EventDebt;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventDebtResource extends Resource
{
    protected static ?string $model = EventDebt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $recordTitleAttribute = 'creditor_name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_id')
                    ->label('Event')
                    ->required()
                    ->options(Event::pluck('name', 'id')),

                TextInput::make('creditor_name')
                    ->label('Hutang Kepada')
                    ->required()
                    ->maxLength(255),

                TextInput::make('amount')
                    ->label('Nominal Hutang')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),

                TextInput::make('description')
                    ->label('Keterangan')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event.name')
                    ->label('Event')
                    ->searchable(),

                TextColumn::make('creditor_name')
                    ->label('Hutang Kepada')
                    ->searchable(),

                TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(40),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEventDebts::route('/'),
        ];
    }
}
