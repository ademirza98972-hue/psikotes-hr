<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekIzin
{
    public function handle(Request $request, Closure $next, string $kodeIzin): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $pengguna = Auth::user();

        if (! $pengguna->hasIzin($kodeIzin)) {
            return $this->tolakAkses($request, $kodeIzin);
        }

        return $next($request);
    }

    protected function tolakAkses(Request $request, string $kodeIzin): Response
    {
        $request->session()->flash('error', 'Anda tidak punya akses ke halaman ini.');

        $tujuan = \Illuminate\Support\Facades\Route::has('dashboard')
            ? redirect()->route('dashboard')
            : redirect('/');

        return $tujuan;
    }
}
