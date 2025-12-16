<?php

namespace App\Providers;

use App\Domains\Shared\Services\Audit\AuditLogger;
use App\Domains\Shared\Services\FrontendDetection\FrontendUrlService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;
use App\Domains\Users\Provider\LinkedInOpenIDProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AuditLogger::class, function ($app) {
            return new AuditLogger($app->make(\Illuminate\Http\Request::class));
        });

        $this->app->singleton(FrontendUrlService::class, function ($app) {
            return new FrontendUrlService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $socialite = $this->app->make(Factory::class);

        $socialite->extend('linkedin', function ($app) use ($socialite) {
            $config = $app['config']['services.linkedin'];
            return $socialite->buildProvider(LinkedInOpenIDProvider::class, $config);
        });

        ResetPassword::createUrlUsing(function ($user, $token) {
            $urlService = app(FrontendUrlService::class);

            return $urlService->makeUrl('reset-password', [
                'token' => $token,
                'email' => $user->email,
            ]);
        });

    }
}
