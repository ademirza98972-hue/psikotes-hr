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

        // Sama dengan SesiTes::statusDisplay, tapi dalam bentuk query
        $saringStatus = fn ($q, ?string $status) => match ($status) {
            'Draft'         => $q->where('status', 'Draft'),
            'Belum Dimulai' => $q->where('status', '!=', 'Draft')->where('tanggal_mulai', '>', $today),
            'Aktif'         => $q->where('status', '!=', 'Draft')
                                 ->where('tanggal_mulai', '<=', $today)
                                 ->where('tanggal_selesai', '>=', $today),
            'Selesai'       => $q->where('status', '!=', 'Draft')->where('tanggal_selesai', '<', $today),
            default         => $q,
        };
        $saringStatus($query, $filterStatus);

        $penjadwalan = $query->orderByDesc('tanggal_mulai')
            ->paginate($perPage)
            ->withQueryString();

        $ringkasan = collect(['Aktif', 'Belum Dimulai', 'Draft', 'Selesai'])
            ->mapWithKeys(fn ($st) => [$st => $saringStatus(SesiTes::query(), $st)->count()]);

        return view('admin.penjadwalan-tes.index', [
            'penjadwalan'  => $penjadwalan,
            'cari'         => $cari,
            'filterStatus' => $filterStatus,
            'ringkasan'    => $ringkasan,
            'bukaTambah'   => $request->boolean('tambah'),
        ] + $this->pilihanForm());
    }

    protected function pilihanForm(): array
    {
        return [
            'daftarAlatTes'    => AlatTes::where('is_aktif', true)->orderBy('nama')->get(),
            'daftarDepartemen' => Departemen::orderBy('nama_departemen')->get(),
        ];
    }

    public function tambah(): RedirectResponse
    {
        return redirect()->route('admin.penjadwalan-tes.index', ['tambah' => 1]);
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

    public function detail(Request $request, int $id): View
    {
        $sesi = SesiTes::with([
            'departemen',
            'alatTes',
            'pesertaSesiTesRecords.user',
            'pesertaSesiTesRecords.alatTes',
        ])->findOrFail($id);

        $sudahPeserta = $sesi->pesertaSesiTesRecords->pluck('user_id');

        // Hanya akun aktif: kandidat menunggu/ditolak dan karyawan nonaktif tidak bisa login mengerjakan tes
        $daftarUser = User::whereIn('tipe_akun', ['karyawan', 'kandidat'])
            ->where('status', 'aktif')
            ->whereNotIn('id', $sudahPeserta)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'tipe_akun']);

        return view('admin.penjadwalan-tes.detail', [
            'sesi'      => $sesi,
            'daftarUser' => $daftarUser,
            'bukaUbah'  => $request->boolean('ubah'),
        ] + $this->pilihanForm());
    }

    public function edit(int $id): RedirectResponse
    {
        return redirect()->route('admin.penjadwalan-tes.detail', ['id' => $id, 'ubah' => 1]);
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

        $sesi = SesiTes::with('alatTes')->findOrFail($id);
        if (array_diff($validated['alat_tes_ids'], $sesi->alatTes->pluck('id')->all())) {
            return back()->withErrors(['alat_tes_ids' => 'Alat tes harus dipilih dari alat tes sesi ini.'])->withInput();
        }
        $ditambahkan = 0;
        $sudahAda = 0;

        DB::transaction(function () use ($validated, $sesi, &$ditambahkan, &$sudahAda) {
            $sudahTerdaftar = PesertaSesiTes::where('sesi_tes_id', $sesi->id)
                ->whereIn('user_id', $validated['user_ids'])
                ->pluck('user_id')
                ->flip()
                ->all();

            foreach ($validated['user_ids'] as $userId) {
                if (isset($sudahTerdaftar[$userId])) {
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
            $this->sinkronJumlahPeserta($sesi);
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

        // Menghapus baris ini ikut menghapus log aktivitas (cascade) dan menghilangkan peserta dari Hasil Tes
        if ($peserta->status_pengerjaan !== 'Belum Mengerjakan' || $peserta->waktu_mulai) {
            return back()->with($this::FLASH_ERROR, 'Peserta sudah mulai atau selesai mengerjakan; hasilnya tidak bisa dihapus dari sesi.');
        }

        $peserta->delete();
        $this->sinkronJumlahPeserta(SesiTes::findOrFail($id));

        return redirect()
            ->route('admin.penjadwalan-tes.detail', $id)
            ->with($this::FLASH_SUCCESS, 'Peserta berhasil dihapus.');
    }

    // jumlah_peserta dibaca halaman Hasil Tes; hitung ulang dari data, bukan increment, supaya selisih lama ikut pulih
    protected function sinkronJumlahPeserta(SesiTes $sesi): void
    {
        $sesi->update(['jumlah_peserta' => $sesi->pesertaSesiTesRecords()->count()]);
    }
}
