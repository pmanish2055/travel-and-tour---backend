<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-sparkles')
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
                                Section::make('Activity Details')
                                    ->description('Manage activities for packages')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->placeholder('Trekking')
                                            ->helperText('Activity name'),
                                        TextInput::make('slug')
                                            ->required()
                                            ->placeholder('trekking-activity')
                                            ->helperText('URL slug'),
                                        Textarea::make('description')
                                            ->columnSpanFull()
                                            ->rows(3)
                                            ->helperText('Short description'),
                                        TextInput::make('icon')
                                            ->placeholder('map')
                                            ->helperText('Icon name, e.g. map'),
                                        Toggle::make('is_active')
                                            ->label('Active')
                                            ->helperText('Visible on frontend')
                                            ->inline(false)
                                            ->default(true),
                                    ]),
                            ]),
                    ])            ])
            ->columns(1);
    }
}
