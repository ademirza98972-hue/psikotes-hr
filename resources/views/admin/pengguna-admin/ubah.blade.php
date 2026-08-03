@extends('layouts.admin', ['judulHalaman' => 'Ubah Admin/Staff'])

@section('content')
<div class="mb-8">
    <a href="{{ route('admin.pengguna-admin.index') }}" class="inline-flex items-center gap-1 text-[#2C5F6F] text-[14px] font-medium hover:underline transition-all mb-2">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali
    </a>
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-[24px] font-bold text-[#001a22] leading-snug">Ubah Pengguna Internal: {{ $pengguna->name }}</h2>
            <p class="text-[14px] text-[#41484b] mt-1">Perbarui informasi profil dan hak akses pengguna sistem.</p>
        </div>
        @if($pengguna->status === 'aktif')
            <div class="flex items-center gap-2 bg-[#f0fdf4] border border-green-200 px-3 py-1.5 rounded-full">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                <span class="text-[12px] font-bold text-green-700">AKUN AKTIF</span>
            </div>
        @else
            <div class="flex items-center gap-2 bg-[#f5f5f5] border border-[#c0c8cb] px-3 py-1.5 rounded-full">
                <div class="w-2 h-2 rounded-full bg-[#757575]"></div>
                <span class="text-[12px] font-bold text-[#757575]">AKUN NONAKTIF</span>
            </div>
        @endif
    </div>
</div>

@php
    $isSuperAdmin = $pengguna->peran && $pengguna->peran->nama_peran === 'Super Admin';
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- LEFT COLUMN: Profile Card --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white border border-[#c0c8cb] rounded-xl p-6 flex flex-col items-center text-center">
            <div class="w-20 h-20 rounded-full {{ $isSuperAdmin ? 'bg-[#001a22] text-white' : 'bg-[#001a22]/10 text-[#001a22]' }} flex items-center justify-center font-bold text-[20px] border-2 border-[#e0e3e5] mb-4">
                {{ strtoupper(mb_substr(explode(' ', $pengguna->name)[0], 0, 1, 'UTF-8') . (strlen(trim(explode(' ', $pengguna->name)[1] ?? '')) > 0 ? mb_substr(explode(' ', $pengguna->name)[1], 0, 1, 'UTF-8') : '')) }}
            </div>
            <h3 class="text-[16px] font-semibold text-[#191c1e]">{{ $pengguna->name }}</h3>
            <p class="text-[12px] text-[#41484b] mt-1">{{ $pengguna->peran->nama_peran ?? '-' }} &middot; Terdaftar {{ $pengguna->created_at->format('M Y') }}</p>
        </div>
        <div class="bg-white border border-[#c0c8cb] rounded-xl p-6">
            <h4 class="text-[11px] font-semibold uppercase tracking-widest text-[#41484b] mb-4">Informasi Pengguna</h4>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-[14px] text-[#41484b]">ID Pengguna</span>
                    <span class="text-[14px] font-bold text-[#001a22]">USR-{{ str_pad($pengguna->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[14px] text-[#41484b]">Email</span>
                    <span class="text-[14px] text-[#191c1e] text-right max-w-[60%] truncate">{{ $pengguna->email }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[14px] text-[#41484b]">No HP</span>
                    <span class="text-[14px] text-[#191c1e]">{{ $pengguna->no_hp ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN: Form --}}
    <div class="lg:col-span-2 space-y-6">
        @if ($errors->any())
            <div class="rounded-lg border border-[#ba1a1a] bg-[#fef2f2] px-4 py-3 text-[14px] text-[#93000a]">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.pengguna-admin.perbarui', $pengguna->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- PERSONAL DATA SECTION --}}
            <div class="bg-white border border-[#c0c8cb] rounded-xl p-6">
                <h4 class="text-[11px] font-semibold uppercase tracking-widest text-[#41484b] mb-6 pb-2 border-b border-[#c0c8cb]/50">DATA PERSONAL</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="peran_id" class="block text-[12px] font-medium text-[#191c1e]">Peran</label>
                        <select id="peran_id" name="peran_id" required
                                class="w-full bg-[#f7f9fb] border border-[#c0c8cb] rounded-xl px-4 py-3 text-[14px] text-[#191c1e] focus:border-[#2C5F6F] focus:ring-2 focus:ring-[#2C5F6F] transition-all outline-none">
                            <option value="">-- Pilih Peran Internal --</option>
                            @foreach ($daftarPeran as $peran)
                                <option value="{{ $peran->id }}" {{ (string)old('peran_id', $pengguna->peran_id) === (string)$peran->id ? 'selected' : '' }}>
                                    {{ $peran->nama_peran }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label for="name" class="block text-[12px] font-medium text-[#191c1e]">Nama Lengkap</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $pengguna->name) }}" required
                               class="w-full bg-[#f7f9fb] border border-[#c0c8cb] rounded-xl px-4 py-3 text-[14px] text-[#191c1e] focus:border-[#2C5F6F] focus:ring-2 focus:ring-[#2C5F6F] transition-all outline-none">
                    </div>
                    <div class="space-y-2">
                        <label for="email" class="block text-[12px] font-medium text-[#191c1e]">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $pengguna->email) }}" required
                               class="w-full bg-[#f7f9fb] border border-[#c0c8cb] rounded-xl px-4 py-3 text-[14px] text-[#191c1e] focus:border-[#2C5F6F] focus:ring-2 focus:ring-[#2C5F6F] transition-all outline-none">
                    </div>
                    <div class="space-y-2">
                        <label for="no_hp" class="block text-[12px] font-medium text-[#191c1e]">No HP</label>
                        <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp', $pengguna->no_hp) }}" required
                               class="w-full bg-[#f7f9fb] border border-[#c0c8cb] rounded-xl px-4 py-3 text-[14px] text-[#191c1e] focus:border-[#2C5F6F] focus:ring-2 focus:ring-[#2C5F6F] transition-all outline-none">
                    </div>
                    <div class="space-y-2 md:col-span-2">
                        <label class="block text-[12px] font-medium text-[#191c1e]">Status Pengguna</label>
                        <div class="flex gap-6 mt-2">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="status" value="aktif" {{ old('status', $pengguna->status) === 'aktif' ? 'checked' : '' }}
                                       class="w-4 h-4 text-[#2C5F6F] border-[#c0c8cb] focus:ring-[#2C5F6F]">
                                <span class="text-[14px] text-[#191c1e] group-hover:text-[#2C5F6F] transition-colors">Aktif</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="status" value="nonaktif" {{ old('status', $pengguna->status) === 'nonaktif' ? 'checked' : '' }}
                                       class="w-4 h-4 text-[#2C5F6F] border-[#c0c8cb] focus:ring-[#2C5F6F]">
                                <span class="text-[14px] text-[#191c1e] group-hover:text-[#2C5F6F] transition-colors">Nonaktif</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PASSWORD SECTION --}}
            <div class="bg-white border border-[#c0c8cb] rounded-xl p-6 relative overflow-hidden">
                <div class="absolute top-4 right-4 opacity-5">
                    <span class="material-symbols-outlined text-[48px]">lock</span>
                </div>
                <h4 class="text-[11px] font-semibold uppercase tracking-widest text-[#41484b] mb-1">PASSWORD BARU</h4>
                <p class="text-[12px] text-[#41484b] mb-6">(kosongkan jika tidak ingin mengubah)</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="password" class="block text-[12px] font-medium text-[#191c1e]">Password</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" minlength="8"
                                   class="w-full bg-[#f7f9fb] border border-[#c0c8cb] rounded-xl px-4 py-3 text-[14px] text-[#191c1e] focus:border-[#2C5F6F] focus:ring-2 focus:ring-[#2C5F6F] transition-all outline-none pr-12 placeholder:text-[#919eab]"
                                   placeholder="Minimal 8 karakter">
                            <button type="button" onclick="togglePassword('password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#41484b] hover:text-[#191c1e] transition-colors">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label for="password_confirmation" class="block text-[12px] font-medium text-[#191c1e]">Konfirmasi Password</label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" type="password" minlength="8"
                                   class="w-full bg-[#f7f9fb] border border-[#c0c8cb] rounded-xl px-4 py-3 text-[14px] text-[#191c1e] focus:border-[#2C5F6F] focus:ring-2 focus:ring-[#2C5F6F] transition-all outline-none pr-12 placeholder:text-[#919eab]"
                                   placeholder="Ulangi password">
                            <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#41484b] hover:text-[#191c1e] transition-colors">
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FORM ACTIONS --}}
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('admin.pengguna-admin.index') }}"
                   class="px-6 py-2.5 rounded-xl border border-[#71787b] text-[14px] font-medium text-[#41484b] hover:bg-[#f2f4f6] transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-8 py-2.5 rounded-xl bg-[#2C5F6F] text-white text-[14px] font-semibold hover:opacity-90 active:scale-[0.98] transition-all flex items-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">save</span>
                    Perbarui
                </button>
            </div>
        </form>
    </div>
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
