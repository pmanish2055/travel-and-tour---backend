{{-- 
  File: resources/views/filament/pages/manage-company-settings.blade.php
  Purpose: Blade view for Company Settings Page (master setup). 
           Renders the Filament form with tabs, groups, sidebar as you requested.
           This page is inside "Company" group in sidebar (navigationGroup: Company).
           The form is defined in app/Filament/Pages/ManageCompanySettings.php:form()
           Each tab/section is grouped, sidebar shows helper. This is the MASTER page for all travel sites.
--}}

<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end">
            <x-filament::button type="submit">
                Save Company Settings
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
