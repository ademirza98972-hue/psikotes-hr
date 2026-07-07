<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function admin(Request $request): View
    {
        return view('admin.dashboard', [
            'pengguna' => $request->user(),
        ]);
    }

    public function peserta(Request $request): View
    {
        return view('peserta.dashboard', [
            'pengguna' => $request->user(),
        ]);
    }
}