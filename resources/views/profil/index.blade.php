@extends('layouts.' . (auth()->user()->tipe_akun === 'kandidat' ? 'peserta' : 'admin'), ['judulHalaman' => 'Profil Saya'])

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
        <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 md:p-8">
            <div class="mb-6">
                <h2 class="text-title-sm font-title-sm text-on-surface">Foto Profil</h2>
                <p class="text-body-md text-on-surface-variant mt-1">
                    Unggah foto profil Anda. Format yang didukung: JPG, JPEG, PNG. Ukuran maksimal 2 MB.
                </p>
            </div>

            <form method="POST" action="{{ route('profil.unggah-foto') }}" enctype="multipart/form-data">
                @csrf

                <div class="flex flex-col md:flex-row items-center md:items-start gap-8 py-4">
                    {{-- Avatar --}}
                    <div class="relative group shrink-0">
                        <div class="w-32 h-32 rounded-full border-4 border-surface-container-high overflow-hidden bg-surface-container">
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
                            <label for="foto_profil" class="block text-label-sm font-label-sm text-on-surface">Pilih Foto Baru</label>
                            <div>
                                <input id="foto_profil" name="foto_profil" type="file"
                                       accept="image/jpeg,image/jpg,image/png"
                                       class="block w-full text-sm text-on-surface-variant file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-action-teal file:text-on-primary hover:file:opacity-90 cursor-pointer border border-outline-variant rounded-lg px-3 py-2 bg-white transition-colors @error('foto_profil') border-rose-500 @enderror">
                            </div>
                            <p class="text-[11px] text-on-surface-variant italic">File akan mengganti foto profil yang lama (jika ada).</p>
                        </div>
                    </div>
                </div>

                @error('foto_profil')
                    <p class="text-xs text-rose-600 mt-1 ml-1">{{ $message }}</p>
                @enderror

                <div class="flex justify-end mt-6 pt-6 border-t border-surface-container">
                    <button type="submit"
                            class="bg-action-teal text-on-primary px-5 py-2.5 rounded-xl text-body-md font-semibold flex items-center gap-2 hover:opacity-90 active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-[20px]">upload</span>
                        Unggah Foto
                    </button>
                </div>
            </form>
        </section>

        {{-- ============================================================ --}}
        {{-- CARD 2: DATA DIRI                                             --}}
        {{-- ============================================================ --}}
        <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 md:p-8">
            <div class="mb-6">
                <h2 class="text-title-sm font-title-sm text-on-surface">Data Diri</h2>
                <p class="text-body-md text-on-surface-variant mt-1">
                    Hanya kolom No. HP yang dapat Anda ubah sendiri. Untuk data lainnya, silakan hubungi Admin/HR.
                </p>
            </div>

            <form method="POST" action="{{ route('profil.perbarui') }}" class="space-y-stack-md">
                @csrf
                @method('PUT')

                {{-- Nama Lengkap --}}
                <div class="space-y-1.5">
                    <label class="block text-label-sm font-label-sm text-on-surface">Nama Lengkap</label>
                    <input class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface-variant font-body-md cursor-not-allowed"
                           disabled readonly type="text" value="{{ $user->name }}">
                    <div class="flex items-center gap-1 text-on-surface-variant">
                        <span class="material-symbols-outlined text-[14px]">info</span>
                        <span class="text-[11px]">Untuk mengubah data ini, hubungi Admin/HR.</span>
                    </div>
                </div>

                {{-- Email --}}
                <div class="space-y-1.5">
                    <label class="block text-label-sm font-label-sm text-on-surface">Email</label>
                    <input class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface-variant font-body-md cursor-not-allowed"
                           disabled readonly type="email" value="{{ $user->email }}">
                    <div class="flex items-center gap-1 text-on-surface-variant">
                        <span class="material-symbols-outlined text-[14px]">info</span>
                        <span class="text-[11px]">Untuk mengubah data ini, hubungi Admin/HR.</span>
                    </div>
                </div>

                {{-- Field khusus karyawan --}}
                @if($tipe === 'karyawan' || $profilKaryawan)
                    @if($profilKaryawan)
                        <div class="space-y-1.5">
                            <label class="block text-label-sm font-label-sm text-on-surface">NIK Karyawan</label>
                            <input class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface-variant font-body-md cursor-not-allowed"
                                   disabled readonly type="text" value="{{ $profilKaryawan->nik_karyawan ?? '-' }}">
                            <div class="flex items-center gap-1 text-on-surface-variant">
                                <span class="material-symbols-outlined text-[14px]">info</span>
                                <span class="text-[11px]">Untuk mengubah data ini, hubungi Admin/HR.</span>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-label-sm font-label-sm text-on-surface">Departemen</label>
                            <input class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface-variant font-body-md cursor-not-allowed"
                                   disabled readonly type="text" value="{{ $dataKaryawan->departemen ?? $profilKaryawan->departemen ?? '-' }}">
                            <div class="flex items-center gap-1 text-on-surface-variant">
                                <span class="material-symbols-outlined text-[14px]">info</span>
                                <span class="text-[11px]">Untuk mengubah data ini, hubungi Admin/HR.</span>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-label-sm font-label-sm text-on-surface">Jabatan</label>
                            <input class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface-variant font-body-md cursor-not-allowed"
                                   disabled readonly type="text" value="{{ $dataKaryawan->jabatan ?? $profilKaryawan->jabatan ?? '-' }}">
                            <div class="flex items-center gap-1 text-on-surface-variant">
                                <span class="material-symbols-outlined text-[14px]">info</span>
                                <span class="text-[11px]">Untuk mengubah data ini, hubungi Admin/HR.</span>
                            </div>
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
                            <label class="block text-label-sm font-label-sm text-on-surface">NIK KTP (Kandidat)</label>
                            <input class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface-variant font-body-md cursor-not-allowed"
                                   disabled readonly type="text" value="{{ $profilKandidat->nik_kandidat ?? '-' }}">
                            <div class="flex items-center gap-1 text-on-surface-variant">
                                <span class="material-symbols-outlined text-[14px]">info</span>
                                <span class="text-[11px]">Untuk mengubah data ini, hubungi Admin/HR.</span>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-label-sm font-label-sm text-on-surface">Posisi Dilamar</label>
                            <input class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface-variant font-body-md cursor-not-allowed"
                                   disabled readonly type="text" value="{{ $profilKandidat->posisi_dilamar ?? '-' }}">
                            <div class="flex items-center gap-1 text-on-surface-variant">
                                <span class="material-symbols-outlined text-[14px]">info</span>
                                <span class="text-[11px]">Untuk mengubah data ini, hubungi Admin/HR.</span>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-label-sm font-label-sm text-on-surface">Pendidikan Terakhir</label>
                            <input class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface-variant font-body-md cursor-not-allowed"
                                   disabled readonly type="text" value="{{ $profilKandidat->pendidikan_terakhir ?? '-' }}">
                            <div class="flex items-center gap-1 text-on-surface-variant">
                                <span class="material-symbols-outlined text-[14px]">info</span>
                                <span class="text-[11px]">Untuk mengubah data ini, hubungi Admin/HR.</span>
                            </div>
                        </div>
                    @else
                        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-700">
                            Profil kandidat belum tersedia. Hubungi Admin/HR.
                        </div>
                    @endif
                @endif

                {{-- No HP - FIELD YANG BISA DIEDIT --}}
                <div class="space-y-1.5">
                    <label class="block text-label-sm font-label-sm text-on-surface" for="no_hp">No. HP</label>
                    <div class="focused-input">
                        <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp', $user->no_hp) }}"
                               placeholder="0812XXXXXXXX"
                               class="w-full px-4 py-3 bg-white border border-outline-variant rounded-lg text-on-surface font-body-md transition-all focus:outline-none @error('no_hp') border-rose-500 @enderror">
                    </div>
                    @error('no_hp')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end mt-8 pt-6 border-t border-surface-container">
                    <button type="submit"
                            class="bg-action-teal text-on-primary px-5 py-2.5 rounded-xl text-body-md font-semibold flex items-center gap-2 hover:opacity-90 active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </section>

        {{-- ============================================================ --}}
        {{-- CARD 3: UBAH PASSWORD                                         --}}
        {{-- ============================================================ --}}
        <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 md:p-8">
            <div class="mb-6">
                <h2 class="text-title-sm font-title-sm text-on-surface">Ubah Password</h2>
                <p class="text-body-md text-on-surface-variant mt-1">
                    Demi keamanan akun Anda, gunakan password minimal 8 karakter yang belum pernah dipakai sebelumnya.
                </p>
            </div>

            <form method="POST" action="{{ route('profil.ubah-password') }}" class="space-y-stack-md">
                @csrf
                @method('PUT')

                {{-- Password Lama --}}
                <div class="space-y-1.5 relative" x-data="{ showPwd: false }">
                    <label class="block text-label-sm font-label-sm text-on-surface" for="password_lama">Password Lama</label>
                    <div class="focused-input">
                        <input id="password_lama" name="password_lama"
                               :type="showPwd ? 'text' : 'password'"
                               placeholder="••••••••"
                               class="w-full px-4 py-3 bg-white border border-outline-variant rounded-lg text-on-surface font-body-md transition-all focus:outline-none pr-10 @error('password_lama') border-rose-500 @enderror">
                        <button type="button"
                                @click="showPwd = !showPwd"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-action-teal transition-colors flex items-center">
                            <span class="material-symbols-outlined text-lg" x-text="showPwd ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                    @error('password_lama')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Baru --}}
                <div class="space-y-1.5 relative" x-data="{ showPwd: false }">
                    <label class="block text-label-sm font-label-sm text-on-surface" for="password_baru">Password Baru</label>
                    <div class="focused-input">
                        <input id="password_baru" name="password_baru" minlength="8"
                               :type="showPwd ? 'text' : 'password'"
                               placeholder="••••••••"
                               class="w-full px-4 py-3 bg-white border border-outline-variant rounded-lg text-on-surface font-body-md transition-all focus:outline-none pr-10 @error('password_baru') border-rose-500 @enderror">
                        <button type="button"
                                @click="showPwd = !showPwd"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-action-teal transition-colors flex items-center">
                            <span class="material-symbols-outlined text-lg" x-text="showPwd ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                    <p class="text-[11px] text-on-surface-variant">Minimal 8 karakter.</p>
                    @error('password_baru')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi Password Baru --}}
                <div class="space-y-1.5 relative" x-data="{ showPwd: false }">
                    <label class="block text-label-sm font-label-sm text-on-surface" for="konfirmasi_password_baru">Konfirmasi Password Baru</label>
                    <div class="focused-input">
                        <input id="konfirmasi_password_baru" name="password_baru_confirmation" minlength="8"
                               :type="showPwd ? 'text' : 'password'"
                               placeholder="••••••••"
                               class="w-full px-4 py-3 bg-white border border-outline-variant rounded-lg text-on-surface font-body-md transition-all focus:outline-none pr-10 @error('password_baru_confirmation') border-rose-500 @enderror">
                        <button type="button"
                                @click="showPwd = !showPwd"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-action-teal transition-colors flex items-center">
                            <span class="material-symbols-outlined text-lg" x-text="showPwd ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                    <p class="text-[11px] text-on-surface-variant">Ketik ulang password baru Anda.</p>
                    @error('password_baru_confirmation')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end mt-8 pt-6 border-t border-surface-container">
                    <button type="submit"
                            class="bg-action-teal text-on-primary px-5 py-2.5 rounded-xl text-body-md font-semibold flex items-center gap-2 hover:opacity-90 active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-[20px]">lock_reset</span>
                        Ubah Password
                    </button>
                </div>
            </form>
        </section>

    </div>
@endsection
