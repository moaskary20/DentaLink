<?php

namespace App\Providers\Filament;

use App\Http\Middleware\SetLocale;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Admin\Pages\Dashboard;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    use ConfiguresDentaLinkPanel;
    use LoadsDentaLinkAssets;

    public function panel(Panel $panel): Panel
    {
        return $this->configureDentaLinkPanel(
            $panel
                ->id('admin')
                ->path('admin')
                ->login()
                ->brandName(__('dentalink.brand.admin'))
                ->brandLogo(fn () => view('filament.brand-logo-admin'))
        )
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([])
            ->navigationGroups([
                NavigationGroup::make(__('dentalink.nav.groups.overview')),
                NavigationGroup::make(__('dentalink.nav.groups.management')),
                NavigationGroup::make(__('dentalink.nav.groups.orders')),
                NavigationGroup::make(__('dentalink.nav.groups.operations')),
                NavigationGroup::make(__('dentalink.nav.groups.finance')),
                NavigationGroup::make(__('dentalink.nav.groups.communication')),
                NavigationGroup::make(__('dentalink.nav.groups.approvals')),
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
