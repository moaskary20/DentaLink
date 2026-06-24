<?php

namespace App\Filament\App\Pages;

use App\Models\Lab;
use App\Models\Order;
use App\Services\AiAssistantService;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class AiAssistantPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static string $view = 'filament.app.pages.ai-assistant-page';

    public static function getNavigationGroup(): ?string
    {
        return __('dentalink.nav.groups.ai');
    }

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('dentalink.pages.ai_assistant.nav');
    }

    public function getTitle(): string
    {
        return __('dentalink.pages.ai_assistant.title');
    }

    public string $prompt = '';

    public array $chatHistory = [];

    public array $suggestions = [];

    public array $matchedLabs = [];

    public function mount(): void
    {
        $ai = app(AiAssistantService::class);
        $this->matchedLabs = $ai->matchLabs(Auth::user())->take(3)->all();
        $this->suggestions = $this->buildSuggestions($ai);
        $this->chatHistory[] = [
            'role' => 'assistant',
            'text' => __('dentalink.pages.ai_assistant.greeting'),
        ];
    }

    public function askAi(): void
    {
        if (blank($this->prompt)) {
            return;
        }

        $question = $this->prompt;
        $this->chatHistory[] = ['role' => 'user', 'text' => $question];

        $reply = app(AiAssistantService::class)->chatbotReply(Auth::user(), $question);
        $this->chatHistory[] = ['role' => 'assistant', 'text' => $reply];
        $this->prompt = '';
    }

    protected function buildSuggestions(AiAssistantService $ai): array
    {
        $topLab = $ai->matchLabs(Auth::user())->first();
        $overdue = $ai->detectOverdueOrders(Auth::user())->count();
        $recentService = Order::query()->where('doctor_id', Auth::id())->latest()->value('service_name');

        $items = [];

        if ($topLab) {
            $items[] = [
                'icon' => '🎯',
                'title' => __('dentalink.pages.ai_assistant.suggestions.smart_lab_matching.title'),
                'text' => __('dentalink.pages.ai_assistant.suggestions.smart_lab_matching.text', [
                    'lab' => $topLab->name,
                    'score' => $topLab->match_score,
                    'rating' => $topLab->rating,
                    'price' => $topLab->starting_price,
                    'days' => $topLab->avg_turnaround_days,
                ]),
            ];
        }

        if ($recentService) {
            $items[] = [
                'icon' => '🦷',
                'title' => __('dentalink.pages.ai_assistant.suggestions.material.title'),
                'text' => __('dentalink.pages.ai_assistant.suggestions.material.text', ['service' => $recentService]),
            ];
        }

        if ($overdue > 0) {
            $items[] = [
                'icon' => '⚠️',
                'title' => __('dentalink.pages.ai_assistant.suggestions.delay.title'),
                'text' => __('dentalink.pages.ai_assistant.suggestions.delay.text', ['count' => $overdue]),
            ];
        }

        $items[] = [
            'icon' => '📊',
            'title' => __('dentalink.pages.ai_assistant.suggestions.cost.title'),
            'text' => __('dentalink.pages.ai_assistant.suggestions.cost.text'),
        ];

        return $items;
    }
}
