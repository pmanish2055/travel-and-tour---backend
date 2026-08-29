<?php
/**
 * File: app/Filament/Widgets/Reports/DestinationReportWidget.php
 * Purpose: Destination & Region report — bookings per destination/region for travel site.
 *          Shows which destinations are most popular. Reusable.
 */

namespace App\Filament\Widgets\Reports;

use App\Models\Booking;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;

class DestinationReportWidget extends TableWidget
{
    protected static ?string $heading = 'Destination Report — Bookings by Destination';

    protected int|string|array $columnSpan = 'full';

    public function getTableRecordKey(\Illuminate\Database\Eloquent\Model|array $record): string
    {
        if (is_array($record)) {
            return (string) ($record['key'] ?? $record['destination'] ?? uniqid());
        }
        return (string) ($record->getKey() ?? $record->destination ?? $record->getAttribute('id') ?? uniqid());
    }

    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->query(
                Booking::query()
                    ->join('packages', 'bookings.package_id', '=', 'packages.id')
                    ->join('destinations', 'packages.destination_id', '=', 'destinations.id')
                    ->selectRaw('MIN(bookings.id) as id, destinations.name as destination, COUNT(bookings.id) as bookings, SUM(bookings.total_amount) as revenue')
                    ->groupBy('destinations.name')
                    ->orderByDesc('bookings')
            )
            ->columns([
                TextColumn::make('destination')
                    ->label('Destination')
                    ->searchable(),

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
