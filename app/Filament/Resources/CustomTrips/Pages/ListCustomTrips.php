<?php

namespace App\Filament\Resources\CustomTrips\Pages;

use App\Filament\Resources\CustomTrips\CustomTripResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomTrips extends ListRecords
{
    protected static string $resource = CustomTripResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
