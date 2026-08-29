<?php

namespace App\Filament\Resources\CustomTrips\Pages;

use App\Filament\Resources\CustomTrips\CustomTripResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomTrip extends EditRecord
{
    protected static string $resource = CustomTripResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
