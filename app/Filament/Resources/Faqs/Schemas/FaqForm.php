<?php

namespace App\Filament\Resources\Faqs\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class FaqForm
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
                                Section::make('FAQ Details')
                                    ->description('Global FAQs')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('question')
                                            ->required()
                                            ->placeholder('What is included?')
                                            ->helperText('FAQ question')
                                            ->columnSpanFull(),
                                        Textarea::make('answer')
                                            ->required()
                                            ->rows(4)
                                            ->helperText('Answer text')
                                            ->columnSpanFull(),
                                        TextInput::make('category')
                                            ->placeholder('General')
                                            ->helperText('Group category'),
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
