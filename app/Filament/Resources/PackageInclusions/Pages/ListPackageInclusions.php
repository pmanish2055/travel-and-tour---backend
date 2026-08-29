<?php

namespace App\Filament\Resources\PackageInclusions\Pages;

use App\Filament\Resources\PackageInclusions\PackageInclusionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPackageInclusions extends ListRecords
{
    protected static string $resource = PackageInclusionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
