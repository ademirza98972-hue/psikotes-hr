<?php

use App\Http\Middleware\CekIzin;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Authenticate::redirectUsing(function (Request $request) {
    return str_starts_with($request->path(), 'admin') ? route('login.admin') : route('login');
});

RedirectIfAuthenticated::redirectUsing(function (Request $request) {
    $pengguna = Auth::user();

    if ($pengguna && in_array($pengguna->tipe_akun, ['kandidat', 'karyawan'], true)) {
        return route('peserta.dashboard');
    }

    return route('admin.dashboard');
});

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'izin' => CekIzin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
