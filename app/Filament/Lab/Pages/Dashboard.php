<?php

namespace App\Filament\Lab\Pages;

use App\Filament\Lab\Widgets\LabRecentOrdersWidget;
use App\Filament\Lab\Widgets\LabStatsOverviewWidget;
use App\Filament\Lab\Widgets\LabUrgentOrdersWidget;
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
        return __('dentalink.pages.lab_dashboard.title');
    }

    

    public function getWidgets(): array
    {
        return [
            LabStatsOverviewWidget::class,
            LabRecentOrdersWidget::class,
            LabUrgentOrdersWidget::class,
        ];
    }
}
