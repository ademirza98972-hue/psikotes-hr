@extends('layouts.admin', ['judulHalaman' => 'Ubah Akun Kandidat'])

@section('content')
<div>
    <div class="w-full rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Ubah Akun: {{ $kandidat->name }}</h2>
            <a href="{{ route('admin.data-kandidat.index') }}" class="text-sm text-slate-600 hover:text-slate-800">&larr; Kembali</a>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-md border border-rose-600 bg-rose-600 px-4 py-3 text-sm text-white">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.data-kandidat.perbarui', $kandidat->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Akun</p>
                <div>
                    <label for="nama_kandidat" class="block text-sm font-medium text-slate-700">Nama Kandidat</label>
                    <input id="nama_kandidat" name="nama_kandidat" type="text" value="{{ old('nama_kandidat', $profilKandidat->nama_kandidat ?? $kandidat->name) }}" required
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $kandidat->email) }}" required
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
                <div>
                    <label for="no_hp" class="block text-sm font-medium text-slate-700">No HP</label>
                    <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp', $kandidat->no_hp) }}" required
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
            </div>

            <div class="space-y-4 border-t border-slate-100 pt-4">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Profil Kandidat</p>
                <div>
                    <label for="posisi_dilamar" class="block text-sm font-medium text-slate-700">Posisi yang Dilamar</label>
                    <input id="posisi_dilamar" name="posisi_dilamar" type="text" value="{{ old('posisi_dilamar', $profilKandidat->posisi_dilamar ?? '') }}" required
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
                <div>
                    <label for="pendidikan_terakhir" class="block text-sm font-medium text-slate-700">Pendidikan Terakhir</label>
                    <input id="pendidikan_terakhir" name="pendidikan_terakhir" type="text" value="{{ old('pendidikan_terakhir', $profilKandidat->pendidikan_terakhir ?? '') }}" required
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
                <div>
                    <label for="nik_kandidat" class="block text-sm font-medium text-slate-700">NIK KTP <span class="text-slate-400">(opsional)</span></label>
                    <input id="nik_kandidat" name="nik_kandidat" type="text" value="{{ old('nik_kandidat', $profilKandidat->nik_kandidat ?? '') }}" maxlength="30"
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
            </div>

            <div class="space-y-4 border-t border-slate-100 pt-4">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Password Baru <span class="font-normal normal-case text-slate-400">(kosongkan jika tidak ingin mengubah)</span></p>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <input id="password" name="password" type="password" minlength="8"
                           placeholder="Minimal 8 karakter"
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm placeholder:text-slate-400 focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" minlength="8"
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
            </div>

            <div class="sticky bottom-0 -mx-6 mt-4 flex justify-end gap-2 border-t border-slate-200 bg-white px-6 py-3 shadow-[0_-2px_4px_rgba(0,0,0,0.04)]">
                <a href="{{ route('admin.data-kandidat.index') }}" class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</a>
                <button type="submit" class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">Perbarui</button>
            </div>
        </form>
    </div>
</div>
@endsection