<x-filament-panels::page class="dentalink-page">
    <div class="section-header">
        <div>
            <div class="section-title">@lang('dentalink.pages.ai_assistant.title')</div>
            <div class="section-sub">@lang('dentalink.blades.ai_assistant.subtitle')</div>
        </div>
    </div>

    <div class="grid-2" style="margin-bottom:16px;">
        <div class="ai-card">
            <div class="ai-title">@lang('dentalink.blades.ai_assistant.brand')</div>
            <div class="ai-desc">@lang('dentalink.blades.ai_assistant.description')</div>

            <div class="chat-messages" style="margin-top:16px;max-height:300px;">
                @foreach ($chatHistory as $index => $message)
                    <div
                        wire:key="ai-chat-{{ $index }}"
                        class="chat-bubble {{ $message['role'] === 'user' ? 'sent' : 'received' }}"
                    >
                        {!! \Illuminate\Support\Str::markdown($message['text']) !!}
                    </div>
                @endforeach
            </div>

            <form wire:submit="askAi" style="margin-top:16px;display:flex;gap:8px;">
                <input
                    wire:model="prompt"
                    type="text"
                    class="form-control"
                    placeholder="{{ __('dentalink.blades.ai_assistant.input_placeholder') }}"
                    style="background:#fff;"
                >
                <button type="submit" class="dentalink-btn dentalink-btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="askAi,quickAsk">@lang('dentalink.blades.ai_assistant.ask')</span>
                    <span wire:loading wire:target="askAi,quickAsk">@lang('dentalink.blades.ai_assistant.thinking')</span>
                </button>
            </form>
        </div>

        <div class="card">
            <div class="card-title">@lang('dentalink.blades.ai_assistant.quick_actions')</div>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <button type="button" wire:click="quickAsk('find_lab')" class="dentalink-btn dentalink-btn-outline" style="width:100%;justify-content:center;">
                    @lang('dentalink.blades.ai_assistant.quick_find_lab')
                </button>
                <button type="button" wire:click="quickAsk('analyze')" class="dentalink-btn dentalink-btn-outline" style="width:100%;justify-content:center;">
                    @lang('dentalink.blades.ai_assistant.quick_analyze')
                </button>
                <button type="button" wire:click="quickAsk('suggest')" class="dentalink-btn dentalink-btn-outline" style="width:100%;justify-content:center;">
                    @lang('dentalink.blades.ai_assistant.quick_suggest')
                </button>
            </div>

            @if (count($matchedLabs) > 0)
                <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
                    <div style="font-size:12px;font-weight:700;margin-bottom:10px;">@lang('dentalink.blades.ai_assistant.matched_labs')</div>
                    @foreach ($matchedLabs as $lab)
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border);font-size:12px;">
                            <div>
                                <div style="font-weight:700;">{{ $lab->name }}</div>
                                <div style="color:var(--text-muted);">{{ $lab->match_score }}% · ★{{ number_format($lab->rating, 1) }}</div>
                            </div>
                            <a href="{{ \App\Filament\App\Pages\CreateOrder::getUrl() }}" class="dentalink-btn dentalink-btn-outline" style="font-size:11px;padding:5px 10px;">
                                @lang('dentalink.blades.browse_labs.select_lab')
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-title">@lang('dentalink.blades.ai_assistant.insights')</div>
        @foreach ($suggestions as $suggestion)
            <div class="ai-suggestion" style="margin-bottom:10px;">
                <span class="ai-suggestion-icon">{{ $suggestion['icon'] }}</span>
                <div>
                    <div style="font-size:13px;font-weight:700;">{{ $suggestion['title'] }}</div>
                    <div class="ai-suggestion-text">{{ $suggestion['text'] }}</div>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
