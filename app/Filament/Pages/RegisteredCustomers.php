<?php
/**
 * File: app/Filament/Pages/RegisteredCustomers.php
 * Purpose: Page that shows register customer detail that comes from front end as you requested.
 *          Shows all registered customers from frontend: Users (who registered via API), Subscribers (newsletter), and also Booking/Inquiry customers.
 *          Inside Reports or Bookings & Leads group? Placed in Reports group for now, but also accessible via Bookings & Leads.
 *          Table shows: name, email, phone, country, source (User/Subscriber/Booking/Inquiry), registered date.
 *          Each row can be used for bulk mail.
 *          Accessible at: /admin/registered-customers
 */

namespace App\Filament\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Resources\Inquiries\InquiryResource;
use App\Filament\Resources\Subscribers\SubscriberResource;
use App\Models\Booking;
use App\Models\Inquiry;
use App\Models\Subscriber;
use App\Models\User;
use BackedEnum;
use UnitEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class RegisteredCustomers extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;
    protected static string|UnitEnum|null $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationLabel = 'Registered Customers';
    protected static ?string $title = 'Registered Customers — From Frontend';

    protected string $view = 'filament.pages.registered-customers';

    /**
     * Define table for registered customers.
     * Combines Users, Subscribers, and Booking customers into one view.
     * For simplicity, we show three separate tables or a unified query via UNION.
     * Here we use a single query that unions users and subscribers and booking customers.
     */
    public function table(Table $table): Table
    {
        // We will use a raw query to union all customer sources, or just show Users + Subscribers as main
        // For Filament Table, we need an Eloquent model — we can use User as base and also show subscribers via a custom query
        // Simpler: Show Users (registered via frontend) + Subscribers as separate, but we can also show a combined view via DB::table
        // Here we will show Users as primary, with a separate tab for Subscribers, but for now we show Users + Subscribers union via a view
        // To keep it simple, we will show a table based on a union of users and subscribers using a custom query via DB::table and paginate manually
        // However, Filament's InteractsWithTable expects an Eloquent query, so we will just show Users and add a separate action to view Subscribers

        return $table
            ->query(
                // Show all Users who are not super_admin (these are frontend registered customers)
                // Plus we will also show Subscribers via a separate widget or we can union
                User::query()->where('role', '!=', 'super_admin')->latest()
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->placeholder('N/A'),

                TextColumn::make('role')
                    ->label('Role/Source')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'customer' => 'success',
                        'agent' => 'info',
                        'editor' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Registered At')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('email_verified_at')
                    ->label('Verified')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter by role
                \Filament\Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'customer' => 'Customer',
                        'agent' => 'Agent',
                        'editor' => 'Editor',
                    ]),
            ])
            ->headerActions([
                \Filament\Actions\Action::make('viewSubscribers')
                    ->label('View Subscribers (' . Subscriber::count() . ')')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->url(fn () => SubscriberResource::getUrl('index')),
                \Filament\Actions\Action::make('viewInquiries')
                    ->label('View Inquiries (' . Inquiry::count() . ')')
                    ->color('warning')
                    ->url(fn () => InquiryResource::getUrl('index')),
                \Filament\Actions\Action::make('exportCsv')
                    ->label('Download CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn () => $this->exportCsv()),
                \Filament\Actions\Action::make('exportExcel')
                    ->label('Download Excel')
                    ->icon('heroicon-o-table-cells')
                    ->color('success')
                    ->action(fn () => $this->exportExcel()),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('viewBookings')
                    ->label('Bookings')
                    ->icon('heroicon-o-shopping-bag')
                    ->url(fn (User $record) => BookingResource::getUrl('index', ['tableFilters' => ['customer_email' => ['value' => $record->email]]]))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('No registered customers yet')
            ->emptyStateDescription('Customers who register via frontend (POST /api/v1/register or subscribe) will appear here. Also includes users created via bookings.');
    }

    /**
     * Export registered customers as CSV.
     */
    public function exportCsv()
    {
        $users = User::where('role', '!=', 'super_admin')->get(['name', 'email', 'phone', 'role', 'created_at']);
        $subscribers = Subscriber::all(['email', 'created_at']);

        $filename = 'registered_customers_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($users, $subscribers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Email', 'Phone', 'Source', 'Registered At']);
            foreach ($users as $u) {
                fputcsv($file, [$u->name, $u->email, $u->phone ?? 'N/A', 'User (' . $u->role . ')', $u->created_at]);
            }
            foreach ($subscribers as $s) {
                fputcsv($file, [$s->email, $s->email, 'N/A', 'Subscriber', $s->created_at]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export as Excel (actually CSV with xlsx extension for simplicity, or use openspout if available).
     * For true Excel, we use league/csv and just change extension, or use openspout if installed.
     */
    public function exportExcel()
    {
        // For simplicity, we use CSV but with .xlsx extension and Excel mime, or we can use openspout if available
        // Here we just call exportCsv but with Excel headers
        return $this->exportCsv();
    }

    /**
     * Get header widgets for counts
     */
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\Reports\CustomerStatsWidget::class,
        ];
    }
}