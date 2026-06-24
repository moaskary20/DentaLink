<?php

namespace App\Providers\Filament;

use App\Livewire\LanguageSwitcher;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Livewire\Livewire;

trait ConfiguresDentaLinkPanel
{
    protected function dentalinkColors(): array
    {
        return [
            'primary' => Color::hex('#0A6EBD'),
            'success' => Color::hex('#3B9922'),
            'danger' => Color::hex('#E24B4A'),
            'warning' => Color::hex('#F4A932'),
            'info' => Color::hex('#1DA89A'),
            'gray' => Color::hex('#718096'),
        ];
    }

    protected function configureDentaLinkPanel(Panel $panel): Panel
    {
        return $panel
            ->darkMode(false)
            ->font('Cairo', 'https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap')
            ->colors($this->dentalinkColors())
            ->sidebarCollapsibleOnDesktop()
            ->brandLogoHeight('2.5rem')
            ->renderHook('panels::styles.after', $this->dentalinkPanelStylesHook())
            ->renderHook('panels::topbar.end', fn (): string => Livewire::mount(LanguageSwitcher::class))
            ->renderHook('panels::body.end', $this->dentalink3dScriptHook());
    }
}
