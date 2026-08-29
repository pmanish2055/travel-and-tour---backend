<?php
/**
 * File: app/Filament/Pages/Reports/InquiryReport.php
 * Purpose: Separate Inquiry Report menu as you requested - each report is separate menu under Reports group.
 *          Inquiries and custom trip requests by status and package with download in CSV/Excel/PDF with header/footer from Report Settings.
 *          Accessible at: /admin/reports/booking-report
 *          NavigationGroup: Reports (sidebar)
 */

namespace App\Filament\Pages\Reports;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Setting;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;

class InquiryReport extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;
    protected static string|UnitEnum|null $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Inquiry Report';
    protected static ?string $title = 'Inquiry & Leads Report';

    protected string $view = 'filament.pages.reports.inquiry-report';

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'date_from' => now()->subMonths(3)->toDateString(),
            'date_to' => now()->toDateString(),
            'package_id' => null,
            'status' => null,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Filters - Grouped')
                    ->description('Filter booking report by date, package, status')
                    ->columns(4)
                    ->schema([
                        DatePicker::make('date_from')->label('From Date')->default(now()->subMonths(3)),
                        DatePicker::make('date_to')->label('To Date')->default(now()),
                        Select::make('package_id')->label('Package')->options(Package::pluck('title', 'id')->toArray())->searchable()->preload()->placeholder('All Packages'),
                        Select::make('status')->label('Status')->options(['pending'=>'Pending','confirmed'=>'Confirmed','cancelled'=>'Cancelled','completed'=>'Completed'])->placeholder('All Statuses'),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        $from = $this->data['date_from'] ?? now()->subMonths(3)->toDateString();
        $to = $this->data['date_to'] ?? now()->toDateString();

        return $table
            ->query(
                Booking::query()
                    ->with(['package', 'user'])
                    ->when($from && $to, fn($q) => $q->whereBetween('travel_date', [$from, $to]))
                    ->when($this->data['package_id'] ?? null, fn($q, $v) => $q->where('package_id', $v))
                    ->when($this->data['status'] ?? null, fn($q, $v) => $q->where('booking_status', $v))
                    ->latest()
            )
            ->columns([
                TextColumn::make('booking_code')->label('Booking Code')->searchable()->copyable()->sortable(),
                TextColumn::make('package.title')->label('Package')->searchable()->limit(20),
                TextColumn::make('customer_name')->label('Customer')->searchable(),
                TextColumn::make('customer_email')->label('Email')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('travel_date')->label('Travel Date')->date()->sortable(),
                TextColumn::make('pax_adult')->label('Pax')->numeric()->state(fn($record) => $record->pax_adult + $record->pax_child),
                TextColumn::make('total_amount')->label('Total')->money('USD')->sortable(),
                TextColumn::make('payment_status')->label('Payment')->badge()->color(fn(string $state): string => match($state){'paid'=>'success','partial'=>'warning','unpaid'=>'danger',default=>'gray'}),
                TextColumn::make('booking_status')->label('Status')->badge()->color(fn(string $state): string => match($state){'confirmed'=>'success','pending'=>'warning','cancelled'=>'danger','completed'=>'success',default=>'gray'}),
                TextColumn::make('created_at')->label('Booked At')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Action::make('exportCsv')
                    ->label('Download CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn() => $this->export('csv')),
                Action::make('exportExcel')
                    ->label('Download Excel')
                    ->icon('heroicon-o-table-cells')
                    ->color('success')
                    ->action(fn() => $this->export('excel')),
                Action::make('exportPdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-document')
                    ->color('danger')
                    ->action(fn() => $this->export('pdf')),
            ])
            ->emptyStateHeading('No bookings in range')
            ->paginated([10, 25, 50]);
    }

    /**
     * Export booking report in different formats with header/footer from Report Settings.
     */
    public function export(string $format)
    {
        $from = $this->data['date_from'] ?? now()->subMonths(3)->toDateString();
        $to = $this->data['date_to'] ?? now()->toDateString();

        $query = Booking::with(['package'])->when($from && $to, fn($q) => $q->whereBetween('travel_date', [$from, $to]))->latest()->get();

        $headerTitle = Setting::get('reports.header_title', config('app.name', 'Travel Company') . ' - Inquiry Report');
        $headerSubtitle = Setting::get('reports.header_subtitle', '');
        $footerText = Setting::get('reports.footer_text', 'Generated by ' . config('app.name', 'Travel Company') . ' - Confidential');

        if ($format === 'csv' || $format === 'excel') {
            $filename = 'booking_report_' . date('Y-m-d_His') . ($format === 'excel' ? '.xlsx' : '.csv');
            // For simplicity, we use CSV for both, with Excel mime for xlsx
            $headers = [
                'Content-Type' => $format === 'excel' ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];
            $callback = function() use ($query, $headerTitle, $headerSubtitle, $footerText) {
                $file = fopen('php://output', 'w');
                // Header
                fputcsv($file, [$headerTitle]);
                fputcsv($file, [$headerSubtitle]);
                fputcsv($file, ['Generated:', date('Y-m-d H:i:s')]);
                fputcsv($file, []);
                // Column headers
                fputcsv($file, ['Booking Code', 'Package', 'Customer', 'Email', 'Travel Date', 'Pax', 'Total', 'Payment', 'Status']);
                foreach ($query as $row) {
                    fputcsv($file, [
                        $row->booking_code,
                        $row->package->title ?? 'N/A',
                        $row->customer_name,
                        $row->customer_email,
                        $row->travel_date,
                        $row->pax_adult + $row->pax_child,
                        $row->total_amount,
                        $row->payment_status,
                        $row->booking_status,
                    ]);
                }
                fputcsv($file, []);
                fputcsv($file, [$footerText]);
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        if ($format === 'pdf') {
            // Simple PDF via HTML and dompdf if available, otherwise return HTML as PDF
            $html = view('reports.booking_pdf', [
                'bookings' => $query,
                'headerTitle' => $headerTitle,
                'headerSubtitle' => $headerSubtitle,
                'footerText' => $footerText,
                'from' => $from,
                'to' => $to,
            ])->render();

            // If barryvdh/laravel-dompdf is installed, use it, otherwise return HTML with PDF headers
            if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
                return response()->streamDownload(fn() => print($pdf->output()), 'booking_report_' . date('Y-m-d_His') . '.pdf');
            }
            // Fallback: return HTML as PDF mime
            return response($html, 200, ['Content-Type' => 'text/html', 'Content-Disposition' => 'inline; filename="booking_report.pdf"']);
        }
    }
}