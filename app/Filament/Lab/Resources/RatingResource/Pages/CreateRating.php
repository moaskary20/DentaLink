<?php

namespace App\Filament\Lab\Resources\RatingResource\Pages;

use App\Enums\RatingDirection;
use App\Filament\Lab\Resources\RatingResource;
use App\Support\CurrentLab;
use Filament\Resources\Pages\CreateRecord;

class CreateRating extends CreateRecord
{
    protected static string $resource = RatingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['lab_id'] = CurrentLab::id();
        $data['direction'] = RatingDirection::LabToDoctor->value;

        return $data;
    }
}
