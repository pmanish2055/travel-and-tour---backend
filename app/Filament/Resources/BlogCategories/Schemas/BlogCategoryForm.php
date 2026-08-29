<?php

namespace App\Filament\Resources\BlogCategories\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class BlogCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Category Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-tag')
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
                                Section::make('Category Information')
                                    ->description('Basic category details')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Category Name')
                                            ->placeholder('Travel Tips')
                                            ->required()
                                            ->maxLength(255)
                                            ->helperText('e.g., Travel Tips'),
                                        TextInput::make('slug')
                                            ->label('Slug')
                                            ->placeholder('travel-tips')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->helperText('URL slug'),
                                        Textarea::make('description')
                                            ->label('Description')
                                            ->rows(3)
                                            ->helperText('Short description')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),

                Section::make('Publishing')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->helperText('Hide if off')
                            ->inline(false)
                            ->default(true),
                    ])            ])
            ->columns(1);
    }
}
