<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Booking')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Trip Details')
                            ->icon('heroicon-o-map')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('trip_tab_info')
                                            ->content('This tab manages related data.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Trip')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('booking_code')
                                            ->label('Booking Code')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->placeholder('Auto-generated'),
                                        Select::make('package_id')
                                            ->relationship('package','title')
                                            ->required()
                                            ->searchable()
                                            ->preload(),
                                        Select::make('departure_id')
                                            ->relationship('departure','id')
                                            ->label('Departure')
                                            ->searchable(),
                                        DatePicker::make('travel_date')
                                            ->required(),
                                        TextInput::make('pax_adult')
                                            ->numeric()
                                            ->required()
                                            ->default(1)
                                            ->helperText('Adults'),
                                        TextInput::make('pax_child')
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Children'),
                                        TextInput::make('source')
                                            ->placeholder('web / agent')
                                            ->helperText('Booking source'),
                                    ]),
                                Section::make('Customer')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('customer_name')->required(),
                                        TextInput::make('customer_email')->email()->required(),
                                        TextInput::make('customer_phone')->tel()->required(),
                                        TextInput::make('customer_country')->placeholder('Nepal'),
                                        Textarea::make('special_request')
                                            ->label('Special Request')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        Select::make('user_id')
                                            ->relationship('user','name')
                                            ->label('Linked User')
                                            ->searchable()
                                            ->placeholder('Guest'),
                                    ]),
                            ]),
                        Tab::make('Payment')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('payment_tab_info')
                                            ->content('This tab manages related data.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Payment & Status')
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('total_amount')->numeric()->required()->prefix('Rs'),
                                        TextInput::make('advance_amount')->numeric()->default(0)->prefix('Rs'),
                                        TextInput::make('pax_total')->numeric()->disabled()->dehydrated(false),
                                        Select::make('payment_status')
                                            ->options(['unpaid'=>'Unpaid','partial'=>'Partial','paid'=>'Paid','refunded'=>'Refunded'])
                                            ->required()->default('unpaid'),
                                        Select::make('booking_status')
                                            ->options(['pending'=>'Pending','confirmed'=>'Confirmed','completed'=>'Completed','cancelled'=>'Cancelled'])
                                            ->required()->default('pending'),
                                        DateTimePicker::make('cancelled_at')->label('Cancelled At'),
                                    ]),
                            ]),
                    ])            ])->columns(1);
    }
}
