<?php

namespace App\Filament\Resources\Regions\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class RegionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Region Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-map')
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
                                Section::make('Basic Information')
                                    ->description('Core region details')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        Select::make('parent_id')
                                            ->label('Parent Region')
                                            ->relationship('parent', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Top-level')
                                            ->helperText('Parent hierarchy')
                                            ->columnSpanFull(),
                                        TextInput::make('name')
                                            ->label('Region Name')
                                            ->placeholder('Everest Region')
                                            ->required()
                                            ->helperText('e.g., Everest'),
                                        TextInput::make('slug')
                                            ->label('Slug')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->helperText('URL slug'),
                                        Textarea::make('description')
                                            ->label('Description')
                                            ->rows(3)
                                            ->helperText('Short overview')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Media')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('media_tab_info')
                                            ->content('This tab manages related data.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Images')
                                    ->columnSpanFull()
                                    ->schema([
                                        FileUpload::make('featured_image')
                                            ->label('Featured Image')
                                            ->image()
                                            ->directory('regions')->disk('public')->visibility('public')
                                            ->helperText('Card image')
                                            ->columnSpanFull()
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                                            ->imagePreviewHeight('250'),
                                        FileUpload::make('gallery')
                                            ->label('Gallery')
                                            ->multiple()
                                            ->image()
                                            ->directory('regions/gallery')->disk('public')->visibility('public')
                                            ->helperText('Gallery images')
                                            ->columnSpanFull()
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                                            ->imagePreviewHeight('250')
                                            ->maxFiles(10),
                                    ]),
                            ]),

                        Tab::make('SEO')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('seo_tab_info')
                                            ->content('This tab handles SEO and publishing.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('SEO Settings')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('seo_title')
                                            ->label('SEO Title')
                                            ->maxLength(60)
                                            ->helperText('Max 60 chars'),
                                        TextInput::make('sort_order')
                                            ->label('Sort Order')
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Lower first'),
                                        Textarea::make('seo_description')
                                            ->label('SEO Description')
                                            ->rows(3)
                                            ->maxLength(160)
                                            ->helperText('Max 160 chars')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),

                Section::make('Publishing')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_featured')
                            ->label('Featured')
                            ->helperText('Show on homepage')
                            ->inline(false),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->helperText('Hide if off')
                            ->inline(false)
                            ->default(true),
                    ])            ])
            ->columns(1);
    }
}
