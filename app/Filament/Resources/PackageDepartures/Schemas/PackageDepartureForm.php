<?php

namespace App\Filament\Resources\PackageDepartures\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PackageDepartureForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-calendar')
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
                                Section::make('Departure Details')
                                    ->description('Fixed departure dates')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        Select::make('package_id')
                                            ->label('Package')
                                            ->relationship('package', 'title')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->columnSpanFull(),
                                        TextInput::make('price')
                                            ->label('Price Override')
                                            ->numeric()
                                            ->prefix('$')
                                            ->helperText('Empty = package price'),
                                        DateTimePicker::make('departure_date')
                                            ->label('Departure Date')
                                            ->required()
                                            ->native(false),
                                        DateTimePicker::make('return_date')
                                            ->label('Return Date')
                                            ->required()
                                            ->native(false),
                                    ]),
                                Section::make('Seats & Status')
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('seats_total')
                                            ->label('Total Seats')
                                            ->numeric()
                                            ->default(16),
                                        TextInput::make('seats_booked')
                                            ->label('Booked Seats')
                                            ->numeric()
                                            ->default(0),
                                        Select::make('status')
                                            ->label('Status')
                                            ->options([
                                                'open' => 'Open',
                                                'guaranteed' => 'Guaranteed',
                                                'closed' => 'Closed',
                                                'cancelled' => 'Cancelled',
                                            ])
                                            ->default('open'),
                                        TextInput::make('note')
                                            ->label('Note')
                                            ->placeholder('Festival departure')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])            ])
            ->columns(1);
    }
}
