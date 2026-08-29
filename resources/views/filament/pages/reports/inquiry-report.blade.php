{{--
  File: resources/views/filament/pages/reports/inquiry-report.blade.php
  Purpose: View for inquiry-report report — separate menu under Reports group as you requested.
           Shows filters (form) and table with download buttons (CSV/Excel/PDF with header/footer).
--}}

<x-filament-panels::page>
    <div class="mb-6">
        {{ $this->form }}
    </div>

    {{ $this->table }}

    <div class="mt-6 p-4 bg-gray-50 border rounded text-sm text-gray-600">
        <strong>Download:</strong> Use header actions <strong>Download CSV / Excel / PDF</strong> to export this report.
        PDF includes header/footer from <a href="/admin/report-settings" class="text-primary-600 underline">Report Settings</a> (header title, subtitle, logo, footer text).
        Excel/CSV also include header/footer rows. Filter by date/package/status above to customize.
    </div>
</x-filament-panels::page>