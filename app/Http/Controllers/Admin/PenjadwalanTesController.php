<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\Departemen;
use App\Models\PesertaAlatTes;
use App\Models\PesertaSesiTes;
use App\Models\SesiTes;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PenjadwalanTesController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = in_array((int)$request->input('per_page'), [10, 25, 50, 100]) ? (int)$request->input('per_page') : 10;

        $penjadwalan = SesiTes::withCount([
            'pesertaSesiTes as jumlah_peserta_count',
            'pesertaSesiTes as jumlah_selesai_count' => function ($q) {
                $q->where('status_pengerjaan', 'Selesai');
            }
        ])->with(['departemenTerkait', 'alatTes'])
            ->orderByDesc('tanggal_mulai')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.penjadwalan-tes.index', [
            'penjadwalan' => $penjadwalan,
        ]);
    }

    public function tambah(): View
    {
        $daftarAlatTes = AlatTes::where('is_aktif', true)->orderBy('nama')->get();
        $daftarDepartemen = Departemen::orderBy('nama_departemen')->get();

        return view('admin.penjadwalan-tes.tambah', [
            'daftarAlatTes' => $daftarAlatTes,
            'daftarDepartemen' => $daftarDepartemen,
        ]);
    }

    public function simpan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_sesi'       => 'required|string|max:255',
            'departemen_ids'   => 'nullable|array',
            'departemen_ids.*' => 'exists:departemen,id',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alat_tes_ids'    => 'required|array|min:1',
            'alat_tes_ids.*'  => 'exists:alat_tes,id',
            'status'          => 'required|in:Draft,Aktif,Selesai',
        ]);

        $sesi = SesiTes::create([
            'nama_sesi'             => $validated['nama_sesi'],
            'departemen_terkait_id' => !empty($validated['departemen_ids'])
                                        ? $validated['departemen_ids'][0]
                                        : null,
            'tanggal_mulai'         => $validated['tanggal_mulai'],
            'tanggal_selesai'       => $validated['tanggal_selesai'],
            'status'                => $validated['status'],
            'jumlah_peserta'        => 0,
            'jumlah_selesai'        => 0,
        ]);

        $sesi->alatTes()->sync($validated['alat_tes_ids']);

        return redirect()
            ->route('admin.penjadwalan-tes.index')
            ->with('alert', 'Sesi penjadwalan berhasil dibuat.');
    }

    public function hapus(int $id): RedirectResponse
    {
        SesiTes::findOrFail($id)->delete();

        return redirect()
            ->route('admin.penjadwalan-tes.index')
            ->with('alert', 'Sesi penjadwalan berhasil dihapus.');
    }

    public function detail(int $id): View
    {
        $sesi = SesiTes::with([
            'departemenTerkait',
            'alatTes',
            'pesertaSesiTesRecords.user',
            'pesertaSesiTesRecords.alatTes',
        ])->findOrFail($id);

        $daftarUser = User::whereIn('tipe_akun', ['karyawan', 'kandidat'])
            ->orderBy('name')
            ->get();

        return view('admin.penjadwalan-tes.detail', compact('sesi', 'daftarUser'));
    }

    public function edit(int $id): View
    {
        $sesi = SesiTes::with('alatTes')->findOrFail($id);
        $daftarAlatTes = AlatTes::where('is_aktif', true)->orderBy('nama')->get();
        $daftarDepartemen = Departemen::orderBy('nama_departemen')->get();

        return view('admin.penjadwalan-tes.edit', compact('sesi', 'daftarAlatTes', 'daftarDepartemen'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'nama_sesi'      => 'required|string|max:255',
            'departemen_ids' => 'nullable|array',
            'departemen_ids.*' => 'exists:departemen,id',
            'tanggal_mulai'  => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alat_tes_ids'   => 'nullable|array',
            'alat_tes_ids.*' => 'exists:alat_tes,id',
            'status'         => 'required|in:Draft,Aktif,Selesai',
        ]);

        $sesi = SesiTes::findOrFail($id);
        $sesi->update([
            'nama_sesi'             => $validated['nama_sesi'],
            'departemen_terkait_id' => !empty($validated['departemen_ids'])
                ? $validated['departemen_ids'][0]
                : null,
            'tanggal_mulai'   => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'status'          => $validated['status'],
        ]);
        $sesi->alatTes()->sync($validated['alat_tes_ids'] ?? []);

        return redirect()
            ->route('admin.penjadwalan-tes.detail', $id)
            ->with('alert', 'Sesi penjadwalan berhasil diperbarui.');
    }

    public function tambahPeserta(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'user_ids'        => 'required|array|min:1',
            'user_ids.*'      => 'exists:users,id',
            'alat_tes_ids'    => 'required|array|min:1',
            'alat_tes_ids.*'  => 'exists:alat_tes,id',
        ]);

        $sesi = SesiTes::findOrFail($id);
        $ditambahkan = 0;
        $sudahAda = 0;

        DB::transaction(function () use ($validated, $sesi, &$ditambahkan, &$sudahAda) {
            foreach ($validated['user_ids'] as $userId) {
                $existing = PesertaSesiTes::where('sesi_tes_id', $sesi->id)
                    ->where('user_id', $userId)
                    ->first();
                if ($existing) {
                    $sudahAda++;
                    continue;
                }
                $peserta = PesertaSesiTes::create([
                    'user_id'           => $userId,
                    'sesi_tes_id'       => $sesi->id,
                    'status_pengerjaan' => 'Belum Mengerjakan',
                ]);
                foreach ($validated['alat_tes_ids'] as $alatTesId) {
                    PesertaAlatTes::create([
                        'peserta_sesi_tes_id' => $peserta->id,
                        'alat_tes_id'         => $alatTesId,
                    ]);
                }
                $ditambahkan++;
            }
            $sesi->increment('jumlah_peserta', $ditambahkan);
        });

        $pesan = "$ditambahkan peserta berhasil ditambahkan.";
        if ($sudahAda > 0) {
            $pesan .= " $sudahAda peserta dilewati (sudah ada di sesi).";
        }

        return redirect()
            ->route('admin.penjadwalan-tes.detail', $id)
            ->with('sukses', $pesan);
    }

    public function hapusPeserta(int $id, int $userId): RedirectResponse
    {
        $peserta = PesertaSesiTes::where('sesi_tes_id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $peserta->delete();
        SesiTes::findOrFail($id)->decrement('jumlah_peserta');

        return redirect()
            ->route('admin.penjadwalan-tes.detail', $id)
            ->with('alert', 'Peserta berhasil dihapus.');
    }
}
