<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        view()->composer(
            ['frontend.b2b.*', 'frontend.cart.*', 'frontend.checkout.*'],
            \App\Http\View\Composers\B2BNavigationComposer::class
        );

        view()->composer(
            'frontend.b2c.*',
            \App\Http\View\Composers\B2CNavigationComposer::class
        );
    }
}
