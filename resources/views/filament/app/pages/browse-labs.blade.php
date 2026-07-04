<x-filament-panels::page class="dentalink-page">
    <div class="section-header">
        <div>
            <div class="section-title">@lang('dentalink.blades.browse_labs.title')</div>
            <div class="section-sub">@lang('dentalink.blades.browse_labs.subtitle', ['count' => $this->getLabs()->count()])</div>
        </div>
        @if ($this->hasActiveFilters())
            <button type="button" wire:click="clearFilters" class="dentalink-btn dentalink-btn-outline" style="font-size:12px;padding:6px 12px;">
                @lang('dentalink.blades.browse_labs.clear_filters')
            </button>
        @endif
    </div>

    <div
        class="filters-bar filters-bar--stacked"
        x-data="{ openDropdown: null }"
        @keydown.escape.window="openDropdown = null"
    >
        <div class="filter-group">
            <div class="filter-group-label">@lang('dentalink.blades.browse_labs.search_label')</div>
            <div class="filters-row">
                <div class="search-multi">
                    <div class="search-multi-input-row">
                        <input
                            wire:model.live.debounce.300ms="searchInput"
                            wire:keydown.enter.prevent="addSearchTerm"
                            type="search"
                            class="filter-select filter-search"
                            placeholder="{{ __('dentalink.blades.browse_labs.search_placeholder') }}"
                            autocomplete="off"
                        >
                        <button
                            type="button"
                            wire:click="addSearchTerm"
                            class="dentalink-btn dentalink-btn-outline search-add-btn"
                        >
                            @lang('dentalink.blades.browse_labs.add_search_term')
                        </button>
                    </div>

                    @if ($this->searchTerms !== [])
                        <div class="filter-chips search-terms">
                            @foreach ($this->searchTerms as $term)
                                <button
                                    type="button"
                                    wire:key="search-term-{{ md5($term) }}"
                                    wire:click="removeSearchTerm({{ Js::from($term) }})"
                                    class="filter-chip is-active search-term-chip"
                                    title="{{ __('dentalink.blades.browse_labs.remove_search_term') }}"
                                >
                                    {{ $term }}
                                    <span class="search-term-remove" aria-hidden="true">×</span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="filter-group">
            <div class="filter-group-label">@lang('dentalink.blades.browse_labs.filters_label')</div>
            <div class="filters-row filters-row--dropdowns">
                <div
                    class="multi-dropdown {{ $this->searchTerms !== [] ? 'has-selection' : '' }}"
                    :class="{ 'is-open': openDropdown === 'search' }"
                    @click.outside="if (openDropdown === 'search') openDropdown = null"
                >
                    <button
                        type="button"
                        class="filter-select multi-dropdown-trigger"
                        @click.stop="openDropdown = openDropdown === 'search' ? null : 'search'"
                    >
                        <span class="multi-dropdown-text">{{ $this->searchDropdownLabel() }}</span>
                        <span class="multi-dropdown-caret" :class="{ 'is-open': openDropdown === 'search' }">▾</span>
                    </button>
                    <div class="multi-dropdown-menu" x-show="openDropdown === 'search'" x-cloak @click.stop>
                        @forelse ($this->getSearchSuggestions() as $suggestion)
                            <label class="multi-dropdown-item" wire:key="search-suggestion-{{ md5($suggestion) }}">
                                <input
                                    type="checkbox"
                                    value="{{ $suggestion }}"
                                    @checked(in_array($suggestion, $this->searchTerms, true))
                                    wire:click.prevent="toggleSearchTerm({{ Js::from($suggestion) }})"
                                >
                                <span>{{ $suggestion }}</span>
                            </label>
                        @empty
                            <div class="multi-dropdown-empty">@lang('dentalink.blades.browse_labs.no_options')</div>
                        @endforelse
                    </div>
                </div>

                <div
                    class="multi-dropdown {{ $this->normalizedCountries() !== [] ? 'has-selection' : '' }}"
                    :class="{ 'is-open': openDropdown === 'countries' }"
                    @click.outside="if (openDropdown === 'countries') openDropdown = null"
                >
                    <button
                        type="button"
                        class="filter-select multi-dropdown-trigger"
                        @click.stop="openDropdown = openDropdown === 'countries' ? null : 'countries'"
                    >
                        <span class="multi-dropdown-text">{{ $this->countriesDropdownLabel() }}</span>
                        <span class="multi-dropdown-caret" :class="{ 'is-open': openDropdown === 'countries' }">▾</span>
                    </button>
                    <div class="multi-dropdown-menu" x-show="openDropdown === 'countries'" x-cloak @click.stop>
                        @forelse ($this->getCountries() as $country)
                            <label class="multi-dropdown-item" wire:key="country-option-{{ md5($country) }}">
                                <input
                                    type="checkbox"
                                    value="{{ $country }}"
                                    @checked(in_array($country, $this->normalizedCountries(), true))
                                    wire:click.prevent="toggleCountry({{ Js::from($country) }})"
                                >
                                <span>{{ $country }}</span>
                            </label>
                        @empty
                            <div class="multi-dropdown-empty">@lang('dentalink.blades.browse_labs.no_options')</div>
                        @endforelse
                    </div>
                </div>

                <div
                    class="multi-dropdown {{ $this->normalizedServices() !== [] ? 'has-selection' : '' }}"
                    :class="{ 'is-open': openDropdown === 'services' }"
                    @click.outside="if (openDropdown === 'services') openDropdown = null"
                >
                    <button
                        type="button"
                        class="filter-select multi-dropdown-trigger"
                        @click.stop="openDropdown = openDropdown === 'services' ? null : 'services'"
                    >
                        <span class="multi-dropdown-text">{{ $this->servicesDropdownLabel() }}</span>
                        <span class="multi-dropdown-caret" :class="{ 'is-open': openDropdown === 'services' }">▾</span>
                    </button>
                    <div class="multi-dropdown-menu" x-show="openDropdown === 'services'" x-cloak @click.stop>
                        @foreach ($this->getServiceOptions() as $value => $label)
                            <label class="multi-dropdown-item" wire:key="service-option-{{ $value }}">
                                <input
                                    type="checkbox"
                                    value="{{ $value }}"
                                    @checked(in_array($value, $this->normalizedServices(), true))
                                    wire:click.prevent="toggleService({{ Js::from($value) }})"
                                >
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-group">
            <div class="filter-group-label">@lang('dentalink.blades.browse_labs.metrics_label')</div>
            <div class="filters-row filters-row--dropdowns">
                <div
                    class="multi-dropdown multi-dropdown--rating {{ $this->normalizedRatings() !== [] || $this->sort === 'rating' ? 'has-selection' : '' }}"
                    :class="{ 'is-open': openDropdown === 'rating' }"
                    @click.outside="if (openDropdown === 'rating') openDropdown = null"
                >
                    <button
                        type="button"
                        class="filter-select multi-dropdown-trigger"
                        @click.stop="openDropdown = openDropdown === 'rating' ? null : 'rating'"
                    >
                        <span class="multi-dropdown-text">{{ $this->ratingDropdownLabel() }}</span>
                        <span class="multi-dropdown-caret" :class="{ 'is-open': openDropdown === 'rating' }">▾</span>
                    </button>
                    <div class="multi-dropdown-menu" x-show="openDropdown === 'rating'" x-cloak @click.stop>
                        <button
                            type="button"
                            class="multi-dropdown-action {{ $this->sort === 'rating' ? 'is-active' : '' }}"
                            wire:click="setSort('rating')"
                        >
                            @lang('dentalink.blades.browse_labs.sort_by_metric', ['label' => __('dentalink.blades.browse_labs.sort_rating')])
                        </button>
                        <div class="multi-dropdown-divider"></div>
                        @foreach ($this->getRatingOptions() as $value => $label)
                            <label class="multi-dropdown-item" wire:key="rating-option-{{ $value }}">
                                <input
                                    type="checkbox"
                                    value="{{ $value }}"
                                    @checked(in_array($value, $this->normalizedRatings(), true))
                                    wire:click.prevent="toggleRating({{ Js::from($value) }})"
                                >
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div
                    class="multi-dropdown multi-dropdown--speed {{ $this->normalizedSpeeds() !== [] || $this->sort === 'speed' ? 'has-selection' : '' }}"
                    :class="{ 'is-open': openDropdown === 'speed' }"
                    @click.outside="if (openDropdown === 'speed') openDropdown = null"
                >
                    <button
                        type="button"
                        class="filter-select multi-dropdown-trigger"
                        @click.stop="openDropdown = openDropdown === 'speed' ? null : 'speed'"
                    >
                        <span class="multi-dropdown-text">{{ $this->speedDropdownLabel() }}</span>
                        <span class="multi-dropdown-caret" :class="{ 'is-open': openDropdown === 'speed' }">▾</span>
                    </button>
                    <div class="multi-dropdown-menu" x-show="openDropdown === 'speed'" x-cloak @click.stop>
                        <button
                            type="button"
                            class="multi-dropdown-action {{ $this->sort === 'speed' ? 'is-active' : '' }}"
                            wire:click="setSort('speed')"
                        >
                            @lang('dentalink.blades.browse_labs.sort_by_metric', ['label' => __('dentalink.blades.browse_labs.sort_speed')])
                        </button>
                        <div class="multi-dropdown-divider"></div>
                        @foreach ($this->getSpeedOptions() as $value => $label)
                            <label class="multi-dropdown-item" wire:key="speed-option-{{ $value }}">
                                <input
                                    type="checkbox"
                                    value="{{ $value }}"
                                    @checked(in_array($value, $this->normalizedSpeeds(), true))
                                    wire:click.prevent="toggleSpeed({{ Js::from($value) }})"
                                >
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div
                    class="multi-dropdown multi-dropdown--price {{ $this->normalizedPrices() !== [] || $this->sort === 'price' ? 'has-selection' : '' }}"
                    :class="{ 'is-open': openDropdown === 'price' }"
                    @click.outside="if (openDropdown === 'price') openDropdown = null"
                >
                    <button
                        type="button"
                        class="filter-select multi-dropdown-trigger"
                        @click.stop="openDropdown = openDropdown === 'price' ? null : 'price'"
                    >
                        <span class="multi-dropdown-text">{{ $this->priceDropdownLabel() }}</span>
                        <span class="multi-dropdown-caret" :class="{ 'is-open': openDropdown === 'price' }">▾</span>
                    </button>
                    <div class="multi-dropdown-menu" x-show="openDropdown === 'price'" x-cloak @click.stop>
                        <button
                            type="button"
                            class="multi-dropdown-action {{ $this->sort === 'price' ? 'is-active' : '' }}"
                            wire:click="setSort('price')"
                        >
                            @lang('dentalink.blades.browse_labs.sort_by_metric', ['label' => __('dentalink.blades.browse_labs.sort_price')])
                        </button>
                        <div class="multi-dropdown-divider"></div>
                        @foreach ($this->getPriceOptions() as $value => $label)
                            <label class="multi-dropdown-item" wire:key="price-option-{{ $value }}">
                                <input
                                    type="checkbox"
                                    value="{{ $value }}"
                                    @checked(in_array($value, $this->normalizedPrices(), true))
                                    wire:click.prevent="togglePrice({{ Js::from($value) }})"
                                >
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ai-suggestion" style="margin-bottom:16px;">
        <span class="ai-suggestion-icon">🤖</span>
        <div class="ai-suggestion-text">@lang('dentalink.blades.browse_labs.ai_suggestion')</div>
    </div>

    <div class="labs-grid" wire:key="labs-grid-{{ $this->sort }}-{{ md5(json_encode([$this->activeSearchTerms(), $this->normalizedCountries(), $this->normalizedServices(), $this->normalizedRatings(), $this->normalizedSpeeds(), $this->normalizedPrices()])) }}">
        @forelse ($this->getLabs() as $index => $lab)
            @php $rank = $index + 1; @endphp
            <div class="lab-card" wire:key="lab-card-{{ $lab->id }}-{{ $this->sort }}" @if($index === 0) style="border-color:var(--primary);border-width:2px;" @endif>
                <div class="lab-card-top">
                    @if ($indicator = $this->sortIndicatorFor($lab, $rank))
                        <div class="sort-indicator sort-indicator--{{ $this->sort }}">{{ $indicator }}</div>
                    @endif
                    @if ($index === 0)
                        <div class="ai-suggested-badge">@lang('dentalink.blades.browse_labs.ai_suggested_badge')</div>
                    @endif
                </div>
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
                    <div class="lab-stat lab-stat--rating {{ $this->sort === 'rating' ? 'is-sort-active' : '' }}">
                        <div class="lab-stat-val">{{ $lab->rating !== null ? number_format((float) $lab->rating, 1) : '—' }}</div>
                        <div class="lab-stat-key">@lang('dentalink.blades.browse_labs.stat_rating')</div>
                    </div>
                    <div class="lab-stat lab-stat--speed {{ $this->sort === 'speed' ? 'is-sort-active' : '' }}">
                        <div class="lab-stat-val">
                            @if ($this->speedDaysFor($lab) !== null)
                                @lang('dentalink.units.days_count', ['count' => $this->speedDaysFor($lab)])
                            @else
                                —
                            @endif
                        </div>
                        <div class="lab-stat-key">@lang('dentalink.blades.browse_labs.stat_speed')</div>
                    </div>
                    <div class="lab-stat lab-stat--price {{ $this->sort === 'price' ? 'is-sort-active' : '' }}">
                        <div class="lab-stat-val">
                            @if ($this->startingPriceFor($lab) !== null)
                                ${{ number_format($this->startingPriceFor($lab), 0) }}
                            @else
                                —
                            @endif
                        </div>
                        <div class="lab-stat-key">@lang('dentalink.blades.browse_labs.stat_price')</div>
                    </div>
                </div>
                <a href="{{ \App\Filament\App\Pages\CreateOrder::getUrl() }}" class="dentalink-btn {{ $index === 0 ? 'dentalink-btn-primary' : 'dentalink-btn-outline' }}" style="width:100%;justify-content:center;margin-top:14px;">
                    {{ $index === 0 ? __('dentalink.blades.browse_labs.select_lab') : __('dentalink.blades.browse_labs.view_details') }}
                </a>
            </div>
        @empty
            <div class="lab-card labs-empty">
                <div class="lab-name">@lang('dentalink.blades.browse_labs.no_results')</div>
                <div class="lab-country" style="margin-top:6px;">@lang('dentalink.blades.browse_labs.no_results_hint')</div>
                @if ($this->hasActiveFilters())
                    <button type="button" wire:click="clearFilters" class="dentalink-btn dentalink-btn-outline" style="margin-top:14px;font-size:12px;">
                        @lang('dentalink.blades.browse_labs.clear_filters')
                    </button>
                @endif
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
