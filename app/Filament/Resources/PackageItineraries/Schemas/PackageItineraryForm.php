<?php

namespace App\Filament\Resources\PackageItineraries\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PackageItineraryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-map')
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
                                Section::make('Itinerary Details')
                                    ->description('Day-wise itinerary')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        Select::make('package_id')
                                            ->relationship('package', 'title')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->columnSpanFull(),
                                        TextInput::make('day_number')
                                            ->required()
                                            ->numeric()
                                            ->helperText('Day no.'),
                                        TextInput::make('title')
                                            ->required()
                                            ->helperText('Day title'),
                                        Textarea::make('description')
                                            ->required()
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        TextInput::make('max_altitude_m')
                                            ->numeric()
                                            ->helperText('Altitude (m)'),
                                        TextInput::make('meals')
                                            ->placeholder('B/L/D')
                                            ->helperText('Meal plan'),
                                        TextInput::make('accommodation')
                                            ->helperText('Stay type'),
                                        TextInput::make('overnight_at')
                                            ->helperText('Overnight place'),
                                        TextInput::make('walking_hours')
                                            ->numeric()
                                            ->helperText('Hours'),
                                        TextInput::make('sort_order')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Order'),
                                    ]),
                            ]),
                    ])            ])
            ->columns(1);
    }
}
