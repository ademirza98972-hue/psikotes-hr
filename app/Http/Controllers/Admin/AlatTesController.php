<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlatTesController extends Controller
{
    /**
     * Data dummy hardcode — akan diganti query ke tabel `alat_tes`
     * setelah backend modul Tes dibangun.
     */
    protected const DUMMY_ALAT_TES = [
        [
            'id' => 1,
            'nama' => 'DISC',
            'format_dasar' => 'Skala Likert',
            'durasi_total_menit' => 30,
            'batas_waktu_per_soal_aktif' => false,
            'batas_waktu_per_soal_detik' => null,
            'is_sensitif' => false,
            'jumlah_soal' => 120,
        ],
        [
            'id' => 2,
            'nama' => 'IST (Intelligence Structure Test)',
            'format_dasar' => 'Pilihan Ganda',
            'durasi_total_menit' => 90,
            'batas_waktu_per_soal_aktif' => true,
            'batas_waktu_per_soal_detik' => 60,
            'is_sensitif' => false,
            'jumlah_soal' => 200,
        ],
        [
            'id' => 3,
            'nama' => 'EPPS (Edwards Personal Preference Schedule)',
            'format_dasar' => 'Forced Choice',
            'durasi_total_menit' => 45,
            'batas_waktu_per_soal_aktif' => false,
            'batas_waktu_per_soal_detik' => null,
            'is_sensitif' => false,
            'jumlah_soal' => 225,
        ],
        [
            'id' => 4,
            'nama' => 'MMPI-2',
            'format_dasar' => 'Skala Likert',
            'durasi_total_menit' => 120,
            'batas_waktu_per_soal_aktif' => false,
            'batas_waktu_per_soal_detik' => null,
            'is_sensitif' => true,
            'jumlah_soal' => 567,
        ],
    ];

    public function index(): View
    {
        return view('admin.alat-tes.index', [
            'alatTes' => self::DUMMY_ALAT_TES,
        ]);
    }

    public function tambah(): View
    {
        return view('admin.alat-tes.tambah', [
            'pilihanFormat' => ['Pilihan Ganda', 'Skala Likert', 'Forced Choice'],
        ]);
    }

    public function simpan(Request $request)
    {
        return redirect()
            ->route('admin.alat-tes.index')
            ->with('sukses', 'Fitur simpan akan aktif setelah backend selesai dibangun.');
    }
}