<x-filament-panels::page class="dentalink-page">
    <div class="section-header">
        <div>
            <div class="section-title">@lang('dentalink.pages.notifications.title')</div>
            <div class="section-sub">@lang('dentalink.notifications.subtitle')</div>
        </div>
        <button wire:click="markAllRead" type="button" class="dentalink-btn dentalink-btn-outline">@lang('dentalink.notifications.mark_all_read')</button>
    </div>

    <div class="card">
        @forelse ($this->getNotifications() as $notification)
            <div class="notif-item" wire:click="markAsRead({{ $notification->id }})" style="cursor:pointer;{{ $notification->is_read ? 'opacity:0.7;' : '' }}">
                <span class="notif-icon">{{ $notification->icon ?? '🔔' }}</span>
                <div style="flex:1;">
                    <div class="notif-text">{{ $notification->title }}</div>
                    @if ($notification->body)
                        <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">{{ $notification->body }}</div>
                    @endif
                    <div class="notif-time">{{ $notification->created_at?->diffForHumans() }}</div>
                </div>
                @unless ($notification->is_read)
                    <span class="badge badge-blue">@lang('dentalink.notifications.badge_new')</span>
                @endunless
            </div>
        @empty
            <div class="notif-item">
                <span class="notif-icon">📦</span>
                <div>
                    <div class="notif-text">@lang('dentalink.notifications.fallback.shipping')</div>
                    <div class="notif-time">@lang('dentalink.notifications.fallback.time_2h')</div>
                </div>
                <span class="badge badge-blue">@lang('dentalink.notifications.badge_new')</span>
            </div>
            <div class="notif-item">
                <span class="notif-icon">⚠️</span>
                <div>
                    <div class="notif-text">@lang('dentalink.notifications.fallback.approval')</div>
                    <div class="notif-time">@lang('dentalink.notifications.fallback.time_5h')</div>
                </div>
            </div>
            <div class="notif-item">
                <span class="notif-icon">💳</span>
                <div>
                    <div class="notif-text">@lang('dentalink.notifications.fallback.payment')</div>
                    <div class="notif-time">@lang('dentalink.notifications.fallback.time_yesterday')</div>
                </div>
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
