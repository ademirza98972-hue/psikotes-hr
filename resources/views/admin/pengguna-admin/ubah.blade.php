@extends('layouts.admin', ['judulHalaman' => 'Ubah Admin/Staff'])

@section('content')
@php
    $isSuperAdmin = $pengguna->peran && $pengguna->peran->nama_peran === 'Super Admin';
@endphp
<div class="max-w-2xl mx-auto space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-[24px] font-semibold text-[#00303c]">Ubah Pengguna: {{ $pengguna->name }}</h2>
            <p class="mt-0.5 text-[13px] text-[#40484b]">Perbarui informasi profil dan hak akses pengguna sistem.</p>
        </div>
        <a href="{{ route('admin.pengguna-admin.index') }}"
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

    {{-- PROFILE CARD --}}
    <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm">
        <div class="border-b border-[#e0e3e5] px-6 py-4">
            <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Informasi Pengguna</p>
        </div>
        <div class="px-6 py-5 flex items-center gap-5">
            <div class="w-14 h-14 rounded-full {{ $isSuperAdmin ? 'bg-[#001a22] text-white' : 'bg-[#001a22]/10 text-[#001a22]' }} flex items-center justify-center font-bold text-[18px] border-2 border-[#e0e3e5] shrink-0">
                {{ strtoupper(mb_substr(explode(' ', $pengguna->name)[0], 0, 1, 'UTF-8') . (strlen(trim(explode(' ', $pengguna->name)[1] ?? '')) > 0 ? mb_substr(explode(' ', $pengguna->name)[1], 0, 1, 'UTF-8') : '')) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[15px] font-semibold text-[#191c1e]">{{ $pengguna->name }}</p>
                <p class="text-[12px] text-[#40484b] mt-0.5">{{ $pengguna->peran->nama_peran ?? '-' }} &middot; Terdaftar {{ $pengguna->created_at->format('M Y') }}</p>
                <p class="text-[12px] text-[#40484b] mt-0.5">ID: USR-{{ str_pad($pengguna->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
            @if($pengguna->status === 'aktif')
                <div class="flex items-center gap-2 bg-[#f0fdf4] border border-green-200 px-3 py-1.5 rounded-full shrink-0">
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                    <span class="text-[12px] font-bold text-green-700">AKTIF</span>
                </div>
            @else
                <div class="flex items-center gap-2 bg-[#f5f5f5] border border-[#c0c8cb] px-3 py-1.5 rounded-full shrink-0">
                    <div class="w-2 h-2 rounded-full bg-[#757575]"></div>
                    <span class="text-[12px] font-bold text-[#757575]">NONAKTIF</span>
                </div>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('admin.pengguna-admin.perbarui', $pengguna->id) }}">
        @csrf
        @method('PUT')

        {{-- SECTION: Data Personal --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm">
            <div class="border-b border-[#e0e3e5] px-6 py-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">Data Personal</p>
            </div>
            <div class="space-y-5 px-6 py-5">
                <div>
                    <label for="peran_id" class="block text-[13px] font-medium text-[#191c1e] mb-1">Peran</label>
                    <select id="peran_id" name="peran_id" required
                            class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 appearance-none cursor-pointer">
                        <option value="">-- Pilih Peran Internal --</option>
                        @foreach ($daftarPeran as $peran)
                            <option value="{{ $peran->id }}" {{ (string)old('peran_id', $pengguna->peran_id) === (string)$peran->id ? 'selected' : '' }}>
                                {{ $peran->nama_peran }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="name" class="block text-[13px] font-medium text-[#191c1e] mb-1">Nama Lengkap</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $pengguna->name) }}" required
                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 @error('name') border-rose-400 @enderror">
                </div>
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
                    <label class="block text-[13px] font-medium text-[#191c1e] mb-2">Status Pengguna</label>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="status" value="aktif" {{ old('status', $pengguna->status) === 'aktif' ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#2C5F6F] border-[#c0c8cb] focus:ring-[#2C5F6F]">
                            <span class="text-sm text-[#191c1e] group-hover:text-[#2C5F6F] transition-colors">Aktif</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="status" value="nonaktif" {{ old('status', $pengguna->status) === 'nonaktif' ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#2C5F6F] border-[#c0c8cb] focus:ring-[#2C5F6F]">
                            <span class="text-sm text-[#191c1e] group-hover:text-[#2C5F6F] transition-colors">Nonaktif</span>
                        </label>
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
                    <div class="relative">
                        <input id="password" name="password" type="password" minlength="8"
                               placeholder="Minimal 8 karakter"
                               class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 pr-11 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20">
                        <button type="button" onclick="togglePassword('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#40484b] hover:text-[#191c1e] transition-colors">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-[13px] font-medium text-[#191c1e] mb-1">Konfirmasi Password</label>
                    <div class="relative">
                        <input id="password_confirmation" name="password_confirmation" type="password" minlength="8"
                               placeholder="Ulangi password"
                               class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 pr-11 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20">
                        <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#40484b] hover:text-[#191c1e] transition-colors">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ACTION BAR --}}
        <div class="sticky bottom-0 mt-4 flex items-center justify-end gap-3 rounded-xl border border-[#e0e3e5] bg-white/95 px-6 py-4 shadow-[0_-2px_12px_rgba(0,0,0,0.06)] backdrop-blur">
            <a href="{{ route('admin.pengguna-admin.index') }}"
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
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('.material-symbols-outlined');
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility';
    }
}
</script>

<style>[x-cloak] { display: none !important; }</style>
@endsection
