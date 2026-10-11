<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Peran;
use App\Models\SesiTes;
use App\Models\AlatTes;
use App\Models\LogAktivitasTes;
use App\Models\PesertaSesiTes;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private function getPesertaLoginSaatIni(): object
    {
        /** @var \App\Models\User $userAsli */
        $userAsli = auth()->user();

        return (object) [
            'id' => $userAsli->id,
            'name' => $userAsli->name,
            'email' => $userAsli->email,
            'tipe_akun' => $userAsli->tipe_akun ?? 'peserta',
            'foto_profil' => $userAsli->foto_profil,
            'status' => $userAsli->status ?? 'aktif',
        ];
    }

    private function getSesiTes(): array
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if (!$user) {
            return [];
        }

        $sesiTes = SesiTes::whereHas('pesertaSesiTes', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->where('status', '!=', 'Draft')
            ->with(['departemen', 'pesertaSesiTesRecords.alatTes'])
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return $sesiTes->map(function ($sesi) use ($user) {
            $peserta = $sesi->pesertaSesiTesRecords->firstWhere('user_id', $user->id);
            $statusPengerjaan = $peserta ? $peserta->status_pengerjaan : 'Belum Mengerjakan';

            $today = Carbon::today();
            $startDate = Carbon::parse($sesi->tanggal_mulai);
            $endDate = Carbon::parse($sesi->tanggal_selesai);

            if ($sesi->status === 'Draft') {
                return null;
            }

            if ($today > $endDate) {
                $statusSesi = 'Selesai';
            } elseif ($today >= $startDate) {
                $statusSesi = 'Aktif';
            } else {
                $statusSesi = 'Belum Dimulai';
            }

            return [
                'id' => $sesi->id,
                'nama_sesi' => $sesi->nama_sesi,
                'departemen_terkait' => $sesi->departemen->pluck('nama_departemen')->join(', '),
                'tanggal_mulai' => $sesi->tanggal_mulai,
                'tanggal_selesai' => $sesi->tanggal_selesai,
                'daftar_alat_tes_ditugaskan' => $peserta?->alatTes->pluck('kode')->toArray() ?? [],
                'status_pengerjaan' => $statusPengerjaan,
                'status_sesi' => $statusSesi,
            ];
        })->filter()->values()->toArray();
    }

    public function admin(Request $request): View
    {
        $totalKaryawan = User::where('tipe_akun', 'karyawan')->count();
        $totalKandidat = User::where('tipe_akun', 'kandidat')->count();
        $kandidatMenunggu = User::where('tipe_akun', 'kandidat')->where('status', 'menunggu_verifikasi')->count();
        $kandidatAktif = User::where('tipe_akun', 'kandidat')->where('status', 'aktif')->count();
        $kandidatDitolak = User::where('tipe_akun', 'kandidat')->where('status', 'ditolak')->count();
        $totalAdminStaff = User::where('tipe_akun', 'custom')->count();
        // Akun karyawan buatan HR berstatus nonaktif sampai diaktifkan → belum bisa login
        $karyawanNonaktif = User::where('tipe_akun', 'karyawan')->where('status', 'nonaktif')->count();
        $karyawanWajibGantiPassword = User::where('tipe_akun', 'karyawan')->where('must_change_password', true)->count();
        $totalPeran = Peran::count();

        $kandidatMenungguList = User::where('tipe_akun', 'kandidat')
            ->where('status', 'menunggu_verifikasi')
            ->with('profilKandidat.posisi')
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();

        $batas7Hari = Carbon::now()->subDays(7);
        $karyawanBaru7Hari = User::where('tipe_akun', 'karyawan')->where('created_at', '>=', $batas7Hari)->count();

        $hariIni = Carbon::today();

        // Sesi tes: berjalan (dalam periode) & mendatang
        $sesiAktif = SesiTes::where('status', 'Aktif')->whereDate('tanggal_selesai', '>=', $hariIni);
        $jumlahSesiBerjalan = (clone $sesiAktif)->whereDate('tanggal_mulai', '<=', $hariIni)->count();
        $jumlahSesiMendatang = (clone $sesiAktif)->whereDate('tanggal_mulai', '>', $hariIni)->count();
        $daftarSesi = (clone $sesiAktif)
            ->withCount([
                'pesertaSesiTesRecords as total_peserta',
                'pesertaSesiTesRecords as total_selesai' => fn ($q) => $q->where('status_pengerjaan', 'Selesai'),
            ])
            ->orderBy('tanggal_mulai')
            ->take(5)
            ->get();

        // Status pengerjaan peserta di sesi terbit (bukan Draft). Yang belum selesai dibedakan
        // menurut sesinya: masih berjalan (Aktif & belum lewat tanggal) → sedang/belum mulai,
        // sudah berakhir (lewat tanggal atau ditutup 'Selesai') → tidak selesai
        $berjalan = "(sesi_tes.status = 'Aktif' AND sesi_tes.tanggal_selesai >= ?)";
        $tgl = $hariIni->toDateString();
        $statusPeserta = PesertaSesiTes::join('sesi_tes', 'sesi_tes.id', '=', 'peserta_sesi_tes.sesi_tes_id')
            ->whereNull('sesi_tes.deleted_at')
            ->where('sesi_tes.status', '!=', 'Draft')
            ->selectRaw("COUNT(*) as total,
                SUM(status_pengerjaan = 'Selesai') as selesai,
                SUM(status_pengerjaan <> 'Selesai' AND $berjalan AND waktu_mulai IS NOT NULL) as sedang,
                SUM(status_pengerjaan <> 'Selesai' AND $berjalan AND waktu_mulai IS NULL) as belum,
                SUM(status_pengerjaan <> 'Selesai' AND NOT $berjalan) as tidak_selesai", [$tgl, $tgl, $tgl])
            ->first();
        $pesertaTotal = (int) $statusPeserta->total;
        $pesertaSelesai = (int) $statusPeserta->selesai;
        $pesertaSedang = (int) $statusPeserta->sedang;
        $pesertaBelum = (int) $statusPeserta->belum;
        $pesertaTidakSelesai = (int) $statusPeserta->tidak_selesai;
        $tingkatPenyelesaian = $pesertaTotal > 0 ? round($pesertaSelesai / $pesertaTotal * 100) : 0;

        // Tren harian 90 hari: tes selesai & pendaftaran akun
        $awalTren = $hariIni->copy()->subDays(89);
        $sesiTerbit = fn ($q) => $q->where('status', '!=', 'Draft');
        $rawSelesai = PesertaSesiTes::where('status_pengerjaan', 'Selesai')
            ->whereHas('sesiTes', $sesiTerbit)
            ->whereDate('tanggal_pengerjaan', '>=', $awalTren)
            ->selectRaw('DATE(tanggal_pengerjaan) as tanggal, COUNT(*) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');
        $rawPendaftaran = User::where('created_at', '>=', $awalTren)
            ->selectRaw('DATE(created_at) as tanggal, COUNT(*) as total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $tren = ['label' => [], 'selesai' => [], 'pendaftaran' => []];
        for ($hari = $awalTren->copy(); $hari->lte($hariIni); $hari->addDay()) {
            $key = $hari->format('Y-m-d');
            $tren['label'][] = $hari->translatedFormat('j M');
            $tren['selesai'][] = (int) ($rawSelesai[$key] ?? 0);
            $tren['pendaftaran'][] = (int) ($rawPendaftaran[$key] ?? 0);
        }

        // Hasil tes terbaru
        $hasilTerbaru = PesertaSesiTes::where('status_pengerjaan', 'Selesai')
            ->whereHas('sesiTes', $sesiTerbit)->whereHas('user')
            ->with(['user:id,name,tipe_akun,foto_profil', 'sesiTes:id,nama_sesi', 'alatTes:alat_tes.id,kode'])
            ->orderByDesc('tanggal_pengerjaan')->orderByDesc('updated_at')
            ->take(5)
            ->get();

        // Peringatan integritas: peserta dengan log mencurigakan terbanyak/terbaru
        $peringatan30Hari = LogAktivitasTes::where('mencurigakan', true)->where('created_at', '>=', now()->subDays(30))->count();
        $pesertaMencurigakan = PesertaSesiTes::whereHas('sesiTes')->whereHas('user')
            ->whereHas('logAktivitas', fn ($q) => $q->where('mencurigakan', true))
            ->withCount(['logAktivitas as jumlah_peringatan' => fn ($q) => $q->where('mencurigakan', true)])
            ->withMax(['logAktivitas as peringatan_terakhir' => fn ($q) => $q->where('mencurigakan', true)], 'created_at')
            ->with(['user:id,name,foto_profil', 'sesiTes:id,nama_sesi'])
            ->orderByDesc('peringatan_terakhir')
            ->take(5)
            ->get();
        $rincianPeringatan = LogAktivitasTes::where('mencurigakan', true)
            ->whereIn('peserta_sesi_tes_id', $pesertaMencurigakan->pluck('id'))
            ->selectRaw('peserta_sesi_tes_id, tipe_kejadian, COUNT(*) as total')
            ->groupBy('peserta_sesi_tes_id', 'tipe_kejadian')
            ->get()
            ->groupBy('peserta_sesi_tes_id');

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
        $tanggalHariIni = Carbon::now()->translatedFormat('l, j F Y');

        return view('admin.dashboard', compact(
            'totalKaryawan',
            'totalKandidat',
            'kandidatMenunggu',
            'kandidatAktif',
            'kandidatDitolak',
            'totalAdminStaff',
            'karyawanNonaktif',
            'karyawanWajibGantiPassword',
            'totalPeran',
            'kandidatMenungguList',
            'karyawanBaru7Hari',
            'jumlahSesiBerjalan',
            'jumlahSesiMendatang',
            'daftarSesi',
            'pesertaTotal',
            'pesertaSelesai',
            'pesertaSedang',
            'pesertaBelum',
            'pesertaTidakSelesai',
            'tingkatPenyelesaian',
            'tren',
            'hasilTerbaru',
            'peringatan30Hari',
            'pesertaMencurigakan',
            'rincianPeringatan',
            'sapaan',
            'namaPengguna',
            'tanggalHariIni'
        ));
    }

    public function aktivitas(Request $request): View
    {
        $kataKunci = $request->input('cari');

        $perPage = $this->getPerPage($request, 20);

        $aktivitas = User::query()
            ->when($kataKunci, function ($query, $kataKunci) {
                $query->where(function ($q) use ($kataKunci) {
                    $q->where('name', 'like', '%' . $kataKunci . '%')
                        ->orWhere('email', 'like', '%' . $kataKunci . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.aktivitas.index', [
            'aktivitas' => $aktivitas,
            'kataKunci' => $kataKunci,
        ]);
    }

    public function peserta(Request $request): View
    {
        $pengguna = $this->getPesertaLoginSaatIni();
        $sesiTes = $this->getSesiTes();

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
        $sesi = SesiTes::with(['departemen'])
            ->find($sesiId);

        if (!$sesi) {
            return redirect()->route('peserta.dashboard')->with('error', 'Sesi tes tidak ditemukan.');
        }

        // Validasi: pastikan user login terdaftar sebagai peserta di sesi ini
        $pesertaSesi = PesertaSesiTes::where('sesi_tes_id', $sesiId)
            ->where('user_id', auth()->id())
            ->with('alatTes')
            ->first();
        if (!$pesertaSesi) {
            return redirect()->route('peserta.dashboard')->with('error', 'Anda tidak memiliki akses ke sesi tes ini.');
        }

        if ($sesi->status === 'Draft') {
            abort(403, 'Sesi tes belum aktif.');
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

        $kodeAlatTes = $pesertaSesi->alatTes->pluck('kode')->toArray();

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
            'departemen_terkait' => $sesi->departemen->pluck('nama_departemen')->join(', '),
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
    public function panduan(): View
    {
        $alatTes = AlatTes::where('is_aktif', true)
            ->orderBy('nama')
            ->get(['kode', 'nama', 'deskripsi', 'format_dasar', 'durasi_total_menit', 'jumlah_soal']);

        return view('peserta.panduan', ['alatTes' => $alatTes]);
    }
}
