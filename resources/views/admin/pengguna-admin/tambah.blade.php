@extends('layouts.admin', ['judulHalaman' => 'Tambah Admin/Staff'])

@section('content')
<div class="mb-8">
    <a href="{{ route('admin.pengguna-admin.index') }}" class="inline-flex items-center gap-1 text-[#2C5F6F] text-[14px] font-medium hover:underline transition-all mb-2">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali
    </a>
    <h2 class="text-[24px] font-bold text-[#001a22] leading-snug">Tambah Pengguna Internal</h2>
</div>

{{-- FORM CONTAINER --}}
<div class="bg-white border border-[#c0c8cb] rounded-xl overflow-hidden shadow-sm">
    <form method="POST" action="{{ route('admin.pengguna-admin.simpan') }}" class="p-8 space-y-8">
        @csrf

        @if ($errors->any())
            <div class="mb-0 rounded-lg border border-[#ba1a1a] bg-[#fef2f2] px-4 py-3 text-[14px] text-[#93000a]">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ROLE SECTION --}}
        <div class="space-y-3 max-w-2xl">
            <label class="block text-[11px] font-semibold uppercase tracking-widest text-[#41484b]">Peran</label>
            <select id="peran_id" name="peran_id" required
                    class="w-full bg-[#f7f9fb] border border-[#c0c8cb] rounded-xl px-4 py-3 text-[14px] text-[#191c1e] focus:border-[#2C5F6F] focus:ring-2 focus:ring-[#2C5F6F] transition-all outline-none">
                <option value="">-- Pilih Peran Internal --</option>
                @foreach ($daftarPeran as $peran)
                    <option value="{{ $peran->id }}" {{ (string)old('peran_id') === (string)$peran->id ? 'selected' : '' }}>
                        {{ $peran->nama_peran }}
                    </option>
                @endforeach
            </select>
            <p class="text-[12px] text-[#41484b] flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">info</span>
                Hanya peran internal (bukan Kandidat/Karyawan) yang tersedia di sini.
            </p>
        </div>

        <hr class="border-[#c0c8cb] opacity-30">

        {{-- ACCOUNT SECTION --}}
        <div class="space-y-5">
            <h3 class="text-[11px] font-semibold uppercase tracking-widest text-[#41484b] border-l-4 border-[#2C5F6F] pl-3">Akun</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="name" class="block text-[12px] font-medium text-[#191c1e]">Nama Lengkap</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required
                           class="w-full bg-[#f7f9fb] border border-[#c0c8cb] rounded-xl px-4 py-3 text-[14px] text-[#191c1e] focus:border-[#2C5F6F] focus:ring-2 focus:ring-[#2C5F6F] transition-all outline-none placeholder:text-[#919eab]"
                           placeholder="Contoh: Budi Santoso">
                </div>
                <div class="space-y-2">
                    <label for="email" class="block text-[12px] font-medium text-[#191c1e]">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                           class="w-full bg-[#f7f9fb] border border-[#c0c8cb] rounded-xl px-4 py-3 text-[14px] text-[#191c1e] focus:border-[#2C5F6F] focus:ring-2 focus:ring-[#2C5F6F] transition-all outline-none placeholder:text-[#919eab]"
                           placeholder="email@perusahaan.com">
                </div>
                <div class="space-y-2">
                    <label for="no_hp" class="block text-[12px] font-medium text-[#191c1e]">No HP</label>
                    <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp') }}" required
                           class="w-full bg-[#f7f9fb] border border-[#c0c8cb] rounded-xl px-4 py-3 text-[14px] text-[#191c1e] focus:border-[#2C5F6F] focus:ring-2 focus:ring-[#2C5F6F] transition-all outline-none placeholder:text-[#919eab]"
                           placeholder="081234567890">
                </div>
                <div class="space-y-2">
                    <label for="status" class="block text-[12px] font-medium text-[#191c1e]">Status</label>
                    <select id="status" name="status" required
                            class="w-full bg-[#f7f9fb] border border-[#c0c8cb] rounded-xl px-4 py-3 text-[14px] text-[#191c1e] focus:border-[#2C5F6F] focus:ring-2 focus:ring-[#2C5F6F] transition-all outline-none">
                        <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>
        </div>

        <hr class="border-[#c0c8cb] opacity-30">

        {{-- PASSWORD SECTION --}}
        <div class="space-y-5">
            <h3 class="text-[11px] font-semibold uppercase tracking-widest text-[#41484b] border-l-4 border-[#2C5F6F] pl-3">Password</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="password" class="block text-[12px] font-medium text-[#191c1e]">Password</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" required minlength="8"
                               class="w-full bg-[#f7f9fb] border border-[#c0c8cb] rounded-xl px-4 py-3 text-[14px] text-[#191c1e] focus:border-[#2C5F6F] focus:ring-2 focus:ring-[#2C5F6F] transition-all outline-none pr-12 placeholder:text-[#919eab]"
                               placeholder="Masukkan password">
                        <button type="button" onclick="togglePassword('password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#41484b] hover:text-[#191c1e] transition-colors">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                    <p class="text-[12px] text-[#41484b]">Minimal 8 karakter.</p>
                </div>
                <div class="space-y-2">
                    <label for="password_confirmation" class="block text-[12px] font-medium text-[#191c1e]">Konfirmasi Password</label>
                    <div class="relative">
                        <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8"
                               class="w-full bg-[#f7f9fb] border border-[#c0c8cb] rounded-xl px-4 py-3 text-[14px] text-[#191c1e] focus:border-[#2C5F6F] focus:ring-2 focus:ring-[#2C5F6F] transition-all outline-none pr-12 placeholder:text-[#919eab]"
                               placeholder="Ulangi password">
                        <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-[#41484b] hover:text-[#191c1e] transition-colors">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- FOOTER ACTIONS --}}
        <div class="flex justify-end items-center gap-4 pt-2 border-t border-[#c0c8cb]">
            <a href="{{ route('admin.pengguna-admin.index') }}"
               class="px-6 py-2.5 rounded-xl border border-[#71787b] text-[14px] font-medium text-[#41484b] hover:bg-[#f2f4f6] transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-8 py-2.5 rounded-xl bg-[#2C5F6F] text-white text-[14px] font-semibold hover:opacity-90 active:scale-[0.98] transition-all flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined text-[20px]">save</span>
                Simpan
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
