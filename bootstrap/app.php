<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.guest' => \App\Http\Middleware\RedirectIfAuthenticatedAdmin::class,
            'manufacturing-team.guest' => \App\Http\Middleware\RedirectIfAuthenticatedManufacturingTeam::class,
            'sales-team.guest' => \App\Http\Middleware\RedirectIfAuthenticatedSalesTeam::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
