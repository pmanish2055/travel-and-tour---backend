<?php

namespace App\Filament\Resources\PackageInclusions\Pages;

use App\Filament\Resources\PackageInclusions\PackageInclusionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPackageInclusion extends EditRecord
{
    protected static string $resource = PackageInclusionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
