@extends('layouts.admin', ['judulHalaman' => 'Ubah Akun Karyawan'])

@section('content')
@php
    $profilKaryawan = $pengguna->profilKaryawan;
@endphp
<div class="max-w-2xl mx-auto space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-[24px] font-semibold text-[#00303c]">Ubah Akun: {{ $pengguna->name }}</h2>
            <p class="mt-0.5 text-[13px] text-[#40484b]">Perbarui informasi akun karyawan.</p>
        </div>
        <a href="{{ route('admin.akun-karyawan.index') }}"
           class="inline-flex items-center gap-1.5 rounded-xl border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali
        </a>
    </div>

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-5 py-4">
            <p class="mb-2 text-sm font-semibold text-rose-700">Terdapat kesalahan:</p>
            <ul class="list-disc space-y-1 pl-5 text-sm text-rose-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.akun-karyawan.perbarui', $pengguna->id) }}">
        @csrf
        @method('PUT')

        {{-- SECTION: Akun --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm">
            <div class="border-b border-[#e0e3e5] px-6 py-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Informasi Akun</p>
            </div>
            <div class="space-y-5 px-6 py-5">
                <div>
                    <label for="email" class="block text-[13px] font-medium text-[#191c1e] mb-1">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $pengguna->email) }}" required
                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 @error('email') border-rose-400 @enderror">
                </div>
                <div>
                    <label for="no_hp" class="block text-[13px] font-medium text-[#191c1e] mb-1">No HP</label>
                    <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp', $pengguna->no_hp) }}" required
                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 @error('no_hp') border-rose-400 @enderror">
                </div>
                <div>
                    <label for="jenis_kelamin" class="block text-[13px] font-medium text-[#191c1e] mb-1">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required
                            class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 appearance-none cursor-pointer">
                        <option value="">-- Pilih --</option>
                        <option value="L" @if(old('jenis_kelamin', $pengguna->jenis_kelamin) == 'L') selected @endif>Laki-laki</option>
                        <option value="P" @if(old('jenis_kelamin', $pengguna->jenis_kelamin) == 'P') selected @endif>Perempuan</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- SECTION: Data Karyawan --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm mt-4">
            <div class="border-b border-[#e0e3e5] px-6 py-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Data Karyawan</p>
            </div>
            <div class="space-y-5 px-6 py-5">
                <div>
                    <label class="block text-[13px] font-medium text-[#191c1e] mb-1">NIK</label>
                    <input type="text" value="{{ optional($profilKaryawan)->nik_karyawan }}" disabled
                           class="block w-full cursor-not-allowed rounded-lg border border-[#c0c8cb] bg-[#f2f4f6] px-3 py-2.5 text-sm text-[#40484b] shadow-sm">
                    <p class="mt-1.5 text-[11px] text-[#40484b]">NIK tidak dapat diubah setelah akun dibuat.</p>
                </div>
                <div>
                    <label for="nama_karyawan" class="block text-[13px] font-medium text-[#191c1e] mb-1">Nama Karyawan</label>
                    <input id="nama_karyawan" name="nama_karyawan" type="text" value="{{ old('nama_karyawan', optional($profilKaryawan)->nama_karyawan) }}" required
                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 @error('nama_karyawan') border-rose-400 @enderror">
                    <p class="mt-1.5 text-[11px] text-[#40484b]">Nama ini akan digunakan untuk nama tampilan akun dan data profil karyawan.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="departemen" class="block text-[13px] font-medium text-[#191c1e] mb-1">Departemen</label>
                        <select id="departemen" name="departemen" required
                                class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 appearance-none cursor-pointer">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departemen as $item)
                                <option value="{{ $item->id }}" @if(old('departemen', $currentDepartemenId) == $item->id) selected @endif>{{ $item->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="posisi" class="block text-[13px] font-medium text-[#191c1e] mb-1">
                            Jabatan <span class="text-[#40484b] font-normal">(opsional)</span>
                        </label>
                        <select id="posisi" name="jabatan"
                                class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 appearance-none cursor-pointer">
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach($posisi as $item)
                                <option value="{{ $item->id }}" @if(old('jabatan', $currentPosisiId) == $item->id) selected @endif>{{ $item->nama_posisi }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION: Password --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm mt-4">
            <div class="border-b border-[#e0e3e5] px-6 py-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Password Baru <span class="font-normal normal-case text-[#40484b]">(kosongkan jika tidak ingin mengubah)</span></p>
            </div>
            <div class="space-y-5 px-6 py-5">
                <div>
                    <label for="password" class="block text-[13px] font-medium text-[#191c1e] mb-1">Password</label>
                    <input id="password" name="password" type="password" minlength="8"
                           placeholder="Minimal 8 karakter"
                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 @error('password') border-rose-400 @enderror">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-[13px] font-medium text-[#191c1e] mb-1">Konfirmasi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" minlength="8"
                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20">
                </div>
            </div>
        </div>

        {{-- ACTION BAR --}}
        <div class="sticky bottom-0 mt-4 flex items-center justify-end gap-3 rounded-xl border border-[#e0e3e5] bg-white/95 px-6 py-4 shadow-[0_-2px_12px_rgba(0,0,0,0.06)] backdrop-blur">
            <a href="{{ route('admin.akun-karyawan.index') }}"
               class="rounded-xl border border-[#c0c8cb] px-5 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">Batal</a>
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#1E414C] transition-all active:scale-95">
                <span class="material-symbols-outlined text-[16px]">save</span>
                Perbarui
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const departemenSelect = document.getElementById('departemen');
    const posisiSelect = document.getElementById('posisi');

    const posisiUrl = '{{ str_replace("__DEPT_ID__", "{departemenId}", route("api.posisi.daftar", ["departemen" => "__DEPT_ID__"])) }}';

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
