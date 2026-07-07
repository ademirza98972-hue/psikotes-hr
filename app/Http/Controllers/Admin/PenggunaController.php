<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PerbaruiPenggunaRequest;
use App\Http\Requests\SimpanPenggunaRequest;
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

class PenggunaController extends Controller
{
    public function index(Request $request): View
    {
        $kataKunci = $request->input('cari');

        $pengguna = User::with('peran')
            ->when($kataKunci, function ($query, $kataKunci) {
                $query->where(function ($q) use ($kataKunci) {
                    $q->where('name', 'like', '%' . $kataKunci . '%')
                        ->orWhere('email', 'like', '%' . $kataKunci . '%');
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.pengguna.index', [
            'pengguna' => $pengguna,
            'kataKunci' => $kataKunci,
        ]);
    }

    public function tambah(): View
    {
        return view('admin.pengguna.tambah', [
            'daftarPeran' => Peran::orderBy('nama_peran')->get(),
        ]);
    }

    public function simpan(SimpanPenggunaRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $tipeAkun = $data['tipe_akun'];

        DB::transaction(function () use ($data, $tipeAkun) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'no_hp' => $data['no_hp'],
                'tipe_akun' => $tipeAkun,
                'peran_id' => $data['peran_id'],
                'status' => $data['status'],
            ]);

            $this->simpanProfil($user, $tipeAkun, $data);
        });

        return redirect()
            ->route('admin.pengguna.index')
            ->with('sukses', 'Pengguna baru berhasil ditambahkan.');
    }

    public function ubah(int $id): View
    {
        $pengguna = User::with(['peran', 'profilKandidat', 'profilKaryawan'])->findOrFail($id);

        return view('admin.pengguna.ubah', [
            'pengguna' => $pengguna,
            'daftarPeran' => Peran::orderBy('nama_peran')->get(),
        ]);
    }

    public function perbarui(PerbaruiPenggunaRequest $request, int $id): RedirectResponse
    {
        $pengguna = User::findOrFail($id);
        $data = $request->validated();
        $tipeAkun = $data['tipe_akun'];

        DB::transaction(function () use ($pengguna, $data, $tipeAkun) {
            $payload = [
                'name' => $data['name'],
                'email' => $data['email'],
                'no_hp' => $data['no_hp'],
                'tipe_akun' => $tipeAkun,
                'peran_id' => $data['peran_id'],
                'status' => $data['status'],
            ];

            if (! empty($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
            }

            $pengguna->update($payload);

            $this->hapusProfilLama($pengguna);
            $this->simpanProfil($pengguna, $tipeAkun, $data);
        });

        return redirect()
            ->route('admin.pengguna.index')
            ->with('sukses', 'Data pengguna berhasil diperbarui.');
    }

    public function hapus(int $id): RedirectResponse
    {
        $pengguna = User::findOrFail($id);

        if ($pengguna->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $pengguna->delete();

        return redirect()
            ->route('admin.pengguna.index')
            ->with('sukses', 'Pengguna berhasil dihapus.');
    }

    protected function simpanProfil(User $user, string $tipeAkun, array $data): void
    {
        if ($tipeAkun === 'kandidat') {
            ProfilKandidat::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'posisi_dilamar' => $data['posisi_dilamar'],
                    'pendidikan_terakhir' => $data['pendidikan_terakhir'],
                    'no_ktp' => $data['no_ktp'] ?? null,
                ]
            );
        } elseif ($tipeAkun === 'karyawan') {
            ProfilKaryawan::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nik' => $data['nik'],
                    'departemen' => $data['departemen'],
                    'jabatan' => $data['jabatan'] ?? null,
                ]
            );
        }
    }

    protected function hapusProfilLama(User $user): void
    {
        $user->profilKandidat()?->delete();
        $user->profilKaryawan()?->delete();
    }
}