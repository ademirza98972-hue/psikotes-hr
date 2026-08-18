<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\DataKaryawan;
use App\Models\Departemen;
use App\Models\Peran;
use App\Models\ProfilKandidat;
use App\Models\ProfilKaryawan;
use App\Models\User;
use App\Models\Posisi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AutentikasiController extends Controller
{
    public function tampilkanLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $kredensial = $request->only('email', 'password');

        $user = User::where('email', $kredensial['email'])->first();

        if ($user && Hash::check($kredensial['password'], $user->password) && $user->status === 'nonaktif') {
            return back()
                ->withInput($request->only('email', 'ingat_saya'))
                ->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi Admin/HR.']);
        }

        $kredensial['status'] = 'aktif';

        if (! Auth::attempt($kredensial, $request->boolean('ingat_saya'))) {
            return back()
                ->withInput($request->only('email', 'ingat_saya'))
                ->withErrors(['email' => 'Email atau password salah, atau akun belum aktif.']);
        }

        $request->session()->regenerate();

        return redirect()->intended($this->tujuanDashboard());
    }

    public function tampilkanRegister(): View
    {
        $departemen = Departemen::orderBy('nama_departemen')->get();
        $posisi = Posisi::with('departemen')->orderBy('departemen_id')->orderBy('nama_posisi')->get();
        return view('auth.register', compact('departemen', 'posisi'));
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $tipeAkun = $data['tipe_akun'];

        if ($tipeAkun === 'karyawan') {
            $dataMaster = DataKaryawan::where('nik_karyawan', $data['nik_karyawan'])->first();

            if (! $dataMaster || strcasecmp($dataMaster->nama_karyawan, $data['name']) !== 0) {
                return back()
                    ->withInput()
                    ->withErrors(['nik_karyawan' => 'NIK dan nama tidak ditemukan pada data karyawan terdaftar. Silakan hubungi HR.']);
            }

            if ($dataMaster->status === 'sudah_terpakai') {
                return back()
                    ->withInput()
                    ->withErrors(['nik_karyawan' => 'NIK ini sudah terdaftar sebelumnya.']);
            }
        }

        $peran = Peran::where('nama_peran', ucfirst($tipeAkun))->first();

        if (! $peran) {
            return back()
                ->withInput()
                ->withErrors(['tipe_akun' => 'Peran untuk tipe akun ini belum tersedia di sistem.']);
        }

        DB::transaction(function () use ($data, $peran, $tipeAkun) {
            $statusUser = $tipeAkun === 'karyawan' ? 'aktif' : 'menunggu_verifikasi';

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'no_hp' => $data['no_hp'],
                'tipe_akun' => $tipeAkun,
                'peran_id' => $peran->id,
                'status' => $statusUser,
            ]);

            if ($tipeAkun === 'kandidat') {
                ProfilKandidat::create([
                    'user_id' => $user->id,
                    'nama_kandidat' => $data['name'],
                    'posisi_dilamar' => $data['posisi_dilamar'],
                    'pendidikan_terakhir' => $data['pendidikan_terakhir'],
                    'nik_kandidat' => $data['nik_kandidat'] ?? null,
                ]);
            } elseif ($tipeAkun === 'karyawan') {
                $dataMaster = DataKaryawan::where('nik_karyawan', $data['nik_karyawan'])
                    ->lockForUpdate()
                    ->first();

                ProfilKaryawan::create([
                    'user_id' => $user->id,
                    'data_karyawan_id' => $dataMaster->id,
                    'nama_karyawan' => $dataMaster->nama_karyawan,
                    'nik_karyawan' => $dataMaster->nik_karyawan,
                    'departemen' => $dataMaster->departemen,
                    'jabatan' => $dataMaster->jabatan,
                ]);

                $dataMaster->update(['status' => 'sudah_terpakai']);
            }
        });

        return redirect()
            ->route('login')
            ->with('sukses', 'Registrasi berhasil, silakan login');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function tujuanDashboard(): string
    {
        $user = Auth::user();

        if ($user && in_array($user->tipe_akun, ['kandidat', 'karyawan'], true)) {
            return route('peserta.dashboard');
        }

        $peta = [
            'dashboard.lihat'       => 'admin.dashboard',
            'data_karyawan.kelola'  => 'admin.data-karyawan.index',
            'pengguna.lihat'        => 'admin.akun-karyawan.index',
            'soal.lihat'            => 'admin.alat-tes.index',
            'kategori_tes.kelola'   => 'admin.alat-tes.index',
            'hasil_tes.lihat'       => 'admin.hasil-tes.index',
            'peran.kelola'          => 'admin.peran.index',
            'pengguna_admin.kelola' => 'admin.pengguna-admin.index',
            'master_data.kelola'    => 'admin.departemen.index',
            'data_terhapus.kelola'  => 'admin.data-terhapus.index',
        ];

        foreach ($peta as $izin => $namaRoute) {
            if ($user && $user->hasIzin($izin)) {
                return route($namaRoute);
            }
        }

        return route('admin.dashboard');
    }
}