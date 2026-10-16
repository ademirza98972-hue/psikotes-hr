@php
    $u = auth()->user();

    // [rute, polaAktif, ikon, label, izin (salah satu cukup)]
    $semuaMenu = [
        'Menu Utama' => [
            ['admin.dashboard', 'admin.dashboard', 'dashboard', 'Dashboard', ['dashboard.lihat']],
            ['admin.akun-karyawan.index', 'admin.akun-karyawan.*', 'badge', 'Akun Karyawan', ['pengguna.lihat']],
            ['admin.data-kandidat.index', 'admin.data-kandidat.*', 'person_search', 'Akun Kandidat', ['pengguna.lihat']],
        ],
        'Tes & Assessment' => [
            ['admin.alat-tes.index', 'admin.alat-tes.*', 'quiz', 'Alat Tes', ['soal.lihat', 'kategori_tes.kelola']],
            ['admin.bank-soal.index', 'admin.bank-soal.*', 'database', 'Bank Soal', ['soal.lihat', 'kategori_tes.kelola']],
            ['admin.penjadwalan-tes.index', 'admin.penjadwalan-tes.*', 'calendar_month', 'Penjadwalan Tes', ['soal.lihat', 'kategori_tes.kelola']],
            ['admin.hasil-tes.index', 'admin.hasil-tes.*', 'analytics', 'Hasil Tes', ['hasil_tes.lihat']],
        ],
        'Sistem' => [
            ['admin.peran.index', 'admin.peran.*', 'admin_panel_settings', 'Peran & Izin', ['peran.kelola']],
            ['admin.pengguna-admin.index', 'admin.pengguna-admin.*', 'manage_accounts', 'Admin & Staff', ['pengguna_admin.kelola']],
            ['admin.departemen.index', 'admin.departemen.*', 'apartment', 'Departemen', ['departemen.kelola']],
            ['admin.posisi.index', 'admin.posisi.*', 'work', 'Posisi', ['posisi.kelola']],
            ['admin.data-terhapus.index', 'admin.data-terhapus.*', 'delete', 'Data Terhapus', ['data_terhapus.kelola']],
        ],
    ];

    $jumlahTrash = 0;
    if ($u?->hasIzin('data_terhapus.kelola')) {
        $jumlahTrash = \App\Models\User::whereIn('tipe_akun', ['karyawan', 'kandidat'])->onlyTrashed()->count()
            + \App\Models\User::where('tipe_akun', 'custom')->onlyTrashed()
                ->whereHas('peran', fn ($q) => $q->whereNotIn('nama_peran', ['Kandidat', 'Karyawan']))->count()
            + \App\Models\DataKaryawan::onlyTrashed()->count()
            + \App\Models\Departemen::onlyTrashed()->count()
            + \App\Models\Posisi::onlyTrashed()->count()
            + \App\Models\Peran::onlyTrashed()->count()
            + \App\Models\AlatTes::onlyTrashed()->count();
    }

    $menu = collect($semuaMenu)->map(fn ($items) => collect($items)
        ->filter(fn ($i) => collect($i[4])->contains(fn ($izin) => $u?->hasIzin($izin)))
        ->map(fn ($i) => [$i[0], $i[1], $i[2], $i[3], $i[0] === 'admin.data-terhapus.index' ? $jumlahTrash : 0])
        ->values()->all()
    )->all();
@endphp

@extends('layouts.panel', ['menu' => $menu, 'beranda' => 'admin.dashboard', 'subjudul' => 'Panel Admin'])
