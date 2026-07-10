<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerbaruiProfilRequest;
use App\Http\Requests\UbahPasswordRequest;
use App\Http\Requests\UnggahFotoProfilRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function index(): View
    {
        $user = Auth::user()->load(['profilKaryawan.dataKaryawan', 'profilKandidat']);

        return view('profil.index', [
            'judulHalaman' => 'Edit Profil',
            'user'         => $user,
        ]);
    }

    public function perbarui(PerbaruiProfilRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $user->no_hp = $request->input('no_hp');
        $user->save();

        return redirect()
            ->route('profil.index')
            ->with('sukses', 'Data diri berhasil diperbarui.');
    }

    public function ubahPassword(UbahPasswordRequest $request): RedirectResponse
    {
        $user = Auth::user();

        if (! Hash::check($request->input('password_lama'), $user->password)) {
            throw ValidationException::withMessages([
                'password_lama' => 'Password lama yang Anda masukkan salah.',
            ]);
        }

        if (Hash::check($request->input('password_baru'), $user->password)) {
            throw ValidationException::withMessages([
                'password_baru' => 'Password baru tidak boleh sama dengan password lama.',
            ]);
        }

        $user->password = Hash::make($request->input('password_baru'));
        $user->save();

        return redirect()
            ->route('profil.index')
            ->with('sukses', 'Password berhasil diubah.');
    }

    public function unggahFoto(UnggahFotoProfilRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $file = $request->file('foto_profil');

        $extension = $file->getClientOriginalExtension() ?: $file->extension();
        $namaFile  = 'user_' . $user->id . '_' . time() . '.' . strtolower($extension);

        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $path = $file->storeAs('foto-profil', $namaFile, 'public');

        $user->foto_profil = $path;
        $user->save();

        return redirect()
            ->route('profil.index')
            ->with('sukses', 'Foto profil berhasil diperbarui.');
    }
}