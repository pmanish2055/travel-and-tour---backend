<?php
/**
 * File: app/Filament/Widgets/Reports/CustomerStatsWidget.php
 * Purpose: Stats for registered customers — part of Reports group.
 */

namespace App\Filament\Widgets\Reports;

use App\Models\Subscriber;
use App\Models\User;
use App\Models\Booking;
use App\Models\Inquiry;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CustomerStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Registered Users', User::where('role', '!=', 'super_admin')->count())
                ->description('Users from frontend')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Subscribers', Subscriber::count())
                ->description('Newsletter subscribers')
                ->icon('heroicon-o-envelope')
                ->color('success'),

            Stat::make('Total Bookings', Booking::count())
                ->description('Bookings from customers')
                ->icon('heroicon-o-shopping-bag')
                ->color('info'),

            Stat::make('Inquiries', Inquiry::count())
                ->description('Leads from frontend')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('warning'),
        ];
    }
}
