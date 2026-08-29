<?php

namespace App\Filament\Resources\PackageFaqs\Pages;

use App\Filament\Resources\PackageFaqs\PackageFaqResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPackageFaq extends EditRecord
{
    protected static string $resource = PackageFaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
