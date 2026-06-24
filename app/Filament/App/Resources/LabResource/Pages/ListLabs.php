<?php

namespace App\Filament\App\Resources\LabResource\Pages;

use App\Filament\App\Resources\LabResource;
use Filament\Resources\Pages\ListRecords;

class ListLabs extends ListRecords
{
    protected static string $resource = LabResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
