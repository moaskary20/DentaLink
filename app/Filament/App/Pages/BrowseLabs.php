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

    #[Url]
    public string $search = '';

    #[Url]
    public string $country = '';

    #[Url]
    public string $service = '';

    #[Url]
    public string $sort = 'rating';

    public function getAiMatchedLabs()
    {
        return app(AiAssistantService::class)
            ->matchLabs(Auth::user(), $this->service ?: null, $this->country ?: null)
            ->take(3);
    }

    public function getLabs()
    {
        return Lab::query()
            ->with('services')
            ->where('is_active', true)
            ->where('approval_status', ApprovalStatus::Approved)
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->country, fn ($q) => $q->where('country', $this->country))
            ->when($this->service, fn ($q) => $q->whereHas('services', fn ($sq) => $sq->where('category', 'like', "%{$this->service}%")))
            ->when($this->sort === 'rating', fn ($q) => $q->orderByDesc('rating'))
            ->when($this->sort === 'price', fn ($q) => $q->orderBy('starting_price'))
            ->when($this->sort === 'speed', fn ($q) => $q->orderBy('avg_turnaround_days'))
            ->limit(12)
            ->get();
    }

    public function getCountries(): array
    {
        return Lab::query()
            ->whereNotNull('country')
            ->distinct()
            ->pluck('country')
            ->filter()
            ->values()
            ->all();
    }
}
