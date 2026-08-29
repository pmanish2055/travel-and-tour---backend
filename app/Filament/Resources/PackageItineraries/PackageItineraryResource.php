<?php
/**
 * File: app/Filament/Resources/PackageItineraries/PackageItineraryResource.php
 * Purpose: Previously separate menu for Itineraries, now HIDDEN as requested - now managed INSIDE Package via Tabs -> Itinerary repeater.
 *          This resource is kept for direct access if needed but hidden from sidebar (shouldRegisterNavigation = false).
 *          Access via Package -> Itinerary tab, not separate menu.
 */

namespace App\Filament\Resources\PackageItineraries;

use App\Filament\Resources\PackageItineraries\Pages\CreatePackageItinerary;
use App\Filament\Resources\PackageItineraries\Pages\EditPackageItinerary;
use App\Filament\Resources\PackageItineraries\Pages\ListPackageItineraries;
use App\Filament\Resources\PackageItineraries\Schemas\PackageItineraryForm;
use App\Filament\Resources\PackageItineraries\Tables\PackageItinerariesTable;
use App\Models\PackageItinerary;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PackageItineraryResource extends Resource
{
    protected static ?string $model = PackageItinerary::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;
    protected static ?string $recordTitleAttribute = 'title';
    // HIDDEN from sidebar as requested - now inside Package tabs
    protected static bool $shouldRegisterNavigation = false;
    protected static string|UnitEnum|null $navigationGroup = 'Tour Management';

    public static function form(Schema $schema): Schema { return PackageItineraryForm::configure($schema); }
    public static function table(Table $table): Table { return PackageItinerariesTable::configure($table); }
    public static function getRelations(): array { return []; }
    public static function getPages(): array {
        return [
            'index' => ListPackageItineraries::route('/'),
            'create' => CreatePackageItinerary::route('/create'),
            'edit' => EditPackageItinerary::route('/{record}/edit'),
        ];
    }
}
