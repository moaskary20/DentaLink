<x-filament-panels::page class="dentalink-page">
    <div class="section-header">
        <div>
            <div class="section-title">@lang('dentalink.blades.reports.title')</div>
            <div class="section-sub">@lang('dentalink.blades.reports.subtitle')</div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--primary-light);">📋</div>
            <div class="stat-value">{{ $this->getMonthlyOrders() }}</div>
            <div class="stat-label">@lang('dentalink.blades.reports.orders_this_month')</div>
            <div class="stat-change up">@lang('dentalink.blades.reports.change_up')</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--success-light);">⏱</div>
            <div class="stat-value">{{ number_format($this->getAvgTurnaround(), 1) }}</div>
            <div class="stat-label">@lang('dentalink.blades.reports.avg_turnaround_days')</div>
            <div class="stat-change up">@lang('dentalink.blades.reports.turnaround_improved')</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--secondary-light);">🌟</div>
            <div class="stat-value">{{ number_format($this->getAvgLabRating(), 1) }}</div>
            <div class="stat-label">@lang('dentalink.blades.reports.avg_lab_rating')</div>
            <div class="stat-change up">@lang('dentalink.blades.reports.rating_excellent')</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--accent-light);">💵</div>
            <div class="stat-value">${{ number_format($this->getTotalSpending(), 0) }}</div>
            <div class="stat-label">@lang('dentalink.blades.reports.total_spending')</div>
            <div class="stat-change down">@lang('dentalink.blades.reports.change_down')</div>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-title">@lang('dentalink.blades.reports.service_distribution')</div>
            @php $colors = ['var(--primary)', 'var(--secondary)', 'var(--accent)', '#B4B2A9']; @endphp
            @foreach ($this->getServiceDistribution() as $index => $service)
                <div style="margin-bottom:12px;">
                    <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:5px;">
                        <span>{{ $service['name'] }}</span>
                        <span style="font-weight:700;">{{ $service['percent'] }}%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width:{{ $service['percent'] }}%;background:{{ $colors[$index] ?? '#B4B2A9' }};"></div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card">
            <div class="card-title">@lang('dentalink.blades.reports.orders_by_status')</div>
            @if (count($this->getStatusBreakdown()))
                @foreach ($this->getStatusBreakdown() as $item)
                    <div class="approval-row">
                        <div style="flex:1;font-size:13px;">{{ $item['status'] }}</div>
                        <span class="badge badge-blue">{{ $item['total'] }}</span>
                    </div>
                @endforeach
            @else
                <div class="approval-row"><div style="flex:1;">@lang('dentalink.enums.order_status.in_progress')</div><span class="badge badge-blue">8</span></div>
                <div class="approval-row"><div style="flex:1;">@lang('dentalink.enums.order_status.completed')</div><span class="badge badge-green">36</span></div>
                <div class="approval-row"><div style="flex:1;">@lang('dentalink.enums.order_status.quality_review')</div><span class="badge badge-orange">2</span></div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
