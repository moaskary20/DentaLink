<?php

namespace App\Filament\App\Widgets;

use App\Models\Lab;
use App\Models\Order;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AiRecommendationWidget extends Widget
{
    protected static string $view = 'filament.app.widgets.ai-recommendation-widget';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function getRecommendedLab(): ?Lab
    {
        $doctorId = Auth::id();

        $preferredLabId = Order::query()
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->selectRaw('lab_id, count(*) as order_count')
            ->groupBy('lab_id')
            ->orderByDesc('order_count')
            ->value('lab_id');

        if ($preferredLabId) {
            return Lab::query()->find($preferredLabId);
        }

        return Lab::query()
            ->where('is_active', true)
            ->orderByDesc('rating')
            ->first();
    }

    public function getMatchScore(): int
    {
        return 94;
    }
}
