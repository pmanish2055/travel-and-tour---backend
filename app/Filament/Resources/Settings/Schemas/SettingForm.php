<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-information-circle')
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
                                Section::make('Setting Details')
                                    ->description('White-label dynamic settings')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        Select::make('group')
                                            ->label('Group')
                                            ->options([
                                                'company' => 'company',
                                                'seo' => 'seo',
                                                'tokens' => 'tokens',
                                                'reports' => 'reports',
                                                'mail' => 'mail',
                                                'custom' => 'custom',
                                                'general' => 'general',
                                            ])
                                            ->searchable()
                                            ->required()
                                            ->default('custom')
                                            ->helperText('API group filter'),
                                        TextInput::make('key')
                                            ->label('Key')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->placeholder('company.hero_title')
                                            ->helperText('Unique dot notation'),
                                        Textarea::make('value')
                                            ->label('Value')
                                            ->rows(3)
                                            ->helperText('Public via API')
                                            ->columnSpanFull(),
                                        Toggle::make('is_encrypted')
                                            ->label('Encrypted')
                                            ->helperText('Hide from API')
                                            ->inline(false)
                                            ->required(),
                                        TextInput::make('description')
                                            ->label('Description')
                                            ->placeholder('Purpose')
                                            ->helperText('Help text')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])            ])
            ->columns(1);
    }
}
