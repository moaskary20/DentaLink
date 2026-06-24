<x-filament-widgets::widget>
    <div class="card">
        <div class="card-title">@lang('dentalink.widgets.urgent_alerts.heading')</div>
        <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach ($this->getAlerts() as $alert)
                <div class="ai-suggestion">
                    <span class="ai-suggestion-icon">{{ $alert['icon'] }}</span>
                    <div class="ai-suggestion-text">{{ $alert['text'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-widgets::widget>
