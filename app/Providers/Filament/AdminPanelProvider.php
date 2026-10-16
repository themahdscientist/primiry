<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Dashboard;
use App\Filament\Pages\Auth\EmailVerificationPrompt;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\Tenancy\EditSchoolProfile;
use App\Filament\Pages\Tenancy\RegisterSchool;
use App\Models\School;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->tenant(School::class, 'code')
            ->tenantRegistration(RegisterSchool::class)
            ->tenantProfile(EditSchoolProfile::class)
            ->tenantRoutePrefix('school')
            // ->tenantDomain('{tenant:code}.primiry.test')
            ->login()
            ->registration(Register::class)
            ->passwordReset()
            // ->emailVerification(EmailVerificationPrompt::class)
            // ->emailChangeVerification()
            ->profile()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->pages([
                Dashboard::class,
            ])
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->spa(hasPrefetching: true)
            ->unsavedChangesAlerts()
            ->databaseTransactions()
            ->sidebarCollapsibleOnDesktop()
            ->resourceCreatePageRedirect('index')
            ->resourceEditPageRedirect('index');
        // ->spaUrlExceptions(fn(): array => [])
        // ->strictAuthorization()
        // ->viteTheme('')
        // ->favicon(asset('favicon.svg'))
        // ->brandLogo(asset('images/logo-dark.svg'))
        // ->darkModeBrandLogo(asset('images/logo-light.svg'))
    }
}
