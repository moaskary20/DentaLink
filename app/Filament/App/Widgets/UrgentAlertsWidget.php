<?php

namespace App\Filament\App\Widgets;

use App\Enums\OrderStatus;
use App\Models\AppNotification;
use App\Models\Order;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class UrgentAlertsWidget extends Widget
{
    protected static string $view = 'filament.app.widgets.urgent-alerts-widget';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function getAlerts(): Collection
    {
        $doctorId = Auth::id();

        $alerts = collect();

        Order::query()
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->where('status', OrderStatus::QualityReview)
            ->latest()
            ->limit(3)
            ->get()
            ->each(function (Order $order) use ($alerts) {
                $alerts->push([
                    'icon' => '⚠️',
                    'text' => __('dentalink.widgets.urgent_alerts.quality_review', ['number' => $order->order_number]),
                ]);
            });

        Order::query()
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->where('status', OrderStatus::Shipping)
            ->latest()
            ->limit(2)
            ->get()
            ->each(function (Order $order) use ($alerts) {
                $alerts->push([
                    'icon' => '📦',
                    'text' => __('dentalink.widgets.urgent_alerts.shipping', [
                        'number' => $order->order_number,
                        'date' => $order->expected_delivery_at?->format('M j'),
                    ]),
                ]);
            });

        AppNotification::query()
            ->when($doctorId, fn ($q) => $q->where('user_id', $doctorId))
            ->where('is_read', false)
            ->latest()
            ->limit(3)
            ->get()
            ->each(function (AppNotification $notification) use ($alerts) {
                $alerts->push([
                    'icon' => $notification->icon ?? '🔔',
                    'text' => $notification->title,
                ]);
            });

        if ($alerts->isEmpty()) {
            return collect([
                ['icon' => '⚠️', 'text' => __('dentalink.widgets.urgent_alerts.fallback.approval')],
                ['icon' => '📦', 'text' => __('dentalink.widgets.urgent_alerts.fallback.shipping')],
                ['icon' => '💳', 'text' => __('dentalink.widgets.urgent_alerts.fallback.invoice')],
            ]);
        }

        return $alerts->take(5);
    }
}
