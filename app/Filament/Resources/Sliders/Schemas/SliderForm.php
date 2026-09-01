<?php

namespace App\Filament\Resources\Sliders\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SliderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-photo')
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
                                Section::make('Slider Details')
                                    ->description('Homepage hero slider')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('title')
                                            ->required()
                                            ->placeholder('Explore Nepal')
                                            ->helperText('Main heading'),
                                        TextInput::make('subtitle')
                                            ->placeholder('Adventure awaits')
                                            ->helperText('Sub heading'),
                                        FileUpload::make('image')
                                            ->image()
                                            ->required()
                                            ->directory('sliders')->disk('public')->visibility('public')
                                            ->helperText('1920x800 recommended')
                                            ->columnSpanFull()
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg','image/jpg','image/png','image/webp'])
                                            ->imagePreviewHeight('250')->openable()->downloadable(),
                                        TextInput::make('cta_text')
                                            ->label('CTA Text')
                                            ->placeholder('Book Now')
                                            ->helperText('Button label'),
                                        TextInput::make('cta_link')
                                            ->label('CTA Link')
                                            ->placeholder('/packages')
                                            ->helperText('Button URL'),
                                        TextInput::make('sort_order')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Lower shows first'),
                                        Toggle::make('is_active')
                                            ->label('Active')
                                            ->helperText('Visible on homepage')
                                            ->inline(false)
                                            ->default(true),
                                    ]),
                            ]),
                    ])            ])
            ->columns(1);
    }
}
