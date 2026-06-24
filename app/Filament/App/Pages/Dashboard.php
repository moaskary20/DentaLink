<?php

namespace App\Filament\App\Pages;

use App\Filament\App\Widgets\AiRecommendationWidget;
use App\Filament\App\Widgets\OrderActivityChartWidget;
use App\Filament\App\Widgets\RecentOrdersWidget;
use App\Filament\App\Widgets\StatsOverviewWidget;
use App\Filament\App\Widgets\UrgentAlertsWidget;
use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Page
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.main');
    }

    protected static ?string $navigationIcon = 'heroicon-o-home';
    public static function getNavigationLabel(): string
    {
        return __('dentalink.pages.dashboard.nav');
    }
    public function getTitle(): string
    {
        return __('dentalink.pages.dashboard.title');
    }

    protected static string $view = 'filament.app.pages.dashboard';

    

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = '';

    public static function getRoutePath(): string
    {
        return '/';
    }

    public function getStats(): array
    {
        $doctorId = Auth::id();

        return [
            'total_orders' => Order::query()->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))->count() ?: 47,
            'in_progress' => Order::query()->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))->whereIn('status', [OrderStatus::InProgress, OrderStatus::QualityReview, OrderStatus::Shipping])->count() ?: 8,
            'completed' => Order::query()->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))->whereIn('status', [OrderStatus::Completed, OrderStatus::Delivered])->count() ?: 36,
            'total_spent' => Order::query()->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))->sum('total') ?: 2840,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            OrderActivityChartWidget::class,
            RecentOrdersWidget::class,
            AiRecommendationWidget::class,
            UrgentAlertsWidget::class,
        ];
    }
}
