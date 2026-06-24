<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;

class NotificationService
{
    public function notify(User $user, string $title, string $body, ?string $icon = '🔔', ?string $type = null): AppNotification
    {
        return AppNotification::query()->create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
            'icon' => $icon,
            'type' => $type,
            'is_read' => false,
        ]);
    }

    public function notifyOrderUpdate(int $userId, string $orderNumber, string $message, string $icon = '📋'): void
    {
        $user = User::query()->find($userId);

        if ($user) {
            $this->notify($user, "Order #{$orderNumber}", $message, $icon, 'order');
        }
    }
}
