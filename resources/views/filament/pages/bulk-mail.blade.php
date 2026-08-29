{{--
  File: resources/views/filament/pages/bulk-mail.blade.php
  Purpose: View for BulkMail page — renders form for bulk mail to registered customers.
--}}

<x-filament-panels::page>
    <form wire:submit="sendMail" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button type="submit" color="primary">
                Send Bulk Mail
            </x-filament::button>
        </div>
    </form>

    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded text-sm text-blue-800">
        <strong>Bulk Mail:</strong> Select recipients (Subscribers + Users from frontend) and compose mail. Uses mail format from Report Settings. Will be logged to <code>storage/logs/laravel.log</code> when MAIL_MAILER=log (local). On server with SMTP, will actually send.
    </div>
</x-filament-panels::page>
