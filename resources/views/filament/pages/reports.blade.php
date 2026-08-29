{{--
  File: resources/views/filament/pages/reports.blade.php
  Purpose: Blade view for Reports Group — shows 6+ travel reports as you requested.
           Each report is in a Section/Tab with Group, sidebar filters. Master reusable.
           Data from Reports.php get*Report() methods.
--}}

<x-filament-panels::page>
    {{-- Filters Form --}}
    <div class="mb-6">
        {{ $this->form }}
    </div>

    {{-- Header Widgets (Booking Stats, Revenue Stats) --}}
    <div class="grid grid-cols-1 gap-6 mb-6">
        <x-filament-widgets::widgets :widgets="$this->getHeaderWidgets()" :columns="2" />
    </div>

    @php
        $booking = $this->getBookingReport();
        $revenue = $this->getRevenueReport();
        $inquiry = $this->getInquiryReport();
        $package = $this->getPackagePerformance();
        $destination = $this->getDestinationReport();
        $payment = $this->getPaymentReport();
    @endphp

    {{-- Tabs for Reports --}}
    <x-filament::tabs>
        <x-filament::tabs.item :active="true">
            <x-slot name="label">Booking Report</x-slot>
            <div class="space-y-4 p-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-white p-4 rounded shadow">
                        <div class="text-sm text-gray-500">Total Bookings</div>
                        <div class="text-2xl font-bold">{{ $booking['total'] }}</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded shadow">
                        <div class="text-sm text-green-600">Confirmed</div>
                        <div class="text-2xl font-bold text-green-700">{{ $booking['confirmed'] }}</div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded shadow">
                        <div class="text-sm text-yellow-600">Pending</div>
                        <div class="text-2xl font-bold text-yellow-700">{{ $booking['pending'] }}</div>
                    </div>
                    <div class="bg-red-50 p-4 rounded shadow">
                        <div class="text-sm text-red-600">Cancelled</div>
                        <div class="text-2xl font-bold text-red-700">{{ $booking['cancelled'] }}</div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded shadow">
                        <div class="text-sm text-gray-500">Total Pax</div>
                        <div class="text-xl font-bold">{{ $booking['total_pax'] }}</div>
                    </div>
                    <div class="bg-white p-4 rounded shadow">
                        <div class="text-sm text-gray-500">Total Revenue (Bookings)</div>
                        <div class="text-xl font-bold">${{ number_format($booking['total_revenue'], 2) }}</div>
                    </div>
                </div>
                <div class="text-xs text-gray-400">Filtered by date: {{ $this->data['date_from'] ?? 'N/A' }} to {{ $this->data['date_to'] ?? 'N/A' }} — Master report, reusable for any travel site.</div>
            </div>
        </x-filament::tabs.item>

        <x-filament::tabs.item>
            <x-slot name="label">Revenue Report</x-slot>
            <div class="space-y-4 p-4">
                <div class="bg-white p-4 rounded shadow">
                    <h3 class="font-bold mb-2">Revenue by Gateway</h3>
                    @if($revenue['by_gateway']->isEmpty())
                        <p class="text-sm text-gray-400">No completed payments in range. Demo: Create bookings with payments.</p>
                    @else
                        <table class="w-full text-sm">
                            <thead><tr class="border-b"><th class="text-left p-2">Gateway</th><th class="text-right p-2">Count</th><th class="text-right p-2">Total</th></tr></thead>
                            <tbody>
                                @foreach($revenue['by_gateway'] as $row)
                                    <tr class="border-b"><td class="p-2">{{ ucfirst($row->gateway) }}</td><td class="text-right p-2">{{ $row->count }}</td><td class="text-right p-2">${{ number_format($row->total, 2) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    <div class="mt-3 text-right font-bold">Total Revenue: ${{ number_format($revenue['total'] ?? 0, 2) }}</div>
                </div>
                <div class="bg-white p-4 rounded shadow">
                    <h3 class="font-bold mb-2">Monthly Revenue</h3>
                    @if($revenue['monthly']->isEmpty())
                        <p class="text-sm text-gray-400">No monthly data.</p>
                    @else
                        <table class="w-full text-sm">
                            <thead><tr class="border-b"><th class="text-left p-2">Month</th><th class="text-right p-2">Total</th></tr></thead>
                            <tbody>
                                @foreach($revenue['monthly'] as $row)
                                    <tr class="border-b"><td class="p-2">{{ $row->month }}</td><td class="text-right p-2">${{ number_format($row->total, 2) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </x-filament::tabs.item>

        <x-filament::tabs.item>
            <x-slot name="label">Package Performance</x-slot>
            <div class="space-y-4 p-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded shadow">
                        <h3 class="font-bold mb-2">Most Booked (Top 5)</h3>
                        @if($package['most_booked']->isEmpty())
                            <p class="text-sm text-gray-400">No bookings yet.</p>
                        @else
                            <table class="w-full text-sm">
                                <thead><tr class="border-b"><th class="text-left p-2">Package</th><th class="text-right p-2">Bookings</th><th class="text-right p-2">Revenue</th></tr></thead>
                                <tbody>
                                    @foreach($package['most_booked'] as $row)
                                        <tr class="border-b"><td class="p-2">{{ $row->package->title ?? 'N/A' }}</td><td class="text-right p-2">{{ $row->bookings }}</td><td class="text-right p-2">${{ number_format($row->revenue, 2) }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                    <div class="bg-white p-4 rounded shadow">
                        <h3 class="font-bold mb-2">Most Viewed (Top 5)</h3>
                        <table class="w-full text-sm">
                            <thead><tr class="border-b"><th class="text-left p-2">Package</th><th class="text-right p-2">Views</th></tr></thead>
                            <tbody>
                                @foreach($package['most_viewed'] as $row)
                                    <tr class="border-b"><td class="p-2">{{ $row->title }}</td><td class="text-right p-2">{{ $row->view_count }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </x-filament::tabs.item>

        <x-filament::tabs.item>
            <x-slot name="label">Inquiry & Leads</x-slot>
            <div class="space-y-4 p-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach(['new' => 'New', 'contacted' => 'Contacted', 'converted' => 'Converted', 'closed' => 'Closed'] as $key => $label)
                        <div class="bg-white p-4 rounded shadow">
                            <div class="text-sm text-gray-500">{{ $label }}</div>
                            <div class="text-xl font-bold">{{ $inquiry['inquiries_by_status'][$key] ?? 0 }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded shadow">
                        <div class="text-sm text-gray-500">Custom Trip Requests</div>
                        <div class="text-xl font-bold">{{ $inquiry['custom_trips'] }}</div>
                    </div>
                    <div class="bg-white p-4 rounded shadow">
                        <div class="text-sm text-gray-500">Conversion Rate</div>
                        <div class="text-xl font-bold">{{ $inquiry['conversion_rate'] }}%</div>
                    </div>
                </div>
                <div class="text-sm text-gray-500">Total Leads: {{ $inquiry['total_leads'] }} — Master for any travel site.</div>
            </div>
        </x-filament::tabs.item>

        <x-filament::tabs.item>
            <x-slot name="label">Payment Report</x-slot>
            <div class="space-y-4 p-4">
                <div class="bg-white p-4 rounded shadow">
                    <h3 class="font-bold mb-2">Payments by Status</h3>
                    <table class="w-full text-sm">
                        <thead><tr class="border-b"><th class="text-left p-2">Status</th><th class="text-right p-2">Count</th><th class="text-right p-2">Total</th></tr></thead>
                        <tbody>
                            @foreach($payment['by_status'] as $row)
                                <tr class="border-b"><td class="p-2">{{ ucfirst($row->status) }}</td><td class="text-right p-2">{{ $row->count }}</td><td class="text-right p-2">${{ number_format($row->total ?? 0, 2) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-2 text-sm text-red-600">Failed Payments: {{ $payment['failed_count'] }}</div>
                </div>
            </div>
        </x-filament::tabs.item>

        <x-filament::tabs.item>
            <x-slot name="label">Destination Report</x-slot>
            <div class="space-y-4 p-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded shadow">
                        <h3 class="font-bold mb-2">Bookings by Destination</h3>
                        @if($destination['by_destination']->isEmpty())
                            <p class="text-sm text-gray-400">No destination data.</p>
                        @else
                            <table class="w-full text-sm">
                                <thead><tr class="border-b"><th class="text-left p-2">Destination</th><th class="text-right p-2">Bookings</th><th class="text-right p-2">Revenue</th></tr></thead>
                                <tbody>
                                    @foreach($destination['by_destination'] as $row)
                                        <tr class="border-b"><td class="p-2">{{ $row->destination }}</td><td class="text-right p-2">{{ $row->bookings }}</td><td class="text-right p-2">${{ number_format($row->revenue, 2) }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                    <div class="bg-white p-4 rounded shadow">
                        <h3 class="font-bold mb-2">Bookings by Region</h3>
                        @if($destination['by_region']->isEmpty())
                            <p class="text-sm text-gray-400">No region data.</p>
                        @else
                            <table class="w-full text-sm">
                                <thead><tr class="border-b"><th class="text-left p-2">Region</th><th class="text-right p-2">Bookings</th></tr></thead>
                                <tbody>
                                    @foreach($destination['by_region'] as $row)
                                        <tr class="border-b"><td class="p-2">{{ $row->region }}</td><td class="text-right p-2">{{ $row->bookings }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </x-filament::tabs.item>
    </x-filament::tabs>

    {{-- Footer Widgets --}}
    <div class="mt-6">
        <x-filament-widgets::widgets :widgets="$this->getFooterWidgets()" :columns="2" />
    </div>
</x-filament-panels::page>
