<?php

namespace App\Filament\Resources\PackageDepartures\Pages;

use App\Filament\Resources\PackageDepartures\PackageDepartureResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPackageDepartures extends ListRecords
{
    protected static string $resource = PackageDepartureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
