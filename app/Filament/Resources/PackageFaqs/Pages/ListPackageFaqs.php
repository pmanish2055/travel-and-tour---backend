<?php

namespace App\Filament\Resources\PackageFaqs\Pages;

use App\Filament\Resources\PackageFaqs\PackageFaqResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPackageFaqs extends ListRecords
{
    protected static string $resource = PackageFaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
