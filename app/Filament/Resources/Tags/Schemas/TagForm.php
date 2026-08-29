<?php

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-tag')
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
                                Section::make('Tag Details')
                                    ->description('Product tags for SEO')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->placeholder('Family')
                                            ->helperText('Tag name'),
                                        TextInput::make('slug')
                                            ->required()
                                            ->placeholder('family-tag')
                                            ->helperText('URL slug'),
                                        Textarea::make('description')
                                            ->columnSpanFull()
                                            ->rows(3)
                                            ->helperText('Short description'),
                                        TextInput::make('color')
                                            ->placeholder('#f59e0b')
                                            ->helperText('Badge hex color'),
                                        Toggle::make('is_active')
                                            ->label('Active')
                                            ->helperText('Visible on frontend')
                                            ->inline(false)
                                            ->default(true),
                                    ]),
                            ]),
                    ])            ])
            ->columns(1);
    }
}
