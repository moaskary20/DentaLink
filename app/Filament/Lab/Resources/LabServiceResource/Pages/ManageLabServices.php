<?php

namespace App\Filament\Lab\Resources\LabServiceResource\Pages;

use App\Filament\Lab\Resources\LabServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLabServices extends ManageRecords
{
    protected static string $resource = LabServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
