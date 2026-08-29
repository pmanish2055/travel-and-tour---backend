<?php

namespace App\Filament\Resources\PackageDepartures\Pages;

use App\Filament\Resources\PackageDepartures\PackageDepartureResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPackageDeparture extends EditRecord
{
    protected static string $resource = PackageDepartureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
