@extends('layouts.' . (auth()->user()->tipe_akun === 'kandidat' ? 'peserta' : 'admin'), ['judulHalaman' => 'Edit Profil'])

@section('content')
    <div class="w-full px-4 py-4 space-y-6">

        @php
            $tipe = auth()->user()->tipe_akun ?? null;
            $profilKaryawan = $user->profilKaryawan;
            $profilKandidat = $user->profilKandidat;
            $dataKaryawan = $profilKaryawan?->dataKaryawan;
            $inisial = mb_substr(explode(' ', $user->name)[0], 0, 1);
        @endphp

        {{-- ============================================================ --}}
        {{-- CARD FOTO PROFIL                                               --}}
        {{-- ============================================================ --}}
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-900">Foto Profil</h2>
                <p class="mt-1 text-xs text-slate-500">
                    Unggah foto profil Anda. Format yang didukung: JPG, JPEG, PNG. Ukuran maksimal 2 MB.
                </p>
            </div>

            <form method="POST" action="{{ route('profil.unggah-foto') }}" enctype="multipart/form-data" class="px-6 py-5">
                @csrf

                <div class="flex flex-col items-center gap-6 sm:flex-row">
                    {{-- Preview foto / inisial --}}
                    <div class="shrink-0">
                        @if($user->foto_profil)
                            <img src="{{ asset('storage/' . $user->foto_profil) }}"
                                 alt="Foto Profil"
                                 class="h-32 w-32 rounded-full border-4 border-slate-200 object-cover">
                        @else
                            <div class="flex h-32 w-32 items-center justify-center rounded-full border-4 border-slate-200 bg-[#2C5F6F]/10 text-4xl font-semibold text-[#2C5F6F]">
                                {{ $inisial }}
                            </div>
                        @endif
                    </div>

                    {{-- Input file --}}
                    <div class="flex-1 w-full">
                        <label for="foto_profil" class="block text-sm font-medium text-slate-700">Pilih Foto Baru</label>
                        <input id="foto_profil" name="foto_profil" type="file"
                               accept="image/jpeg,image/jpg,image/png"
                               class="mt-1 block w-full text-sm text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-[#2C5F6F] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-[#234853] focus:outline-none">
                        <p class="mt-1 text-xs text-slate-400">File akan mengganti foto profil yang lama (jika ada).</p>
                        @error('foto_profil')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit"
                            class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">
                        Unggah Foto
                    </button>
                </div>
            </form>
        </div>

        {{-- ============================================================ --}}
        {{-- CARD 1 - DATA DIRI                                             --}}
        {{-- ============================================================ --}}
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-900">Data Diri</h2>
                <p class="mt-1 text-xs text-slate-500">
                    Hanya kolom No. HP yang dapat Anda ubah sendiri. Untuk data lainnya, silakan hubungi Admin/HR.
                </p>
            </div>

            <form method="POST" action="{{ route('profil.perbarui') }}" class="px-6 py-5 space-y-5">
                @csrf
                @method('PUT')

                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                    <input type="text" value="{{ $user->name }}" disabled
                           class="mt-1 block w-full cursor-not-allowed rounded-md border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500">
                    <p class="mt-1 text-xs text-slate-400">Untuk mengubah data ini, hubungi Admin/HR.</p>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700">Email</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           class="mt-1 block w-full cursor-not-allowed rounded-md border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500">
                    <p class="mt-1 text-xs text-slate-400">Untuk mengubah data ini, hubungi Admin/HR.</p>
                </div>

                {{-- Field khusus karyawan --}}
                @if($tipe === 'karyawan' || $profilKaryawan)
                    @if($profilKaryawan)
                        <div>
                            <label class="block text-sm font-medium text-slate-700">NIK Karyawan</label>
                            <input type="text" value="{{ $profilKaryawan->nik_karyawan ?? '-' }}" disabled
                                   class="mt-1 block w-full cursor-not-allowed rounded-md border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500">
                            <p class="mt-1 text-xs text-slate-400">Untuk mengubah data ini, hubungi Admin/HR.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Departemen</label>
                            <input type="text" value="{{ $dataKaryawan->departemen ?? $profilKaryawan->departemen ?? '-' }}" disabled
                                   class="mt-1 block w-full cursor-not-allowed rounded-md border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500">
                            <p class="mt-1 text-xs text-slate-400">Untuk mengubah data ini, hubungi Admin/HR.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Jabatan</label>
                            <input type="text" value="{{ $dataKaryawan->jabatan ?? $profilKaryawan->jabatan ?? '-' }}" disabled
                                   class="mt-1 block w-full cursor-not-allowed rounded-md border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500">
                            <p class="mt-1 text-xs text-slate-400">Untuk mengubah data ini, hubungi Admin/HR.</p>
                        </div>
                    @else
                        <div class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-700">
                            Profil karyawan belum tersedia. Hubungi Admin/HR.
                        </div>
                    @endif
                @endif

                {{-- Field khusus kandidat --}}
                @if($tipe === 'kandidat' || $profilKandidat)
                    @if($profilKandidat)
                        <div>
                            <label class="block text-sm font-medium text-slate-700">NIK KTP (Kandidat)</label>
                            <input type="text" value="{{ $profilKandidat->nik_kandidat ?? '-' }}" disabled
                                   class="mt-1 block w-full cursor-not-allowed rounded-md border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500">
                            <p class="mt-1 text-xs text-slate-400">Untuk mengubah data ini, hubungi Admin/HR.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Posisi Dilamar</label>
                            <input type="text" value="{{ $profilKandidat->posisi_dilamar ?? '-' }}" disabled
                                   class="mt-1 block w-full cursor-not-allowed rounded-md border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500">
                            <p class="mt-1 text-xs text-slate-400">Untuk mengubah data ini, hubungi Admin/HR.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Pendidikan Terakhir</label>
                            <input type="text" value="{{ $profilKandidat->pendidikan_terakhir ?? '-' }}" disabled
                                   class="mt-1 block w-full cursor-not-allowed rounded-md border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-500">
                            <p class="mt-1 text-xs text-slate-400">Untuk mengubah data ini, hubungi Admin/HR.</p>
                        </div>
                    @else
                        <div class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-700">
                            Profil kandidat belum tersedia. Hubungi Admin/HR.
                        </div>
                    @endif
                @endif

                {{-- No HP - FIELD YANG BISA DIEDIT --}}
                <div>
                    <label for="no_hp" class="block text-sm font-medium text-slate-700">No. HP</label>
                    <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp', $user->no_hp) }}"
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    @error('no_hp')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tombol Submit --}}
                <div class="flex justify-end pt-2">
                    <button type="submit"
                            class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- ============================================================ --}}
        {{-- CARD 2 - UBAH PASSWORD                                         --}}
        {{-- ============================================================ --}}
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-900">Ubah Password</h2>
                <p class="mt-1 text-xs text-slate-500">
                    Demi keamanan akun Anda, gunakan password minimal 8 karakter yang belum pernah dipakai sebelumnya.
                </p>
            </div>

            <form method="POST" action="{{ route('profil.ubah-password') }}" class="px-6 py-5 space-y-5">
                @csrf
                @method('PUT')

                {{-- Password Lama --}}
                <div>
                    <label for="password_lama" class="block text-sm font-medium text-slate-700">Password Lama</label>
                    <input id="password_lama" name="password_lama" type="password"
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    @error('password_lama')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Baru --}}
                <div>
                    <label for="password_baru" class="block text-sm font-medium text-slate-700">Password Baru</label>
                    <input id="password_baru" name="password_baru" type="password" minlength="8"
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    <p class="mt-1 text-xs text-slate-400">Minimal 8 karakter.</p>
                    @error('password_baru')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi Password Baru --}}
                <div>
                    <label for="konfirmasi_password_baru" class="block text-sm font-medium text-slate-700">Konfirmasi Password Baru</label>
                    <input id="konfirmasi_password_baru" name="password_baru_confirmation" type="password" minlength="8"
                           class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    <p class="mt-1 text-xs text-slate-400">Ketik ulang password baru Anda.</p>
                </div>

                {{-- Tombol Submit --}}
                <div class="flex justify-end pt-2">
                    <button type="submit"
                            class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection
