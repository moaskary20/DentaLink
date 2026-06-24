<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Doctor = 'doctor';
    case Lab = 'lab';

    public function label(): string
    {
        return match ($this) {
            self::Admin => __('dentalink.enums.user_role.admin'),
            self::Doctor => __('dentalink.enums.user_role.doctor'),
            self::Lab => __('dentalink.enums.user_role.lab'),
        };
    }
}
