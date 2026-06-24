<?php

namespace App\Observers;

use App\Enums\RatingDirection;
use App\Models\Lab;
use App\Models\Rating;

class RatingObserver
{
    public function saved(Rating $rating): void
    {
        if ($rating->direction === RatingDirection::DoctorToLab) {
            $avg = Rating::query()
                ->where('lab_id', $rating->lab_id)
                ->where('direction', RatingDirection::DoctorToLab)
                ->avg('score');

            Lab::query()->where('id', $rating->lab_id)->update(['rating' => round($avg, 1)]);
        }
    }
}
