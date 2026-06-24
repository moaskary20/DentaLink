<?php

namespace App\Filament\App\Resources\RatingResource\Pages;

use App\Filament\App\Resources\RatingResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateRating extends CreateRecord
{
    protected static string $resource = RatingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['doctor_id'] = Auth::id();
        $data['direction'] = \App\Enums\RatingDirection::DoctorToLab->value;

        return $data;
    }
}
