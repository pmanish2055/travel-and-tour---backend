<?php

namespace App\Filament\Resources\TeamMembers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class TeamMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-users')
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
                                Section::make('Team Member Details')
                                    ->description('Team for About page')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->placeholder('John Doe'),
                                        TextInput::make('designation')
                                            ->required()
                                            ->placeholder('Guide'),
                                        FileUpload::make('photo')
                                            ->label('Photo')
                                            ->image()
                                            ->directory('team')
                                            ->helperText('400x400 JPG')
                                            ->columnSpanFull()
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                                            ->imagePreviewHeight('250'),
                                        Textarea::make('bio')
                                            ->rows(3)
                                            ->helperText('Short bio')
                                            ->columnSpanFull(),
                                        TextInput::make('facebook')
                                            ->placeholder('https://facebook.com/...')
                                            ->helperText('Facebook URL'),
                                        TextInput::make('instagram')
                                            ->placeholder('https://instagram.com/...')
                                            ->helperText('Instagram URL'),
                                        TextInput::make('linkedin')
                                            ->placeholder('https://linkedin.com/...')
                                            ->helperText('LinkedIn URL'),
                                        TextInput::make('sort_order')
                                            ->required()
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Lower shows first'),
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
