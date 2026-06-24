<?php

namespace App\Filament\Lab\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Rating;
use App\Support\CurrentLab;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LabStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $labId = CurrentLab::id();

        if (! $labId) {
            return [
                Stat::make(__('dentalink.widgets.lab.lab_profile'), __('dentalink.widgets.lab.not_linked'))
                    ->description(__('dentalink.widgets.lab.not_linked_desc'))
                    ->color('danger'),
            ];
        }

        $orders = Order::query()->where('lab_id', $labId);

        $inProgress = (clone $orders)->whereIn('status', [
            OrderStatus::Received,
            OrderStatus::InProgress,
            OrderStatus::QualityReview,
            OrderStatus::Shipping,
        ])->count();

        $completed = (clone $orders)->whereIn('status', [OrderStatus::Completed, OrderStatus::Delivered])->count();

        $revenue = (clone $orders)->whereIn('status', [OrderStatus::Completed, OrderStatus::Delivered])->sum('cost');

        $avgRating = Rating::query()->where('lab_id', $labId)->avg('score');

        $lab = CurrentLab::get();

        return [
            Stat::make(__('dentalink.widgets.lab.total_orders'), number_format($orders->count()))
                ->description(__('dentalink.widgets.lab.total_orders_desc'))
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary'),
            Stat::make(__('dentalink.widgets.lab.in_progress'), number_format($inProgress))
                ->description(__('dentalink.widgets.lab.in_progress_desc'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
            Stat::make(__('dentalink.widgets.lab.completed'), number_format($completed))
                ->description(__('dentalink.widgets.lab.completed_desc'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make(__('dentalink.widgets.lab.revenue'), '$'.number_format($revenue, 0))
                ->description(__('dentalink.widgets.lab.revenue_desc'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
            Stat::make(__('dentalink.widgets.lab.rating'), $avgRating ? number_format($avgRating, 1).' ★' : ($lab?->rating ? number_format($lab->rating, 1).' ★' : __('dentalink.common.na')))
                ->description(__('dentalink.widgets.lab.rating_desc'))
                ->descriptionIcon('heroicon-m-star')
                ->color('gray'),
            Stat::make(__('dentalink.widgets.lab.avg_turnaround'), __('dentalink.units.days_count', ['count' => $lab?->avg_turnaround_days ?? 7]))
                ->description(__('dentalink.widgets.lab.avg_turnaround_desc'))
                ->descriptionIcon('heroicon-m-truck')
                ->color('gray'),
        ];
    }
}
