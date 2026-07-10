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
        return view('peserta.dashboard', [
            'pengguna' => $request->user(),
        ]);
    }
}
