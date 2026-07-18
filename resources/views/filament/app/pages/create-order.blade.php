<x-filament-panels::page class="dentalink-page">
    <div class="section-header">
        <div>
            <div class="section-title">@lang('dentalink.blades.create_order.title')</div>
            <div class="section-sub">@lang('dentalink.blades.create_order.subtitle')</div>
        </div>
    </div>

    <div class="step-tabs">
        <div class="step-tab {{ $currentStep === 1 ? 'active' : '' }}">@lang('dentalink.blades.create_order.step_case')</div>
        <div class="step-tab {{ $currentStep === 2 ? 'active' : '' }}">@lang('dentalink.blades.create_order.step_upload')</div>
        <div class="step-tab {{ $currentStep === 3 ? 'active' : '' }}">@lang('dentalink.blades.create_order.step_lab')</div>
        <div class="step-tab {{ $currentStep === 4 ? 'active' : '' }}">@lang('dentalink.blades.create_order.step_payment')</div>
    </div>

    <div class="grid-2">
        <div class="card">
            @if ($currentStep <= 3)
                <div class="card-title">
                    @if ($currentStep === 1) @lang('dentalink.blades.create_order.step_title_case')
                    @elseif ($currentStep === 2) @lang('dentalink.blades.create_order.step_title_upload')
                    @else @lang('dentalink.blades.create_order.step_title_lab')
                    @endif
                </div>
                <form wire:submit="nextStep">
                    {{ $this->form }}
                    <div style="display:flex;gap:8px;margin-top:16px;">
                        @if ($currentStep > 1)
                            <button type="button" wire:click="previousStep" class="dentalink-btn dentalink-btn-outline">@lang('dentalink.actions.back')</button>
                        @endif
                        <button type="button" wire:click="nextStep" class="dentalink-btn dentalink-btn-primary">@lang('dentalink.actions.continue')</button>
                    </div>
                </form>
            @else
                <div class="card-title">@lang('dentalink.blades.create_order.step_title_review')</div>
                <p style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">@lang('dentalink.blades.create_order.review_hint')</p>

                <div style="margin-bottom:16px;">
                    {{ $this->form }}
                </div>

                <button type="button" wire:click="submitOrder" wire:loading.attr="disabled" class="dentalink-btn dentalink-btn-primary" style="width:100%;justify-content:center;">
                    <span wire:loading.remove wire:target="submitOrder">
                        @lang('dentalink.blades.create_order.submit_order', ['amount' => number_format($this->getTotal(), 2)])
                    </span>
                    <span wire:loading wire:target="submitOrder">
                        @lang('dentalink.blades.create_order.redirecting_payment')
                    </span>
                </button>
            @endif
        </div>

        <div style="display:flex;flex-direction:column;gap:16px;">
            @if ($currentStep === 2)
                <div class="card">
                    <div class="card-title">@lang('dentalink.blades.create_order.upload_guide_title')</div>
                    <div class="upload-zone">
                        <div class="upload-icon">🦷</div>
                        <div class="upload-text">@lang('dentalink.blades.create_order.upload_guide_text')</div>
                        <div class="upload-sub">@lang('dentalink.blades.create_order.upload_guide_formats')</div>
                    </div>
                </div>
            @endif

            <div class="card" style="background:var(--primary-light);border-color:#B5D4F4;">
                <div style="font-size:13px;font-weight:700;color:var(--primary-dark);margin-bottom:10px;">@lang('dentalink.blades.create_order.ai_analysis_title')</div>
                <div class="ai-suggestion" style="background:#fff;">
                    <span class="ai-suggestion-icon">✅</span>
                    <div class="ai-suggestion-text">@lang('dentalink.blades.create_order.ai_quality_ok')</div>
                </div>
                <div class="ai-suggestion" style="background:#fff;margin-top:8px;">
                    <span class="ai-suggestion-icon">💡</span>
                    <div class="ai-suggestion-text">@lang('dentalink.blades.create_order.ai_material_hint')</div>
                </div>
            </div>

            <div class="card" style="text-align:center;">
                <div style="font-size:13px;font-weight:700;margin-bottom:14px;">@lang('dentalink.blades.create_order.cost_summary_title')</div>
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:6px 0;border-bottom:1px solid var(--border);">
                    <span>@lang('dentalink.fields.service')</span><span style="font-weight:700;">${{ number_format($this->getEstimatedCost(), 2) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:6px 0;border-bottom:1px solid var(--border);">
                    <span>@lang('dentalink.blades.create_order.platform_fee')</span><span>${{ number_format($this->getCommission(), 2) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:14px;font-weight:800;padding:10px 0;color:var(--primary);">
                    <span>@lang('dentalink.blades.create_order.total')</span><span>${{ number_format($this->getTotal(), 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
