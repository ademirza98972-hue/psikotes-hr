<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginPesertaRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Peran;
use App\Models\ProfilKandidat;
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
    public function tampilkanLoginPeserta(): View
    {
        return view('auth.login');
    }

    public function tampilkanLoginAdmin(): View
    {
        return view('auth.login-admin');
    }

    public function loginPeserta(LoginPesertaRequest $request): RedirectResponse
    {
        $identifier = $request->input('identifier');
        $password   = $request->input('password');

        // Kurung wajib: tanpa closure, OR membuat akun admin lolos lewat email
        $user = User::where(fn ($q) => $q->where('email', $identifier)->orWhere('id_karyawan', $identifier))
            ->whereIn('tipe_akun', ['kandidat', 'karyawan'])
            ->first();

        if (! $user) {
            return back()
                ->withInput($request->only('identifier', 'ingat_saya'))
                ->withErrors(['identifier' => 'Email/ID Karyawan atau password salah, atau akun belum aktif.']);
        }

        if ($user->status === 'nonaktif') {
            return back()
                ->withInput($request->only('identifier', 'ingat_saya'))
                ->withErrors(['identifier' => 'Akun Anda telah dinonaktifkan. Silakan hubungi Admin/HR.']);
        }

        if ($user->status !== 'aktif') {
            return back()
                ->withInput($request->only('identifier', 'ingat_saya'))
                ->withErrors(['identifier' => 'Email/ID Karyawan atau password salah, atau akun belum aktif.']);
        }

        if (! Hash::check($password, $user->password)) {
            return back()
                ->withInput($request->only('identifier', 'ingat_saya'))
                ->withErrors(['identifier' => 'Email/ID Karyawan atau password salah, atau akun belum aktif.']);
        }

        Auth::login($user, $request->boolean('ingat_saya'));
        $request->session()->regenerate();

        return redirect()->intended(route('peserta.dashboard'));
    }

    public function loginAdmin(LoginRequest $request): RedirectResponse
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

        $user = Auth::user();

        if (in_array($user->tipe_akun, ['kandidat', 'karyawan'], true)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->withErrors(['email' => 'Akun ini bukan akun admin. Silakan login melalui portal peserta.']);
        }

        $request->session()->regenerate();

        // URL intended sisa sesi peserta (/peserta/...) jangan dipakai untuk admin
        if (! str_starts_with(parse_url($request->session()->get('url.intended', ''), PHP_URL_PATH) ?? '', '/admin')) {
            $request->session()->forget('url.intended');
        }

        return redirect()->intended($this->tujuanDashboard());
    }

    public function tampilkanRegister(): View
    {
        $posisi = Posisi::with('departemen')->orderBy('departemen_id')->orderBy('nama_posisi')->get();
        return view('auth.register', compact('posisi'));
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $data    = $request->validated();
        $tipeAkun = $data['tipe_akun'];

        // Karyawan tidak bisa self-register lagi — akun dibuat otomatis oleh HR
        if ($tipeAkun === 'karyawan') {
            return back()
                ->withInput()
                ->withErrors(['tipe_akun' => 'Akun karyawan dibuat oleh HR. Silakan hubungi HR untuk mendapatkan ID Karyawan Anda.']);
        }

        $peran = Peran::where('nama_peran', 'Kandidat')->first();

        if (! $peran) {
            return back()
                ->withInput()
                ->withErrors(['tipe_akun' => 'Peran untuk tipe akun ini belum tersedia di sistem.']);
        }

        DB::transaction(function () use ($data, $peran) {
            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => Hash::make($data['password']),
                'tipe_akun' => 'kandidat',
                'peran_id'  => $peran->id,
                'status'    => 'menunggu_verifikasi',
            ]);

            ProfilKandidat::create([
                'user_id'             => $user->id,
                'no_hp'               => $data['no_hp'],
                'jenis_kelamin'       => $data['jenis_kelamin'] ?? null,
                'posisi_id'           => $data['posisi_dilamar'],
                'pendidikan_terakhir' => $data['pendidikan_terakhir'],
                'tanggal_lahir'       => $data['tanggal_lahir'] ?? null,
                'nik_kandidat'        => $data['nik_kandidat'] ?? null,
            ]);
        });

        return redirect()
            ->route('login')
            ->with('sukses', 'Registrasi berhasil. Akun Anda sedang menunggu verifikasi dari HR.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $tipeAkun = Auth::user()?->tipe_akun;
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $routeLogin = in_array($tipeAkun, ['kandidat', 'karyawan'], true) ? 'login' : 'login.admin';
        return redirect()->route($routeLogin);
    }

    protected function tujuanDashboard(): string
    {
        $user = Auth::user();

        if ($user && in_array($user->tipe_akun, ['kandidat', 'karyawan'], true)) {
            return route('peserta.dashboard');
        }

        $peta = [
            'dashboard.lihat'       => 'admin.dashboard',
            'pengguna.lihat'        => 'admin.akun-karyawan.index',
            'soal.lihat'            => 'admin.alat-tes.index',
            'kategori_tes.kelola'   => 'admin.alat-tes.index',
            'hasil_tes.lihat'       => 'admin.hasil-tes.index',
            'peran.kelola'          => 'admin.peran.index',
            'pengguna_admin.kelola' => 'admin.pengguna-admin.index',
            'departemen.kelola'     => 'admin.departemen.index',
            'posisi.kelola'         => 'admin.posisi.index',
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
