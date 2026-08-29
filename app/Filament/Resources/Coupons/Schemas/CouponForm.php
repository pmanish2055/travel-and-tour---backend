<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-ticket')
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
                                Section::make('Coupon Details')
                                    ->description('Discount coupons')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('code')
                                            ->required()
                                            ->placeholder('NEPAL10')
                                            ->helperText('Coupon code'),
                                        Select::make('discount_type')
                                            ->options([
                                                'percent' => 'Percent',
                                                'fixed' => 'Fixed',
                                            ])
                                            ->required()
                                            ->default('percent'),
                                        TextInput::make('value')
                                            ->required()
                                            ->numeric()
                                            ->placeholder('10')
                                            ->helperText('Discount value'),
                                        Toggle::make('is_active')
                                            ->label('Active')
                                            ->helperText('Enable coupon')
                                            ->inline(false)
                                            ->default(true),
                                        DatePicker::make('valid_from')
                                            ->helperText('Start date'),
                                        DatePicker::make('valid_to')
                                            ->helperText('End date'),
                                        TextInput::make('usage_limit')
                                            ->numeric()
                                            ->placeholder('100')
                                            ->helperText('Max uses'),
                                        TextInput::make('used_count')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Times used'),
                                    ]),
                            ]),
                    ])            ])
            ->columns(1);
    }
}
