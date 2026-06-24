<?php

namespace App\Filament\Admin\Resources\LabServiceResource\Pages;

use App\Filament\Admin\Resources\LabServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLabService extends EditRecord
{
    protected static string $resource = LabServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
