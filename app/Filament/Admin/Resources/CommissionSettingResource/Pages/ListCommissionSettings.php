<?php

namespace App\Filament\Admin\Resources\CommissionSettingResource\Pages;

use App\Filament\Admin\Resources\CommissionSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommissionSettings extends ListRecords
{
    protected static string $resource = CommissionSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
