<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-chat-bubble-left')
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
                                Section::make('Contact Message')
                                    ->description('Messages from contact form')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('name')
                                            ->required(),
                                        TextInput::make('email')
                                            ->label('Email address')
                                            ->email()
                                            ->required(),
                                        TextInput::make('phone')
                                            ->tel()
                                            ->helperText('Contact phone'),
                                        TextInput::make('subject')
                                            ->placeholder('Subject'),
                                        Textarea::make('message')
                                            ->required()
                                            ->rows(4)
                                            ->columnSpanFull(),
                                        Toggle::make('is_read')
                                            ->required()
                                            ->inline(false)
                                            ->default(false),
                                    ]),
                            ]),
                    ])            ])
            ->columns(1);
    }
}
