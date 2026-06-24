<x-filament-panels::page class="dentalink-page">
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#E8F4FD;">📋</div>
            <div class="stat-value">{{ number_format($this->getStats()['total_orders']) }}</div>
            <div class="stat-label">@lang('dentalink.widgets.stats.total_orders')</div>
            <div class="stat-change up">@lang('dentalink.widgets.stats.change_up_month')</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#E6F7F6;">⏳</div>
            <div class="stat-value">{{ number_format($this->getStats()['in_progress']) }}</div>
            <div class="stat-label">@lang('dentalink.widgets.stats.orders_in_progress')</div>
            <div class="stat-change up">@lang('dentalink.widgets.stats.change_up_new_orders')</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#EAF3DE;">✅</div>
            <div class="stat-value">{{ number_format($this->getStats()['completed']) }}</div>
            <div class="stat-label">@lang('dentalink.widgets.stats.completed_orders')</div>
            <div class="stat-change up">@lang('dentalink.widgets.stats.change_up_accuracy')</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#FEF5E7;">💵</div>
            <div class="stat-value">${{ number_format($this->getStats()['total_spent'], 0) }}</div>
            <div class="stat-label">@lang('dentalink.widgets.stats.total_spent')</div>
            <div class="stat-change down">@lang('dentalink.widgets.stats.change_down_month')</div>
        </div>
    </div>

    @foreach ($this->getFooterWidgets() as $widget)
        @livewire($widget, key($widget))
    @endforeach
</x-filament-panels::page>
