<?php

namespace App\Filament\Resources\BlogTags\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class BlogTagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tag Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-hashtag')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('general_tab_info')
                                            ->content('This tab manages basic package details.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Tag Information')
                                    ->description('Basic tag details')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Tag Name')
                                            ->placeholder('Adventure')
                                            ->required()
                                            ->helperText('e.g., Adventure'),
                                        TextInput::make('slug')
                                            ->label('Slug')
                                            ->placeholder('adventure-tag')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->helperText('Filter URL param'),
                                    ]),
                            ]),
                    ])            ])
            ->columns(1);
    }
}
