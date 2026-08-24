<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PerbaruiPosisiRequest;
use App\Http\Requests\SimpanPosisiRequest;
use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Posisi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosisiController extends Controller
{
    public function index(Request $request): View
    {
        $kataKunci = $request->input('cari');
        $filterDepartemenId = $request->input('departemen');

        $query = Posisi::with('departemen')
            ->orderBy('departemen_id')
            ->orderBy('nama_posisi');

        $query->when($kataKunci, function ($q) use ($kataKunci) {
            $q->where('nama_posisi', 'like', '%' . $kataKunci . '%');
        });

        $query->when($filterDepartemenId, function ($q) use ($filterDepartemenId) {
            $q->where('departemen_id', $filterDepartemenId);
        });

        $perPage = $this->getPerPage($request, 15);

        $posisi = $query->paginate($perPage)->withQueryString();
        $departemen = Departemen::orderBy('nama_departemen')->get();

        return view('admin.posisi.index', [
            'posisi' => $posisi,
            'departemen' => $departemen,
            'kataKunci' => $kataKunci,
            'filterDepartemen' => $filterDepartemenId,
        ]);
    }

    public function store(SimpanPosisiRequest $request): RedirectResponse
    {
        Posisi::updateOrCreate(
            ['nama_posisi' => $request->nama_posisi, 'departemen_id' => $request->departemen_id],
            ['nama_posisi' => $request->nama_posisi, 'departemen_id' => $request->departemen_id]
        );

        return redirect()->route('admin.posisi.index')
            ->with($this::FLASH_SUCCESS, 'Data posisi berhasil ditambahkan.');
    }

    public function update(PerbaruiPosisiRequest $request, Posisi $posisi): RedirectResponse
    {
        $posisi->update($request->validated());

        return redirect()->route('admin.posisi.index')
            ->with($this::FLASH_SUCCESS, 'Data posisi berhasil diperbarui.');
    }

    public function destroy(Posisi $posisi): RedirectResponse
    {
        if ($posisi->karyawan()->exists()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus posisi yang masih memiliki karyawan terkait.');
        }

        $posisi->delete();

        return redirect()->route('admin.posisi.index')
            ->with($this::FLASH_SUCCESS, 'Data posisi berhasil dihapus.');
    }

    public function daftarByDepartemen(int $departemenId): JsonResponse
    {
        $posisi = Posisi::where('departemen_id', $departemenId)
            ->orderBy('nama_posisi')
            ->get(['id', 'nama_posisi']);

        return response()->json($posisi);
    }
}