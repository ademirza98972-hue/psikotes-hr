@extends('layouts.admin', ['judulHalaman' => 'Ubah Akun Karyawan'])

@section('content')
@php
    $profilKaryawan = $pengguna->profilKaryawan;
@endphp
<div>
    <div class="w-full rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Ubah Akun: {{ $pengguna->name }}</h2>
            <a href="{{ route('admin.akun-karyawan.index') }}" class="text-sm text-slate-600 hover:text-slate-800">&larr; Kembali</a>
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

        <form method="POST" action="{{ route('admin.akun-karyawan.perbarui', $pengguna->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Akun</p>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $pengguna->email) }}" required
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
                <div>
                    <label for="no_hp" class="block text-sm font-medium text-slate-700">No HP</label>
                    <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp', $pengguna->no_hp) }}" required
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                </div>
            </div>

            <div class="space-y-4 border-t border-slate-100 pt-4">
                <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Data Karyawan</p>
                <div>
                    <label class="block text-sm font-medium text-slate-700">NIK</label>
                    <input type="text" value="{{ optional($profilKaryawan)->nik_karyawan }}" disabled
                           class="mt-1 block w-full cursor-not-allowed rounded-md border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-500 shadow-sm">
                    <p class="mt-1 text-xs text-slate-500">NIK tidak dapat diubah setelah akun dibuat.</p>
                </div>
                <div>
                    <label for="nama_karyawan" class="block text-sm font-medium text-slate-700">Nama Karyawan</label>
                    <input id="nama_karyawan" name="nama_karyawan" type="text" value="{{ old('nama_karyawan', optional($profilKaryawan)->nama_karyawan) }}" required
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    <p class="mt-1 text-xs text-slate-500">Nama ini akan digunakan untuk nama tampilan akun dan data profil karyawan.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="departemen" class="block text-sm font-medium text-slate-700">Departemen</label>
                        <select id="departemen" name="departemen" required
                                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departemen as $item)
                                <option value="{{ $item->id }}" @if(old('departemen', $currentDepartemenId) == $item->id) selected @endif>{{ $item->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="posisi" class="block text-sm font-medium text-slate-700">Jabatan <span class="text-slate-400">(opsional)</span></label>
                        <select id="posisi" name="jabatan"
                                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach($posisi as $item)
                                <option value="{{ $item->id }}" @if(old('jabatan', $currentPosisiId) == $item->id) selected @endif>{{ $item->nama_posisi }}</option>
                            @endforeach
                        </select>
                    </div>
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
                <a href="{{ route('admin.akun-karyawan.index') }}" class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</a>
                <button type="submit" class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const departemenSelect = document.getElementById('departemen');
    const posisiSelect = document.getElementById('posisi');

    const posisiUrl = '{{ route("api.posisi.daftar", ":deptId") }}'.replace(':deptId', '{departemenId}');

    departemenSelect.addEventListener('change', function () {
        const deptId = this.value;
        posisiSelect.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
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

                // Re-select the saved position after loading
                const savedId = '{{ $currentPosisiId ?? "" }}';
                if (savedId) {
                    posisiSelect.value = savedId;
                }
            });
    });
});
</script>
@endsection
