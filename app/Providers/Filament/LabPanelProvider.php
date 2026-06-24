<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Lab\Pages\Auth\RegisterLab;
use App\Filament\Lab\Pages\Dashboard;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use App\Http\Middleware\SetLocale;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class LabPanelProvider extends PanelProvider
{
    use ConfiguresDentaLinkPanel;
    use LoadsDentaLinkAssets;

    public function panel(Panel $panel): Panel
    {
        return $this->configureDentaLinkPanel(
            $panel
                ->id('lab')
                ->path('lab')
                ->login()
                ->registration(RegisterLab::class)
                ->brandName(__('dentalink.brand.lab'))
                ->brandLogo(fn () => view('filament.brand-logo-lab'))
        )
            ->discoverResources(in: app_path('Filament/Lab/Resources'), for: 'App\\Filament\\Lab\\Resources')
            ->discoverPages(in: app_path('Filament/Lab/Pages'), for: 'App\\Filament\\Lab\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Lab/Widgets'), for: 'App\\Filament\\Lab\\Widgets')
            ->navigationGroups([
                NavigationGroup::make(__('dentalink.nav.groups.overview')),
                NavigationGroup::make(__('dentalink.nav.groups.orders')),
                NavigationGroup::make(__('dentalink.nav.groups.communication')),
                NavigationGroup::make(__('dentalink.nav.groups.settings')),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
