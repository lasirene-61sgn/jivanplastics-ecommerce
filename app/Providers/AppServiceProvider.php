<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Order;

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

        // Share new (pending) orders count to admin sidebar nav
        View::composer('partials.admin-nav-links', function ($view) {
            $newOrdersCount = Order::where('status', 'pending')->count();
            $view->with('newOrdersCount', $newOrdersCount);
        });
    }
}
