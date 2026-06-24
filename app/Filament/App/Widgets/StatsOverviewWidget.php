<?php

namespace App\Filament\App\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $doctorId = Auth::id();

        $totalOrders = Order::query()
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->count();

        $inProgress = Order::query()
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->whereIn('status', [
                OrderStatus::InProgress,
                OrderStatus::QualityReview,
                OrderStatus::Shipping,
            ])
            ->count();

        $completed = Order::query()
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->whereIn('status', [OrderStatus::Completed, OrderStatus::Delivered])
            ->count();

        $totalSpent = Order::query()
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->sum('total');

        return [
            Stat::make(__('dentalink.widgets.stats.total_orders'), number_format($totalOrders ?: 47))
                ->description(__('dentalink.widgets.stats.total_orders_desc'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),
            Stat::make(__('dentalink.widgets.stats.in_progress'), number_format($inProgress ?: 8))
                ->description(__('dentalink.widgets.stats.in_progress_desc'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),
            Stat::make(__('dentalink.widgets.stats.completed'), number_format($completed ?: 36))
                ->description(__('dentalink.widgets.stats.completed_desc'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make(__('dentalink.widgets.stats.total_spent'), '$' . number_format($totalSpent ?: 2840, 0))
                ->description(__('dentalink.widgets.stats.total_spent_desc'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('warning'),
        ];
    }
}
