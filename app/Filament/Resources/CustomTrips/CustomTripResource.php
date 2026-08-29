<?php

namespace App\Filament\Resources\CustomTrips;

use App\Filament\Resources\CustomTrips\Pages\CreateCustomTrip;
use App\Filament\Resources\CustomTrips\Pages\EditCustomTrip;
use App\Filament\Resources\CustomTrips\Pages\ListCustomTrips;
use App\Filament\Resources\CustomTrips\Schemas\CustomTripForm;
use App\Filament\Resources\CustomTrips\Tables\CustomTripsTable;
use App\Models\CustomTrip;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CustomTripResource extends Resource
{
    protected static ?string $model = CustomTrip::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static string|UnitEnum|null $navigationGroup = 'Bookings & Leads';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CustomTripForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomTripsTable::configure($table);
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
            'index' => ListCustomTrips::route('/'),
            'create' => CreateCustomTrip::route('/create'),
            'edit' => EditCustomTrip::route('/{record}/edit'),
        ];
    }
}
