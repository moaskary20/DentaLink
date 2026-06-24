<?php

namespace App\Filament\App\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use App\Filament\App\Resources\OrderResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['doctor_id'] = Auth::id();
        $data['order_number'] = 'ORD-' . random_int(1000, 9999);
        $data['status'] = OrderStatus::Received;
        $data['cost'] = $data['cost'] ?? 280;
        $data['commission'] = round(($data['cost'] ?? 280) * 0.05, 2);
        $data['total'] = ($data['cost'] ?? 280) + ($data['commission'] ?? 14);

        return $data;
    }
}
