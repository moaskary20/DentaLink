<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\App\Pages\Auth\RegisterDoctor;
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

class AppPanelProvider extends PanelProvider
{
    use ConfiguresDentaLinkPanel;
    use LoadsDentaLinkAssets;

    public function panel(Panel $panel): Panel
    {
        return $this->configureDentaLinkPanel(
            $panel
                ->id('app')
                ->path('doctor')
                ->login()
                ->registration(RegisterDoctor::class)
                ->brandName(__('dentalink.brand.name'))
                ->brandLogo(fn () => view('filament.brand-logo-doctor'))
        )
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->navigationGroups([
                NavigationGroup::make(__('dentalink.nav.groups.main')),
                NavigationGroup::make(__('dentalink.nav.groups.order_management')),
                NavigationGroup::make(__('dentalink.nav.groups.laboratories')),
                NavigationGroup::make(__('dentalink.nav.groups.finance')),
                NavigationGroup::make(__('dentalink.nav.groups.communication')),
                NavigationGroup::make(__('dentalink.nav.groups.ai')),
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
