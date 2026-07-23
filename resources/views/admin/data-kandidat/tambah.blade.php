@extends('layouts.admin', ['judulHalaman' => 'Tambah Akun Kandidat'])

@section('content')
<div>
    <div class="w-full rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Tambah Akun Kandidat</h2>
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

        <form method="POST" action="{{ route('admin.data-kandidat.simpan') }}" class="space-y-5">
            @csrf

            <div class="space-y-4">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Akun</p>
                <div>
                    <label for="nama_kandidat" class="block text-sm font-medium text-slate-700">Nama Kandidat</label>
                    <input id="nama_kandidat" name="nama_kandidat" type="text" value="{{ old('nama_kandidat') }}" required
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
                <div>
                    <label for="no_hp" class="block text-sm font-medium text-slate-700">No HP</label>
                    <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp') }}" required
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <input id="password" name="password" type="password" minlength="8" required
                           placeholder="Minimal 8 karakter"
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm placeholder:text-slate-400 focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" required
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
            </div>

            <div class="space-y-4 border-t border-slate-100 pt-4">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Profil Kandidat</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="departemen" class="block text-sm font-medium text-slate-700">Departemen</label>
                        <select id="departemen" name="departemen" required
                                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departemen as $item)
                                <option value="{{ $item->id }}" @if(old('departemen') == $item->id) selected @endif>{{ $item->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="posisi_dilamar" class="block text-sm font-medium text-slate-700">Posisi yang Dilamar</label>
                        <select id="posisi_dilamar" name="posisi_dilamar" required
                                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                            <option value="">-- Pilih Posisi --</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="pendidikan_terakhir" class="block text-sm font-medium text-slate-700">Pendidikan Terakhir</label>
                    <input id="pendidikan_terakhir" name="pendidikan_terakhir" type="text" value="{{ old('pendidikan_terakhir') }}" required
                           placeholder="contoh: S1 Psikologi"
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm placeholder:text-slate-400 focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
                <div>
                    <label for="nik_kandidat" class="block text-sm font-medium text-slate-700">NIK KTP <span class="text-rose-600">*</span></label>
                    <input id="nik_kandidat" name="nik_kandidat" type="text" value="{{ old('nik_kandidat') }}" maxlength="16" required
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
            </div>

            <div class="rounded-md border border-sky-600 bg-sky-500 px-4 py-3 text-sm text-white">
                <span class="font-medium text-white">Catatan:</span> Kandidat yang didaftarkan oleh HR otomatis berstatus
                <span class="font-semibold text-white">Aktif</span> dan tidak memerlukan verifikasi manual.
            </div>

            <div class="sticky bottom-0 -mx-6 mt-4 flex justify-end gap-2 border-t border-slate-200 bg-white px-6 py-3 shadow-[0_-2px_4px_rgba(0,0,0,0.04)]">
                <a href="{{ route('admin.data-kandidat.index') }}" class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</a>
                <button type="submit" class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const departemenSelect = document.getElementById('departemen');
    const posisiSelect = document.getElementById('posisi_dilamar');

    const posisiUrl = '{{ str_replace("__DEPT_ID__", "{departemenId}", route("api.posisi.daftar", ["departemen" => "__DEPT_ID__"])) }}';

    departemenSelect.addEventListener('change', function () {
        const deptId = this.value;
        posisiSelect.innerHTML = '<option value="">-- Pilih Posisi --</option>';
        if (!deptId) { return; }

        fetch(posisiUrl.replace('{departemenId}', deptId))
            .then(r => r.json())
            .then(data => {
                data.forEach(function (p) {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = p.nama_posisi;
                    posisiSelect.appendChild(opt);
                });
            });
    });
});
</script>
@endsection
