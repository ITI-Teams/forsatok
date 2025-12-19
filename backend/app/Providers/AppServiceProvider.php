<?php

namespace App\Providers;

use App\Domains\Shared\Services\Audit\AuditLogger;
use App\Domains\Shared\Services\FrontendDetection\FrontendUrlService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
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
            $source = $urlService->getSource();

            return match ($source) {
                'react_dashboard' => $urlService->makeUrl('auth/employer/reset-password', [
                    'token' => $token,
                    'email' => $user->email,
                ]),
                'hive' => $urlService->makeUrl('auth/reset-password', [
                    'token' => $token,
                    'email' => $user->email,
                ]),
                'jobhub' => $urlService->makeUrl('reset-password', [
                    'token' => $token,
                    'email' => $user->email,
                ]),
                default => rtrim(config('app.url'), '/') . route('password.reset', [
                    'token' => $token,
                    'email' => $user->email,
                ], false),
            };
        });

        VerifyEmail::createUrlUsing(function ($notifiable) {
            $urlService = app(FrontendUrlService::class);
            $source = $urlService->getSource();

            $routeName = match ($source) {
                'react_dashboard', 'hive' => 'dashboard.auth.verification.verify.api',
                'jobhub' => 'api.verification.verify',
                default => 'verification.verify',
            };

            // Generate standard signed verification URL
            $verificationUrl = URL::temporarySignedRoute(
                $routeName,
                Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );

            // For external frontends, we direct them to a frontend page which will then call the API
            return match ($source) {
                'react_dashboard' => $urlService->makeUrl('auth/employer/verify-email', [
                    'url' => $verificationUrl,
                ]),
                'hive' => $urlService->makeUrl('auth/verify-email', [
                    'url' => $verificationUrl
                ]),
                'jobhub' => $urlService->makeUrl('verify-email', [
                    'url' => $verificationUrl
                ]),
                default => $verificationUrl,
            };
        });

        $this->configureRateLimiting();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Rate limiter for authentication attempts (Login, Registration, Password Reset)
        \Illuminate\Support\Facades\RateLimiter::for('auth', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip());
        });

        // Rate limiter for general API interaction
        \Illuminate\Support\Facades\RateLimiter::for('api', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limiter for searching and filtering (more resource intensive)
        \Illuminate\Support\Facades\RateLimiter::for('searching', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(30)->by($request->ip());
        });

        // Rate limiter for contact form submissions
        \Illuminate\Support\Facades\RateLimiter::for('contact', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(3)->by($request->ip());
        });
    }
}
