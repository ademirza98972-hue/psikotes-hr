<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekIzin
{
    /** `izin:a,b` = cukup punya salah satu izin (sama dengan aturan menu sidebar). */
    public function handle(Request $request, Closure $next, string ...$kodeIzin): Response
    {
        if (! Auth::check()) {
            $routeLogin = str_starts_with($request->path(), 'admin') ? 'login.admin' : 'login';
            return redirect()->route($routeLogin);
        }

        $pengguna = Auth::user();

        if (! collect($kodeIzin)->contains(fn ($kode) => $pengguna->hasIzin($kode))) {
            return $this->tolakAkses($request, implode(',', $kodeIzin));
        }

        return $next($request);
    }

    protected function tolakAkses(Request $request, string $kodeIzin): Response
    {
        abort(403, 'Anda tidak punya akses ke halaman ini.');
    }
}
