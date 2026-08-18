<?php

namespace App\Http\Middleware;

use App\Services\LicenseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLicense
{
    public function __construct(private LicenseService $licenseService) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('up')) {
            return $next($request);
        }

        if ($this->licenseService->isValid()) {
            return $next($request);
        }

        return response()->view('license.invalid', [], Response::HTTP_SERVICE_UNAVAILABLE);
    }
}
