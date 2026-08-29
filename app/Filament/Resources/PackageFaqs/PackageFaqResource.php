<?php
/**
 * File: app/Filament/Resources/PackageFaqs/PackageFaqResource.php
 * Purpose: Previously separate FAQ menu, now HIDDEN - managed INSIDE Package via Tabs -> FAQs & Equipment.
 *          Hidden from sidebar as requested.
 */

namespace App\Filament\Resources\PackageFaqs;

use App\Filament\Resources\PackageFaqs\Pages\CreatePackageFaq;
use App\Filament\Resources\PackageFaqs\Pages\EditPackageFaq;
use App\Filament\Resources\PackageFaqs\Pages\ListPackageFaqs;
use App\Filament\Resources\PackageFaqs\Schemas\PackageFaqForm;
use App\Filament\Resources\PackageFaqs\Tables\PackageFaqsTable;
use App\Models\PackageFaq;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PackageFaqResource extends Resource
{
    protected static ?string $model = PackageFaq::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;
    protected static ?string $recordTitleAttribute = 'question';
    protected static bool $shouldRegisterNavigation = false;
    protected static string|UnitEnum|null $navigationGroup = 'Tour Management';
    public static function form(Schema $schema): Schema { return PackageFaqForm::configure($schema); }
    public static function table(Table $table): Table { return PackageFaqsTable::configure($table); }
    public static function getRelations(): array { return []; }
    public static function getPages(): array {
        return [ 'index' => ListPackageFaqs::route('/'), 'create' => CreatePackageFaq::route('/create'), 'edit' => EditPackageFaq::route('/{record}/edit'), ];
    }
}
