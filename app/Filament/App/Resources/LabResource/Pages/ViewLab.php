<?php

namespace App\Filament\App\Resources\LabResource\Pages;

use App\Filament\App\Pages\CreateOrder;
use App\Filament\App\Resources\LabResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLab extends ViewRecord
{
    protected static string $resource = LabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create_order')
                ->label(__('dentalink.pages.create_order.nav'))
                ->icon('heroicon-o-plus-circle')
                ->url(CreateOrder::getUrl()),
        ];
    }
}
