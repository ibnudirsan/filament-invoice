<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Filament\Auth\Login;
use Filament\Enums\ThemeMode;
use App\Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use SolutionForest\FilamentAccessManagement\FilamentAccessManagementPanel;

class CmsPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('cms')
            ->path('cms')
            ->login()
            ->colors([
                'primary' => Color::hex('#0D3CFA'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                EasyFooterPlugin::make(),
                FilamentEditProfilePlugin::make()
                    ->slug('profile')
                    ->setTitle('Profile')
                    ->setNavigationLabel('Profile')
                    ->setNavigationGroup('Account')
                    ->setIcon('heroicon-o-user')
                    ->setSort(10)
                    ->shouldRegisterNavigation(true)
                    ->shouldShowDeleteAccountForm(true)
                    ->shouldShowSanctumTokens()
                    ->shouldShowBrowserSessionsForm()
                    ->shouldShowAvatarForm(
                        value: false,
                        directory: 'avatars',
                        rules: 'mimes:jpeg,png|max:1024'
                    ),
                FilamentAccessManagementPanel::make()
            ])
            ->sidebarWidth('14rem')
            ->sidebarCollapsibleOnDesktop()
            ->path('admin')
            ->login(Login::class)
            ->brandLogo(fn() => view('brand'))
            ->darkMode()
            ->defaultThemeMode(ThemeMode::Dark)
            ->favicon(asset('favicon.ico'))
            ->databaseNotifications()
            ->databaseNotificationsPolling(null);
    }
}
