<?php

namespace App\Filament\Admin\Resources\CommissionSettingResource\Pages;

use App\Filament\Admin\Resources\CommissionSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommissionSetting extends EditRecord
{
    protected static string $resource = CommissionSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
