<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Yahoo\YahooExtendSocialite;

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
        app(\Illuminate\Contracts\Foundation\Application::class)
            ->make(\Illuminate\Contracts\Events\Dispatcher::class)
            ->listen(SocialiteWasCalled::class, YahooExtendSocialite::class);
    }
}
