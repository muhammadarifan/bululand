<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages\CreatePost;
use App\Filament\Resources\PostResource\Pages\EditPost;
use App\Filament\Resources\PostResource\Pages\ListPosts;
use App\Models\Post;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\ToggleButtons;
use Filament\Tables\Columns\ImageColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                Select::make('event_id')
                                    ->label('Event')
                                    ->required()
                                    ->searchable()
                                    ->relationship('event', 'name'),

                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),

                                ToggleButtons::make('type')
                                    ->required()
                                    ->inline()
                                    ->options([
                                        'announcement' => 'Announcement',
                                        'activity' => 'Activity',
                                        'committee' => 'Committee',
                                        'winner' => 'Winner',
                                        'documentation' => 'Documentation',
                                        'report' => 'Report',
                                    ])
                                    ->colors([
                                        'announcement' => 'info',
                                        'activity' => 'success',
                                        'committee' => 'warning',
                                        'winner' => 'success',
                                        'documentation' => 'primary',
                                        'report' => 'gray',
                                    ])
                                    ->icons([
                                        'announcement' => 'heroicon-o-megaphone',
                                        'activity' => 'heroicon-o-calendar',
                                        'committee' => 'heroicon-o-users',
                                        'winner' => 'heroicon-o-trophy',
                                        'documentation' => 'heroicon-o-photo',
                                        'report' => 'heroicon-o-document-chart-bar',
                                    ]),

                                FileUpload::make('thumbnail')
                                    ->image()
                                    ->directory('posts/thumbnails'),

                                DateTimePicker::make('published_at')
                                    ->nullable(),
                            ]),

                        Tab::make('Content')
                            ->schema([
                                RichEditor::make('content')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('event.name')
                    ->label('Event'),
                TextEntry::make('title'),
                TextEntry::make('content')
                    ->html(),
                TextEntry::make('type'),
                TextEntry::make('published_at')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('event.name')
                    ->label('Event')
                    ->searchable(),

                ImageColumn::make('thumbnail')
                    ->width(80)
                    ->height(60),

                TextColumn::make('title')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('type')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'announcement' => 'info',
                        'activity' => 'success',
                        'committee' => 'warning',
                        'winner' => 'success',
                        'documentation' => 'primary',
                        'report' => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'announcement' => 'Announcement',
                        'activity' => 'Activity',
                        'committee' => 'Committee',
                        'winner' => 'Winner',
                        'documentation' => 'Documentation',
                        'report' => 'Report',
                    })
                    ->searchable(),

                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
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
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
