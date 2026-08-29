<?php

namespace App\Filament\Resources\Addons\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class AddonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-information-circle')
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
                                Section::make('Addon Details')
                                    ->description('Extra services for packages')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Addon Name')
                                            ->placeholder('Extra Porter')
                                            ->required()
                                            ->columnSpanFull(),
                                        TextInput::make('slug')
                                            ->label('Slug')
                                            ->required()
                                            ->unique(ignoreRecord: true),
                                        Textarea::make('description')
                                            ->label('Description')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        TextInput::make('price')
                                            ->label('Price')
                                            ->required()
                                            ->numeric()
                                            ->prefix('$')
                                            ->helperText('e.g., 50'),
                                        TextInput::make('price_type')
                                            ->label('Price Type')
                                            ->required()
                                            ->default('per_person')
                                            ->helperText('per_person or per_group'),
                                        TextInput::make('icon')
                                            ->label('Icon')
                                            ->placeholder('user')
                                            ->helperText('Icon name'),
                                    ]),
                            ]),
                    ]),

                Section::make('Publishing')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Visible on frontend'),
                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower shows first'),
                    ])            ])
            ->columns(1);
    }
}
