<?php

namespace App\Http\Middleware;

use App\Support\DentaLinkLocale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = Auth::user()?->locale
            ?? session('locale')
            ?? $request->cookie('locale')
            ?? config('app.locale');

        if (DentaLinkLocale::isSupported($locale)) {
            app()->setLocale($locale);
            session(['locale' => $locale]);
        }

        return $next($request);
    }
}
