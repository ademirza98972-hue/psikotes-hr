@extends('layouts.' . (in_array(auth()->user()->tipe_akun, ['kandidat', 'karyawan']) ? 'peserta' : 'admin'), ['judulHalaman' => 'Profil Saya'])

@section('content')
    @php
        $tipe = auth()->user()->tipe_akun ?? null;
        $profilKaryawan = $user->profilKaryawan;
        $profilKandidat = $user->profilKandidat;
        $dataKaryawan = $profilKaryawan?->dataKaryawan;
        $inisial = mb_substr(explode(' ', $user->name)[0], 0, 1);
    @endphp

    <div class="max-w-[800px] w-full space-y-8">

        {{-- ============================================================ --}}
        {{-- CARD 1: FOTO PROFIL                                           --}}
        {{-- ============================================================ --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm">
            <div class="border-b border-[#e0e3e5] px-6 py-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">FOTO PROFIL</p>
                <p class="text-[13px] text-[#40484b] mt-0.5">Unggah foto profil Anda. Format yang didukung: JPG, JPEG, PNG. Ukuran maksimal 2 MB.</p>
            </div>
            <div class="px-6 py-6 space-y-5">
                <form method="POST" action="{{ route('profil.unggah-foto') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="flex flex-col md:flex-row items-center md:items-start gap-8 py-4">
                        {{-- Avatar --}}
                        <div class="relative group shrink-0">
                            <div class="w-32 h-32 rounded-full border-4 border-[#e0e3e5] overflow-hidden bg-[#f2f4f6]">
                                @if($user->foto_profil)
                                    <img src="{{ asset('storage/' . $user->foto_profil) }}"
                                         alt="Foto Profil"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-[#2C5F6F]/10 text-[#2C5F6F] text-4xl font-semibold">
                                        {{ $inisial }}
                                    </div>
                                @endif
                            </div>
                            <div class="absolute inset-0 rounded-full bg-primary/20 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity pointer-events-none">
                                <span class="material-symbols-outlined text-white text-3xl">photo_camera</span>
                            </div>
                        </div>

                        {{-- Upload Controls --}}
                        <div class="flex-1 space-y-4 text-center md:text-left w-full">
                            <div class="space-y-1.5">
                                <label for="foto_profil" class="block text-[13px] font-medium text-[#191c1e] mb-1">Pilih Foto Baru</label>
                                <div>
                                    <input id="foto_profil" name="foto_profil" type="file"
                                           accept="image/jpeg,image/jpg,image/png"
                                           class="block w-full text-sm text-[#40484b] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#2C5F6F] file:text-white hover:file:bg-[#1E414C] cursor-pointer border border-[#c0c8cb] rounded-lg px-3 py-2 bg-white @error('foto_profil') border-rose-400 @enderror">
                                </div>
                                <p class="text-[11px] text-[#40484b] italic">File akan mengganti foto profil yang lama (jika ada).</p>
                            </div>
                        </div>
                    </div>

                    @error('foto_profil')
                        <p class="text-xs text-rose-600 mt-1 ml-1">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end pt-5 border-t border-[#e0e3e5]">
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#1E414C] transition-all active:scale-95">
                            <span class="material-symbols-outlined text-[20px]">upload</span>
                            Unggah Foto
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- CARD 2: DATA DIRI                                             --}}
        {{-- ============================================================ --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm">
            <div class="border-b border-[#e0e3e5] px-6 py-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">DATA DIRI</p>
                <p class="text-[13px] text-[#40484b] mt-0.5">Hanya kolom No. HP yang dapat Anda ubah sendiri. Untuk data lainnya, silakan hubungi Admin/HR.</p>
            </div>
            <div class="px-6 py-6 space-y-5">
                <form method="POST" action="{{ route('profil.perbarui') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Nama Lengkap --}}
                    <div class="space-y-1.5">
                        <label class="block text-[13px] font-medium text-[#191c1e] mb-1">Nama Lengkap</label>
                        <input class="block w-full rounded-lg border border-[#c0c8cb] bg-[#f2f4f6] px-3 py-2.5 text-sm text-[#40484b] cursor-not-allowed"
                               disabled readonly type="text" value="{{ $user->name }}">
                        <p class="mt-1.5 text-[11px] text-[#40484b] flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px]">info</span>
                            Untuk mengubah data ini, hubungi Admin/HR.
                        </p>
                    </div>

                    {{-- Email --}}
                    <div class="space-y-1.5">
                        <label class="block text-[13px] font-medium text-[#191c1e] mb-1">Email</label>
                        <input class="block w-full rounded-lg border border-[#c0c8cb] bg-[#f2f4f6] px-3 py-2.5 text-sm text-[#40484b] cursor-not-allowed"
                               disabled readonly type="email" value="{{ $user->email }}">
                        <p class="mt-1.5 text-[11px] text-[#40484b] flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px]">info</span>
                            Untuk mengubah data ini, hubungi Admin/HR.
                        </p>
                    </div>

                    {{-- Field khusus karyawan --}}
                    @if($tipe === 'karyawan' || $profilKaryawan)
                        @if($profilKaryawan)
                            <div class="space-y-1.5">
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1">NIK Karyawan</label>
                                <input class="block w-full rounded-lg border border-[#c0c8cb] bg-[#f2f4f6] px-3 py-2.5 text-sm text-[#40484b] cursor-not-allowed"
                                       disabled readonly type="text" value="{{ $profilKaryawan->nik_karyawan ?? '-' }}">
                                <p class="mt-1.5 text-[11px] text-[#40484b] flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">info</span>
                                    Untuk mengubah data ini, hubungi Admin/HR.
                                </p>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1">Departemen</label>
                                <input class="block w-full rounded-lg border border-[#c0c8cb] bg-[#f2f4f6] px-3 py-2.5 text-sm text-[#40484b] cursor-not-allowed"
                                       disabled readonly type="text" value="{{ $dataKaryawan->departemen ?? $profilKaryawan->departemen ?? '-' }}">
                                <p class="mt-1.5 text-[11px] text-[#40484b] flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">info</span>
                                    Untuk mengubah data ini, hubungi Admin/HR.
                                </p>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1">Jabatan</label>
                                <input class="block w-full rounded-lg border border-[#c0c8cb] bg-[#f2f4f6] px-3 py-2.5 text-sm text-[#40484b] cursor-not-allowed"
                                       disabled readonly type="text" value="{{ $dataKaryawan->jabatan ?? $profilKaryawan->jabatan ?? '-' }}">
                                <p class="mt-1.5 text-[11px] text-[#40484b] flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">info</span>
                                    Untuk mengubah data ini, hubungi Admin/HR.
                                </p>
                            </div>
                        @else
                            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-700">
                                Profil karyawan belum tersedia. Hubungi Admin/HR.
                            </div>
                        @endif
                    @endif

                    {{-- Field khusus kandidat --}}
                    @if($tipe === 'kandidat' || $profilKandidat)
                        @if($profilKandidat)
                            <div class="space-y-1.5">
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1">NIK KTP (Kandidat)</label>
                                <input class="block w-full rounded-lg border border-[#c0c8cb] bg-[#f2f4f6] px-3 py-2.5 text-sm text-[#40484b] cursor-not-allowed"
                                       disabled readonly type="text" value="{{ $profilKandidat->nik_kandidat ?? '-' }}">
                                <p class="mt-1.5 text-[11px] text-[#40484b] flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">info</span>
                                    Untuk mengubah data ini, hubungi Admin/HR.
                                </p>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1">Posisi Dilamar</label>
                                <input class="block w-full rounded-lg border border-[#c0c8cb] bg-[#f2f4f6] px-3 py-2.5 text-sm text-[#40484b] cursor-not-allowed"
                                       disabled readonly type="text" value="{{ $profilKandidat->posisi_dilamar ?? '-' }}">
                                <p class="mt-1.5 text-[11px] text-[#40484b] flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">info</span>
                                    Untuk mengubah data ini, hubungi Admin/HR.
                                </p>
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[13px] font-medium text-[#191c1e] mb-1">Pendidikan Terakhir</label>
                                <input class="block w-full rounded-lg border border-[#c0c8cb] bg-[#f2f4f6] px-3 py-2.5 text-sm text-[#40484b] cursor-not-allowed"
                                       disabled readonly type="text" value="{{ $profilKandidat->pendidikan_terakhir ?? '-' }}">
                                <p class="mt-1.5 text-[11px] text-[#40484b] flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">info</span>
                                    Untuk mengubah data ini, hubungi Admin/HR.
                                </p>
                            </div>
                        @else
                            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-700">
                                Profil kandidat belum tersedia. Hubungi Admin/HR.
                            </div>
                        @endif
                    @endif

                    {{-- No HP - FIELD YANG BISA DIEDIT --}}
                    <div class="space-y-1.5">
                        <label class="block text-[13px] font-medium text-[#191c1e] mb-1" for="no_hp">No. HP</label>
                        <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp', $user->no_hp) }}"
                               placeholder="0812XXXXXXXX"
                               class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 @error('no_hp') border-rose-400 @enderror">
                        @error('no_hp')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-5 border-t border-[#e0e3e5]">
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#1E414C] transition-all active:scale-95">
                            <span class="material-symbols-outlined text-[20px]">save</span>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- CARD 3: UBAH PASSWORD                                         --}}
        {{-- ============================================================ --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm">
            <div class="border-b border-[#e0e3e5] px-6 py-4">
                <p class="text-[11px] font-bold uppercase tracking-wider text-[#2C5F6F]">UBAH PASSWORD</p>
                <p class="text-[13px] text-[#40484b] mt-0.5">Demi keamanan akun Anda, gunakan password minimal 8 karakter yang belum pernah dipakai sebelumnya.</p>
            </div>
            <div class="px-6 py-6 space-y-5">
                <form method="POST" action="{{ route('profil.ubah-password') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    {{-- Password Lama --}}
                    <div class="space-y-1.5 relative" x-data="{ showPwd: false }">
                        <label class="block text-[13px] font-medium text-[#191c1e] mb-1" for="password_lama">Password Lama</label>
                        <div class="relative">
                            <input id="password_lama" name="password_lama"
                                   :type="showPwd ? 'text' : 'password'"
                                   placeholder="••••••••"
                                   class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 pr-10 @error('password_lama') border-rose-400 @enderror">
                            <button type="button"
                                    @click="showPwd = !showPwd"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#40484b] hover:text-[#2C5F6F] transition-colors flex items-center">
                                <span class="material-symbols-outlined text-lg" x-text="showPwd ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                        @error('password_lama')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Baru --}}
                    <div class="space-y-1.5 relative" x-data="{ showPwd: false }">
                        <label class="block text-[13px] font-medium text-[#191c1e] mb-1" for="password_baru">Password Baru</label>
                        <div class="relative">
                            <input id="password_baru" name="password_baru" minlength="8"
                                   :type="showPwd ? 'text' : 'password'"
                                   placeholder="••••••••"
                                   class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 pr-10 @error('password_baru') border-rose-400 @enderror">
                            <button type="button"
                                    @click="showPwd = !showPwd"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#40484b] hover:text-[#2C5F6F] transition-colors flex items-center">
                                <span class="material-symbols-outlined text-lg" x-text="showPwd ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                        <p class="text-[11px] text-[#40484b]">Minimal 8 karakter.</p>
                        @error('password_baru')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password Baru --}}
                    <div class="space-y-1.5 relative" x-data="{ showPwd: false }">
                        <label class="block text-[13px] font-medium text-[#191c1e] mb-1" for="konfirmasi_password_baru">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input id="konfirmasi_password_baru" name="password_baru_confirmation" minlength="8"
                                   :type="showPwd ? 'text' : 'password'"
                                   placeholder="••••••••"
                                   class="block w-full rounded-lg border border-[#c0c8cb] bg-white px-3 py-2.5 text-sm text-[#191c1e] shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20 pr-10 @error('password_baru_confirmation') border-rose-400 @enderror">
                            <button type="button"
                                    @click="showPwd = !showPwd"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#40484b] hover:text-[#2C5F6F] transition-colors flex items-center">
                                <span class="material-symbols-outlined text-lg" x-text="showPwd ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                        <p class="text-[11px] text-[#40484b]">Ketik ulang password baru Anda.</p>
                        @error('password_baru_confirmation')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-5 border-t border-[#e0e3e5]">
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#1E414C] transition-all active:scale-95">
                            <span class="material-symbols-outlined text-[20px]">lock_reset</span>
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
