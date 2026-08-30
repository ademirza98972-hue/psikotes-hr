<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TambahPesertaSesiRequest;
use App\Http\Requests\PerbaruiSesiTesRequest;
use App\Http\Requests\SimpanSesiTesRequest;
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
        $perPage = $this->getPerPage($request, 10);
        $cari = $request->input('cari');
        $filterStatus = $request->input('status');
        $today = now()->toDateString();

        $query = SesiTes::withCount([
            'pesertaSesiTes as jumlah_peserta_count',
            'pesertaSesiTes as jumlah_selesai_count' => fn($q) => $q->where('status_pengerjaan', 'Selesai'),
        ])->with(['departemen', 'alatTes']);

        $query->when($cari, fn($q) => $q->where('nama_sesi', 'like', '%' . $cari . '%'));

        match ($filterStatus) {
            'Draft'    => $query->where('status', 'Draft'),
            'Aktif'    => $query->where('status', '!=', 'Draft')
                                ->where('tanggal_mulai', '<=', $today)
                                ->where('tanggal_selesai', '>=', $today),
            'Selesai'  => $query->where('status', '!=', 'Draft')
                                ->where('tanggal_selesai', '<', $today),
            default    => null,
        };

        $penjadwalan = $query->orderByDesc('tanggal_mulai')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.penjadwalan-tes.index', [
            'penjadwalan'  => $penjadwalan,
            'cari'         => $cari,
            'filterStatus' => $filterStatus,
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

    public function simpan(SimpanSesiTesRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $sesi = SesiTes::create([
            'nama_sesi'       => $validated['nama_sesi'],
            'tanggal_mulai'   => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'status'          => $validated['status'],
            'jumlah_peserta'  => 0,
            'jumlah_selesai'  => 0,
        ]);

        $sesi->departemen()->sync($validated['departemen_ids'] ?? []);
        $sesi->alatTes()->sync($validated['alat_tes_ids']);

        return redirect()
            ->route('admin.penjadwalan-tes.index')
            ->with($this::FLASH_SUCCESS, 'Sesi penjadwalan berhasil dibuat.');
    }

    public function hapus(int $id): RedirectResponse
    {
        SesiTes::findOrFail($id)->delete();

        return redirect()
            ->route('admin.penjadwalan-tes.index')
            ->with($this::FLASH_SUCCESS, 'Sesi penjadwalan berhasil dihapus.');
    }

    public function detail(int $id): View
    {
        $sesi = SesiTes::with([
            'departemen',
            'alatTes',
            'pesertaSesiTesRecords.user',
            'pesertaSesiTesRecords.alatTes',
        ])->findOrFail($id);

        $sudahPeserta = $sesi->pesertaSesiTesRecords->pluck('user_id');

        $daftarUser = User::whereIn('tipe_akun', ['karyawan', 'kandidat'])
            ->whereNotIn('id', $sudahPeserta)
            ->orderBy('name')
            ->get();

        return view('admin.penjadwalan-tes.detail', compact('sesi', 'daftarUser'));
    }

    public function edit(int $id): View
    {
        $sesi = SesiTes::with(['alatTes', 'departemen'])->findOrFail($id);
        $daftarAlatTes = AlatTes::where('is_aktif', true)->orderBy('nama')->get();
        $daftarDepartemen = Departemen::orderBy('nama_departemen')->get();

        return view('admin.penjadwalan-tes.edit', compact('sesi', 'daftarAlatTes', 'daftarDepartemen'));
    }

    public function update(PerbaruiSesiTesRequest $request, int $id): RedirectResponse
    {
        $validated = $request->validated();

        $sesi = SesiTes::findOrFail($id);
        $sesi->update([
            'nama_sesi'       => $validated['nama_sesi'],
            'tanggal_mulai'   => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'status'          => $validated['status'],
        ]);
        $sesi->departemen()->sync($validated['departemen_ids'] ?? []);
        $sesi->alatTes()->sync($validated['alat_tes_ids'] ?? []);

        return redirect()
            ->route('admin.penjadwalan-tes.detail', $id)
            ->with($this::FLASH_SUCCESS, 'Sesi penjadwalan berhasil diperbarui.');
    }

    public function tambahPeserta(TambahPesertaSesiRequest $request, int $id): RedirectResponse
    {
        $validated = $request->validated();

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
            ->with($this::FLASH_SUCCESS, $pesan);
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
            ->with($this::FLASH_SUCCESS, 'Peserta berhasil dihapus.');
    }
}
