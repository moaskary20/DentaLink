<x-filament-panels::page class="dentalink-page">
    @php $order = $this->orderRecord; @endphp
    <div class="section-header">
        <div>
            <div class="section-title">@lang('dentalink.blades.order_tracking.title', ['number' => $order?->order_number ?? $this->order])</div>
            <div class="section-sub">
                {{ $order?->service_name ?? __('dentalink.blades.order_tracking.fallback_service') }} |
                {{ $order?->lab?->name ?? __('dentalink.blades.order_tracking.fallback_lab') }} |
                @lang('dentalink.blades.order_tracking.expected_delivery', ['date' => $order?->expected_delivery_at?->format('M j') ?? 'Jul 25'])
            </div>
        </div>
        <div class="inline-actions">
            <span class="badge badge-blue">{{ $order?->status?->label() ?? __('dentalink.blades.order_tracking.fallback_status') }}</span>
            @if ($order?->is_express)
                <span class="express-badge">@lang('dentalink.fields.express')</span>
            @endif
        </div>
    </div>

    <div class="card" style="margin-bottom:16px;">
        <div class="card-title">@lang('dentalink.blades.order_tracking.stages_title')</div>
        <div class="timeline">
            @foreach ($this->getStages() as $index => $stage)
                @php
                    $status = is_array($stage) ? ($stage['status'] ?? 'pending') : ($stage->completed_at ? 'done' : ($stage->is_current ? 'active' : 'pending'));
                    $label = is_array($stage) ? $stage['label'] : ($stage->label ?? $stage->status?->label());
                    $date = is_array($stage) ? ($stage['date'] ?? '') : ($stage->completed_at?->format('M j') ?? $stage->expected_at?->format('M j') ?? '');
                @endphp
                <div class="timeline-step {{ $status === 'done' ? 'done' : '' }}">
                    <div class="timeline-dot {{ $status === 'done' ? 'done' : ($status === 'active' ? 'active' : '') }}">
                        {{ $status === 'done' ? '✓' : ($index + 1) }}
                    </div>
                    <div class="timeline-label">
                        {{ $label }}<br>
                        @if ($date)
                            <span style="font-weight:700;font-size:9px;color:{{ $status === 'active' ? 'var(--primary)' : ($status === 'done' ? '#3B9922' : 'inherit') }};">{{ $date }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div style="margin-top:16px;">
            <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px;">
                <span style="color:var(--text-muted);">@lang('dentalink.blades.order_tracking.progress')</span>
                <span style="font-weight:700;color:var(--primary);">{{ $this->getProgressPercent() }}%</span>
            </div>
            <div class="progress" style="height:10px;">
                <div class="progress-bar" style="width:{{ $this->getProgressPercent() }}%;background:var(--primary);"></div>
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-title">@lang('dentalink.blades.order_tracking.activity_log')</div>
            @foreach ($this->getLogs() as $log)
                <div class="notif-item">
                    <span class="notif-icon">{{ is_array($log) ? $log['icon'] : ($log->icon ?? '📋') }}</span>
                    <div>
                        <div class="notif-text">{{ is_array($log) ? $log['message'] : $log->message }}</div>
                        <div class="notif-time">{{ is_array($log) ? $log['time'] : $log->logged_at?->format('M j — g:i A') }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="card">
                <div class="card-title">@lang('dentalink.sections.order_details')</div>
                <table class="dentalink-table">
                    <tbody>
                        <tr><td style="color:var(--text-muted);">@lang('dentalink.fields.service')</td><td style="font-weight:600;">{{ $order?->service_name ?? __('dentalink.blades.order_tracking.fallback_service') }}</td></tr>
                        <tr><td style="color:var(--text-muted);">@lang('dentalink.fields.lab')</td><td>{{ $order?->lab?->name ?? __('dentalink.blades.order_tracking.fallback_lab') }}</td></tr>
                        <tr><td style="color:var(--text-muted);">@lang('dentalink.fields.shade')</td><td>{{ $order?->shade ?? 'A2' }}</td></tr>
                        <tr><td style="color:var(--text-muted);">@lang('dentalink.fields.material')</td><td>{{ $order?->material ?? __('dentalink.blades.order_tracking.fallback_material') }}</td></tr>
                        <tr><td style="color:var(--text-muted);">@lang('dentalink.fields.tooth_area_short')</td><td>{{ $order?->tooth_number ?? __('dentalink.blades.order_tracking.fallback_tooth') }}</td></tr>
                        <tr><td style="color:var(--text-muted);">@lang('dentalink.fields.total')</td><td style="font-weight:700;color:var(--primary);">${{ number_format($order?->total ?? 294, 2) }}</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="card">
                <div class="card-title">@lang('dentalink.blades.order_tracking.required_actions')</div>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <button type="button" wire:click="approveQuality" class="dentalink-btn dentalink-btn-success" style="width:100%;justify-content:center;"
                        @if(!$order || $order->status->value !== 'quality_review') disabled @endif>
                        @lang('dentalink.blades.order_tracking.approve_quality')
                    </button>
                    <a href="{{ \App\Filament\App\Pages\ChatPage::getUrl() }}" class="dentalink-btn dentalink-btn-outline" style="width:100%;justify-content:center;">@lang('dentalink.blades.order_tracking.message_lab')</a>
                    <button type="button" class="dentalink-btn dentalink-btn-danger" style="width:100%;justify-content:center;">@lang('dentalink.blades.order_tracking.request_revision')</button>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
