@extends('layouts.admin', ['judulHalaman' => 'Ubah Akun Kandidat'])

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-[24px] font-semibold text-foreground">Ubah Akun: {{ $kandidat->name }}</h2>
            <p class="mt-0.5 text-[13px] text-[#40484b]">Perbarui informasi akun kandidat.</p>
        </div>
        <a href="{{ route('admin.data-kandidat.index') }}"
           class="inline-flex items-center gap-1.5 rounded-xl border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali
        </a>
    </div>

    @if ($errors->any())
    <div class="rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
        <ul class="list-disc space-y-1 pl-4">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.data-kandidat.perbarui', $kandidat->id) }}">
        @csrf
        @method('PUT')

        {{-- SECTION: Informasi Akun --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm">
            <div class="border-b border-[#e0e3e5] px-6 py-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-primary">Informasi Akun</p>
            </div>
            <div class="space-y-5 px-6 py-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="email" class="block text-[13px] font-medium text-[#191c1e] mb-1">Email <span class="text-rose-500">*</span></label>
                        <input id="email" name="email" type="email" value="{{ old('email', $kandidat->email) }}" required
                               class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 @error('email') border-rose-400 @enderror">
                        @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="no_hp" class="block text-[13px] font-medium text-[#191c1e] mb-1">No HP <span class="text-rose-500">*</span></label>
                        <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp', $profilKandidat->no_hp ?? '') }}" required
                               class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 @error('no_hp') border-rose-400 @enderror">
                        @error('no_hp')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION: Profil Kandidat --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm">
            <div class="border-b border-[#e0e3e5] px-6 py-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-primary">Profil Kandidat</p>
            </div>
            <div class="space-y-5 px-6 py-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="nama_kandidat" class="block text-[13px] font-medium text-[#191c1e] mb-1">Nama Kandidat <span class="text-rose-500">*</span></label>
                        <input id="nama_kandidat" name="nama_kandidat" type="text" value="{{ old('nama_kandidat', $kandidat->name) }}" required
                               class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 @error('nama_kandidat') border-rose-400 @enderror">
                        @error('nama_kandidat')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="nik_kandidat" class="block text-[13px] font-medium text-[#191c1e] mb-1">NIK KTP <span class="text-rose-500">*</span></label>
                        <input id="nik_kandidat" name="nik_kandidat" type="text" value="{{ old('nik_kandidat', $profilKandidat->nik_kandidat ?? '') }}" maxlength="16" required
                               class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm font-mono text-[#191c1e] shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 @error('nik_kandidat') border-rose-400 @enderror">
                        @error('nik_kandidat')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="tanggal_lahir" class="block text-[13px] font-medium text-[#191c1e] mb-1">Tanggal Lahir</label>
                        <input id="tanggal_lahir" name="tanggal_lahir" type="date" value="{{ old('tanggal_lahir', $profilKandidat->tanggal_lahir ?? '') }}"
                               class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 @error('tanggal_lahir') border-rose-400 @enderror">
                        @error('tanggal_lahir')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="jenis_kelamin" class="block text-[13px] font-medium text-[#191c1e] mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <select id="jenis_kelamin" name="jenis_kelamin" required
                                    class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 pr-9 text-sm text-[#191c1e] shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 appearance-none cursor-pointer">
                                <option value="">-- Pilih --</option>
                                <option value="L" @if(old('jenis_kelamin', $profilKandidat->jenis_kelamin ?? '') == 'L') selected @endif>Laki-laki</option>
                                <option value="P" @if(old('jenis_kelamin', $profilKandidat->jenis_kelamin ?? '') == 'P') selected @endif>Perempuan</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5">
                                <span class="material-symbols-outlined text-[18px] text-[#6b7280]">expand_more</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="departemen" class="block text-[13px] font-medium text-[#191c1e] mb-1">Departemen <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <select id="departemen" name="departemen" required
                                    class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 pr-9 text-sm text-[#191c1e] shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 appearance-none cursor-pointer">
                                <option value="">-- Pilih Departemen --</option>
                                @foreach($departemen as $item)
                                    <option value="{{ $item->id }}" @if(old('departemen', $currentDepartemenId) == $item->id) selected @endif>{{ $item->nama_departemen }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5">
                                <span class="material-symbols-outlined text-[18px] text-[#6b7280]">expand_more</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="posisi_dilamar" class="block text-[13px] font-medium text-[#191c1e] mb-1">Posisi yang Dilamar <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <select id="posisi_dilamar" name="posisi_dilamar" required
                                    class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 pr-9 text-sm text-[#191c1e] shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 appearance-none cursor-pointer">
                                <option value="">-- Pilih Posisi --</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5">
                                <span class="material-symbols-outlined text-[18px] text-[#6b7280]">expand_more</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="pendidikan_terakhir" class="block text-[13px] font-medium text-[#191c1e] mb-1">Pendidikan Terakhir <span class="text-rose-500">*</span></label>
                    <input id="pendidikan_terakhir" name="pendidikan_terakhir" type="text" value="{{ old('pendidikan_terakhir', $profilKandidat->pendidikan_terakhir ?? '') }}" required
                           class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 @error('pendidikan_terakhir') border-rose-400 @enderror">
                    @error('pendidikan_terakhir')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- SECTION: Password --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm">
            <div class="border-b border-[#e0e3e5] px-6 py-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-primary">
                    Password Baru
                    <span class="font-normal normal-case text-[#40484b]">(kosongkan jika tidak ingin mengubah)</span>
                </p>
            </div>
            <div class="space-y-5 px-6 py-5">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="block text-[13px] font-medium text-[#191c1e] mb-1">Password Baru</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" minlength="8"
                                   placeholder="Minimal 8 karakter"
                                   class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 pr-10 text-sm text-[#191c1e] shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 @error('password') border-rose-400 @enderror">
                            <button type="button" onclick="togglePw('password', this)"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-[#6b7280] hover:text-[#191c1e]">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                        @error('password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-[13px] font-medium text-[#191c1e] mb-1">Konfirmasi Password</label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" type="password" minlength="8"
                                   placeholder="Ulangi password baru"
                                   class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 pr-10 text-sm text-[#191c1e] shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <button type="button" onclick="togglePw('password_confirmation', this)"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-[#6b7280] hover:text-[#191c1e]">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ACTION BAR --}}
        <div class="sticky bottom-0 flex items-center justify-end gap-3 rounded-xl border border-[#e0e3e5] bg-white/95 px-6 py-4 shadow-[0_-2px_12px_rgba(0,0,0,0.06)] backdrop-blur">
            <a href="{{ route('admin.data-kandidat.index') }}"
               class="rounded-xl border border-[#c0c8cb] px-5 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">Batal</a>
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90 transition-all active:scale-95">
                <span class="material-symbols-outlined text-[16px]">save</span>
                Perbarui
            </button>
        </div>
    </form>
</div>

<script>
function togglePw(id, btn) {
    const inp = document.getElementById(id);
    const icon = btn.querySelector('.material-symbols-outlined');
    if (inp.type === 'password') { inp.type = 'text'; icon.textContent = 'visibility_off'; }
    else { inp.type = 'password'; icon.textContent = 'visibility'; }
}

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

                const savedId = '{{ old("posisi_dilamar", $profilKandidat->posisi_id ?? "") }}';
                if (savedId) {
                    for (let i = 0; i < posisiSelect.options.length; i++) {
                        if (String(posisiSelect.options[i].value) === String(savedId)) {
                            posisiSelect.selectedIndex = i;
                            break;
                        }
                    }
                }
            });
    });

    if (departemenSelect.value) {
        departemenSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
