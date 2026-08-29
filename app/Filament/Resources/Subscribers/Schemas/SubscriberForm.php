<?php

namespace App\Filament\Resources\Subscribers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SubscriberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-envelope')
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
                                Section::make('Subscriber Details')
                                    ->description('Newsletter subscribers')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('email')
                                            ->label('Email address')
                                            ->email()
                                            ->required()
                                            ->columnSpanFull(),
                                        Toggle::make('is_verified')
                                            ->required()
                                            ->inline(false),
                                        DateTimePicker::make('verified_at')
                                            ->label('Verified At'),
                                    ]),
                            ]),
                    ])            ])
            ->columns(1);
    }
}
