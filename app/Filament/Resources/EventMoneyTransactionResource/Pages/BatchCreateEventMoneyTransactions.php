<?php

namespace App\Filament\Resources\EventMoneyTransactionResource\Pages;

use App\Filament\Resources\EventMoneyTransactionResource;
use App\Models\EventMoneyTransaction;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class BatchCreateEventMoneyTransactions extends Page
{
    protected static string $resource = EventMoneyTransactionResource::class;

    protected static string $view = 'filament.resources.event-money-transaction-resource.pages.batch-create-event-money-transactions';

    public ?array $data = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_id')
                    ->label('Event')
                    ->required()
                    ->searchable()
                    ->columnSpanFull()
                    ->relationship('event', 'name'),

                Select::make('house_ids')
                    ->label('Houses')
                    ->required()
                    ->multiple()
                    ->searchable()
                    ->columnSpanFull()
                    ->relationship('house', 'code'),

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

                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->columnSpanFull()
                    ->prefix('Rp'),
            ]);
    }

    public function create(): void
    {
        $data = $this->form->getRawState();

        DB::transaction(function () use ($data) {
            foreach ($data['house_ids'] as $houseId) {
                EventMoneyTransaction::create([
                    'event_id' => $data['event_id'],
                    'house_id' => $houseId,
                    'description' => $data['description'],
                    'type' => $data['type'],
                    'category' => 'contribution',
                    'amount' => $data['amount'],
                ]);
            }
        });

        Notification::make()
            ->title(count($data['house_ids']).' transactions created successfully')
            ->success()
            ->send();

        $this->redirect(EventMoneyTransactionResource::getUrl('index'));
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Create Batch')
                ->submit('create'),
        ];
    }
}
