<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Blog Post Details')
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
                                Section::make('Basic Information')
                                    ->description('Core blog identification')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Post Title')
                                            ->placeholder('Best Time to Visit Nepal')
                                            ->required()
                                            ->maxLength(255)
                                            ->helperText('Shown on listing')
                                            ->columnSpanFull(),
                                        TextInput::make('slug')
                                            ->label('URL Slug')
                                            ->placeholder('best-time-to-visit-nepal')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->helperText('URL: /blogs/{slug}'),
                                        Select::make('blog_category_id')
                                            ->label('Category')
                                            ->relationship('category', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Select category'),
                                        Select::make('user_id')
                                            ->label('Author')
                                            ->relationship('author', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->helperText('Author user'),
                                        Textarea::make('excerpt')
                                            ->label('Excerpt')
                                            ->placeholder('Short excerpt')
                                            ->rows(3)
                                            ->maxLength(500)
                                            ->helperText('Card excerpt')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Content')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('content_tab_info')
                                            ->content('This tab manages related data.')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
                                Section::make('Main Content')
                                    ->description('Rich content')
                                    ->columnSpanFull()
                                    ->schema([
                                        RichEditor::make('content')
                                            ->label('Content')
                                            ->required()
                                            ->helperText('Rich text content')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Media')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make('Tab Information')
                                    ->icon('heroicon-o-information-circle')
                                    ->collapsible()
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('media_tab_info')
                                            ->content('This tab manages related data.')
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
                                            ->directory('blogs/featured')->disk('public')->visibility('public')
                                            ->helperText('1200x630 recommended')
                                            ->columnSpanFull()
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                                            ->imagePreviewHeight('250'),
                                    ]),
                                Section::make('Gallery')
                                    ->description('Additional images')
                                    ->columnSpanFull()
                                    ->schema([
                                        FileUpload::make('gallery')
                                            ->label('Gallery Images')
                                            ->multiple()
                                            ->image()
                                            ->directory('blogs/gallery')->disk('public')->visibility('public')
                                            ->helperText('Multiple images')
                                            ->columnSpanFull()
                                            ->maxSize(5120)
                                            ->acceptedFileTypes(['image/jpeg','image/png','image/webp'])
                                            ->imagePreviewHeight('250')
                                            ->maxFiles(10),
                                    ]),
                                Section::make('Video')
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('video_url')
                                            ->label('Video URL')
                                            ->url()
                                            ->placeholder('https://youtube.com/...')
                                            ->helperText('Embed video')
                                            ->columnSpanFull(),
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
                                Section::make('SEO Settings')
                                    ->description('Meta for Google')
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('seo_title')
                                            ->label('SEO Title')
                                            ->maxLength(60)
                                            ->helperText('Max 60 chars'),
                                        TextInput::make('seo_keywords')
                                            ->label('SEO Keywords')
                                            ->placeholder('nepal travel')
                                            ->helperText('Comma separated'),
                                        Textarea::make('seo_description')
                                            ->label('SEO Description')
                                            ->rows(3)
                                            ->maxLength(160)
                                            ->helperText('Max 160 chars')
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('Tags & Taxonomy')
                                    ->description('Tags for SEO')
                                    ->columnSpanFull()
                                    ->schema([
                                        Select::make('tags')
                                            ->label('Blog Tags')
                                            ->relationship('tags', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->createOptionForm([
                                                TextInput::make('name')->required(),
                                                TextInput::make('slug')->helperText('Auto-generates'),
                                            ])
                                            ->helperText('Select tags')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),

                Section::make('Publishing')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft - Not visible',
                                'published' => 'Published - Visible',
                                'archived' => 'Archived - Hidden',
                            ])
                            ->required()
                            ->default('draft'),
                        DateTimePicker::make('published_at')
                            ->label('Publish Date')
                            ->helperText('Leave empty = now'),
                        Toggle::make('is_featured')
                            ->label('Featured')
                            ->helperText('Homepage feature')
                            ->inline(false),
                        TextInput::make('view_count')
                            ->label('Views')
                            ->numeric()
                            ->default(0)
                            ->helperText('Auto-increments'),
                    ])            ])
            ->columns(1);
    }
}
