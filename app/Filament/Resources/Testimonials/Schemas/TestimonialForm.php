<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\DatePicker;
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

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-chat-bubble-left-ellipsis')
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
                                Section::make('Testimonial Details')
                                    ->description('Customer reviews')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        Select::make('package_id')
                                            ->relationship('package', 'title')
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Related package'),
                                        TextInput::make('customer_name')
                                            ->required()
                                            ->placeholder('Jane Doe'),
                                        TextInput::make('customer_country')
                                            ->placeholder('USA')
                                            ->helperText('Country'),
                                        TextInput::make('rating')
                                            ->required()
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(5)
                                            ->helperText('1-5 rating'),
                                        FileUpload::make('avatar')
                                            ->label('Avatar')
                                            ->image()
                                            ->directory('testimonials')->disk('public')->visibility('public')
                                            ->avatar()
                                            ->helperText('200x200 avatar')
                                            ->columnSpanFull()
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg','image/jpg','image/png','image/webp'])
                                            ->imagePreviewHeight('250')->openable()->downloadable(),
                                        Textarea::make('comment')
                                            ->required()
                                            ->rows(4)
                                            ->helperText('Review text')
                                            ->columnSpanFull(),
                                        DatePicker::make('trip_date')
                                            ->helperText('Trip date'),
                                        Select::make('status')
                                            ->options([
                                                'pending' => 'Pending',
                                                'approved' => 'Approved',
                                                'rejected' => 'Rejected',
                                            ])
                                            ->required()
                                            ->default('pending'),
                                        Toggle::make('is_featured')
                                            ->label('Featured')
                                            ->helperText('Show on homepage')
                                            ->inline(false)
                                            ->default(false),
                                    ]),
                            ]),
                    ])            ])
            ->columns(1);
    }
}
