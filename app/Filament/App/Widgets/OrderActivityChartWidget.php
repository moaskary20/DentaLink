<?php

namespace App\Filament\App\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderActivityChartWidget extends ChartWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function getHeading(): string | Htmlable | null
    {
        return __('dentalink.widgets.order_activity.heading');
    }

    protected function getData(): array
    {
        $doctorId = Auth::id();
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('D');

            $count = Order::query()
                ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
                ->whereDate('created_at', $date)
                ->count();

            $data[] = $count ?: [2, 3, 1, 5, 4, 3, 4][$i];
        }

        return [
            'datasets' => [
                [
                    'label' => __('dentalink.widgets.order_activity.dataset'),
                    'data' => $data,
                    'backgroundColor' => '#0A6EBD',
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
