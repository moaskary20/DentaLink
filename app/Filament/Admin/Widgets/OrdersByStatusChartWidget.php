<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersByStatusChartWidget extends ChartWidget
{
    public function getHeading(): ?string
    {
        return __('dentalink.widgets.orders_by_status.heading');
    }


    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $counts = collect(OrderStatus::cases())->mapWithKeys(function (OrderStatus $status) {
            return [$status->label() => Order::query()->where('status', $status)->count()];
        });

        return [
            'datasets' => [
                [
                    'label' => __('dentalink.widgets.chart.orders_label'),
                    'data' => $counts->values()->all(),
                    'backgroundColor' => [
                        '#718096',
                        '#0A6EBD',
                        '#F4A932',
                        '#1DA89A',
                        '#1DA89A',
                        '#3B9922',
                        '#E24B4A',
                    ],
                ],
            ],
            'labels' => $counts->keys()->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
