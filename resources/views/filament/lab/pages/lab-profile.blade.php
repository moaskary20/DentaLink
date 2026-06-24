<x-filament-panels::page class="dentalink-page">
    @php $lab = $this->getLab(); @endphp

    @if (! $lab)
        <div class="card">
            <div class="section-title" style="margin-bottom:8px;">@lang('dentalink.blades.lab_profile.not_linked_title')</div>
            <p style="color:var(--text-muted);font-size:13px;">
                @lang('dentalink.blades.lab_profile.not_linked_body')
            </p>
        </div>
    @else
        <div class="stats-grid" style="margin-bottom:24px;">
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--primary-light);">⭐</div>
                <div class="stat-value">{{ number_format($lab->rating, 1) }}</div>
                <div class="stat-label">@lang('dentalink.blades.lab_profile.stat_rating')</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--success-light);;">✓</div>
                <div class="stat-value">{{ $lab->approval_status?->label() ?? __('dentalink.enums.approval_status.approved') }}</div>
                <div class="stat-label">@lang('dentalink.blades.lab_profile.stat_status')</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--accent-light);">🌍</div>
                <div class="stat-value" style="font-size:18px;">{{ $lab->city }}, {{ $lab->country }}</div>
                <div class="stat-label">@lang('dentalink.blades.lab_profile.stat_location')</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:var(--secondary-light);">💵</div>
                <div class="stat-value">${{ number_format($lab->starting_price, 0) }}+</div>
                <div class="stat-label">@lang('dentalink.blades.lab_profile.stat_starting_price')</div>
            </div>
        </div>

        <div class="card">
            <div class="card-title">@lang('dentalink.blades.lab_profile.edit_profile')</div>
            <form wire:submit="save">
                {{ $this->form }}
                <div style="margin-top:20px;">
                    <x-filament::button type="submit">
                        @lang('dentalink.actions.save_changes')
                    </x-filament::button>
                </div>
            </form>
        </div>
    @endif
</x-filament-panels::page>
