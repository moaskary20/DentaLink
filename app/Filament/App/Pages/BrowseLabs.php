<?php

namespace App\Filament\App\Pages;

use App\Enums\ApprovalStatus;
use App\Models\Lab;
use App\Services\AiAssistantService;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;

class BrowseLabs extends Page
{
    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.laboratories');
    }

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    public static function getNavigationLabel(): string
    {
        return __('dentalink.pages.browse_labs.nav');
    }

    public function getTitle(): string
    {
        return __('dentalink.pages.browse_labs.title');
    }

    protected static string $view = 'filament.app.pages.browse-labs';

    protected static ?int $navigationSort = 1;

    public string $searchInput = '';

    /** @var list<string> */
    #[Url]
    public array $searchTerms = [];

    /** @var list<string> */
    #[Url]
    public array $countries = [];

    /** @var list<string> */
    #[Url]
    public array $services = [];

    /** @var list<string> Minimum rating thresholds, e.g. "4.5" */
    #[Url]
    public array $ratings = [];

    /** @var list<string> Maximum speed days, e.g. "7" */
    #[Url]
    public array $speeds = [];

    /** @var list<string> Maximum prices, e.g. "250" */
    #[Url]
    public array $prices = [];

    #[Url]
    public string $sort = 'rating';

    /** @var \Illuminate\Support\Collection<int, Lab>|null */
    protected $labsCache = null;

    public function addSearchTerm(?string $term = null): void
    {
        $raw = trim($term ?? $this->searchInput);

        if ($raw === '') {
            return;
        }

        $parts = preg_split('/[,،;|]+/u', $raw, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        foreach ($parts as $part) {
            $value = $this->normalizeSearchTerm($part);

            if ($value === '' || in_array($value, $this->searchTerms, true)) {
                continue;
            }

            $this->searchTerms[] = $value;
        }

        $this->searchTerms = array_values($this->searchTerms);
        $this->searchInput = '';
        $this->labsCache = null;
    }

    public function removeSearchTerm(string $term): void
    {
        $this->searchTerms = array_values(array_filter(
            $this->searchTerms,
            fn (string $item) => $item !== $term,
        ));
        $this->labsCache = null;
    }

    public function toggleSearchTerm(string $term): void
    {
        $value = $this->normalizeSearchTerm($term);

        if ($value === '') {
            return;
        }

        if (in_array($value, $this->searchTerms, true)) {
            $this->removeSearchTerm($value);

            return;
        }

        $this->searchTerms[] = $value;
        $this->searchTerms = array_values($this->searchTerms);
        $this->labsCache = null;
    }

    public function toggleCountry(string $country): void
    {
        $this->countries = $this->toggleInList($this->countries, $country);
        $this->labsCache = null;
    }

    public function toggleService(string $service): void
    {
        $this->services = $this->toggleInList($this->services, $service);
        $this->labsCache = null;
    }

    public function toggleRating(string $rating): void
    {
        $this->ratings = $this->toggleInList($this->ratings, $rating);
        $this->sort = 'rating';
        $this->labsCache = null;
    }

    public function toggleSpeed(string $speed): void
    {
        $this->speeds = $this->toggleInList($this->speeds, $speed);
        $this->sort = 'speed';
        $this->labsCache = null;
    }

    public function togglePrice(string $price): void
    {
        $this->prices = $this->toggleInList($this->prices, $price);
        $this->sort = 'price';
        $this->labsCache = null;
    }

    public function searchDropdownLabel(): string
    {
        return $this->multiDropdownLabel(
            $this->searchTerms,
            __('dentalink.blades.browse_labs.search_suggestions'),
        );
    }

    public function countriesDropdownLabel(): string
    {
        return $this->multiDropdownLabel(
            $this->normalizedCountries(),
            __('dentalink.blades.browse_labs.filter_countries'),
        );
    }

    public function servicesDropdownLabel(): string
    {
        $selected = $this->normalizedServices();
        $labels = collect($this->getServiceOptions())
            ->only($selected)
            ->values()
            ->all();

        return $this->multiDropdownLabel(
            $labels !== [] ? $labels : $selected,
            __('dentalink.blades.browse_labs.filter_services'),
        );
    }

    public function ratingDropdownLabel(): string
    {
        $labels = collect($this->getRatingOptions())
            ->only($this->normalizedRatings())
            ->values()
            ->all();

        return $this->metricDropdownLabel(
            $labels,
            __('dentalink.blades.browse_labs.sort_rating'),
            'rating',
        );
    }

    public function speedDropdownLabel(): string
    {
        $labels = collect($this->getSpeedOptions())
            ->only($this->normalizedSpeeds())
            ->values()
            ->all();

        return $this->metricDropdownLabel(
            $labels,
            __('dentalink.blades.browse_labs.sort_speed'),
            'speed',
        );
    }

    public function priceDropdownLabel(): string
    {
        $labels = collect($this->getPriceOptions())
            ->only($this->normalizedPrices())
            ->values()
            ->all();

        return $this->metricDropdownLabel(
            $labels,
            __('dentalink.blades.browse_labs.sort_price'),
            'price',
        );
    }

    /**
     * @param  list<string>  $selectedLabels
     */
    protected function metricDropdownLabel(array $selectedLabels, string $placeholder, string $metric): string
    {
        $label = $this->multiDropdownLabel($selectedLabels, $placeholder);

        if ($this->sort === $metric && $selectedLabels === []) {
            return __('dentalink.blades.browse_labs.sort_active', ['label' => $placeholder]);
        }

        return $label;
    }

    /**
     * @param  list<string>  $selected
     */
    protected function multiDropdownLabel(array $selected, string $placeholder): string
    {
        $count = count($selected);

        if ($count === 0) {
            return $placeholder;
        }

        if ($count === 1) {
            return $selected[0];
        }

        if ($count === 2) {
            return $selected[0].', '.$selected[1];
        }

        return __('dentalink.blades.browse_labs.selected_count', ['count' => $count]);
    }

    public function clearFilters(): void
    {
        $this->searchInput = '';
        $this->searchTerms = [];
        $this->countries = [];
        $this->services = [];
        $this->ratings = [];
        $this->speeds = [];
        $this->prices = [];
        $this->sort = 'rating';
        $this->labsCache = null;
    }

    public function hasActiveFilters(): bool
    {
        return $this->activeSearchTerms() !== []
            || $this->normalizedCountries() !== []
            || $this->normalizedServices() !== []
            || $this->normalizedRatings() !== []
            || $this->normalizedSpeeds() !== []
            || $this->normalizedPrices() !== []
            || $this->sort !== 'rating';
    }

    public function updatedSort(string $value): void
    {
        $allowed = ['rating', 'price', 'speed'];

        if (! in_array($value, $allowed, true)) {
            $this->sort = 'rating';
        }
    }

    public function getAiMatchedLabs()
    {
        return app(AiAssistantService::class)
            ->matchLabs(
                Auth::user(),
                $this->services[0] ?? null,
                $this->countries[0] ?? null,
            )
            ->take(3);
    }

    public function getLabs()
    {
        return $this->labsCache ??= $this->loadLabs();
    }

    public function setSort(string $sort): void
    {
        if (! in_array($sort, ['rating', 'price', 'speed'], true)) {
            return;
        }

        $this->sort = $sort;
        $this->labsCache = null;
    }

    /**
     * @return array<string, string>
     */
    public function getRatingOptions(): array
    {
        return [
            '4.0' => __('dentalink.blades.browse_labs.rating_option', ['value' => '4.0']),
            '4.5' => __('dentalink.blades.browse_labs.rating_option', ['value' => '4.5']),
            '4.8' => __('dentalink.blades.browse_labs.rating_option', ['value' => '4.8']),
            '5.0' => __('dentalink.blades.browse_labs.rating_option', ['value' => '5.0']),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getSpeedOptions(): array
    {
        return [
            '5' => __('dentalink.blades.browse_labs.speed_option', ['value' => 5]),
            '7' => __('dentalink.blades.browse_labs.speed_option', ['value' => 7]),
            '10' => __('dentalink.blades.browse_labs.speed_option', ['value' => 10]),
            '14' => __('dentalink.blades.browse_labs.speed_option', ['value' => 14]),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getPriceOptions(): array
    {
        return [
            '200' => __('dentalink.blades.browse_labs.price_option', ['value' => 200]),
            '250' => __('dentalink.blades.browse_labs.price_option', ['value' => 250]),
            '300' => __('dentalink.blades.browse_labs.price_option', ['value' => 300]),
            '400' => __('dentalink.blades.browse_labs.price_option', ['value' => 400]),
        ];
    }

    /**
     * @return list<string>
     */
    public function normalizedRatings(): array
    {
        return array_values(array_filter(
            array_map('strval', $this->ratings),
            fn (string $value) => array_key_exists($value, $this->getRatingOptions()),
        ));
    }

    /**
     * @return list<string>
     */
    public function normalizedSpeeds(): array
    {
        return array_values(array_filter(
            array_map('strval', $this->speeds),
            fn (string $value) => array_key_exists($value, $this->getSpeedOptions()),
        ));
    }

    /**
     * @return list<string>
     */
    public function normalizedPrices(): array
    {
        return array_values(array_filter(
            array_map('strval', $this->prices),
            fn (string $value) => array_key_exists($value, $this->getPriceOptions()),
        ));
    }

    public function sortIndicatorFor(Lab $lab, int $rank): ?string
    {
        return match ($this->sort) {
            'price' => ($price = $this->startingPriceFor($lab)) !== null
                ? __('dentalink.blades.browse_labs.sort_indicator_price', [
                    'rank' => $rank,
                    'value' => number_format($price, 0),
                ])
                : null,
            'speed' => ($days = $this->speedDaysFor($lab)) !== null
                ? __('dentalink.blades.browse_labs.sort_indicator_speed', [
                    'rank' => $rank,
                    'value' => $days,
                ])
                : null,
            'rating' => ($lab->rating !== null)
                ? __('dentalink.blades.browse_labs.sort_indicator_rating', [
                    'rank' => $rank,
                    'value' => number_format((float) $lab->rating, 1),
                ])
                : null,
            default => null,
        };
    }

    protected function loadLabs()
    {
        $minRating = $this->selectedMinRating();

        $labs = Lab::query()
            ->with(['services' => fn ($q) => $this->constrainServicesQuery($q)])
            ->withAvg(
                ['services as services_avg_turnaround' => fn ($q) => $this->constrainServicesQuery($q)],
                'turnaround_days',
            )
            ->withMin(
                ['services as services_min_price' => fn ($q) => $this->constrainServicesQuery($q)],
                'price',
            )
            ->where('is_active', true)
            ->where('approval_status', ApprovalStatus::Approved)
            ->when($this->activeSearchTerms() !== [], fn ($q) => $this->applySearch($q, $this->activeSearchTerms()))
            ->when($this->normalizedCountries() !== [], fn ($q) => $q->whereIn('country', $this->normalizedCountries()))
            ->when($this->normalizedServices() !== [], fn ($q) => $this->applyServiceFilters($q))
            ->when($minRating !== null, fn ($q) => $q->where('rating', '>=', $minRating));

        $this->applySortToQuery($labs);

        return $this->sortLabs(
            $this->applyMetricFilters($labs->get())
        )->take(24)->values();
    }

    protected function selectedMinRating(): ?float
    {
        $ratings = $this->normalizedRatings();

        if ($ratings === []) {
            return null;
        }

        // Strictest selected threshold (e.g. 4.5+ and 4.8+ => 4.8+).
        return max(array_map('floatval', $ratings));
    }

    protected function selectedMaxSpeed(): ?float
    {
        $speeds = $this->normalizedSpeeds();

        if ($speeds === []) {
            return null;
        }

        // Strictest selected limit (e.g. ≤5 and ≤7 => ≤5).
        return min(array_map('floatval', $speeds));
    }

    protected function selectedMaxPrice(): ?float
    {
        $prices = $this->normalizedPrices();

        if ($prices === []) {
            return null;
        }

        // Strictest selected limit (e.g. ≤200 and ≤250 => ≤200).
        return min(array_map('floatval', $prices));
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Lab>  $labs
     * @return \Illuminate\Support\Collection<int, Lab>
     */
    protected function applyMetricFilters($labs)
    {
        $maxSpeed = $this->selectedMaxSpeed();
        $maxPrice = $this->selectedMaxPrice();

        return $labs
            ->when($maxSpeed !== null, fn ($items) => $items->filter(function (Lab $lab) use ($maxSpeed) {
                $days = $this->speedDaysFor($lab);

                return $days !== null && $days <= $maxSpeed;
            }))
            ->when($maxPrice !== null, fn ($items) => $items->filter(function (Lab $lab) use ($maxPrice) {
                $price = $this->startingPriceFor($lab);

                return $price !== null && $price <= $maxPrice;
            }))
            ->values();
    }

    /**
     * Average turnaround from real lab services (falls back to lab profile field).
     */
    public function speedDaysFor(Lab $lab): ?float
    {
        if ($lab->services_avg_turnaround !== null) {
            return round((float) $lab->services_avg_turnaround, 1);
        }

        if ($lab->relationLoaded('services') && $lab->services->isNotEmpty()) {
            return round((float) $lab->services->avg('turnaround_days'), 1);
        }

        return $lab->avg_turnaround_days !== null
            ? round((float) $lab->avg_turnaround_days, 1)
            : null;
    }

    /**
     * Lowest service price from real lab services (falls back to lab profile field).
     */
    public function startingPriceFor(Lab $lab): ?float
    {
        if ($lab->services_min_price !== null) {
            return (float) $lab->services_min_price;
        }

        if ($lab->relationLoaded('services') && $lab->services->isNotEmpty()) {
            return (float) $lab->services->min('price');
        }

        return $lab->starting_price !== null ? (float) $lab->starting_price : null;
    }

    /**
     * @return list<string>
     */
    public function normalizedCountries(): array
    {
        return array_values(array_filter(
            array_map('strval', $this->countries),
            fn (string $country) => $country !== '',
        ));
    }

    /**
     * @return list<string>
     */
    public function normalizedServices(): array
    {
        return array_values(array_filter(
            array_map('strval', $this->services),
            fn (string $service) => $service !== '',
        ));
    }

    public function getCountries(): array
    {
        $fromDb = Lab::query()
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->all();

        return collect([...$fromDb, 'Qatar', 'UAE', 'Saudi Arabia'])
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function getServiceOptions(): array
    {
        return [
            'Crown' => __('dentalink.service_categories.crown'),
            'Bridge' => __('dentalink.service_categories.bridge'),
            'Implant' => __('dentalink.service_categories.implant'),
            'Veneer' => __('dentalink.service_categories.veneer'),
            'Denture' => __('dentalink.service_categories.denture'),
        ];
    }

    /**
     * Quick-pick values from real lab data for multi-select search.
     *
     * @return list<string>
     */
    public function getSearchSuggestions(): array
    {
        $labs = Lab::query()
            ->where('is_active', true)
            ->where('approval_status', ApprovalStatus::Approved)
            ->with('services')
            ->get();

        $suggestions = collect();

        foreach ($labs as $lab) {
            $suggestions->push($lab->name, $lab->city, $lab->country);

            foreach ((array) $lab->specialties as $specialty) {
                $suggestions->push($specialty);
            }

            foreach ($lab->services as $service) {
                $suggestions->push($service->name, $service->category);
            }
        }

        return $suggestions
            ->filter(fn ($value) => is_string($value) && trim($value) !== '')
            ->map(fn (string $value) => $this->normalizeSearchTerm($value))
            ->unique(fn (string $value) => mb_strtolower($value))
            ->sort(SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    public function activeSearchTerms(): array
    {
        $terms = array_values(array_filter(
            array_map(fn ($term) => $this->normalizeSearchTerm((string) $term), $this->searchTerms),
            fn (string $term) => $term !== '',
        ));

        $draft = $this->normalizeSearchTerm($this->searchInput);

        if ($draft !== '' && ! in_array($draft, $terms, true)) {
            $terms[] = $draft;
        }

        return $terms;
    }

    protected function normalizeSearchTerm(string $term): string
    {
        $term = trim(preg_replace('/\s+/u', ' ', $term) ?? '');

        return mb_substr($term, 0, 80);
    }

    /**
     * Match labs against any selected term across all relevant fields (broad OR search).
     *
     * @param  list<string>  $terms
     */
    protected function applySearch($query, array $terms)
    {
        return $query->where(function ($outer) use ($terms) {
            foreach ($terms as $term) {
                $outer->orWhere(function ($q) use ($term) {
                    $this->applySingleSearchTerm($q, $term);
                });
            }
        });
    }

    protected function applySingleSearchTerm($query, string $term): void
    {
        $like = '%'.$this->escapeLike($term).'%';
        $driver = $query->getConnection()->getDriverName();
        $likeOperator = $driver === 'pgsql' ? 'ilike' : 'like';
        $textCast = in_array($driver, ['pgsql', 'sqlite'], true) ? 'TEXT' : 'CHAR';

        $query->where(function ($q) use ($like, $likeOperator, $term, $textCast) {
            $q->where('name', $likeOperator, $like)
                ->orWhere('description', $likeOperator, $like)
                ->orWhere('city', $likeOperator, $like)
                ->orWhere('country', $likeOperator, $like)
                ->orWhere('address', $likeOperator, $like)
                ->orWhere('email', $likeOperator, $like)
                ->orWhere('phone', $likeOperator, $like)
                ->orWhere('specialties', $likeOperator, $like)
                ->orWhere('logo', $likeOperator, $like)
                ->orWhere('license_file', $likeOperator, $like)
                ->orWhereHas('services', function ($sq) use ($like, $likeOperator, $term, $textCast) {
                    $sq->where('name', $likeOperator, $like)
                        ->orWhere('category', $likeOperator, $like)
                        ->orWhereRaw("CAST(price AS {$textCast}) LIKE ?", [$like])
                        ->orWhereRaw("CAST(turnaround_days AS {$textCast}) LIKE ?", [$like]);

                    if (is_numeric($term)) {
                        $number = (float) $term;
                        $sq->orWhere('price', $number)
                            ->orWhere('turnaround_days', (int) $term);
                    }
                })
                ->orWhereHas('user', function ($uq) use ($like, $likeOperator) {
                    $uq->where('name', $likeOperator, $like)
                        ->orWhere('email', $likeOperator, $like)
                        ->orWhere('phone', $likeOperator, $like)
                        ->orWhere('country', $likeOperator, $like);
                })
                ->orWhereRaw("CAST(rating AS {$textCast}) LIKE ?", [$like])
                ->orWhereRaw("CAST(starting_price AS {$textCast}) LIKE ?", [$like])
                ->orWhereRaw("CAST(avg_turnaround_days AS {$textCast}) LIKE ?", [$like]);

            if (is_numeric($term)) {
                $number = (float) $term;
                $q->orWhere('rating', $number)
                    ->orWhere('starting_price', $number)
                    ->orWhere('avg_turnaround_days', (int) $term);
            }

            $featuredTerms = ['featured', 'مميز', 'mis en avant', 'premium'];
            if (in_array(mb_strtolower($term), $featuredTerms, true)) {
                $q->orWhere('is_featured', true);
            }
        });
    }

    protected function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }

    protected function applyServiceFilters($query)
    {
        $services = $this->normalizedServices();

        return $query->where(function ($q) use ($services) {
            foreach ($services as $service) {
                $like = '%'.$service.'%';

                $q->orWhereHas('services', function ($sq) use ($like) {
                    $sq->where('category', 'like', $like)
                        ->orWhere('name', 'like', $like);
                })->orWhere('specialties', 'like', $like);
            }
        });
    }

    protected function constrainServicesQuery($query): void
    {
        $query->where(function ($q) {
            $q->where('is_active', true)->orWhereNull('is_active');
        });

        $services = $this->normalizedServices();

        if ($services === []) {
            return;
        }

        $query->where(function ($q) use ($services) {
            foreach ($services as $service) {
                $like = '%'.$service.'%';

                $q->orWhere('category', 'like', $like)
                    ->orWhere('name', 'like', $like);
            }
        });
    }

    protected function applySortToQuery($query): void
    {
        match ($this->sort) {
            'price' => $query
                ->orderByRaw('COALESCE(services_min_price, starting_price, 999999999) asc')
                ->orderBy('name'),
            'speed' => $query
                ->orderByRaw('COALESCE(services_avg_turnaround, avg_turnaround_days, 999999) asc')
                ->orderBy('name'),
            default => $query
                ->orderByDesc('rating')
                ->orderBy('name'),
        };
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Lab>  $labs
     * @return \Illuminate\Support\Collection<int, Lab>
     */
    protected function sortLabs($labs)
    {
        return match ($this->sort) {
            'price' => $labs
                ->sortBy(fn (Lab $lab) => [
                    $this->startingPriceFor($lab) ?? PHP_FLOAT_MAX,
                    $lab->name,
                ])
                ->values(),
            'speed' => $labs
                ->sortBy(fn (Lab $lab) => [
                    $this->speedDaysFor($lab) ?? PHP_INT_MAX,
                    $lab->name,
                ])
                ->values(),
            default => $labs
                ->sortBy(fn (Lab $lab) => [
                    -1 * (float) ($lab->rating ?? 0),
                    $lab->name,
                ])
                ->values(),
        };
    }

    /**
     * @param  list<string>  $list
     * @return list<string>
     */
    protected function toggleInList(array $list, string $value): array
    {
        if (in_array($value, $list, true)) {
            return array_values(array_filter($list, fn (string $item) => $item !== $value));
        }

        $list[] = $value;

        return array_values($list);
    }
}
