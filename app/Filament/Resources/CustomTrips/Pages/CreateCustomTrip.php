<?php

namespace App\Filament\Resources\CustomTrips\Pages;

use App\Filament\Resources\CustomTrips\CustomTripResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomTrip extends CreateRecord
{
    protected static string $resource = CustomTripResource::class;
}
