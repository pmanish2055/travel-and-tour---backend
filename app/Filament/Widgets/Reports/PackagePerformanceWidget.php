<?php
/**
 * File: app/Filament/Widgets/Reports/PackagePerformanceWidget.php
 * Purpose: Package performance report — most booked, most viewed as you requested for travel reports.
 *          Shows table of top packages. Part of Reports group footer.
 */

namespace App\Filament\Widgets\Reports;

use App\Models\Booking;
use App\Models\Package;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class PackagePerformanceWidget extends TableWidget
{
    protected static ?string $heading = 'Package Performance — Most Booked (Master Report)';

    protected int|string|array $columnSpan = 'full';

    public function getTableRecordKey(\Illuminate\Database\Eloquent\Model|array $record): string
    {
        if (is_array($record)) {
            return (string) ($record['key'] ?? $record['package_id'] ?? uniqid());
        }
        return (string) ($record->getKey() ?? $record->package_id ?? $record->getAttribute('id') ?? uniqid());
    }

    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->query(
                Booking::query()
                    ->selectRaw('MIN(id) as id, package_id, COUNT(*) as bookings, SUM(total_amount) as revenue')
                    ->groupBy('package_id')
                    ->orderByDesc('bookings')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('package.title')
                    ->label('Package')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('bookings')
                    ->label('Bookings')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('revenue')
                    ->label('Revenue')
                    ->money('USD')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
