<?php

use App\Http\Controllers\AutentikasiController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AutentikasiController::class, 'tampilkanLogin'])->name('login');
    Route::post('/login', [AutentikasiController::class, 'login']);

    Route::get('/daftar', [AutentikasiController::class, 'tampilkanRegister'])->name('register');
    Route::post('/daftar', [AutentikasiController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/keluar', [AutentikasiController::class, 'logout'])->name('logout');

    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/peserta/dashboard', [DashboardController::class, 'peserta'])->name('peserta.dashboard');
});