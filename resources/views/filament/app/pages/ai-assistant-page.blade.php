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
            <form wire:submit="askAi" style="margin-top:16px;display:flex;gap:8px;">
                <input wire:model="prompt" type="text" class="form-control" placeholder="{{ __('dentalink.blades.ai_assistant.input_placeholder') }}" style="background:#fff;">
                <button type="submit" class="dentalink-btn dentalink-btn-primary">@lang('dentalink.blades.ai_assistant.ask')</button>
            </form>
        </div>
        <div class="card">
            <div class="card-title">@lang('dentalink.blades.ai_assistant.quick_actions')</div>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <button type="button" class="dentalink-btn dentalink-btn-outline" style="width:100%;justify-content:center;">@lang('dentalink.blades.ai_assistant.quick_find_lab')</button>
                <button type="button" class="dentalink-btn dentalink-btn-outline" style="width:100%;justify-content:center;">@lang('dentalink.blades.ai_assistant.quick_analyze')</button>
                <button type="button" class="dentalink-btn dentalink-btn-outline" style="width:100%;justify-content:center;">@lang('dentalink.blades.ai_assistant.quick_suggest')</button>
            </div>
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
