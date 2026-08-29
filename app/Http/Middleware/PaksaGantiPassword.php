<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaksaGantiPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            $user &&
            $user->tipe_akun === 'karyawan' &&
            $user->must_change_password &&
            ! $request->routeIs('profil.ubah-password', 'profil.index', 'logout')
        ) {
            return redirect()->route('profil.index')
                ->with('info', 'Silakan ganti password Anda terlebih dahulu sebelum melanjutkan.');
        }

        return $next($request);
    }
}
