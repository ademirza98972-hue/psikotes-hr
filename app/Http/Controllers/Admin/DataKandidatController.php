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

        $statusValid = ['menunggu_verifikasi', 'aktif', 'ditolak'];
        if (! in_array($filterStatus, $statusValid, true)) {
            $filterStatus = null;
        }

        $filterPosisiNama = $filterPosisiId
            ? Posisi::whereKey($filterPosisiId)->value('nama_posisi')
            : null;

        $query = User::with(['profilKandidat'])
            ->where('tipe_akun', 'kandidat');

        $query->when($kataKunci, function ($q) use ($kataKunci) {
            $q->where(function ($inner) use ($kataKunci) {
                $inner->where('name', 'like', '%' . $kataKunci . '%')
                    ->orWhere('email', 'like', '%' . $kataKunci . '%')
                    ->orWhereHas('profilKandidat', function ($pk) use ($kataKunci) {
                        $pk->where('nama_kandidat', 'like', '%' . $kataKunci . '%')
                            ->orWhere('nik_kandidat', 'like', '%' . $kataKunci . '%');
                    });
            });
        });

        $query->when($filterStatus, function ($q) use ($filterStatus) {
            $q->where('status', $filterStatus);
        });

        $query->when($filterPosisiNama, function ($q) use ($filterPosisiNama) {
            $q->whereHas('profilKandidat', fn ($pk) => $pk->where('posisi_dilamar', $filterPosisiNama));
        });

        $kandidat = $query->with(['profilKandidat.posisi'])
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $semuaPosisi = Posisi::orderBy('nama_posisi')->get();

        return view('admin.data-kandidat.index', [
            'kandidat' => $kandidat,
            'kataKunci' => $kataKunci,
            'semuaPosisi' => $semuaPosisi,
            'filterStatus' => $filterStatus,
            'filterPosisi' => $filterPosisiId,
        ]);
    }

    public function tambah(): View
    {
        $departemen = Departemen::orderBy('nama_departemen')->get();

        return view('admin.data-kandidat.tambah', compact('departemen'));
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

        $namaPosisi = Posisi::whereKey($data['posisi_dilamar'])->value('nama_posisi');

        DB::transaction(function () use ($data, $peran, $namaPosisi) {
            $user = User::create([
                'name' => $data['nama_kandidat'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'no_hp' => $data['no_hp'],
                'tipe_akun' => 'kandidat',
                'peran_id' => $peran->id,
                'status' => 'aktif',
            ]);

            ProfilKandidat::create([
                'user_id' => $user->id,
                'nama_kandidat' => $data['nama_kandidat'],
                'posisi_dilamar' => $namaPosisi,
                'pendidikan_terakhir' => $data['pendidikan_terakhir'],
                'nik_kandidat' => $data['nik_kandidat'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.data-kandidat.index')
            ->with('sukses', 'Akun kandidat berhasil ditambahkan.');
    }

    public function ubah(int $id): View
    {
        $kandidat = User::with('profilKandidat')->findOrFail($id);

        abort_unless($kandidat->tipe_akun === 'kandidat', 404);

        $departemen = Departemen::orderBy('nama_departemen')->get();

        return view('admin.data-kandidat.ubah', [
            'kandidat' => $kandidat,
            'profilKandidat' => $kandidat->profilKandidat,
            'departemen' => $departemen,
        ]);
    }

    public function perbarui(PerbaruiKandidatRequest $request, int $id): RedirectResponse
    {
        $kandidat = User::findOrFail($id);

        abort_unless($kandidat->tipe_akun === 'kandidat', 404);

        $data = $request->validated();

        $namaPosisi = Posisi::whereKey($data['posisi_dilamar'])->value('nama_posisi');

        DB::transaction(function () use ($kandidat, $data, $namaPosisi) {
            $payload = [
                'name' => $data['nama_kandidat'],
                'email' => $data['email'],
                'no_hp' => $data['no_hp'],
            ];

            if (! empty($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
            }

            $kandidat->update($payload);

            $profil = $kandidat->profilKandidat;
            if ($profil) {
                $profil->update([
                    'nama_kandidat' => $data['nama_kandidat'],
                    'posisi_dilamar' => $namaPosisi,
                    'pendidikan_terakhir' => $data['pendidikan_terakhir'],
                    'nik_kandidat' => $data['nik_kandidat'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('admin.data-kandidat.index')
            ->with('sukses', 'Data kandidat berhasil diperbarui.');
    }

    public function hapus(int $id): RedirectResponse
    {
        $kandidat = User::findOrFail($id);

        abort_unless($kandidat->tipe_akun === 'kandidat', 404);

        if ($kandidat->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        DB::transaction(function () use ($kandidat) {
            $kandidat->delete();
        });

        return redirect()
            ->route('admin.data-kandidat.index')
            ->with('sukses', 'Akun kandidat berhasil dihapus.');
    }

    public function approve(int $id): RedirectResponse
    {
        $kandidat = User::findOrFail($id);

        abort_unless($kandidat->tipe_akun === 'kandidat', 404);

        if ($kandidat->status !== 'menunggu_verifikasi') {
            return back()->with('error', 'Hanya akun dengan status menunggu_verifikasi yang dapat diaktifkan.');
        }

        $kandidat->update(['status' => 'aktif']);

        return redirect()
            ->route('admin.data-kandidat.index')
            ->with('sukses', 'Kandidat berhasil diaktifkan.');
    }

    public function tolak(int $id): RedirectResponse
    {
        $kandidat = User::findOrFail($id);

        abort_unless($kandidat->tipe_akun === 'kandidat', 404);

        if ($kandidat->status !== 'menunggu_verifikasi') {
            return back()->with('error', 'Hanya akun dengan status menunggu_verifikasi yang dapat ditolak.');
        }

        $kandidat->update(['status' => 'ditolak']);

        return redirect()
            ->route('admin.data-kandidat.index')
            ->with('sukses', 'Kandidat berhasil ditolak. Data tetap disimpan.');
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
            ->with('sukses', $kandidat->fresh()->status === 'aktif' ? 'Kandidat berhasil diaktifkan.' : 'Kandidat berhasil dinonaktifkan.');
    }
}