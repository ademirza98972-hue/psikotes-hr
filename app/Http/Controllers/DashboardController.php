<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DataKaryawan;
use App\Models\Peran;
use App\Models\SesiTes;
use App\Models\AlatTes;
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
        /** @var \App\Models\User $userAsli */
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
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@company.com',
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
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user) {
            return [];
        }

        $sesiTes = SesiTes::whereHas('pesertaSesiTes', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['departemenTerkait', 'alatTes' => function ($q) {
                $q->select('alat_tes.id', 'alat_tes.kode');
            }, 'pesertaSesiTesRecords'])
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return $sesiTes->map(function ($sesi) use ($user) {
            $peserta = $sesi->pesertaSesiTesRecords->firstWhere('user_id', $user->id);
            $statusPengerjaan = $peserta ? $peserta->status_pengerjaan : 'Belum Mengerjakan';

            $today = Carbon::today();
            $startDate = Carbon::parse($sesi->tanggal_mulai);
            $endDate = Carbon::parse($sesi->tanggal_selesai);

            if ($today > $endDate) {
                $statusSesi = 'Kedaluwarsa';
            } elseif ($today >= $startDate) {
                $statusSesi = 'Aktif';
            } else {
                $statusSesi = 'Belum Dimulai';
            }

            return [
                'id' => $sesi->id,
                'nama_sesi' => $sesi->nama_sesi,
                'departemen_terkait' => $sesi->departemenTerkait?->nama_departemen ?? '',
                'tanggal_mulai' => $sesi->tanggal_mulai,
                'tanggal_selesai' => $sesi->tanggal_selesai,
                'daftar_alat_tes_ditugaskan' => $sesi->alatTes->pluck('kode')->toArray(),
                'status_pengerjaan' => $statusPengerjaan,
                'status_sesi' => $statusSesi,
            ];
        })->toArray();
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
    public function instruksi(int $sesiId)
    {
        $sesi = SesiTes::with(['departemenTerkait', 'alatTes'])
            ->find($sesiId);

        if (!$sesi) {
            return redirect()->route('peserta.dashboard')->with('error', 'Sesi tes tidak ditemukan.');
        }

        $startDate = Carbon::parse($sesi->tanggal_mulai);
        $endDate = Carbon::parse($sesi->tanggal_selesai);
        $today = Carbon::now()->startOfDay();

        if ($today > $endDate) {
            $statusPengerjaan = 'Selesai';
        } elseif ($today >= $startDate) {
            $statusPengerjaan = 'Sedang Berjalan';
        } else {
            $statusPengerjaan = 'Belum Mengerjakan';
        }

        $kodeAlatTes = $sesi->alatTes->pluck('kode')->toArray();

        $daftarAlatTesDenganDetail = [];
        foreach ($kodeAlatTes as $kodeAlat) {
            $alat = AlatTes::where('kode', $kodeAlat)->first();
            $daftarAlatTesDenganDetail[] = [
                'nama_alat_tes' => $alat ? $alat->nama : $this->getNamaAlatTesFull($kodeAlat),
                'kode_alat_tes' => $kodeAlat,
                'durasi_menit'  => $alat->durasi_total_menit ?? 60,
                'jumlah_soal'   => $alat->jumlah_soal ?? 50,
                'format_dasar'  => $alat->format_dasar ?? 'Pilihan Ganda',
            ];
        }

        return view('peserta.instruksi', [
            'nama_sesi' => $sesi->nama_sesi,
            'daftar_alat_tes_ditugaskan' => $kodeAlatTes,
            'daftarAlatTesDenganDetail' => $daftarAlatTesDenganDetail,
            'departemen_terkait' => $sesi->departemenTerkait?->nama_departemen ?? '',
            'sesiId' => $sesi->id,
        ]);
    }

    /**
     * Dapatkan nama lengkap alat tes berdasarkan kode
     */
    private function getNamaAlatTesFull(string $kode): string
    {
        $alat = AlatTes::where('kode', $kode)->first();
        return $alat ? $alat->nama : $kode;
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
