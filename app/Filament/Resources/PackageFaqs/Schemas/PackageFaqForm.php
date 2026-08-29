<?php

namespace App\Filament\Resources\PackageFaqs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PackageFaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-question-mark-circle')
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
                                Section::make('Package FAQ Details')
                                    ->description('FAQs per package')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        Select::make('package_id')
                                            ->relationship('package', 'title')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->columnSpanFull(),
                                        TextInput::make('question')
                                            ->required()
                                            ->columnSpanFull(),
                                        Textarea::make('answer')
                                            ->required()
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        TextInput::make('sort_order')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Lower shows first'),
                                    ]),
                            ]),
                    ])            ])
            ->columns(1);
    }
}
