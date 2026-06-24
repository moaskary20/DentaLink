<?php

namespace App\Support;

use App\Models\Lab;
use Illuminate\Support\Facades\Auth;

class CurrentLab
{
    public static function get(): ?Lab
    {
        static $lab = null;

        if ($lab !== null) {
            return $lab ?: null;
        }

        $userId = Auth::id();

        if (! $userId) {
            return null;
        }

        $lab = Lab::query()->where('user_id', $userId)->first();

        return $lab;
    }

    public static function id(): ?int
    {
        return static::get()?->id;
    }
}
