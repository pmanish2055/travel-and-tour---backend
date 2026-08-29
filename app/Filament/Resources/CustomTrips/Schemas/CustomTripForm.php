<?php

namespace App\Filament\Resources\CustomTrips\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class CustomTripForm
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
                                Section::make('Custom Trip Request')
                                    ->description('Build-your-own-trip requests')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('name')
                                            ->required(),
                                        TextInput::make('email')
                                            ->label('Email address')
                                            ->email()
                                            ->required(),
                                        TextInput::make('phone')
                                            ->tel()
                                            ->required(),
                                        TextInput::make('country')
                                            ->placeholder('Nepal'),
                                        TextInput::make('destination_interest')
                                            ->label('Destination Interest')
                                            ->columnSpanFull(),
                                        TextInput::make('duration_days')
                                            ->numeric()
                                            ->helperText('Days'),
                                        TextInput::make('budget')
                                            ->numeric()
                                            ->prefix('$'),
                                        DatePicker::make('travel_date'),
                                        TextInput::make('pax')
                                            ->numeric()
                                            ->helperText('Guests'),
                                        Textarea::make('interests')
                                            ->columnSpanFull()
                                            ->rows(2)
                                            ->helperText('Interests'),
                                        Textarea::make('message')
                                            ->columnSpanFull()
                                            ->rows(3)
                                            ->helperText('Customer message'),
                                        TextInput::make('status')
                                            ->required()
                                            ->default('new')
                                            ->helperText('Status'),
                                    ]),
                            ]),
                    ])            ])
            ->columns(1);
    }
}
