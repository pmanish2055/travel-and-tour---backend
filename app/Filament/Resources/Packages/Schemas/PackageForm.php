<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Schema;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Package Details')
                    ->columnSpanFull()
                    ->tabs([

                        Tab::make('General Details')
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
                                Section::make('Basic Information')
                                    ->description('Core package details')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Package Title')
                                            ->placeholder('Everest Base Camp 14 Days')
                                            ->required()
                                            ->maxLength(255)
                                            ->helperText('Frontend card title')
                                            ->columnSpanFull(),
                                        TextInput::make('slug')
                                            ->label('URL Slug')
                                            ->placeholder('everest-base-camp-14-days')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->helperText('URL: /packages/{slug}'),
                                        Select::make('category_id')
                                            ->label('Category')
                                            ->relationship('category', 'name')
                                            ->required()
                                            ->preload()
                                            ->searchable()
                                            ->helperText('Select category'),
                                        Select::make('destination_id')
                                            ->label('Primary Destination')
                                            ->relationship('destination', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Main destination'),
                                        Select::make('region_id')
                                            ->label('Region')
                                            ->relationship('region', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Region grouping'),
                                        Select::make('difficulty')
                                            ->label('Difficulty')
                                            ->options([
                                                'easy' => 'Easy',
                                                'moderate' => 'Moderate',
                                                'hard' => 'Hard',
                                                'strenuous' => 'Strenuous',
                                                'challenging' => 'Challenging',
                                            ])
                                            ->required()
                                            ->default('moderate')
                                            ->helperText('Fitness level'),
                                        Select::make('trip_type')
                                            ->label('Trip Type')
                                            ->options([
                                                'private' => 'Private',
                                                'fixed_departure' => 'Fixed Departure',
                                                'daily' => 'Daily',
                                            ])
                                            ->required()
                                            ->default('private')
                                            ->helperText('Departure type'),
                                        TextInput::make('duration_days')
                                            ->label('Duration (Days)')
                                            ->required()
                                            ->numeric()
                                            ->minValue(1)
                                            ->helperText('Total days'),
                                        TextInput::make('duration_nights')
                                            ->label('Duration (Nights)')
                                            ->required()
                                            ->numeric()
                                            ->helperText('Total nights'),
                                        TextInput::make('group_size_min')
                                            ->label('Min Group Size')
                                            ->numeric()
                                            ->default(2)
                                            ->helperText('Min travelers'),
                                        TextInput::make('group_size_max')
                                            ->label('Max Group Size')
                                            ->numeric()
                                            ->default(16)
                                            ->helperText('Max travelers'),
                                        TextInput::make('max_altitude_m')
                                            ->label('Max Altitude (m)')
                                            ->numeric()
                                            ->placeholder('5364')
                                            ->helperText('Highest point')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Trip Specifics')
                                    ->description('Accommodation, meals, etc.')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('accommodation')
                                            ->label('Accommodation')
                                            ->placeholder('Teahouse / Hotel')
                                            ->helperText('Stay type'),
                                        TextInput::make('meal_plan')
                                            ->label('Meal Plan')
                                            ->placeholder('B/B')
                                            ->helperText('Meal codes'),
                                        TextInput::make('transportation')
                                            ->label('Transportation')
                                            ->placeholder('Private vehicle')
                                            ->columnSpanFull(),
                                        TagsInput::make('best_season')
                                            ->label('Best Season')
                                            ->placeholder('Spring')
                                            ->helperText('Press enter')
                                            ->columnSpanFull(),
                                        RichEditor::make('overview')
                                            ->label('Overview')
                                            ->helperText('Detail page content')
                                            ->columnSpanFull(),
                                        Select::make('activities')
                                            ->label('Activities')
                                            ->relationship('activities', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->helperText('Select activities')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Itinerary')
                            ->icon('heroicon-o-map')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('itinerary_tab_info')
                                            ->content('This tab manages day-wise itinerary.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Day-wise Itinerary')
                                    ->description('Each day of trip')
                                    ->columnSpanFull()
                                    ->schema([
                                        Repeater::make('itineraries')
                                            ->label('Itinerary Days')
                                            ->relationship('itineraries')
                                            ->orderColumn('sort_order')
                                            ->reorderable()
                                            ->collapsible()
                                            ->collapsed()
                                            ->itemLabel(fn (array $state): ?string => isset($state['title']) ? "Day {$state['day_number']}: {$state['title']}" : 'New Day')
                                            ->schema([
                                                Grid::make(3)->schema([
                                                    TextInput::make('day_number')
                                                        ->label('Day No.')
                                                        ->numeric()
                                                        ->required()
                                                        ->helperText('1,2,3...'),
                                                    TextInput::make('title')
                                                        ->label('Day Title')
                                                        ->placeholder('Trek to Dingboche')
                                                        ->required()
                                                        ->columnSpan(2),
                                                ]),
                                                Textarea::make('description')
                                                    ->label('Day Description')
                                                    ->required()
                                                    ->rows(3)
                                                    ->helperText('Day details')
                                                    ->columnSpanFull(),
                                                Grid::make(3)->schema([
                                                    TextInput::make('max_altitude_m')
                                                        ->label('Altitude (m)')
                                                        ->numeric()
                                                        ->placeholder('4410'),
                                                    TextInput::make('meals')
                                                        ->label('Meals')
                                                        ->placeholder('B/L/D meal'),
                                                    TextInput::make('accommodation')
                                                        ->label('Accommodation')
                                                        ->placeholder('Teahouse'),
                                                ]),
                                                Grid::make(2)->schema([
                                                    TextInput::make('overnight_at')
                                                        ->label('Overnight At')
                                                        ->placeholder('Dingboche'),
                                                    TextInput::make('walking_hours')
                                                        ->label('Walking Hours')
                                                        ->numeric()
                                                        ->placeholder('5-6'),
                                                ]),
                                            ])
                                            ->addActionLabel('Add Day')
                                            ->helperText('Drag to reorder')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Includes / Excludes')
                            ->icon('heroicon-o-check-circle')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('includes_tab_info')
                                            ->content('This tab manages inclusions and exclusions.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('What is Included')
                                    ->description('Included items')
                                    ->columnSpanFull()
                                    ->schema([
                                        Repeater::make('includes')
                                            ->label('Includes')
                                            ->relationship('includes')
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Include Title')
                                                    ->placeholder('All ground transport')
                                                    ->required()
                                                    ->columnSpan(2),
                                                TextInput::make('icon')
                                                    ->label('Icon')
                                                    ->placeholder('check'),
                                                \Filament\Forms\Components\Hidden::make('type')
                                                    ->default('include'),
                                            ])
                                            ->columns(3)
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'New Include')
                                            ->addActionLabel('Add Include')
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('What is Excluded')
                                    ->description('Excluded items')
                                    ->columnSpanFull()
                                    ->schema([
                                        Repeater::make('excludes')
                                            ->label('Excludes')
                                            ->relationship('excludes')
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Exclude Title')
                                                    ->placeholder('International flights')
                                                    ->required(),
                                                TextInput::make('icon')
                                                    ->label('Icon')
                                                    ->placeholder('x-mark'),
                                                \Filament\Forms\Components\Hidden::make('type')
                                                    ->default('exclude'),
                                            ])
                                            ->columns(2)
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'New Exclude')
                                            ->addActionLabel('Add Exclude')
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('All Includes/Excludes')
                                    ->description('Advanced combined list')
                                    ->columnSpanFull()
                                    ->schema([
                                        Repeater::make('inclusions')
                                            ->label('All Items')
                                            ->relationship('inclusions')
                                            ->schema([
                                                Select::make('type')
                                                    ->options(['include' => 'Include', 'exclude' => 'Exclude'])
                                                    ->required()
                                                    ->default('include'),
                                                TextInput::make('title')->required(),
                                                Textarea::make('description')->rows(2),
                                            ])
                                            ->columns(3)
                                            ->collapsible()
                                            ->collapsed()
                                            ->helperText('Use if needed per item')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Departures')
                            ->icon('heroicon-o-calendar')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('departures_tab_info')
                                            ->content('This tab manages departure dates and seats.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Fixed Departures')
                                    ->description('Fixed group tour dates')
                                    ->columnSpanFull()
                                    ->schema([
                                        Repeater::make('departures')
                                            ->label('Departure Dates')
                                            ->relationship('departures')
                                            ->schema([
                                                Grid::make(3)->schema([
                                                    DateTimePicker::make('departure_date')
                                                        ->label('Departure Date')
                                                        ->required()
                                                        ->native(false)
                                                        ->helperText('Start date'),
                                                    DateTimePicker::make('return_date')
                                                        ->label('Return Date')
                                                        ->required()
                                                        ->helperText('End date'),
                                                    TextInput::make('price')
                                                        ->label('Price')
                                                        ->numeric()
                                                        ->prefix('$')
                                                        ->helperText('Override price'),
                                                ]),
                                                Grid::make(3)->schema([
                                                    TextInput::make('seats_total')
                                                        ->label('Total Seats')
                                                        ->numeric()
                                                        ->default(16),
                                                    TextInput::make('seats_booked')
                                                        ->label('Booked Seats')
                                                        ->numeric()
                                                        ->default(0),
                                                    Select::make('status')
                                                        ->label('Status')
                                                        ->options([
                                                            'open' => 'Open',
                                                            'guaranteed' => 'Guaranteed',
                                                            'closed' => 'Closed',
                                                            'cancelled' => 'Cancelled',
                                                        ])
                                                        ->default('open'),
                                                ]),
                                                TextInput::make('note')
                                                    ->label('Note')
                                                    ->placeholder('Festival departure')
                                                    ->columnSpanFull(),
                                            ])
                                            ->itemLabel(fn (array $state): ?string => ($state['departure_date'] ?? 'New') . ' - ' . ($state['status'] ?? 'open'))
                                            ->addActionLabel('Add Departure')
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Pricing')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('pricing_tab_info')
                                            ->content('This tab manages pricing and tiers.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Base Pricing')
                                    ->description('Main price')
                                    ->columns(3)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('price')
                                            ->label('Base Price')
                                            ->required()
                                            ->numeric()
                                            ->prefix('$')
                                            ->helperText('Per person'),
                                        TextInput::make('discount_price')
                                            ->label('Discount Price')
                                            ->numeric()
                                            ->prefix('$')
                                            ->helperText('Sale price'),
                                        Select::make('currency')
                                            ->label('Currency')
                                            ->options(['NPR' => 'NPR', 'USD' => 'USD'])
                                            ->required()
                                            ->default('USD'),
                                        Select::make('price_type')
                                            ->label('Price Type')
                                            ->options(['per_person' => 'Per Person', 'per_group' => 'Per Group'])
                                            ->default('per_person'),
                                        Toggle::make('is_price_on_request')
                                            ->label('Price on Request')
                                            ->helperText('Hide price'),
                                    ]),

                                Section::make('Pricing Tiers')
                                    ->description('Single vs group pricing')
                                    ->columnSpanFull()
                                    ->schema([
                                        Repeater::make('pricings')
                                            ->label('Pricing Tiers')
                                            ->relationship('pricings')
                                            ->orderColumn('sort_order')
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => ($state['title'] ?? 'New Tier') . ' - $' . ($state['price_per_person'] ?? '0'))
                                            ->schema([
                                                Grid::make(3)->schema([
                                                    TextInput::make('title')
                                                        ->label('Tier Title')
                                                        ->placeholder('Single Traveler')
                                                        ->required(),
                                                    Select::make('type')
                                                        ->label('Type')
                                                        ->options([
                                                            'single' => 'Single',
                                                            'group' => 'Group',
                                                            'private' => 'Private',
                                                            'fixed' => 'Fixed',
                                                        ])
                                                        ->required()
                                                        ->default('group'),
                                                    Select::make('currency')
                                                        ->label('Currency')
                                                        ->options(['NPR' => 'NPR', 'USD' => 'USD'])
                                                        ->default('USD'),
                                                ]),
                                                Grid::make(3)->schema([
                                                    TextInput::make('pax_min')
                                                        ->label('Min Pax')
                                                        ->numeric()
                                                        ->default(1)
                                                        ->required(),
                                                    TextInput::make('pax_max')
                                                        ->label('Max Pax')
                                                        ->numeric()
                                                        ->placeholder('Unlimited'),
                                                    TextInput::make('price_per_person')
                                                        ->label('Price per Person')
                                                        ->numeric()
                                                        ->required()
                                                        ->prefix('$'),
                                                ]),
                                                Grid::make(2)->schema([
                                                    TextInput::make('total_price')
                                                        ->label('Total Price')
                                                        ->numeric()
                                                        ->prefix('$')
                                                        ->helperText('Per group total'),
                                                    Toggle::make('is_active')
                                                        ->label('Active')
                                                        ->default(true),
                                                ]),
                                                Textarea::make('description')
                                                    ->label('Description')
                                                    ->placeholder('Includes guide')
                                                    ->rows(2)
                                                    ->columnSpanFull(),
                                            ])
                                            ->addActionLabel('Add Tier')
                                            ->helperText('Add single/group tiers')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('FAQs & Equipment')
                            ->icon('heroicon-o-question-mark-circle')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('faqs_equipment_tab_info')
                                            ->content('This tab manages related data.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Frequently Asked Questions')
                                    ->description('Package FAQs')
                                    ->columnSpanFull()
                                    ->schema([
                                        Repeater::make('faqs')
                                            ->label('FAQs')
                                            ->relationship('faqs')
                                            ->schema([
                                                TextInput::make('question')
                                                    ->label('Question')
                                                    ->placeholder('Do I need TIMS?')
                                                    ->required()
                                                    ->columnSpanFull(),
                                                Textarea::make('answer')
                                                    ->label('Answer')
                                                    ->required()
                                                    ->rows(3)
                                                    ->helperText('Accordion answer')
                                                    ->columnSpanFull(),
                                            ])
                                            ->itemLabel(fn (array $state): ?string => $state['question'] ?? 'New FAQ')
                                            ->addActionLabel('Add FAQ')
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('Equipment / Gear List')
                                    ->description('Gear required')
                                    ->columnSpanFull()
                                    ->schema([
                                        Repeater::make('equipment')
                                            ->label('Equipment')
                                            ->relationship('equipment')
                                            ->schema([
                                                TextInput::make('item')
                                                    ->label('Item')
                                                    ->placeholder('Down jacket')
                                                    ->required(),
                                                Textarea::make('description')
                                                    ->label('Description')
                                                    ->rows(2)
                                                    ->placeholder('-10°C rating'),
                                                Toggle::make('is_required')
                                                    ->label('Required')
                                                    ->default(true),
                                            ])
                                            ->columns(3)
                                            ->itemLabel(fn (array $state): ?string => $state['item'] ?? 'New Item')
                                            ->addActionLabel('Add Gear')
                                            ->reorderable()
                                            ->collapsible()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Gallery')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('gallery_tab_info')
                                            ->content('This tab manages package images.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Featured Image')
                                    ->description('Main image')
                                    ->columnSpanFull()
                                    ->schema([
                                        FileUpload::make('featured_image')
                                            ->label('Featured Image')
                                            ->image()
                                            ->directory('packages/featured')
                                            ->helperText('1200x800 recommended')
                                            ->columnSpanFull()
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                                            ->imagePreviewHeight('250'),
                                    ]),
                                Section::make('Gallery Images')
                                    ->description('Detail slider images')
                                    ->columnSpanFull()
                                    ->schema([
                                        FileUpload::make('gallery')
                                            ->label('Gallery')
                                            ->multiple()
                                            ->image()
                                            ->directory('packages/gallery')
                                            ->helperText('Upload 5-10 images')
                                            ->columnSpanFull()
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                                            ->imagePreviewHeight('250')
                                            ->maxFiles(10),
                                    ]),
                            ]),

                        Tab::make('SEO & Tags')
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
                                \Filament\Schemas\Components\Grid::make(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        Section::make('Publishing')
                                            ->description('Visibility')
                                            ->columns(2)
                                            ->schema([
                                                Select::make('status')->label('Status')->options(['draft'=>'Draft','published'=>'Published','archived'=>'Archived'])->required()->default('draft'),
                                                DateTimePicker::make('published_at')->label('Publish Date'),
                                                TextInput::make('sort_order')->label('Sort Order')->numeric()->default(0),
                                                Toggle::make('featured')->label('Featured')->inline(false),
                                                Toggle::make('is_trending')->label('Trending')->inline(false),
                                                Toggle::make('is_popular')->label('Popular')->inline(false),
                                                TextInput::make('view_count')->label('Views')->numeric()->default(0)->disabled(),
                                            ]),
                                        Section::make('SEO Settings')
                                            ->description('Meta for Google')
                                            ->columns(1)
                                            ->schema([
                                                TextInput::make('seo_title')->label('SEO Title')->maxLength(60),
                                                TextInput::make('seo_keywords')->label('SEO Keywords')->placeholder('everest trek'),
                                                Textarea::make('seo_description')->label('SEO Description')->rows(3)->maxLength(160),
                                            ]),
                                    ]),
                                Section::make('Product Tags')
                                    ->description('Tags')
                                    ->columnSpanFull()
                                    ->schema([
                                        Select::make('tags')->label('Product Tags')->relationship('tags','name')->multiple()->preload()->searchable()->columnSpanFull(),
                                        Select::make('activities')->label('Activities')->relationship('activities','name')->multiple()->preload()->columnSpanFull(),
                                        Select::make('addons')->label('Addons')->relationship('addons','name')->multiple()->preload()->columnSpanFull(),
                                    ]),
                                Section::make('Highlights')
                                    ->columnSpanFull()
                                    ->schema([
                                        TagsInput::make('highlights')->label('Highlights')->placeholder('Trek to EBC 5364m')->columnSpanFull(),
                                    ]),
                            ]),
                    ])            ])
            ->columns(1);
    }
}
