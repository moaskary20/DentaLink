<?php

namespace App\Providers;

use App\Http\Responses\LogoutResponse;
use App\Models\Lab;
use App\Models\Rating;
use App\Observers\LabObserver;
use App\Observers\RatingObserver;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    public function boot(): void
    {
        Rating::observe(RatingObserver::class);
        Lab::observe(LabObserver::class);
    }
}
