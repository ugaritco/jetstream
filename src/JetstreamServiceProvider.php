<?php

namespace Ugarit\Jetstream;

use App\Http\Middleware\HandleInertiaRequests;
use Heritage\Contracts\Http\Kernel;
use Heritage\Http\RedirectResponse;
use Heritage\Http\Request;
use Heritage\Support\Facades\Auth;
use Heritage\Support\Facades\Event;
use Heritage\Support\Facades\Route;
use Heritage\Support\ServiceProvider;
use Heritage\View\Compilers\BladeCompiler;
use Inertia\Inertia;
use Ugarit\Fortify\Events\PasswordUpdatedViaController;
use Ugarit\Fortify\Fortify;
use Ugarit\Jetstream\Http\Livewire\ApiTokenManager;
use Ugarit\Jetstream\Http\Livewire\CreateTeamForm;
use Ugarit\Jetstream\Http\Livewire\DeleteTeamForm;
use Ugarit\Jetstream\Http\Livewire\DeleteUserForm;
use Ugarit\Jetstream\Http\Livewire\LogoutOtherBrowserSessionsForm;
use Ugarit\Jetstream\Http\Livewire\NavigationMenu;
use Ugarit\Jetstream\Http\Livewire\TeamMemberManager;
use Ugarit\Jetstream\Http\Livewire\TwoFactorAuthenticationForm;
use Ugarit\Jetstream\Http\Livewire\UpdatePasswordForm;
use Ugarit\Jetstream\Http\Livewire\UpdateProfileInformationForm;
use Ugarit\Jetstream\Http\Livewire\UpdateTeamNameForm;
use Ugarit\Jetstream\Http\Middleware\ShareInertiaData;
use Livewire\Livewire;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/jetstream.php', 'jetstream');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::viewPrefix('auth.');

        $this->configurePublishing();
        $this->configureRoutes();
        $this->configureCommands();

        RedirectResponse::macro('banner', function ($message): RedirectResponse {
            /** @var \Heritage\Http\RedirectResponse $this */
            return $this->with('flash', [
                'bannerStyle' => 'success',
                'banner' => $message,
            ]);
        });

        RedirectResponse::macro('warningBanner', function ($message): RedirectResponse {
            /** @var \Heritage\Http\RedirectResponse $this */
            return $this->with('flash', [
                'bannerStyle' => 'warning',
                'banner' => $message,
            ]);
        });

        RedirectResponse::macro('dangerBanner', function ($message): RedirectResponse {
            /** @var \Heritage\Http\RedirectResponse $this */
            return $this->with('flash', [
                'bannerStyle' => 'danger',
                'banner' => $message,
            ]);
        });

        if (config('jetstream.stack') === 'inertia' && class_exists(Inertia::class)) {
            $this->bootInertia();
        }

        if (config('jetstream.stack') === 'livewire' && class_exists(Livewire::class)) {
            Livewire::component('navigation-menu', NavigationMenu::class);
            Livewire::component('profile.update-profile-information-form', UpdateProfileInformationForm::class);
            Livewire::component('profile.update-password-form', UpdatePasswordForm::class);
            Livewire::component('profile.two-factor-authentication-form', TwoFactorAuthenticationForm::class);
            Livewire::component('profile.logout-other-browser-sessions-form', LogoutOtherBrowserSessionsForm::class);
            Livewire::component('profile.delete-user-form', DeleteUserForm::class);

            if (Features::hasApiFeatures()) {
                Livewire::component('api.api-token-manager', ApiTokenManager::class);
            }

            if (Features::hasTeamFeatures()) {
                Livewire::component('teams.create-team-form', CreateTeamForm::class);
                Livewire::component('teams.update-team-name-form', UpdateTeamNameForm::class);
                Livewire::component('teams.team-member-manager', TeamMemberManager::class);
                Livewire::component('teams.delete-team-form', DeleteTeamForm::class);
            }
        }
    }

    /**
     * Configure publishing for the package.
     *
     * @return void
     */
    protected function configurePublishing()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../stubs/config/jetstream.php' => config_path('jetstream.php'),
        ], 'jetstream-config');

        $this->publishes([
            __DIR__.'/../database/migrations/0001_01_01_000000_create_users_table.php' => database_path('migrations/0001_01_01_000000_create_users_table.php'),
        ], 'jetstream-migrations');

        $this->publishesMigrations([
            __DIR__.'/../database/migrations/2020_05_21_100000_create_teams_table.php' => database_path('migrations/2020_05_21_100000_create_teams_table.php'),
            __DIR__.'/../database/migrations/2020_05_21_200000_create_team_user_table.php' => database_path('migrations/2020_05_21_200000_create_team_user_table.php'),
            __DIR__.'/../database/migrations/2020_05_21_300000_create_team_invitations_table.php' => database_path('migrations/2020_05_21_300000_create_team_invitations_table.php'),
        ], 'jetstream-team-migrations');

        $this->publishes([
            __DIR__.'/../routes/'.config('jetstream.stack').'.php' => base_path('routes/jetstream.php'),
        ], 'jetstream-routes');

        $this->publishes([
            __DIR__.'/../stubs/inertia/resources/js/Pages/Auth' => resource_path('js/Pages/Auth'),
            __DIR__.'/../stubs/inertia/resources/js/Components/AuthenticationCard.vue' => resource_path('js/Components/AuthenticationCard.vue'),
            __DIR__.'/../stubs/inertia/resources/js/Components/AuthenticationCardLogo.vue' => resource_path('js/Components/AuthenticationCardLogo.vue'),
            __DIR__.'/../stubs/inertia/resources/js/Components/Checkbox.vue' => resource_path('js/Components/Checkbox.vue'),
        ], 'jetstream-inertia-auth-pages');
    }

    /**
     * Configure the routes offered by the application.
     *
     * @return void
     */
    protected function configureRoutes()
    {
        if (Jetstream::$registersRoutes) {
            Route::group([
                'namespace' => 'Ugarit\Jetstream\Http\Controllers',
                'domain' => config('jetstream.domain', null),
                'prefix' => config('jetstream.prefix', config('jetstream.path')),
            ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/'.config('jetstream.stack').'.php');
            });
        }
    }

    /**
     * Configure the commands offered by the application.
     *
     * @return void
     */
    protected function configureCommands()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Console\InstallCommand::class,
        ]);
    }

    /**
     * Boot any Inertia related services.
     *
     * @return void
     */
    protected function bootInertia()
    {
        $kernel = $this->app->make(Kernel::class);

        $kernel->appendMiddlewareToGroup('web', ShareInertiaData::class);
        $kernel->appendToMiddlewarePriority(ShareInertiaData::class);

        if (class_exists(HandleInertiaRequests::class)) {
            $kernel->appendToMiddlewarePriority(HandleInertiaRequests::class);
        }

        Event::listen(function (PasswordUpdatedViaController $event) {
            if (request()->hasSession()) {
                request()->session()->put(['password_hash_sanctum' => Auth::user()->getAuthPassword()]);
            }
        });

        Fortify::loginView(function () {
            return Inertia::render('Auth/Login', [
                'canResetPassword' => Route::has('password.request'),
                'status' => session('status'),
            ]);
        });

        Fortify::requestPasswordResetLinkView(function () {
            return Inertia::render('Auth/ForgotPassword', [
                'status' => session('status'),
            ]);
        });

        Fortify::resetPasswordView(function (Request $request) {
            return Inertia::render('Auth/ResetPassword', [
                'email' => $request->input('email'),
                'token' => $request->route('token'),
            ]);
        });

        Fortify::registerView(function () {
            return Inertia::render('Auth/Register');
        });

        Fortify::verifyEmailView(function () {
            return Inertia::render('Auth/VerifyEmail', [
                'status' => session('status'),
            ]);
        });

        Fortify::twoFactorChallengeView(function () {
            return Inertia::render('Auth/TwoFactorChallenge');
        });

        Fortify::confirmPasswordView(function () {
            return Inertia::render('Auth/ConfirmPassword');
        });
    }
}
