<?php

namespace App\Enums;

enum RatingDirection: string
{
    case DoctorToLab = 'doctor_to_lab';
    case LabToDoctor = 'lab_to_doctor';

    public function label(): string
    {
        return match ($this) {
            self::DoctorToLab => __('dentalink.enums.rating_direction.doctor_to_lab'),
            self::LabToDoctor => __('dentalink.enums.rating_direction.lab_to_doctor'),
        };
    }
}
