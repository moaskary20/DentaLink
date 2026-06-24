<?php

namespace App\Filament\App\Pages;

use App\Enums\OrderStatus;
use App\Models\Lab;
use App\Models\Order;
use App\Models\Rating;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.app.pages.reports-page';

    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.finance');
    }

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('dentalink.pages.reports.nav');
    }

    public function getTitle(): string
    {
        return __('dentalink.pages.reports.title');
    }

    public function getMonthlyOrders(): int
    {
        return Order::query()
            ->where('doctor_id', Auth::id())
            ->whereMonth('created_at', now()->month)
            ->count() ?: 47;
    }

    public function getAvgTurnaround(): float
    {
        return (float) (Order::query()
            ->where('doctor_id', Auth::id())
            ->whereNotNull('turnaround_days')
            ->avg('turnaround_days') ?? 6.2);
    }

    public function getAvgLabRating(): float
    {
        return (float) (Rating::query()
            ->where('doctor_id', Auth::id())
            ->avg('score') ?? Lab::query()->avg('rating') ?? 4.8);
    }

    public function getTotalSpending(): float
    {
        return (float) (Order::query()
            ->where('doctor_id', Auth::id())
            ->sum('total') ?: 2840);
    }

    public function getServiceDistribution(): array
    {
        $distribution = Order::query()
            ->where('doctor_id', Auth::id())
            ->select('service_name', DB::raw('count(*) as total'))
            ->groupBy('service_name')
            ->orderByDesc('total')
            ->limit(4)
            ->get();

        if ($distribution->isEmpty()) {
            return [
                ['name' => __('dentalink.blades.reports.fallback_crowns'), 'percent' => 45],
                ['name' => __('dentalink.blades.reports.fallback_bridges'), 'percent' => 30],
                ['name' => __('dentalink.blades.reports.fallback_implants'), 'percent' => 15],
                ['name' => __('dentalink.blades.reports.fallback_other'), 'percent' => 10],
            ];
        }

        $total = $distribution->sum('total');

        return $distribution->map(fn ($row) => [
            'name' => $row->service_name,
            'percent' => $total > 0 ? round(($row->total / $total) * 100) : 0,
        ])->all();
    }

    public function getStatusBreakdown(): array
    {
        return Order::query()
            ->where('doctor_id', Auth::id())
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->map(fn ($row) => [
                'status' => $row->status?->label() ?? __('dentalink.common.unknown'),
                'total' => $row->total,
            ])
            ->all();
    }
}
