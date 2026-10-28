<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectUsersTo(fn (Request $request) => Auth::guard('customer')->check()
            ? route('account.index')
            : '/admin/products'
        );

        $middleware->redirectGuestsTo(
            fn (Request $request) => $request->is(
                'account',
                'account/*',
                'user/checkout',
                'user/checkout/*',
                'user/orders',
                'user/orders/*'
            )
                ? route('account.login')
                : route('login')
        );

        // $middleware->redirectUsersTo('/admin/products');
        // $middleware->redirectGuestsTo(fn (Request $request) => $request->is('account', 'checkout*')
        //   ? route('account.login')
        //   : route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
