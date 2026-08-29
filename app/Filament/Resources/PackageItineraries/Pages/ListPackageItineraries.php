<?php

namespace App\Filament\Resources\PackageItineraries\Pages;

use App\Filament\Resources\PackageItineraries\PackageItineraryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPackageItineraries extends ListRecords
{
    protected static string $resource = PackageItineraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
