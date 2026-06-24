<x-filament-panels::page class="dentalink-page">
    <div class="section-header">
        <div>
            <div class="section-title">@lang('dentalink.blades.settings.title')</div>
            <div class="section-sub">@lang('dentalink.blades.settings.subtitle')</div>
        </div>
    </div>

    <form wire:submit="save">
        {{ $this->form }}

        <div style="margin-top:16px;">
            <button type="submit" class="dentalink-btn dentalink-btn-primary">@lang('dentalink.actions.save_changes')</button>
        </div>
    </form>
</x-filament-panels::page>
