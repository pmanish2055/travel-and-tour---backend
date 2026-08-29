<?php

namespace App\Filament\Resources\Inquiries\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class InquiryForm
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
                                Section::make('Inquiry Details')
                                    ->description('Package inquiries')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        Select::make('package_id')
                                            ->relationship('package', 'title')
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Related package'),
                                        TextInput::make('name')
                                            ->required()
                                            ->placeholder('John Doe'),
                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->required()
                                            ->helperText('Contact email'),
                                        TextInput::make('phone')
                                            ->tel()
                                            ->required()
                                            ->helperText('Contact phone'),
                                        TextInput::make('country')
                                            ->placeholder('Nepal')
                                            ->helperText('Country'),
                                        DatePicker::make('travel_date')
                                            ->helperText('Planned date'),
                                        TextInput::make('pax')
                                            ->numeric()
                                            ->placeholder('2')
                                            ->helperText('Guests'),
                                        TextInput::make('assigned_to')
                                            ->numeric()
                                            ->placeholder('Staff ID')
                                            ->helperText('Assignee ID'),
                                        Textarea::make('message')
                                            ->required()
                                            ->rows(4)
                                            ->helperText('Inquiry message')
                                            ->columnSpanFull(),
                                        TextInput::make('status')
                                            ->required()
                                            ->default('new')
                                            ->helperText('Status: new/contacted')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])            ])
            ->columns(1);
    }
}
