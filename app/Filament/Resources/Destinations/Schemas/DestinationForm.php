<?php

namespace App\Filament\Resources\Destinations\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class DestinationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Destination Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-map-pin')
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
                                    ->description('Core destination')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        Select::make('region_id')
                                            ->label('Region')
                                            ->relationship('region', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Region link'),
                                        TextInput::make('name')
                                            ->label('Destination Name')
                                            ->placeholder('Ghorepani Poon Hill')
                                            ->required(),
                                        TextInput::make('slug')
                                            ->label('Slug')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->helperText('URL slug'),
                                        Textarea::make('overview')
                                            ->label('Overview')
                                            ->rows(4)
                                            ->helperText('Detail page text')
                                            ->columnSpanFull(),
                                        Textarea::make('short_description')
                                            ->label('Short Description')
                                            ->maxLength(500)
                                            ->helperText('Card excerpt')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Geo & Media')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('geo_tab_info')
                                            ->content('This tab manages related data.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Geography & Altitude')
                                    ->description('Geographic details')
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('altitude_m')
                                            ->label('Altitude (m)')
                                            ->numeric()
                                            ->placeholder('3210')
                                            ->helperText('Max altitude'),
                                        TextInput::make('latitude')
                                            ->label('Latitude')
                                            ->numeric()
                                            ->helperText('e.g., 28.12'),
                                        TextInput::make('longitude')
                                            ->label('Longitude')
                                            ->numeric()
                                            ->helperText('e.g., 83.12'),
                                    ]),
                                Section::make('Media')
                                    ->description('Images and video')
                                    ->columnSpanFull()
                                    ->schema([
                                        FileUpload::make('featured_image')
                                            ->label('Featured Image')
                                            ->image()
                                            ->directory('destinations')->disk('public')->visibility('public')
                                            ->columnSpanFull()
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                                            ->imagePreviewHeight('250'),
                                        TextInput::make('video_url')
                                            ->label('Video URL')
                                            ->url()
                                            ->placeholder('https://youtube.com/...')
                                            ->helperText('Destination video')
                                            ->columnSpanFull(),
                                        FileUpload::make('gallery')
                                            ->label('Gallery Images')
                                            ->multiple()
                                            ->image()
                                            ->directory('destinations/gallery')->disk('public')->visibility('public')
                                            ->helperText('Multiple images')
                                            ->columnSpanFull()
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                                            ->imagePreviewHeight('250')
                                            ->maxFiles(10),
                                        Textarea::make('map_embed')
                                            ->label('Map Embed')
                                            ->rows(3)
                                            ->helperText('Google Maps embed')
                                            ->columnSpanFull(),
                                        \Filament\Forms\Components\TagsInput::make('best_season')
                                            ->label('Best Season')
                                            ->placeholder('Spring')
                                            ->helperText('Press enter')
                                            ->columnSpanFull(),
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
                            ->default(true)
                            ->inline(false)
                            ->helperText('Visible on frontend'),
                    ])            ])
            ->columns(1);
    }
}
