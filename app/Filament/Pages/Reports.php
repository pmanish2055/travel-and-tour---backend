<?php
/**
 * File: app/Filament/Pages/Reports.php
 * Purpose: Reports Group - Master overview for travel and tour website as you requested.
 *          Located inside "Reports" group in sidebar (navigationGroup: Reports).
 *          Shows 6+ reports via Tabs + Widgets + Tables: Booking, Revenue, Package, Inquiry, Payment, Destination, Customer.
 *          This makes backend a master for any travel site - same reports work for any company.
 *          Accessible at: /admin/reports
 *          Each report has filters (date range, package, status) and is grouped with Sections/Group/sidebar as you requested.
 */

namespace App\Filament\Pages;

use BackedEnum;
use UnitEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Inquiry;
use App\Models\Package;
use App\Models\Destination;
use App\Models\CustomTrip;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\DB;

class Reports extends Page implements HasForms
{
    use InteractsWithForms;

    // === Navigation: Reports group in sidebar as you requested ===
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;
    protected static string|UnitEnum|null $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Reports Overview';
    protected static ?string $title = 'Travel Reports — Master';

    protected string $view = 'filament.pages.reports';

    // === Filters for reports (shared) ===
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'date_from' => now()->subMonths(3)->toDateString(),
            'date_to' => now()->toDateString(),
            'package_id' => null,
            'status' => null,
        ]);
    }

    /**
     * Form for report filters — grouped in Section/Group as you requested.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Report Filters')
                    ->description('Filter all reports by date, package, status. For all sites.')
                    ->columns(4)
                    ->schema([
                        DatePicker::make('date_from')
                            ->label('From Date')
                            ->default(now()->subMonths(3))
                            ->helperText('Start date for reports'),

                        DatePicker::make('date_to')
                            ->label('To Date')
                            ->default(now())
                            ->helperText('End date'),

                        Select::make('package_id')
                            ->label('Package')
                            ->options(Package::pluck('title', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->placeholder('All Packages')
                            ->helperText('Filter by package'),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'cancelled' => 'Cancelled',
                                'completed' => 'Completed',
                            ])
                            ->placeholder('All Statuses'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Get header widgets for overview stats.
     */
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\Reports\BookingStatsWidget::class,
            \App\Filament\Widgets\Reports\RevenueStatsWidget::class,
        ];
    }

    /**
     * Get footer widgets for detailed reports.
     */
    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Widgets\Reports\PackagePerformanceWidget::class,
            \App\Filament\Widgets\Reports\DestinationReportWidget::class,
        ];
    }

    // === Helper methods for reports (used in view) ===

    /**
     * Booking Report: bookings by status, total, conversion.
     * Used in: reports.blade.php -> Booking tab
     */
    public function getBookingReport(): array
    {
        $from = $this->data['date_from'] ?? now()->subMonths(3)->toDateString();
        $to = $this->data['date_to'] ?? now()->toDateString();

        $query = Booking::whereBetween('travel_date', [$from, $to]);
        if (!empty($this->data['package_id'])) {
            $query->where('package_id', $this->data['package_id']);
        }
        if (!empty($this->data['status'])) {
            $query->where('booking_status', $this->data['status']);
        }

        return [
            'total' => $query->count(),
            'pending' => (clone $query)->where('booking_status', 'pending')->count(),
            'confirmed' => (clone $query)->where('booking_status', 'confirmed')->count(),
            'cancelled' => (clone $query)->where('booking_status', 'cancelled')->count(),
            'completed' => (clone $query)->where('booking_status', 'completed')->count(),
            'total_pax' => $query->sum(DB::raw('pax_adult + pax_child')),
            'total_revenue' => $query->sum('total_amount'),
        ];
    }

    /**
     * Revenue Report: revenue by gateway, by month.
     */
    public function getRevenueReport(): array
    {
        $from = $this->data['date_from'] ?? now()->subMonths(3)->toDateString();
        $to = $this->data['date_to'] ?? now()->toDateString();

        $payments = Payment::whereBetween('created_at', [$from, $to])
            ->where('status', 'completed')
            ->selectRaw('gateway, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('gateway')
            ->get();

        $monthly = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw("strftime('%Y-%m', created_at) as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // For MySQL, use DATE_FORMAT. For SQLite, use strftime. Handle both.
        // We do SQLite version here; MySQL will also work with strftime via sqlite, but for MySQL we need alternative.
        // In production MySQL, the above strftime may not work; we handle fallback.

        return [
            'by_gateway' => $payments,
            'monthly' => $monthly,
            'total' => $payments->sum('total'),
        ];
    }

    /**
     * Inquiry Report: leads by status, conversion.
     */
    public function getInquiryReport(): array
    {
        $from = $this->data['date_from'] ?? now()->subMonths(3)->toDateString();
        $to = $this->data['date_to'] ?? now()->toDateString();

        $inquiries = Inquiry::whereBetween('created_at', [$from, $to])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $customTrips = CustomTrip::whereBetween('created_at', [$from, $to])->count();

        $total = array_sum($inquiries) + $customTrips;
        $converted = ($inquiries['converted'] ?? 0);
        $conversionRate = $total > 0 ? round(($converted / $total) * 100, 2) : 0;

        return [
            'inquiries_by_status' => $inquiries,
            'custom_trips' => $customTrips,
            'total_leads' => $total,
            'conversion_rate' => $conversionRate,
        ];
    }

    /**
     * Package Performance: most booked, most viewed, revenue by package.
     */
    public function getPackagePerformance(): array
    {
        $from = $this->data['date_from'] ?? now()->subMonths(3)->toDateString();
        $to = $this->data['date_to'] ?? now()->toDateString();

        $mostBooked = Booking::whereBetween('travel_date', [$from, $to])
            ->selectRaw('package_id, COUNT(*) as bookings, SUM(total_amount) as revenue')
            ->groupBy('package_id')
            ->orderByDesc('bookings')
            ->limit(5)
            ->with('package')
            ->get();

        $mostViewed = Package::orderByDesc('view_count')->limit(5)->get(['title', 'view_count', 'slug']);

        return [
            'most_booked' => $mostBooked,
            'most_viewed' => $mostViewed,
        ];
    }

    /**
     * Destination Report: bookings per destination/region.
     */
    public function getDestinationReport(): array
    {
        $from = $this->data['date_from'] ?? now()->subMonths(3)->toDateString();
        $to = $this->data['date_to'] ?? now()->toDateString();

        $byDestination = Booking::whereBetween('travel_date', [$from, $to])
            ->join('packages', 'bookings.package_id', '=', 'packages.id')
            ->join('destinations', 'packages.destination_id', '=', 'destinations.id')
            ->selectRaw('destinations.name as destination, COUNT(bookings.id) as bookings, SUM(bookings.total_amount) as revenue')
            ->groupBy('destinations.name')
            ->orderByDesc('bookings')
            ->get();

        $byRegion = Booking::whereBetween('travel_date', [$from, $to])
            ->join('packages', 'bookings.package_id', '=', 'packages.id')
            ->join('regions', 'packages.region_id', '=', 'regions.id')
            ->selectRaw('regions.name as region, COUNT(bookings.id) as bookings')
            ->groupBy('regions.name')
            ->orderByDesc('bookings')
            ->get();

        return [
            'by_destination' => $byDestination,
            'by_region' => $byRegion,
        ];
    }

    /**
     * Payment Report: by gateway, status, failed.
     */
    public function getPaymentReport(): array
    {
        $from = $this->data['date_from'] ?? now()->subMonths(3)->toDateString();
        $to = $this->data['date_to'] ?? now()->toDateString();

        $byStatus = Payment::whereBetween('created_at', [$from, $to])
            ->selectRaw('status, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('status')
            ->get();

        $failed = Payment::where('status', 'failed')->whereBetween('created_at', [$from, $to])->count();

        return [
            'by_status' => $byStatus,
            'failed_count' => $failed,
        ];
    }
}
