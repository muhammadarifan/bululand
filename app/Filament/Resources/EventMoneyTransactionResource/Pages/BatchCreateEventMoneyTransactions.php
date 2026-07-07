<?php

namespace App\Filament\Resources\EventMoneyTransactionResource\Pages;

use App\Filament\Resources\EventMoneyTransactionResource;
use App\Models\Event;
use App\Models\EventMoneyTransaction;
use App\Models\House;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class BatchCreateEventMoneyTransactions extends Page
{
    protected static string $resource = EventMoneyTransactionResource::class;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    protected ?string $heading = 'Create Batch House Contribution';

    protected static ?string $title = 'Create Batch House Contribution';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_id')
                    ->label('Event')
                    ->required()
                    ->columnSpanFull()
                    ->options(Event::pluck('name', 'id')),

                Select::make('house_ids')
                    ->label('Houses')
                    ->required()
                    ->multiple()
                    ->columnSpanFull()
                    ->options(House::pluck('code', 'id')),

                TextInput::make('description')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

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
                    'type' => 'in',
                    'category' => 'contribution',
                    'amount' => $data['amount'],
                ]);
            }
        });

        Notification::make()
            ->title(count($data['house_ids']) . ' transactions created successfully')
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

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('create')
            ->footer([
                Actions::make($this->getFormActions())
                    ->alignment($this->getFormActionsAlignment())
                    ->key('form-actions'),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }
}
