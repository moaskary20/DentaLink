<?php

namespace App\Filament\App\Pages;

use App\Models\AppNotification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class NotificationsPage extends Page
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.communication');
    }

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    public static function getNavigationLabel(): string
    {
        return __('dentalink.pages.notifications.nav');
    }
    public function getTitle(): string
    {
        return __('dentalink.pages.notifications.title');
    }

    protected static string $view = 'filament.app.pages.notifications-page';

    

    protected static ?int $navigationSort = 2;

    public function getNotifications()
    {
        return AppNotification::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->limit(20)
            ->get();
    }

    public function markAsRead(int $id): void
    {
        AppNotification::query()
            ->where('user_id', Auth::id())
            ->whereKey($id)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    public function markAllRead(): void
    {
        AppNotification::query()
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }
}
