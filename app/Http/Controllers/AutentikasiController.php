<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Peran;
use App\Models\ProfilKandidat;
use App\Models\ProfilKaryawan;
use App\Models\User;
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
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $tipeAkun = $data['tipe_akun'];

        $peran = Peran::where('nama_peran', ucfirst($tipeAkun))->first();

        if (! $peran) {
            return back()
                ->withInput()
                ->withErrors(['tipe_akun' => 'Peran untuk tipe akun ini belum tersedia di sistem.']);
        }

        DB::transaction(function () use ($data, $peran, $tipeAkun) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'no_hp' => $data['no_hp'],
                'tipe_akun' => $tipeAkun,
                'peran_id' => $peran->id,
                'status' => 'menunggu_verifikasi',
            ]);

            if ($tipeAkun === 'kandidat') {
                ProfilKandidat::create([
                    'user_id' => $user->id,
                    'posisi_dilamar' => $data['posisi_dilamar'],
                    'pendidikan_terakhir' => $data['pendidikan_terakhir'],
                    'no_ktp' => $data['no_ktp'] ?? null,
                ]);
            } else {
                ProfilKaryawan::create([
                    'user_id' => $user->id,
                    'nik' => $data['nik'],
                    'departemen' => $data['departemen'],
                    'jabatan' => $data['jabatan'] ?? null,
                ]);
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

        return route('admin.dashboard');
    }
}