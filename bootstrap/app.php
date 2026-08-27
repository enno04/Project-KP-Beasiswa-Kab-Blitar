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
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'prevent-back-history' => \App\Http\Middleware\PreventBackHistory::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\PreventBackHistory::class,
        ]);

        // TODO: PENTING! Hapus pengecualian CSRF ini sebelum project di-deploy ke server resmi (Production)
        // Ini hanya untuk mencegah error 419 saat testing menggunakan IP lokal di HP
        $middleware->validateCsrfTokens(except: [
            'pendaftaran',
            'pendaftaran/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
