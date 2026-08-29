<?php
/**
 * File: app/Filament/Widgets/Reports/RevenueStatsWidget.php
 * Purpose: Revenue stats for Reports group — shows total revenue, by gateway, failed.
 */

namespace App\Filament\Widgets\Reports;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RevenueStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $pending = Payment::where('status', 'pending')->count();
        $failed = Payment::where('status', 'failed')->count();
        $totalPayments = Payment::count();

        return [
            Stat::make('Total Revenue (Payments)', '$' . number_format($totalRevenue, 2))
                ->description('Completed payments')
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Total Payments', $totalPayments)
                ->description('All payment attempts')
                ->icon('heroicon-o-credit-card')
                ->color('primary'),

            Stat::make('Pending Payments', $pending)
                ->description('Awaiting')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Failed', $failed)
                ->description('Failed payments')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}
