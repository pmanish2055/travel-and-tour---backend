<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-credit-card')
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
                                Section::make('Payment Details')
                                    ->description('Booking payments')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        Select::make('booking_id')
                                            ->relationship('booking', 'id')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Linked booking'),
                                        Select::make('gateway')
                                            ->options([
                                                'esewa' => 'eSewa',
                                                'khalti' => 'Khalti',
                                                'stripe' => 'Stripe',
                                                'bank' => 'Bank',
                                            ])
                                            ->required()
                                            ->helperText('Payment gateway'),
                                        TextInput::make('transaction_id')
                                            ->placeholder('TXN123')
                                            ->helperText('Gateway Txn ID'),
                                        TextInput::make('amount')
                                            ->required()
                                            ->numeric()
                                            ->prefix('Rs')
                                            ->helperText('Paid amount'),
                                        TextInput::make('currency')
                                            ->required()
                                            ->default('NPR')
                                            ->helperText('Currency'),
                                        Select::make('status')
                                            ->options([
                                                'pending' => 'Pending',
                                                'completed' => 'Completed',
                                                'failed' => 'Failed',
                                            ])
                                            ->required()
                                            ->default('pending'),
                                        Textarea::make('raw_response')
                                            ->rows(3)
                                            ->helperText('Gateway response')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])            ])
            ->columns(1);
    }
}
