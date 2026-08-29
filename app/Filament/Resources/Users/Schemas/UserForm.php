<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('User')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-user')
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
                                Section::make('Account Information')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->placeholder('Full name')
                                            ->columnSpanFull(),
                                        TextInput::make('email')
                                            ->label('Email address')
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true),
                                        TextInput::make('phone')
                                            ->tel()
                                            ->placeholder('+977-9800000000'),
                                        Select::make('role')
                                            ->options(['super_admin'=>'Super Admin','admin'=>'Admin','editor'=>'Editor','agent'=>'Agent','customer'=>'Customer'])
                                            ->required()
                                            ->default('agent')
                                            ->searchable(),
                                        FileUpload::make('avatar')
                                            ->image()
                                            ->avatar()
                                            ->directory('avatars')
                                            ->columnSpanFull()
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                                            ->imagePreviewHeight('250'),
                                    ]),
                            ]),
                        Tab::make('Security')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('security_tab_info')
                                            ->content('This tab manages related data.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Security')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('password')
                                            ->password()
                                            ->revealable()
                                            ->dehydrated(fn ($state) => filled($state))
                                            ->required(fn (string $context) => $context === 'create')
                                            ->helperText('Blank keeps existing'),
                                        DateTimePicker::make('email_verified_at')
                                            ->label('Verified At'),
                                    ]),
                            ]),
                    ]),

                Section::make('Status')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Enable account'),
                    ]),
                Section::make('Information')
                    ->icon('heroicon-o-information-circle')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('info')
                            ->content('The users table manages accounts with name, email, phone, role (super_admin/admin/editor/agent/customer), avatar, password and email_verified_at. is_active disables login; role drives Filament access and frontend authentication.')
                            ->columnSpanFull(),
                    ])
            ])->columns(1);
    }
}
