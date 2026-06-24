<x-filament-widgets::widget>
    <div class="ai-card">
        <div class="ai-title">🤖 @lang('dentalink.widgets.ai_recommendation.heading')</div>
        <div class="ai-desc">
            @if ($this->getRecommendedLab())
                @lang('dentalink.widgets.ai_recommendation.text', ['lab' => $this->getRecommendedLab()->name, 'score' => $this->getMatchScore()])
            @else
                @lang('dentalink.widgets.ai_recommendation.fallback')
            @endif
        </div>
        <a href="{{ \App\Filament\App\Pages\AiAssistantPage::getUrl() }}" class="dentalink-btn dentalink-btn-outline" style="margin-top:14px;color:#fff;border-color:rgba(255,255,255,0.4);font-size:12px;padding:7px 14px;">
            @lang('dentalink.widgets.ai_recommendation.explore_more')
        </a>
    </div>
</x-filament-widgets::widget>
