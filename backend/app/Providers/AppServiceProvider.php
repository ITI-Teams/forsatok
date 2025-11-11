<?php

namespace App\Providers;

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
        //
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
    }
}
