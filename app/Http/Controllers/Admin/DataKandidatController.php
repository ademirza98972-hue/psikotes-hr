<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PerbaruiKandidatRequest;
use App\Http\Requests\SimpanKandidatRequest;
use App\Models\Departemen;
use App\Models\Peran;
use App\Models\ProfilKandidat;
use App\Models\Posisi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class DataKandidatController extends Controller
{
    public function index(Request $request): View
    {
        $kataKunci = $request->input('cari');
        $filterStatus = $request->input('status');
        $filterPosisiId = $request->input('posisi');

        $statusValid = ['menunggu_verifikasi', 'aktif', 'nonaktif', 'ditolak'];
        if (! in_array($filterStatus, $statusValid, true)) {
            $filterStatus = null;
        }

        $filterPosisiNama = $filterPosisiId ?: null;

        $query = User::with(['profilKandidat'])
            ->where('tipe_akun', 'kandidat');

        $query->when($kataKunci, function ($q) use ($kataKunci) {
            $q->where(function ($inner) use ($kataKunci) {
                $inner->where('name', 'like', '%' . $kataKunci . '%')
                    ->orWhere('email', 'like', '%' . $kataKunci . '%')
                    ->orWhereHas('profilKandidat', function ($pk) use ($kataKunci) {
                        $pk->where('nik_kandidat', 'like', '%' . $kataKunci . '%');
                    });
            });
        });

        $query->when($filterStatus, function ($q) use ($filterStatus) {
            $q->where('status', $filterStatus);
        });

        $query->when($filterPosisiNama, function ($q) use ($filterPosisiNama) {
            $q->whereHas('profilKandidat', fn ($pk) => $pk->where('posisi_id', $filterPosisiNama));
        });

        $perPage = $this->getPerPage($request, 15);

        $kandidat = $query->with(['profilKandidat.posisi'])
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        $semuaPosisi = Posisi::orderBy('nama_posisi')->get();
        $daftarDepartemen = Departemen::orderBy('nama_departemen')->get();

        return view('admin.data-kandidat.index', [
            'kandidat' => $kandidat,
            'kataKunci' => $kataKunci,
            'semuaPosisi' => $semuaPosisi,
            'filterStatus' => $filterStatus,
            'filterPosisi' => $filterPosisiId,
            'daftarDepartemen' => $daftarDepartemen,
            'ringkasan' => User::where('tipe_akun', 'kandidat')
                ->selectRaw("COUNT(*) as total, SUM(status = 'menunggu_verifikasi') as menunggu, SUM(status = 'aktif') as aktif, SUM(status = 'ditolak') as ditolak")
                ->first(),
            // ?tambah=1 / ?ubah={id} → modal langsung terbuka (tautan lama /tambah & /{id}/ubah diarahkan ke sini)
            'bukaTambah' => $request->boolean('tambah'),
            'bukaUbah' => $request->filled('ubah')
                ? User::with('profilKandidat.posisi')->where('tipe_akun', 'kandidat')->find((int) $request->input('ubah'))
                : null,
        ]);
    }

    public function tambah(): RedirectResponse
    {
        return redirect()->route('admin.data-kandidat.index', ['tambah' => 1]);
    }

    public function simpan(SimpanKandidatRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $peran = Peran::where('nama_peran', 'Kandidat')->first();

        if (! $peran) {
            return back()
                ->withInput()
                ->withErrors(['tipe_akun' => 'Peran Kandidat belum tersedia di sistem. Jalankan seeder terlebih dahulu.']);
        }

        DB::transaction(function () use ($data, $peran) {
            $user = User::create([
                'name' => $data['nama_kandidat'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'tipe_akun' => 'kandidat',
                'peran_id' => $peran->id,
                'status' => 'aktif',
            ]);


            ProfilKandidat::create([
                'user_id' => $user->id,
                'no_hp' => $data['no_hp'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'posisi_id' => $data['posisi_dilamar'],
                'pendidikan_terakhir' => $data['pendidikan_terakhir'],
                'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
                'nik_kandidat' => $data['nik_kandidat'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.data-kandidat.index')
            ->with($this::FLASH_SUCCESS, 'Akun kandidat berhasil ditambahkan.');
    }

    public function ubah(int $id): RedirectResponse
    {
        $kandidat = User::findOrFail($id);

        abort_unless($kandidat->tipe_akun === 'kandidat', 404);

        return redirect()->route('admin.data-kandidat.index', ['ubah' => $id]);
    }

    public function perbarui(PerbaruiKandidatRequest $request, int $id): RedirectResponse
    {
        $kandidat = User::findOrFail($id);

        abort_unless($kandidat->tipe_akun === 'kandidat', 404);

        $data = $request->validated();

        DB::transaction(function () use ($kandidat, $data) {
            $payload = [
                'name' => $data['nama_kandidat'],
                'email' => $data['email'],
            ];

            if (! empty($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
            }

            $kandidat->update($payload);


            $profil = $kandidat->profilKandidat;
            if ($profil) {
                $profil->update([
                    'no_hp' => $data['no_hp'],
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'posisi_id' => $data['posisi_dilamar'],
                    'pendidikan_terakhir' => $data['pendidikan_terakhir'],
                    'tanggal_lahir' => $data['tanggal_lahir'] ?? null,
                    'nik_kandidat' => $data['nik_kandidat'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('admin.data-kandidat.index')
            ->with($this::FLASH_SUCCESS, 'Data kandidat berhasil diperbarui.');
    }

    public function hapus(int $id): RedirectResponse
    {
        $kandidat = User::findOrFail($id);

        abort_unless($kandidat->tipe_akun === 'kandidat', 404);

        if ($kandidat->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        DB::transaction(function () use ($kandidat) {
            $kandidat->deleted_by = Auth::id();
            $kandidat->save();
            $kandidat->delete();
        });

        return redirect()
            ->route('admin.data-kandidat.index')
            ->with($this::FLASH_SUCCESS, 'Akun kandidat berhasil dihapus.');
    }

    public function approve(int $id): RedirectResponse
    {
        return $this->ubahStatusKandidat($id, 'aktif', 'Kandidat berhasil diaktifkan.', 'diaktifkan');
    }

    public function tolak(int $id): RedirectResponse
    {
        return $this->ubahStatusKandidat($id, 'ditolak', 'Kandidat berhasil ditolak. Data tetap disimpan.', 'ditolak');
    }

    private function ubahStatusKandidat(int $id, string $status, string $pesan, string $label): RedirectResponse
    {
        $kandidat = User::findOrFail($id);
        abort_unless($kandidat->tipe_akun === 'kandidat', 404);
        if ($kandidat->status !== 'menunggu_verifikasi') {
            return back()->with('error', "Hanya akun dengan status menunggu_verifikasi yang dapat di{$label}.");
        }
        $kandidat->update(['status' => $status]);
        return redirect()->route('admin.data-kandidat.index')->with($this::FLASH_SUCCESS, $pesan);
    }

    public function toggleStatus(int $id): RedirectResponse
    {
        $kandidat = User::findOrFail($id);

        abort_unless($kandidat->tipe_akun === 'kandidat', 404);

        if ($kandidat->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        if (! in_array($kandidat->status, ['aktif', 'nonaktif'], true)) {
            return back()->with('error', 'Hanya akun dengan status Aktif atau Nonaktif yang dapat ditoggle statusnya.');
        }

        $kandidat->update(['status' => $kandidat->status === 'aktif' ? 'nonaktif' : 'aktif']);

        return redirect()
            ->route('admin.data-kandidat.index')
            ->with($this::FLASH_SUCCESS, $kandidat->fresh()->status === 'aktif' ? 'Kandidat berhasil diaktifkan.' : 'Kandidat berhasil dinonaktifkan.');
    }
}