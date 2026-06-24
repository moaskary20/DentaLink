<?php

namespace App\Filament\App\Pages;

use App\Models\Order;
use App\Services\OrderWorkflowService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;

class OrderTracking extends Page
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.order_management');
    }

    protected static ?string $navigationIcon = 'heroicon-o-map';
    public static function getNavigationLabel(): string
    {
        return __('dentalink.pages.order_tracking.nav');
    }
    public function getTitle(): string
    {
        return __('dentalink.pages.order_tracking.title');
    }

    protected static string $view = 'filament.app.pages.order-tracking';

    

    protected static ?int $navigationSort = 2;

    #[Url]
    public string $order = 'ORD-2847';

    public ?Order $orderRecord = null;

    public function mount(): void
    {
        $this->loadOrder();
    }

    public function loadOrder(): void
    {
        $this->orderRecord = Order::query()
            ->with(['lab', 'stages', 'logs', 'doctor'])
            ->where('order_number', $this->order)
            ->where('doctor_id', Auth::id())
            ->first();
    }

    public function approveQuality(): void
    {
        if (! $this->orderRecord) {
            return;
        }

        try {
            app(OrderWorkflowService::class)->approveQualityStage($this->orderRecord, Auth::id());
            $this->loadOrder();

            Notification::make()->title(__('dentalink.notifications.quality_approved'))->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title(__('dentalink.notifications.approval_failed'))->body($e->getMessage())->danger()->send();
        }
    }

    public function getStages()
    {
        return $this->orderRecord?->stages?->sortBy('sort_order') ?? collect();
    }

    public function getLogs()
    {
        return $this->orderRecord?->logs?->sortByDesc('created_at') ?? collect();
    }

    public function getProgressPercent(): int
    {
        if ($this->orderRecord?->stages?->isNotEmpty()) {
            $total = $this->orderRecord->stages->count();
            $completed = $this->orderRecord->stages->whereNotNull('completed_at')->count();

            return $total > 0 ? (int) round(($completed / $total) * 100) : 0;
        }

        return 0;
    }

    public function isOverdue(): bool
    {
        return $this->orderRecord
            && $this->orderRecord->expected_delivery_at
            && $this->orderRecord->expected_delivery_at->isPast()
            && ! in_array($this->orderRecord->status->value, ['completed', 'cancelled'], true);
    }
}
