<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class CategoryForm
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
                                Section::make('Basic Information')
                                    ->description('Core category details')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Category Name')
                                            ->placeholder('Trekking')
                                            ->required()
                                            ->columnSpanFull(),
                                        TextInput::make('slug')
                                            ->label('Slug')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->helperText('URL slug'),
                                        Textarea::make('description')
                                            ->label('Description')
                                            ->rows(3)
                                            ->helperText('Category description')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Branding')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('branding_tab_info')
                                            ->content('This tab manages related data.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Branding')
                                    ->description('Icon, color and image')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('icon')
                                            ->label('Icon')
                                            ->placeholder('map')
                                            ->helperText('e.g., map'),
                                        TextInput::make('color')
                                            ->label('Color')
                                            ->placeholder('#f59e0b')
                                            ->helperText('Hex color'),
                                        FileUpload::make('featured_image')
                                            ->label('Featured Image')
                                            ->image()
                                            ->directory('categories')
                                            ->columnSpanFull()
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                                            ->imagePreviewHeight('250'),
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
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Hide if off'),
                    ])            ])
            ->columns(1);
    }
}
