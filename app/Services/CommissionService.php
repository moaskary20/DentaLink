<?php

namespace App\Services;

use App\Models\CommissionSetting;
use App\Models\Order;

class CommissionService
{
    public function rateForOrder(bool $isExpress = false, bool $isPremiumLab = false): float
    {
        $slug = match (true) {
            $isExpress => 'express',
            $isPremiumLab => 'premium',
            default => 'standard',
        };

        $setting = CommissionSetting::query()->where('slug', $slug)->where('is_active', true)->first();

        return (float) ($setting?->rate ?? ($isExpress ? 7.0 : 5.0));
    }

    public function calculate(float $cost, bool $isExpress = false, bool $isPremiumLab = false): float
    {
        return round($cost * ($this->rateForOrder($isExpress, $isPremiumLab) / 100), 2);
    }

    public function expressSurcharge(): float
    {
        return 50.0;
    }
}
