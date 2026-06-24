<?php

namespace App\Http\Controllers;

use App\Support\DentaLinkLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LocaleController extends Controller
{
    public function switch(string $locale): RedirectResponse
    {
        if (! DentaLinkLocale::isSupported($locale)) {
            return back();
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);

        cookie()->queue(cookie('locale', $locale, 60 * 24 * 365));

        if ($user = Auth::user()) {
            $user->update(['locale' => $locale]);
        }

        return back();
    }
}
