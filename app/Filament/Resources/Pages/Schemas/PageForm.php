<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-document-text')
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
                                Section::make('Page Details')
                                    ->description('CMS pages like About, Terms')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('title')
                                            ->required()
                                            ->columnSpanFull(),
                                        TextInput::make('slug')
                                            ->required()
                                            ->helperText('URL slug'),
                                        Textarea::make('content')
                                            ->required()
                                            ->rows(5)
                                            ->columnSpanFull(),
                                        TextInput::make('template')
                                            ->placeholder('default')
                                            ->helperText('Blade template'),
                                        TextInput::make('seo_title')
                                            ->helperText('Max 60 chars')
                                            ->maxLength(60),
                                        Textarea::make('seo_description')
                                            ->rows(3)
                                            ->helperText('Max 160 chars')
                                            ->maxLength(160)
                                            ->columnSpanFull(),
                                        Toggle::make('is_system')
                                            ->required()
                                            ->inline(false)
                                            ->helperText('System pages locked'),
                                        TextInput::make('status')
                                            ->required()
                                            ->default('published'),
                                    ]),
                            ]),
                    ])            ])
            ->columns(1);
    }
}
