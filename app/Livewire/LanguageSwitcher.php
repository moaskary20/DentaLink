<?php

namespace App\Livewire;

use App\Support\DentaLinkLocale;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public function switch(string $locale): void
    {
        if (! DentaLinkLocale::isSupported($locale)) {
            return;
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);

        if ($user = Auth::user()) {
            $user->update(['locale' => $locale]);
        }

        $this->redirect(request()->header('Referer', url('/')), navigate: true);
    }

    public function render()
    {
        return view('livewire.language-switcher', [
            'current' => app()->getLocale(),
            'locales' => DentaLinkLocale::labels(),
        ]);
    }
}
