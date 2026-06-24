<?php

namespace App\Filament\Admin\Resources\LabResource\Pages;

use App\Filament\Admin\Resources\LabResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLabs extends ListRecords
{
    protected static string $resource = LabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
