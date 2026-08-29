<?php

namespace App\Filament\Resources\WhyChooseUs;

use App\Filament\Resources\WhyChooseUs\Pages\CreateWhyChooseUs;
use App\Filament\Resources\WhyChooseUs\Pages\EditWhyChooseUs;
use App\Filament\Resources\WhyChooseUs\Pages\ListWhyChooseUs;
use App\Filament\Resources\WhyChooseUs\Schemas\WhyChooseUsForm;
use App\Filament\Resources\WhyChooseUs\Tables\WhyChooseUsTable;
use App\Models\WhyChooseUs;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WhyChooseUsResource extends Resource
{
    protected static ?string $model = WhyChooseUs::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Why Choose Us';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return WhyChooseUsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WhyChooseUsTable::configure($table);
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
            'index' => ListWhyChooseUs::route('/'),
            'create' => CreateWhyChooseUs::route('/create'),
            'edit' => EditWhyChooseUs::route('/{record}/edit'),
        ];
    }
}
