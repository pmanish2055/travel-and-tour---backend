<?php

namespace App\Filament\Resources\Tags;

use App\Filament\Resources\Tags\Pages\CreateTag;
use App\Filament\Resources\Tags\Pages\EditTag;
use App\Filament\Resources\Tags\Pages\ListTags;
use App\Filament\Resources\Tags\Schemas\TagForm;
use App\Filament\Resources\Tags\Tables\TagsTable;
use App\Models\Tag;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * File: app/Filament/Resources/Tags/TagResource.php
 * Purpose: Product tags for SEO as you requested. Tags like Family, Adventure, Budget, Luxury, Honeymoon.
 *          Used in Package SEO & Tags tab via Select with createOptionForm. Grouped under Tour Management.
 */
class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHashtag;
    protected static string|UnitEnum|null $navigationGroup = 'Tour Management';
    protected static ?int $navigationSort = 6;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = 'Product Tags (SEO)';

    public static function form(Schema $schema): Schema
    {
        return TagForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TagsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTags::route('/'),
            'create' => CreateTag::route('/create'),
            'edit' => EditTag::route('/{record}/edit'),
        ];
    }
}
