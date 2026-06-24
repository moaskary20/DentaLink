<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\AdminStatsOverviewWidget;
use App\Filament\Admin\Widgets\OrdersByStatusChartWidget;
use App\Filament\Admin\Widgets\PendingApprovalsWidget;
use App\Filament\Admin\Widgets\RecentPlatformOrdersWidget;
use App\Filament\Admin\Widgets\RecentTransactionsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.overview');
    }

    protected static ?string $navigationIcon = 'heroicon-o-home';
    public function getTitle(): string
    {
        return __('dentalink.pages.admin_dashboard.title');
    }

    

    public function getWidgets(): array
    {
        return [
            AdminStatsOverviewWidget::class,
            RecentPlatformOrdersWidget::class,
            OrdersByStatusChartWidget::class,
            PendingApprovalsWidget::class,
            RecentTransactionsWidget::class,
        ];
    }
}
