<?php

namespace App\Providers\Filament;

use Illuminate\Support\HtmlString;

trait LoadsDentaLinkAssets
{
    protected function dentalinkStylesHook(): \Closure
    {
        return fn (): HtmlString => new HtmlString(
            '<link rel="stylesheet" href="'.asset('css/dentalink.css').'?v=4">'
        );
    }

    protected function dentalinkPanelStylesHook(): \Closure
    {
        return fn (): HtmlString => new HtmlString(
            '<link rel="stylesheet" href="'.asset('css/dentalink.css').'?v=4">'.
            '<link rel="stylesheet" href="'.asset('css/dentalink-panel.css').'?v=6">'.
            '<link rel="stylesheet" href="'.asset('css/dentalink-3d.css').'?v=2">'
        );
    }

    protected function dentalink3dScriptHook(): \Closure
    {
        return fn (): HtmlString => new HtmlString(
            '<script src="'.asset('js/dentalink-3d.js').'?v=1" defer></script>'
        );
    }
}
