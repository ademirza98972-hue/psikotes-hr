<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Posisi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosisiController extends Controller
{
    public function index(): View
    {
        $departemen = Departemen::orderBy('nama_departemen')->get();
        $posisi = Posisi::with('departemen')->orderBy('departemen_id')->orderBy('nama_posisi')->paginate(15)->withQueryString();
        return view('admin.posisi.index', compact('posisi', 'departemen'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_posisi' => 'required|string|max:100',
            'departemen_id' => 'required|exists:departemen,id',
        ]);

        Posisi::updateOrCreate(
            ['nama_posisi' => $request->nama_posisi, 'departemen_id' => $request->departemen_id],
            ['nama_posisi' => $request->nama_posisi, 'departemen_id' => $request->departemen_id]
        );

        return redirect()->route('admin.posisi.index')
            ->with('sukses', 'Data posisi berhasil ditambahkan.');
    }

    public function update(Request $request, Posisi $posisi): RedirectResponse
    {
        $request->validate([
            'nama_posisi' => 'required|string|max:100',
            'departemen_id' => 'required|exists:departemen,id',
        ]);

        $posisi->update($request->only('nama_posisi', 'departemen_id'));

        return redirect()->route('admin.posisi.index')
            ->with('sukses', 'Data posisi berhasil diperbarui.');
    }

    public function destroy(Posisi $posisi): RedirectResponse
    {
        $posisi->delete();

        return redirect()->route('admin.posisi.index')
            ->with('sukses', 'Data posisi berhasil dihapus.');
    }

    public function daftarByDepartemen($departemenId)
    {
        $posisi = Posisi::where('departemen_id', $departemenId)
            ->orderBy('nama_posisi')
            ->get(['id', 'nama_posisi']);

        return response()->json($posisi);
    }
}