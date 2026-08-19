@extends('layouts.admin', ['judulHalaman' => 'Tambah Akun Kandidat'])

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-[24px] font-semibold text-[#00303c]">Tambah Akun Kandidat</h2>
            <p class="mt-0.5 text-[13px] text-[#40484b]">Daftarkan akun kandidat baru ke sistem.</p>
        </div>
        <a href="{{ route('admin.data-kandidat.index') }}"
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

    <form method="POST" action="{{ route('admin.data-kandidat.simpan') }}">
        @csrf

        {{-- SECTION: Akun --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm">
            <div class="border-b border-[#e0e3e5] px-6 py-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Informasi Akun</p>
            </div>
            <div class="space-y-5 px-6 py-5">
                <div>
                    <label for="nik_kandidat" class="block text-[13px] font-medium text-[#191c1e] mb-1">NIK KTP <span class="text-rose-600">*</span></label>
                    <input id="nik_kandidat" name="nik_kandidat" type="text" value="{{ old('nik_kandidat') }}" maxlength="16" required
                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 @error('nik_kandidat') border-rose-400 @enderror">
                </div>
                <div>
                    <label for="nama_kandidat" class="block text-[13px] font-medium text-[#191c1e] mb-1">Nama Kandidat</label>
                    <input id="nama_kandidat" name="nama_kandidat" type="text" value="{{ old('nama_kandidat') }}" required
                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 @error('nama_kandidat') border-rose-400 @enderror">
                </div>
                <div>
                    <label for="email" class="block text-[13px] font-medium text-[#191c1e] mb-1">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 @error('email') border-rose-400 @enderror">
                </div>
                <div>
                    <label for="no_hp" class="block text-[13px] font-medium text-[#191c1e] mb-1">No HP</label>
                    <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp') }}" required
                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 @error('no_hp') border-rose-400 @enderror">
                </div>
                <div>
                    <label for="jenis_kelamin" class="block text-[13px] font-medium text-[#191c1e] mb-1">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" required
                            class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 appearance-none cursor-pointer">
                        <option value="">-- Pilih --</option>
                        <option value="L" @if(old('jenis_kelamin') == 'L') selected @endif>Laki-laki</option>
                        <option value="P" @if(old('jenis_kelamin') == 'P') selected @endif>Perempuan</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- SECTION: Password --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm mt-4">
            <div class="border-b border-[#e0e3e5] px-6 py-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Password</p>
            </div>
            <div class="space-y-5 px-6 py-5">
                <div>
                    <label for="password" class="block text-[13px] font-medium text-[#191c1e] mb-1">Password</label>
                    <input id="password" name="password" type="password" minlength="8" required
                           placeholder="Minimal 8 karakter"
                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 @error('password') border-rose-400 @enderror">
                    <p class="mt-1.5 text-[11px] text-[#40484b]">Minimal 8 karakter.</p>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-[13px] font-medium text-[#191c1e] mb-1">Konfirmasi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" required
                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20">
                </div>
            </div>
        </div>

        {{-- SECTION: Profil Kandidat --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm mt-4">
            <div class="border-b border-[#e0e3e5] px-6 py-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Profil Kandidat</p>
            </div>
            <div class="space-y-5 px-6 py-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="departemen" class="block text-[13px] font-medium text-[#191c1e] mb-1">Departemen</label>
                        <select id="departemen" name="departemen" required
                                class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 appearance-none cursor-pointer">
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departemen as $item)
                                <option value="{{ $item->id }}" @if(old('departemen') == $item->id) selected @endif>{{ $item->nama_departemen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="posisi_dilamar" class="block text-[13px] font-medium text-[#191c1e] mb-1">Posisi yang Dilamar</label>
                        <select id="posisi_dilamar" name="posisi_dilamar" required
                                class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 appearance-none cursor-pointer">
                            <option value="">-- Pilih Posisi --</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="pendidikan_terakhir" class="block text-[13px] font-medium text-[#191c1e] mb-1">Pendidikan Terakhir</label>
                    <input id="pendidikan_terakhir" name="pendidikan_terakhir" type="text" value="{{ old('pendidikan_terakhir') }}" required
                           placeholder="contoh: S1 Psikologi"
                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 @error('pendidikan_terakhir') border-rose-400 @enderror">
                </div>
            </div>
        </div>

        {{-- INFO NOTE --}}
        <div class="rounded-xl border border-sky-200 bg-sky-50 px-5 py-4 mt-4">
            <p class="text-sm text-sky-800"><span class="font-semibold">Catatan:</span> Kandidat yang didaftarkan oleh HR otomatis berstatus <span class="font-semibold">Aktif</span> dan tidak memerlukan verifikasi manual.</p>
        </div>

        {{-- ACTION BAR --}}
        <div class="sticky bottom-0 mt-4 flex items-center justify-end gap-3 rounded-xl border border-[#e0e3e5] bg-white/95 px-6 py-4 shadow-[0_-2px_12px_rgba(0,0,0,0.06)] backdrop-blur">
            <a href="{{ route('admin.data-kandidat.index') }}"
               class="rounded-xl border border-[#c0c8cb] px-5 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">Batal</a>
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#1E414C] transition-all active:scale-95">
                <span class="material-symbols-outlined text-[16px]">save</span>
                Simpan
            </button>
        </div>
    </form>
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
