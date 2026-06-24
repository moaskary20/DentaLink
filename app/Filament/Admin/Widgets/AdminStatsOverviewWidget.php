<?php

namespace App\Filament\Admin\Widgets;

use App\Enums\ApprovalStatus;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\ApprovalRequest;
use App\Models\Conversation;
use App\Models\Lab;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalCommission = Order::query()->sum('commission');
        $pendingApprovals = ApprovalRequest::query()->where('status', ApprovalStatus::Pending)->count();

        return [
            Stat::make(__('dentalink.widgets.admin.registered_doctors'), User::query()->where('role', UserRole::Doctor)->count())
                ->description(__('dentalink.widgets.admin.registered_doctors_desc'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make(__('dentalink.widgets.admin.certified_labs'), Lab::query()->where('approval_status', ApprovalStatus::Approved)->count())
                ->description(__('dentalink.widgets.admin.certified_labs_desc'))
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),
            Stat::make(__('dentalink.widgets.admin.total_orders'), Order::query()->count())
                ->description(__('dentalink.widgets.admin.total_orders_desc', [
                    'count' => Order::query()->whereIn('status', [OrderStatus::InProgress, OrderStatus::QualityReview, OrderStatus::Shipping])->count(),
                ]))
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('success'),
            Stat::make(__('dentalink.widgets.admin.platform_revenue'), '$'.number_format($totalCommission, 0))
                ->description(__('dentalink.widgets.admin.platform_revenue_desc'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
            Stat::make(__('dentalink.widgets.admin.transactions'), Transaction::query()->count())
                ->description(__('dentalink.widgets.admin.transactions_desc'))
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('gray'),
            Stat::make(__('dentalink.widgets.admin.conversations'), Conversation::query()->count())
                ->description(__('dentalink.widgets.admin.conversations_desc'))
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('gray'),
            Stat::make(__('dentalink.widgets.admin.pending_approvals'), $pendingApprovals)
                ->description(__('dentalink.widgets.admin.pending_approvals_desc'))
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingApprovals > 0 ? 'danger' : 'success'),
        ];
    }
}
