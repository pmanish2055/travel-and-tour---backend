<?php

namespace App\Filament\Resources\PackageItineraries\Pages;

use App\Filament\Resources\PackageItineraries\PackageItineraryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPackageItinerary extends EditRecord
{
    protected static string $resource = PackageItineraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
