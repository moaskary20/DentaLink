<x-filament-panels::page class="dentalink-page">
    <div class="section-header">
        <div>
            <div class="section-title">@lang('dentalink.blades.browse_labs.title')</div>
            <div class="section-sub">@lang('dentalink.blades.browse_labs.subtitle', ['count' => $this->getLabs()->count() ?: 24])</div>
        </div>
    </div>

    <div class="filters-bar">
        <input wire:model.live.debounce.300ms="search" type="text" class="filter-select" placeholder="{{ __('dentalink.blades.browse_labs.search_placeholder') }}" style="max-width:260px;">
        <select wire:model.live="country" class="filter-select">
            <option value="">@lang('dentalink.blades.browse_labs.all_countries')</option>
            @foreach ($this->getCountries() as $c)
                <option value="{{ $c }}">{{ $c }}</option>
            @endforeach
            <option value="Qatar">Qatar</option>
            <option value="UAE">UAE</option>
            <option value="Saudi Arabia">Saudi Arabia</option>
        </select>
        <select wire:model.live="service" class="filter-select">
            <option value="">@lang('dentalink.blades.browse_labs.all_services')</option>
            <option value="Crown">@lang('dentalink.service_categories.crown')</option>
            <option value="Bridge">@lang('dentalink.service_categories.bridge')</option>
            <option value="Implant">@lang('dentalink.service_categories.implant')</option>
            <option value="Denture">@lang('dentalink.service_categories.full_denture')</option>
        </select>
        <select wire:model.live="sort" class="filter-select">
            <option value="rating">@lang('dentalink.blades.browse_labs.sort_rating')</option>
            <option value="price">@lang('dentalink.blades.browse_labs.sort_price')</option>
            <option value="speed">@lang('dentalink.blades.browse_labs.sort_speed')</option>
        </select>
    </div>

    <div class="ai-suggestion" style="margin-bottom:16px;">
        <span class="ai-suggestion-icon">🤖</span>
        <div class="ai-suggestion-text">@lang('dentalink.blades.browse_labs.ai_suggestion')</div>
    </div>

    <div class="labs-grid">
        @forelse ($this->getLabs() as $index => $lab)
            <div class="lab-card" @if($index === 0) style="border-color:var(--primary);border-width:2px;" @endif>
                @if ($index === 0)
                    <div style="background:var(--primary-light);color:var(--primary);font-size:10px;font-weight:700;padding:3px 10px;border-radius:4px;display:inline-block;margin-bottom:10px;">@lang('dentalink.blades.browse_labs.ai_suggested_badge')</div>
                @endif
                <div class="lab-header">
                    <div>
                        <div class="lab-name">{{ $lab->name }}</div>
                        <div class="lab-country">{{ $lab->city ? $lab->city . ', ' : '' }}{{ $lab->country }}</div>
                    </div>
                    <div class="stars">{{ str_repeat('★', (int) round($lab->rating ?? 4)) }}{{ str_repeat('☆', 5 - (int) round($lab->rating ?? 4)) }}</div>
                </div>
                <div class="lab-tags">
                    @foreach ($lab->services->take(3) as $service)
                        <span class="badge badge-blue">{{ $service->category ?? $service->name }}</span>
                    @endforeach
                    @if ($lab->services->isEmpty())
                        <span class="badge badge-blue">@lang('dentalink.service_categories.crown')</span>
                        <span class="badge badge-teal">@lang('dentalink.service_categories.bridge')</span>
                    @endif
                </div>
                <div class="lab-stats">
                    <div><div class="lab-stat-val">{{ number_format($lab->rating ?? 4.8, 1) }}</div><div class="lab-stat-key">@lang('dentalink.blades.browse_labs.stat_rating')</div></div>
                    <div><div class="lab-stat-val">@lang('dentalink.units.days_count', ['count' => $lab->avg_turnaround_days ?? 5])</div><div class="lab-stat-key">@lang('dentalink.blades.browse_labs.stat_average')</div></div>
                    <div><div class="lab-stat-val">${{ number_format($lab->starting_price ?? 240, 0) }}+</div><div class="lab-stat-key">@lang('dentalink.blades.browse_labs.stat_from')</div></div>
                </div>
                <a href="{{ \App\Filament\App\Pages\CreateOrder::getUrl() }}" class="dentalink-btn {{ $index === 0 ? 'dentalink-btn-primary' : 'dentalink-btn-outline' }}" style="width:100%;justify-content:center;margin-top:14px;">
                    {{ $index === 0 ? __('dentalink.blades.browse_labs.select_lab') : __('dentalink.blades.browse_labs.view_details') }}
                </a>
            </div>
        @empty
            <div class="lab-card">
                <div class="lab-header">
                    <div>
                        <div class="lab-name">@lang('dentalink.blades.browse_labs.fallback_lab_name')</div>
                        <div class="lab-country">@lang('dentalink.blades.browse_labs.fallback_location')</div>
                    </div>
                    <div class="stars">★★★★★</div>
                </div>
                <div class="lab-tags">
                    <span class="badge badge-blue">@lang('dentalink.service_categories.crown')</span>
                    <span class="badge badge-teal">@lang('dentalink.service_categories.bridge')</span>
                    <span class="badge badge-green">@lang('dentalink.service_categories.implant')</span>
                </div>
                <div class="lab-stats">
                    <div><div class="lab-stat-val">4.9</div><div class="lab-stat-key">@lang('dentalink.blades.browse_labs.stat_rating')</div></div>
                    <div><div class="lab-stat-val">@lang('dentalink.units.days_count', ['count' => 5])</div><div class="lab-stat-key">@lang('dentalink.blades.browse_labs.stat_average')</div></div>
                    <div><div class="lab-stat-val">$240+</div><div class="lab-stat-key">@lang('dentalink.blades.browse_labs.stat_from')</div></div>
                </div>
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
