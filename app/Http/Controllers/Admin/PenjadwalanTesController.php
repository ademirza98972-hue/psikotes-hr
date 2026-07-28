<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PenjadwalanTesController extends Controller
{
    protected const DUMMY_ALAT_TES = [
        ['id' => 1, 'nama' => 'DISC',  'format_dasar' => 'Skala Likert'],
        ['id' => 2, 'nama' => 'IST',   'format_dasar' => 'Pilihan Ganda'],
        ['id' => 3, 'nama' => 'EPPS',  'format_dasar' => 'Forced Choice'],
        ['id' => 4, 'nama' => 'MMPI-2','format_dasar' => 'Skala Likert'],
    ];

    protected const DUMMY_DEPARTEMEN = ['Finance', 'Produksi', 'HR', 'IT', 'Marketing', 'Operasional'];

    // Data dummy sesi penjadwalan (sesi lama)
    protected const DUMMY_PENJADWALAN = [
        [
            'id' => 1,
            'nama_sesi' => 'Rekrutmen Staff Finance Batch 1',
            'departemen_terkait' => 'Finance',
            'tanggal_mulai' => '2026-08-01',
            'tanggal_selesai' => '2026-08-07',
            'status' => 'Aktif',
            'daftar_alat_tes' => ['IST', 'DISC'],
            'jumlah_peserta' => 15,
            'jumlah_selesai' => 6,
        ],
        [
            'id' => 2,
            'nama_sesi' => 'Assessment Tahunan Karyawan Divisi Produksi',
            'departemen_terkait' => 'Produksi',
            'tanggal_mulai' => '2026-07-15',
            'tanggal_selesai' => '2026-07-30',
            'status' => 'Selesai',
            'daftar_alat_tes' => ['DISC', 'EPPS', 'MMPI-2'],
            'jumlah_peserta' => 42,
            'jumlah_selesai' => 42,
        ],
        [
            'id' => 3,
            'nama_sesi' => 'Rekrutmen Staff HR',
            'departemen_terkait' => 'HR',
            'tanggal_mulai' => '2026-09-10',
            'tanggal_selesai' => '2026-09-12',
            'status' => 'Draft',
            'daftar_alat_tes' => ['EPPS'],
            'jumlah_peserta' => 8,
            'jumlah_selesai' => 0,
        ],
    ];

    // Karyawan dengan properti departemen dan posisi
    protected const DUMMY_KARYAWAN = [
        ['name' => 'Andi Pratama',   'departemen' => 'Finance', 'posisi' => 'Staff Akuntansi'],
        ['name' => 'Siti Aminah',    'departemen' => 'HR',      'posisi' => 'HR Specialist'],
        ['name' => 'Budi Santoso',   'departemen' => 'Produksi','posisi' => 'Supervisor Produksi'],
        ['name' => 'Dewi Lestari',   'departemen' => 'IT',      'posisi' => 'Analysist IT'],
        ['name' => 'Eko Wibowo',     'departemen' => 'Marketing','posisi' => 'Staff Marketing'],
        ['name' => 'Fitri Handayani','departemen' => 'Operasional','posisi' => 'Admin Operasional'],
        ['name' => 'Galih Mahendra', 'departemen' => 'Finance', 'posisi' => 'Staff Keuangan'],
        ['name' => 'Hesti Rahmawati','departemen' => 'HR',      'posisi' => 'Rekruter'],
    ];

    // Kandidat dengan properti departemen dan posisi
    protected const DUMMY_KANDIDAT = [
        ['name' => 'Rio Saputra',       'departemen' => 'Produksi', 'posisi' => 'Calon Supervisor'],
        ['name' => 'Nurul Aini',        'departemen' => 'HR',       'posisi' => 'Calon HR Specialist'],
        ['name' => 'Bagas Maulana',     'departemen' => 'IT',       'posisi' => 'Calon IT Analyst'],
        ['name' => 'Citra Kirana',      'departemen' => 'Marketing','posisi' => 'Calon Staff Marketing'],
        ['name' => 'Dimas Setiawan',    'departemen' => 'Finance',  'posisi' => 'Calon Akuntan'],
        ['name' => 'Erna Wulandari',    'departemen' => 'Produksi', 'posisi' => 'Calon Operator'],
        ['name' => 'Fajar Nugroho',     'departemen' => 'HR',       'posisi' => 'Calon Rekruter'],
        ['name' => 'Gita Permata',      'departemen' => 'IT',       'posisi' => 'Calon Analis'],
        ['name' => 'Hadi Wijaya',       'departemen' => 'Marketing','posisi' => 'Calon Promoter'],
        ['name' => 'Indah Cahyani',     'departemen' => 'Finance',  'posisi' => 'Calon Staff'],
    ];

    public function index(): View
    {
        return view('admin.penjadwalan-tes.index', [
            'penjadwalan' => self::DUMMY_PENJADWALAN,
        ]);
    }

    public function tambah(): View
    {
        return view('admin.penjadwalan-tes.tambah', [
            'daftarAlatTes' => self::DUMMY_ALAT_TES,
            'daftarDepartemen' => self::DUMMY_DEPARTEMEN,
            'daftarKaryawan' => self::DUMMY_KARYAWAN,
            'daftarKandidat' => self::DUMMY_KANDIDAT,
        ]);
    }
}