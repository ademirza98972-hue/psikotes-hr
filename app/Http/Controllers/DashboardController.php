<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DataKaryawan;
use App\Models\Peran;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Data peserta login sebenarnya (auth()->user()) atau fallback ke dummy
     * RETURN OBJEK agar kompatibel dengan view yang menggunakan $pengguna->property
     */
    private function getPesertaLoginSaatIni(): object
    {
        $userAsli = auth()->user(); // Ambil user asli yang sedang login

        if ($userAsli) {
            // Gunakan data asli jika tersedia
            return (object) [
                'id' => $userAsli->id,
                'name' => $userAsli->name,
                'email' => $userAsli->email,
                'tipe_akun' => $userAsli->tipe_akun ?? 'peserta',
                'foto_profil' => $userAsli->foto_profil,
                'status' => $userAsli->status ?? 'aktif',
            ];
        }

        // Fallback data dummy jika belum ada auth sungguhan
        return (object) [
            'id' => 1,
            'name' => 'Andi Pratama',
            'email' => 'andi.pratama@company.com',
            'tipe_akun' => 'peserta',
            'foto_profil' => null,
            'status' => 'aktif',
        ];
    }

    /**
     * Data dummy sesi tes yang ditugaskan kepada peserta simulasi
     * Minimal 3 sesi dengan status berbeda (Belum Mengerjakan, Sedang Berjalan, Selesai)
     * Assignment bersifat per-individu, bukan paket bersama
     */
    private function getSesiTesDummy(): array
    {
        return [
            // Sesi Belum Mengerjakan - jadwal depan
            [
                'id' => 1,
                'nama_sesi' => 'Tes Rekrutmen Q3 2025',
                'departemen_terkait' => 'Marketing',
                'tanggal_mulai' => '2025-08-15',
                'tanggal_selesai' => '2025-08-25',
                'daftar_alat_tes_ditugaskan' => ['DISC'],
                'status_pengerjaan' => 'Belum Mengerjakan',
            ],

            // Sesi Sedang Berjalan - jadwal tengah
            [
                'id' => 2,
                'nama_sesi' => 'Evaluasi Kompetensi Internal',
                'departemen_terkait' => 'IT Development',
                'tanggal_mulai' => '2025-08-01',
                'tanggal_selesai' => '2025-08-10',
                'daftar_alat_tes_ditugaskan' => ['IST', 'DISC', 'EPPS'],
                'status_pengerjaan' => 'Sedang Berjalan',
            ],

            // Sesi Selesai - jadwal selesai
            [
                'id' => 3,
                'nama_sesi' => 'Assessment Awal Karyawan',
                'departemen_terkait' => 'HR Administration',
                'tanggal_mulai' => '2025-07-01',
                'tanggal_selesai' => '2025-07-05',
                'daftar_alat_tes_ditugaskan' => ['MMPI-2', 'IST'],
                'status_pengerjaan' => 'Selesai',
            ],
        ];
    }

    public function admin(Request $request): View
    {
        $totalKaryawan = User::where('tipe_akun', 'karyawan')->count();
        $totalKandidat = User::where('tipe_akun', 'kandidat')->count();
        $kandidatMenunggu = User::where('tipe_akun', 'kandidat')->where('status', 'menunggu_verifikasi')->count();
        $kandidatAktif = User::where('tipe_akun', 'kandidat')->where('status', 'aktif')->count();
        $kandidatDitolak = User::where('tipe_akun', 'kandidat')->where('status', 'ditolak')->count();
        $totalAdminStaff = User::where('tipe_akun', 'custom')->count();
        $nikBelumTerpakai = DataKaryawan::where('status', 'belum_terpakai')->count();
        $totalPeran = Peran::count();
        $totalDataKaryawan = DataKaryawan::count();

        // Kandidat menunggu (max 2, oldest first)
        $kandidatMenungguList = User::where('tipe_akun', 'kandidat')
            ->where('status', 'menunggu_verifikasi')
            ->orderBy('created_at', 'asc')
            ->take(2)
            ->get();

        // Trend 7 hari terakhir
        $batas7Hari = Carbon::now()->subDays(7);
        $karyawanBaru7Hari = User::where('tipe_akun', 'karyawan')->where('created_at', '>=', $batas7Hari)->count();
        $kandidatBaru7Hari = User::where('tipe_akun', 'kandidat')->where('created_at', '>=', $batas7Hari)->count();
        $adminStaffBaru7Hari = User::where('tipe_akun', 'custom')->where('created_at', '>=', $batas7Hari)->count();

        // Pendaftaran 7 hari terakhir (semua tipe_akun), dikelompokkan per tanggal
        $rawPendaftaran = User::select(DB::raw('DATE(created_at) as tanggal'), DB::raw('COUNT(*) as total'))
            ->where('created_at', '>=', $batas7Hari)
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal')
            ->toArray();

        $pendaftaran7Hari = [];
        $labelTanggal = [];
        for ($i = 6; $i >= 0; $i--) {
            $hari = Carbon::now()->subDays($i)->startOfDay();
            $key = $hari->format('Y-m-d');
            $pendaftaran7Hari[] = (int) ($rawPendaftaran[$key] ?? 0);
            $labelTanggal[] = $hari->translatedFormat('D, d M');
        }

        // Sapaan berdasarkan jam saat ini + tanggal lengkap Indonesia
        $jamSekarang = (int) Carbon::now()->hour;
        if ($jamSekarang >= 4 && $jamSekarang < 11) {
            $sapaan = 'Selamat pagi';
        } elseif ($jamSekarang >= 11 && $jamSekarang < 15) {
            $sapaan = 'Selamat siang';
        } elseif ($jamSekarang >= 15 && $jamSekarang < 18) {
            $sapaan = 'Selamat sore';
        } else {
            $sapaan = 'Selamat malam';
        }
        $namaPengguna = $request->user()->name ?? 'Admin';
        Carbon::setLocale('id');
        $tanggalHariIni = Carbon::now()->translatedFormat('l, j F Y');

        return view('admin.dashboard', compact(
            'request',
            'totalKaryawan',
            'totalKandidat',
            'kandidatMenunggu',
            'kandidatAktif',
            'kandidatDitolak',
            'totalAdminStaff',
            'nikBelumTerpakai',
            'totalPeran',
            'totalDataKaryawan',
            'kandidatMenungguList',
            'karyawanBaru7Hari',
            'kandidatBaru7Hari',
            'adminStaffBaru7Hari',
            'pendaftaran7Hari',
            'labelTanggal',
            'sapaan',
            'namaPengguna',
            'tanggalHariIni'
        ));
    }

    public function aktivitas(Request $request): View
    {
        $kataKunci = $request->input('cari');

        $aktivitas = User::query()
            ->when($kataKunci, function ($query, $kataKunci) {
                $query->where(function ($q) use ($kataKunci) {
                    $q->where('name', 'like', '%' . $kataKunci . '%')
                        ->orWhere('email', 'like', '%' . $kataKunci . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.aktivitas.index', [
            'aktivitas' => $aktivitas,
            'kataKunci' => $kataKunci,
        ]);
    }

    public function peserta(Request $request): View
    {
        // Gunakan data peserta simulasi sementara
        $pengguna = $this->getPesertaLoginSaatIni();

        // Ambil data sesi tes yang ditugaskan ke peserta ini
        $sesiTes = $this->getSesiTesDummy();

        return view('peserta.dashboard', [
            'pengguna' => $pengguna,
            'sesiTes' => $sesiTes,
        ]);
    }

    /**
     * Halaman instruksi pengerjaan tes
     * Menampilkan daftar alat tes yang ditugaskan dan peraturan umum
     */
    public function instruksi($sesiId)
    {
        // Ambil data sesi berdasarkan $sesiId dari data dummy
        $sesi = null;
        $sesiDummy = $this->getSesiTesDummy();

        foreach ($sesiDummy as $s) {
            if ((string)$s['id'] === (string)$sesiId || (int)$s['id'] === (int)$sesiId) {
                $sesi = $s;
                break;
            }
        }

        // Jika tidak ditemukan, redirect ke dashboard
        if (!$sesi) {
            return redirect()->route('peserta.dashboard')->with('error', 'Sesi tes tidak ditemukan.');
        }

        // Buat detail tambahan dummy per alat tes (durasi_menit, jumlah_soal, format_dasar)
        $detailAlatTes = [
            'IST'   => ['durasi_menit' => 45, 'jumlah_soal' => 30, 'format_dasar' => 'Pilihan Ganda'],
            'DISC'  => ['durasi_menit' => 30, 'jumlah_soal' => 25, 'format_dasar' => 'Pilihan Ganda'],
            'EPPS'  => ['durasi_menit' => 40, 'jumlah_soal' => 28, 'format_dasar' => 'Pilihan Ganda'],
            'MMPI-2' => ['durasi_menit' => 90, 'jumlah_soal' => 175, 'format_dasar' => 'Pernyataan True/False'],
        ];

        $daftarAlatTesDenganDetail = [];
        foreach ($sesi['daftar_alat_tes_ditugaskan'] as $kodeAlat) {
            $daftarAlatTesDenganDetail[] = [
                'nama_alat_tes' => $this->getNamaAlatTesFull($kodeAlat),
                'kode_alat_tes' => $kodeAlat,
                'durasi_menit'  => $detailAlatTes[$kodeAlat]['durasi_menit'] ?? 60,
                'jumlah_soal'   => $detailAlatTes[$kodeAlat]['jumlah_soal'] ?? 50,
                'format_dasar'  => $detailAlatTes[$kodeAlat]['format_dasar'] ?? 'Pilihan Ganda',
            ];
        }

        return view('peserta.instruksi', [
            'nama_sesi' => $sesi['nama_sesi'],
            'daftar_alat_tes_ditugaskan' => $sesi['daftar_alat_tes_ditugaskan'],
            'daftarAlatTesDenganDetail' => $daftarAlatTesDenganDetail,
            'departemen_terkait' => $sesi['departemen_terkait'],
            'sesiId' => $sesi['id'],
        ]);
    }

    /**
     * Dapatkan nama lengkap alat tes berdasarkan kode
     */
    private function getNamaAlatTesFull($kode): string
    {
        $namaLengkap = [
            'IST'    => 'Intelligenz Struktur Test',
            'DISC'   => 'Dominance, Influence, Steadiness, Compliance',
            'EPPS'   => 'Edwards Personal Preference Schedule',
            'MMPI-2' => 'Minnesota Multiphasic Personality Inventory-2',
        ];

        return $namaLengkap[$kode] ?? $kode;
    }

    /**
     * Placeholder untuk halaman pengerjaan tes (sedang dikerjakan)
     * Akan diisi nanti ketika module tes sudah lengkap
     */
    public function mulaiTes($sesiId)
    {
        return view('peserta.tes-placeholder');
    }
}
