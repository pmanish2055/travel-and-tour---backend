{{--
  File: resources/views/filament/pages/registered-customers.blade.php
  Purpose: View for Registered Customers page — shows customers that come from frontend.
--}}

<x-filament-panels::page>
    <div class="mb-6">
        <x-filament-widgets::widgets :widgets="$this->getHeaderWidgets()" :columns="3" />
    </div>

    {{ $this->table }}

    <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded text-sm text-amber-800">
        <strong>Registered Customers:</strong> This table shows all customers who registered via frontend.
        <ul class="list-disc list-inside mt-2">
            <li><strong>Users</strong> — Created via frontend Register API (<code>POST /api/v1/register</code>) or via Bookings (customer_email). Role: customer/agent.</li>
            <li><strong>Subscribers</strong> — Newsletter subscribers via <code>POST /api/v1/subscribe</code> (shown via header action "View Subscribers").</li>
            <li><strong>Inquiries/Custom Trips</strong> — Leads from frontend forms (header action "View Inquiries").</li>
        </ul>
        <div class="mt-2">
            Click <strong>Download CSV/Excel</strong> in header to export. Use bulk mail page to send to these customers.
        </div>
    </div>
</x-filament-panels::page>
