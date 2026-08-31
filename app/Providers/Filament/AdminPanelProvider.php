<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use App\Models\Setting;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * File: app/Providers/Filament/AdminPanelProvider.php
 * Purpose: Configures the Filament 5 Admin Panel (core admin UI).
 *          Defines panel ID, path, auth, colors, middleware, plugins (Shield for RBAC).
 *          Auto-discovers Resources, Pages, Widgets under app/Filament.
 *          Accessed at: /admin (login required)
 */
class AdminPanelProvider extends PanelProvider
{
    /**
     * Define the admin panel configuration.
     * This method is called by Filament to build the panel.
     * @param Panel $panel The panel instance to configure
     * @return Panel Configured panel
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->profile(\App\Filament\Pages\Auth\EditProfile::class, isSimple: false)
            ->authGuard('web')
            ->brandName(fn () => Setting::get('company.name', config('app.name', 'Tour Admin')))
            ->brandLogo(fn () => Setting::get('company.logo') ? asset('storage/' . Setting::get('company.logo')) : null)
            ->favicon(fn () => Setting::get('company.favicon') ? asset('storage/' . Setting::get('company.favicon')) : asset('favicon.ico'))
            ->colors([
                'primary' => Color::Amber,
            ])
            ->maxContentWidth('full')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
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
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            // Ensure profile is accessible to any authenticated user (user icon -> My Profile)
            ->userMenuItems([
                'profile' => \Filament\Navigation\MenuItem::make()
                    ->label('My Profile')
                    ->icon('heroicon-o-user-circle')
                    ->url(fn (): string => \App\Filament\Pages\Auth\EditProfile::getUrl()),
            ]);
    }
}
