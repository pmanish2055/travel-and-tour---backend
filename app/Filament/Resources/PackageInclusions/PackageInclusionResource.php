<?php
/**
 * File: app/Filament/Resources/PackageInclusions/PackageInclusionResource.php
 * Purpose: Previously separate menu for Includes/Excludes, now HIDDEN - managed INSIDE Package via Tabs -> Includes/Excludes repeater.
 *          Hidden from sidebar as requested. Use Package -> Includes/Excludes tab.
 */

namespace App\Filament\Resources\PackageInclusions;

use App\Filament\Resources\PackageInclusions\Pages\CreatePackageInclusion;
use App\Filament\Resources\PackageInclusions\Pages\EditPackageInclusion;
use App\Filament\Resources\PackageInclusions\Pages\ListPackageInclusions;
use App\Filament\Resources\PackageInclusions\Schemas\PackageInclusionForm;
use App\Filament\Resources\PackageInclusions\Tables\PackageInclusionsTable;
use App\Models\PackageInclusion;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PackageInclusionResource extends Resource
{
    protected static ?string $model = PackageInclusion::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckCircle;
    protected static ?string $recordTitleAttribute = 'title';
    protected static bool $shouldRegisterNavigation = false; // Hidden as requested
    protected static string|UnitEnum|null $navigationGroup = 'Tour Management';
    public static function form(Schema $schema): Schema { return PackageInclusionForm::configure($schema); }
    public static function table(Table $table): Table { return PackageInclusionsTable::configure($table); }
    public static function getRelations(): array { return []; }
    public static function getPages(): array {
        return [
            'index' => ListPackageInclusions::route('/'),
            'create' => CreatePackageInclusion::route('/create'),
            'edit' => EditPackageInclusion::route('/{record}/edit'),
        ];
    }
}
