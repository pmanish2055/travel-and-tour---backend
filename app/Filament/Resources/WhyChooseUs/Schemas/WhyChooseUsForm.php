<?php

namespace App\Filament\Resources\WhyChooseUs\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class WhyChooseUsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-star')
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
                                Section::make('Feature Details')
                                    ->description('Homepage Why Choose Us')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('title')
                                            ->required()
                                            ->placeholder('Expert Guides'),
                                        TextInput::make('icon')
                                            ->placeholder('star')
                                            ->helperText('Icon name'),
                                        Textarea::make('description')
                                            ->required()
                                            ->rows(3)
                                            ->helperText('Feature text')
                                            ->columnSpanFull(),
                                        TextInput::make('sort_order')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Lower shows first'),
                                        Toggle::make('is_active')
                                            ->label('Active')
                                            ->helperText('Visible on homepage')
                                            ->inline(false)
                                            ->default(true),
                                    ]),
                            ]),
                    ]),

                Section::make('Information')
                    ->icon('heroicon-o-information-circle')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('info')
                            ->content('The why_choose_us table holds homepage feature blocks (e.g., Expert Guides) with title, icon, description, sort_order and is_active. Powers the \'Why Choose Us\' section when active, ordered by sort_order.')
                            ->columnSpanFull(),
                    ]),
            ])
            ->columns(1);
    }
}
