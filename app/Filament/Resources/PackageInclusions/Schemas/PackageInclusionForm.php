<?php

namespace App\Filament\Resources\PackageInclusions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PackageInclusionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-check-circle')
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
                                Section::make('Inclusion Details')
                                    ->description('Includes/Excludes')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        Select::make('package_id')
                                            ->relationship('package', 'title')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->columnSpanFull(),
                                        TextInput::make('type')
                                            ->required()
                                            ->default('include')
                                            ->helperText('include/exclude'),
                                        TextInput::make('title')
                                            ->required()
                                            ->columnSpanFull(),
                                        Textarea::make('description')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                        TextInput::make('icon')
                                            ->placeholder('check')
                                            ->helperText('Icon name'),
                                        TextInput::make('sort_order')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Lower first'),
                                    ]),
                            ]),
                    ])            ])
            ->columns(1);
    }
}
