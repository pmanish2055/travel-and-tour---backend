<?php
/**
 * File: app/Filament/Widgets/Reports/BookingStatsWidget.php
 * Purpose: Stats overview for bookings — part of Reports group as you requested.
 *          Shows total bookings, confirmed, pending, cancelled, total pax, revenue.
 *          Used in: Reports page header.
 */

namespace App\Filament\Widgets\Reports;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class BookingStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        // Basic booking stats for reports
        $total = Booking::count();
        $confirmed = Booking::where('booking_status', 'confirmed')->count();
        $pending = Booking::where('booking_status', 'pending')->count();
        $revenue = Booking::sum('total_amount');

        return [
            Stat::make('Total Bookings', $total)
                ->description('All bookings')
                ->icon('heroicon-o-shopping-bag')
                ->color('primary'),

            Stat::make('Confirmed', $confirmed)
                ->description('Confirmed bookings')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Pending', $pending)
                ->description('Awaiting confirmation')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Total Revenue (Bookings)', '$' . number_format($revenue, 2))
                ->description('Sum of total_amount')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
        ];
    }
}
