<?php

use App\Http\Controllers\Admin\AlatTesController;
use App\Http\Controllers\Admin\BankSoalController;
use App\Http\Controllers\Admin\DataKandidatController;
use App\Http\Controllers\Admin\DataKaryawanController;
use App\Http\Controllers\Admin\DataTerhapusController;
use App\Http\Controllers\Admin\HasilTesController;
use App\Http\Controllers\Admin\PeranController;
use App\Http\Controllers\Admin\PenjadwalanTesController;
use App\Http\Controllers\Admin\PenggunaAdminController;
use App\Http\Controllers\Admin\PenggunaController;
use App\Http\Controllers\AutentikasiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengerjaanTesController;
use App\Http\Controllers\ProfilController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Shared posisi API available to all authenticated users and guests
Route::get('api/posisi/daftar/{departemen}', [\App\Http\Controllers\Admin\PosisiController::class, 'daftarByDepartemen'])->name('api.posisi.daftar');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AutentikasiController::class, 'tampilkanLogin'])->name('login');
    Route::post('/login', [AutentikasiController::class, 'login']);

    Route::get('/daftar', [AutentikasiController::class, 'tampilkanRegister'])->name('register');
    Route::post('/daftar', [AutentikasiController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/keluar', [AutentikasiController::class, 'logout'])->name('logout');

    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/admin/aktivitas', [DashboardController::class, 'aktivitas'])->name('admin.aktivitas.index');
    Route::get('/peserta/dashboard', [DashboardController::class, 'peserta'])->name('peserta.dashboard');

    // Route untuk instruksi tes (baru)
    Route::get('/peserta/tes/{sesiId}/instruksi', [DashboardController::class, 'instruksi'])->name('peserta.tes.instruksi');

    // Route untuk halaman pengerjaan tes (sekarang mengarah ke PengerjaanTesController)
    Route::get('/peserta/tes/{sesiId}', [PengerjaanTesController::class, 'kerjakan'])->name('peserta.tes.kerjakan');
    Route::post('/peserta/tes/{sesiId}/jawab', [PengerjaanTesController::class, 'jawab'])->name('peserta.tes.jawab');
    Route::get('/peserta/tes/{sesiId}/selesai', [PengerjaanTesController::class, 'selesai'])->name('peserta.tes.selesai');

    Route::prefix('admin/akun-karyawan')->name('admin.akun-karyawan.')->group(function () {
        Route::get('/', [PenggunaController::class, 'index'])->middleware('izin:pengguna.lihat')->name('index');
        Route::get('/tambah', [PenggunaController::class, 'tambah'])->middleware('izin:pengguna.tambah')->name('tambah');
        Route::post('/', [PenggunaController::class, 'simpan'])->middleware('izin:pengguna.tambah')->name('simpan');
        Route::get('/{pengguna}/ubah', [PenggunaController::class, 'ubah'])->middleware('izin:pengguna.edit')->whereNumber('pengguna')->name('ubah');
        Route::put('/{pengguna}', [PenggunaController::class, 'perbarui'])->middleware('izin:pengguna.edit')->whereNumber('pengguna')->name('perbarui');
        Route::patch('/{pengguna}', [PenggunaController::class, 'perbarui'])->middleware('izin:pengguna.edit')->whereNumber('pengguna')->name('perbarui');
        Route::delete('/{pengguna}', [PenggunaController::class, 'hapus'])->middleware('izin:pengguna.hapus')->whereNumber('pengguna')->name('hapus');
        Route::patch('/{pengguna}/toggle-status', [PenggunaController::class, 'toggleStatus'])->middleware('izin:pengguna.edit')->whereNumber('pengguna')->name('toggle-status');
    });

    Route::prefix('admin/pengguna-admin')->name('admin.pengguna-admin.')->middleware('izin:pengguna_admin.kelola')->group(function () {
        Route::get('/', [PenggunaAdminController::class, 'index'])->name('index');
        Route::get('/tambah', [PenggunaAdminController::class, 'tambah'])->name('tambah');
        Route::post('/', [PenggunaAdminController::class, 'simpan'])->name('simpan');
        Route::get('/{pengguna_admin}/ubah', [PenggunaAdminController::class, 'ubah'])->whereNumber('pengguna_admin')->name('ubah');
        Route::put('/{pengguna_admin}', [PenggunaAdminController::class, 'perbarui'])->whereNumber('pengguna_admin')->name('perbarui');
        Route::patch('/{pengguna_admin}', [PenggunaAdminController::class, 'perbarui'])->whereNumber('pengguna_admin')->name('perbarui');
        Route::delete('/{pengguna_admin}', [PenggunaAdminController::class, 'hapus'])->whereNumber('pengguna_admin')->name('hapus');
        Route::patch('/{pengguna_admin}/toggle-status', [PenggunaAdminController::class, 'toggleStatus'])->whereNumber('pengguna_admin')->name('toggle-status');
    });

    // =========================================
    // PROFIL (semua user)
    // =========================================
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');
    Route::put('/profil', [ProfilController::class, 'perbarui'])->name('profil.perbarui');
    Route::put('/profil/password', [ProfilController::class, 'ubahPassword'])->name('profil.ubah-password');
    Route::post('/profil/foto', [ProfilController::class, 'unggahFoto'])->name('profil.unggah-foto');

    Route::prefix('admin/data-kandidat')->name('admin.data-kandidat.')->group(function () {
        Route::get('/', [DataKandidatController::class, 'index'])->middleware('izin:pengguna.lihat')->name('index');
        Route::get('/tambah', [DataKandidatController::class, 'tambah'])->middleware('izin:pengguna.tambah')->name('tambah');
        Route::post('/', [DataKandidatController::class, 'simpan'])->middleware('izin:pengguna.tambah')->name('simpan');
        Route::get('/{kandidat}/ubah', [DataKandidatController::class, 'ubah'])->middleware('izin:pengguna.edit')->whereNumber('kandidat')->name('ubah');
        Route::put('/{kandidat}', [DataKandidatController::class, 'perbarui'])->middleware('izin:pengguna.edit')->whereNumber('kandidat')->name('perbarui');
        Route::patch('/{kandidat}', [DataKandidatController::class, 'perbarui'])->middleware('izin:pengguna.edit')->whereNumber('kandidat')->name('perbarui');
        Route::delete('/{kandidat}', [DataKandidatController::class, 'hapus'])->middleware('izin:pengguna.hapus')->whereNumber('kandidat')->name('hapus');
        Route::post('/{kandidat}/approve', [DataKandidatController::class, 'approve'])->middleware('izin:pengguna.verifikasi')->whereNumber('kandidat')->name('approve');
        Route::post('/{kandidat}/tolak', [DataKandidatController::class, 'tolak'])->middleware('izin:pengguna.verifikasi')->whereNumber('kandidat')->name('tolak');
        Route::patch('/{kandidat}/toggle-status', [DataKandidatController::class, 'toggleStatus'])->middleware('izin:pengguna.edit')->whereNumber('kandidat')->name('toggle-status');
    });

    // =========================================
    // DEPARTEMEN (standalone)
    // =========================================
    Route::prefix('admin/departemen')->name('admin.departemen.')->middleware('izin:master_data.kelola')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DepartemenController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\DepartemenController::class, 'store'])->name('store');
        Route::patch('/{departemen}', [\App\Http\Controllers\Admin\DepartemenController::class, 'update'])->name('update');
        Route::delete('/{departemen}', [\App\Http\Controllers\Admin\DepartemenController::class, 'destroy'])->name('destroy');
    });

    // =========================================
    // POSISI (standalone)
    // =========================================
    Route::prefix('admin/posisi')->name('admin.posisi.')->middleware('izin:master_data.kelola')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PosisiController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\PosisiController::class, 'store'])->name('store');
        Route::patch('/{posisi}', [\App\Http\Controllers\Admin\PosisiController::class, 'update'])->name('update');
        Route::delete('/{posisi}', [\App\Http\Controllers\Admin\PosisiController::class, 'destroy'])->name('destroy');
    });

    // =========================================
    // DATA KARYAWAN
    // =========================================
    Route::prefix('admin/data-karyawan')->name('admin.data-karyawan.')->group(function () {
        Route::get('/', [DataKaryawanController::class, 'index'])->middleware('izin:data_karyawan.kelola')->name('index');
        Route::get('/tambah', [DataKaryawanController::class, 'tambah'])->middleware('izin:data_karyawan.kelola')->name('tambah');
        Route::post('/', [DataKaryawanController::class, 'simpan'])->middleware('izin:data_karyawan.kelola')->name('simpan');
        Route::get('/{data_karyawan}/ubah', [DataKaryawanController::class, 'ubah'])->middleware('izin:data_karyawan.kelola')->whereNumber('data_karyawan')->name('ubah');
        Route::put('/{data_karyawan}', [DataKaryawanController::class, 'perbarui'])->middleware('izin:data_karyawan.kelola')->whereNumber('data_karyawan')->name('perbarui');
        Route::patch('/{data_karyawan}', [DataKaryawanController::class, 'perbarui'])->middleware('izin:data_karyawan.kelola')->whereNumber('data_karyawan')->name('perbarui');
        Route::delete('/{data_karyawan}', [DataKaryawanController::class, 'hapus'])->middleware('izin:data_karyawan.kelola')->whereNumber('data_karyawan')->name('hapus');
    });

    Route::prefix('admin/peran')->name('admin.peran.')->group(function () {
        Route::get('/', [PeranController::class, 'index'])->middleware('izin:peran.kelola')->name('index');
        Route::get('/tambah', [PeranController::class, 'tambah'])->middleware('izin:peran.kelola')->name('tambah');
        Route::post('/', [PeranController::class, 'simpan'])->middleware('izin:peran.kelola')->name('simpan');
        Route::get('/{peran}/ubah', [PeranController::class, 'ubah'])->middleware('izin:peran.kelola')->whereNumber('peran')->name('ubah');
        Route::put('/{peran}', [PeranController::class, 'perbarui'])->middleware('izin:peran.kelola')->whereNumber('peran')->name('perbarui');
        Route::patch('/{peran}', [PeranController::class, 'perbarui'])->middleware('izin:peran.kelola')->whereNumber('peran')->name('perbarui');
        Route::delete('/{peran}', [PeranController::class, 'hapus'])->middleware('izin:peran.kelola')->whereNumber('peran')->name('hapus');
    });

    // =========================================
    // ALAT TES (placeholder, data dummy hardcode)
    // =========================================
    Route::prefix('admin/alat-tes')->name('admin.alat-tes.')->group(function () {
        Route::get('/', [AlatTesController::class, 'index'])->name('index');
        Route::get('/tambah', [AlatTesController::class, 'tambah'])->name('tambah');
        Route::post('/', [AlatTesController::class, 'simpan'])->name('simpan');
    });

    // =========================================
    // BANK SOAL (placeholder, data dummy hardcode)
    // =========================================
    Route::prefix('admin/bank-soal')->name('admin.bank-soal.')->group(function () {
        Route::get('/', [BankSoalController::class, 'index'])->name('index');
        Route::get('/tambah/{alatTesId}', [BankSoalController::class, 'tambah'])->whereNumber('alatTesId')->name('tambah');
    });

    // =========================================
    // PENJADWALAN TES (placeholder, data dummy hardcode)
    // =========================================
    Route::prefix('admin/penjadwalan-tes')->name('admin.penjadwalan-tes.')->group(function () {
        Route::get('/', [PenjadwalanTesController::class, 'index'])->name('index');
        Route::get('/tambah', [PenjadwalanTesController::class, 'tambah'])->name('tambah');
        Route::post('/', [PenjadwalanTesController::class, 'simpan'])->name('simpan');
    });

    // =========================================
    // DATA TERHAPUS (TRASH)
    // =========================================
    Route::prefix('admin/data-terhapus')->name('admin.data-terhapus.')->middleware('izin:data_terhapus.kelola')->group(function () {
        Route::get('/', [DataTerhapusController::class, 'index'])->name('index');
        Route::post('/pulihkan/{jenis}/{id}', [DataTerhapusController::class, 'pulihkan'])->whereNumber('id')->name('pulihkan');
        Route::post('/hapus-permanen/{jenis}/{id}', [DataTerhapusController::class, 'hapusPermanen'])->whereNumber('id')->name('hapus-permanen');
    });

    // =========================================
    // HASIL TES (baru)
    // =========================================
    Route::prefix('admin/hasil-tes')->name('admin.hasil-tes.')->group(function () {
        Route::get('/', [HasilTesController::class, 'index'])->name('index');
        Route::get('/{sesiId}/{pesertaId}', [HasilTesController::class, 'detail'])->name('detail');
    });
});
