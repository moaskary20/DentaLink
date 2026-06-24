<?php

namespace App\Filament\Lab\Resources\OrderResource\Pages;

use App\Filament\Lab\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;
}
