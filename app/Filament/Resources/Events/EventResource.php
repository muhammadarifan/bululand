<?php

namespace App\Filament\Resources\Events;

use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Models\Event;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'Event';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255)
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state): void {
                        $set('subdomain', str($state)->lower()->replace(' ', '')->toString());
                    }),

                TextInput::make('subdomain')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->columnSpanFull(),

                DateTimePicker::make('active_until')
                    ->label('Active Until')
                    ->helperText('Leave empty if the event has no expiration date.')
                    ->columnSpanFull()
                    ->native(false),

                Section::make('Event Detail')
                    ->columnSpanFull()
                    ->relationship('eventDetail')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('logo')
                            ->image()
                            ->directory('events/logos')
                            ->columnSpanFull(),

                        FileUpload::make('favicon')
                            ->image()
                            ->directory('events/favicons')
                            ->columnSpanFull(),

                        FileUpload::make('hero_image')
                            ->image()
                            ->directory('events/hero-images')
                            ->columnSpanFull(),

                        TextInput::make('hero_title')
                            ->maxLength(255),

                        Textarea::make('hero_subtitle'),

                        TextInput::make('about_title')
                            ->maxLength(255),

                        RichEditor::make('about_content')
                            ->columnSpanFull(),

                        TextInput::make('youtube_url')
                            ->url()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Repeater::make('contacts')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('phone')
                                    ->label('Telepon')
                                    ->tel()
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->grid(2)
                            ->addActionLabel('Tambah Kontak')
                            ->defaultItems(0)
                            ->columnSpanFull(),

                        TextInput::make('facebook_url')
                            ->url()
                            ->maxLength(255),

                        TextInput::make('instagram_url')
                            ->url()
                            ->maxLength(255),

                        RichEditor::make('footer_text')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('subdomain'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Event')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('subdomain')
                    ->searchable(),

                TextColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),

                TextColumn::make('active_until')
                    ->label('Active Until')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordAction(EditAction::class)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'edit' => EditEvent::route('/{record}/edit'),
        ];
    }
}
