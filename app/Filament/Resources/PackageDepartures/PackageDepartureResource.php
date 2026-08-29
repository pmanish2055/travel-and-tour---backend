<?php
/**
 * File: app/Filament/Resources/PackageDepartures/PackageDepartureResource.php
 * Purpose: Previously separate Departures menu, now HIDDEN - managed INSIDE Package via Tabs -> Departures.
 *          Hidden from sidebar as requested. Each departure is a fixed date for a package.
 */

namespace App\Filament\Resources\PackageDepartures;

use App\Filament\Resources\PackageDepartures\Pages\CreatePackageDeparture;
use App\Filament\Resources\PackageDepartures\Pages\EditPackageDeparture;
use App\Filament\Resources\PackageDepartures\Pages\ListPackageDepartures;
use App\Filament\Resources\PackageDepartures\Schemas\PackageDepartureForm;
use App\Filament\Resources\PackageDepartures\Tables\PackageDeparturesTable;
use App\Models\PackageDeparture;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PackageDepartureResource extends Resource
{
    protected static ?string $model = PackageDeparture::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;
    protected static ?string $recordTitleAttribute = 'departure_date';
    protected static bool $shouldRegisterNavigation = false;
    protected static string|UnitEnum|null $navigationGroup = 'Tour Management';
    public static function form(Schema $schema): Schema { return PackageDepartureForm::configure($schema); }
    public static function table(Table $table): Table { return PackageDeparturesTable::configure($table); }
    public static function getRelations(): array { return []; }
    public static function getPages(): array {
        return [ 'index' => ListPackageDepartures::route('/'), 'create' => CreatePackageDeparture::route('/create'), 'edit' => EditPackageDeparture::route('/{record}/edit'), ];
    }
}
