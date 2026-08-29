{{--
  File: resources/views/filament/pages/report-settings.blade.php
  Purpose: View for Report Settings page (header/footer, mail formats).
--}}

<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}
        <div class="flex justify-end">
            <x-filament::button type="submit">
                Save Report Settings
            </x-filament::button>
        </div>
    </form>
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded text-sm text-blue-800">
        <strong>Report Header/Footer:</strong> This header/footer will appear on all PDF reports when you click Download PDF in each report (Booking, Revenue, etc.). Mail templates use @{{variables}}.
    </div>
</x-filament-panels::page>