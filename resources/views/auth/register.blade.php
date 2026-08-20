@extends('layouts.auth', ['judulHalaman' => 'Daftar Akun', 'showVisual' => false])

@section('content')
<div x-data="{
    tipe: '{{ old('tipe_akun', 'kandidat') }}',
    departemenId: '',
    karyawanPosisi: [],
    showPwd: false,
    showPwdConfirm: false,
    async muatPosisi() {
        if (! this.departemenId) {
            this.karyawanPosisi = [];
            return;
        }
        try {
            const r = await fetch('{{ str_replace("__DEPT_ID__", "' + this.departemenId + '", route('api.posisi.daftar', ['departemen' => '__DEPT_ID__'])) }}');
            this.karyawanPosisi = await r.json();
        } catch (e) {
            this.karyawanPosisi = [];
        }
    }
}" class="space-y-6">

    <div class="space-y-1">
        <h2 class="font-body font-semibold text-2xl text-on-surface">Buat Akun Baru</h2>
        <p class="font-body text-sm text-on-surface-variant">Lengkapi data di bawah untuk membuat akun.</p>
    </div>

    @if ($errors->any())
    <div class="rounded-sm border border-rose-600 bg-rose-50 px-4 py-3 text-sm text-rose-700">
        <ul class="list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        {{-- Tipe Akun --}}
        <div class="space-y-2">
            <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant">Tipe Akun</label>
            <div class="grid grid-cols-2 gap-2">
                <label class="flex cursor-pointer items-center justify-center rounded-sm border px-3 py-3 text-sm font-body font-medium transition-colors"
                    :class="tipe === 'kandidat' ? 'border-psikotes bg-psikotes text-white' : 'border-surface-variant text-on-surface-variant hover:border-psikotes'">
                    <input type="radio" name="tipe_akun" value="kandidat" x-model="tipe" class="sr-only">
                    Kandidat
                </label>
                <label class="flex cursor-pointer items-center justify-center rounded-sm border px-3 py-3 text-sm font-body font-medium transition-colors"
                    :class="tipe === 'karyawan' ? 'border-psikotes bg-psikotes text-white' : 'border-surface-variant text-on-surface-variant hover:border-psikotes'">
                    <input type="radio" name="tipe_akun" value="karyawan" x-model="tipe" class="sr-only">
                    Karyawan
                </label>
            </div>
        </div>

        {{-- Email --}}
        <div class="space-y-1.5">
            <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant" for="email">Email</label>
            <div class="relative focused-input group">
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    placeholder="nama@perusahaan.com"
                    class="w-full px-4 py-3 bg-white border rounded-sm text-sm font-body placeholder:text-outline-variant focus:outline-none focus:border-psikotes transition-colors @error('email') border-rose-500 @else border-surface-variant @enderror"
                >
                <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline-variant group-focus-within:text-psikotes transition-colors">mail</span>
            </div>
            @error('email')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- No HP --}}
        <div class="space-y-1.5">
            <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant" for="no_hp">No HP</label>
            <div class="relative focused-input group">
                <input
                    id="no_hp"
                    name="no_hp"
                    type="text"
                    value="{{ old('no_hp') }}"
                    required
                    autocomplete="tel"
                    placeholder="08xxxxxxxxxx"
                    class="w-full px-4 py-3 bg-white border rounded-sm text-sm font-body placeholder:text-outline-variant focus:outline-none focus:border-psikotes transition-colors @error('no_hp') border-rose-500 @else border-surface-variant @enderror"
                >
                <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline-variant group-focus-within:text-psikotes transition-colors">smartphone</span>
            </div>
            @error('no_hp')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Field Kandidat --}}
        <div x-show="tipe === 'kandidat'" x-cloak class="space-y-4 border-t border-surface-variant pt-4">
            <p class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant">Data Kandidat</p>

            <div class="space-y-1.5">
                <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant" for="name_kandidat">Nama Lengkap</label>
                <div class="relative focused-input group">
                    <input
                        id="name_kandidat"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        :disabled="tipe !== 'kandidat'"
                        :required="tipe === 'kandidat'"
                        placeholder="Nama lengkap sesuai KTP"
                        class="w-full px-4 py-3 bg-white border border-surface-variant rounded-sm text-sm font-body placeholder:text-outline-variant focus:outline-none focus:border-psikotes transition-colors"
                    >
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline-variant group-focus-within:text-psikotes transition-colors">person</span>
                </div>
                @error('name')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="space-y-1.5">
                <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant" for="nik_kandidat">NIK KTP <span class="text-rose-600">*</span></label>
                <div class="relative focused-input group">
                    <input
                        id="nik_kandidat"
                        name="nik_kandidat"
                        type="text"
                        value="{{ old('nik_kandidat') }}"
                        :disabled="tipe !== 'kandidat'"
                        :required="tipe === 'kandidat'"
                        maxlength="16"
                        inputmode="numeric"
                        pattern="[0-9]{16}"
                        placeholder="16 digit NIK"
                        class="w-full px-4 py-3 bg-white border rounded-sm text-sm font-body placeholder:text-outline-variant focus:outline-none focus:border-psikotes transition-colors @error('nik_kandidat') border-rose-500 @else border-surface-variant @enderror"
                    >
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline-variant group-focus-within:text-psikotes transition-colors">badge</span>
                </div>
                @error('nik_kandidat')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="space-y-1.5">
                <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant" for="posisi_dilamar">Posisi yang Dilamar</label>
                <div class="relative">
                    <select
                        id="posisi_dilamar"
                        name="posisi_dilamar"
                        :disabled="tipe !== 'kandidat'"
                        :required="tipe === 'kandidat'"
                        class="w-full px-4 py-3 pr-10 bg-white border rounded-sm text-sm font-body focus:outline-none focus:border-psikotes transition-colors appearance-none @error('posisi_dilamar') border-rose-500 @else border-surface-variant @enderror"
                    >
                        <option value="">-- Pilih Posisi --</option>
                        @php $currentDept = null; @endphp
                        @foreach($posisi as $p)
                            @if($p->departemen && $p->departemen->id !== $currentDept)
                                @php $currentDept = $p->departemen->id; @endphp
                                <optgroup label="{{ $p->departemen->nama_departemen }}">
                            @endif
                                <option value="{{ $p->nama_posisi }}" {{ old('posisi_dilamar') == $p->nama_posisi ? 'selected' : '' }}>
                                    {{ $p->nama_posisi }}
                                </option>
                            @if(!$loop->last && (!$posisi[$loop->index + 1]->departemen || $posisi[$loop->index + 1]->departemen->id !== $currentDept))
                                </optgroup>
                            @endif
                            @if($loop->last)
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline-variant text-[18px]">expand_more</span>
                </div>
                @error('posisi_dilamar')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @else
                    <p class="font-body text-xs text-on-surface-variant mt-1">Posisi berasal dari Departemen &amp; Posisi yang dikelola Admin/HR.</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant" for="pendidikan_terakhir">Pendidikan Terakhir</label>
                <div class="relative focused-input group">
                    <input
                        id="pendidikan_terakhir"
                        name="pendidikan_terakhir"
                        type="text"
                        value="{{ old('pendidikan_terakhir') }}"
                        :disabled="tipe !== 'kandidat'"
                        :required="tipe === 'kandidat'"
                        placeholder="Contoh: S1 Teknik Informatika"
                        class="w-full px-4 py-3 bg-white border border-surface-variant rounded-sm text-sm font-body placeholder:text-outline-variant focus:outline-none focus:border-psikotes transition-colors"
                    >
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline-variant group-focus-within:text-psikotes transition-colors">school</span>
                </div>
                @error('pendidikan_terakhir')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant" for="tanggal_lahir_kandidat">Tanggal Lahir</label>
                <div class="relative focused-input group">
                    <input
                        id="tanggal_lahir_kandidat"
                        name="tanggal_lahir"
                        type="date"
                        value="{{ old('tanggal_lahir') }}"
                        :disabled="tipe !== 'kandidat'"
                        class="w-full px-4 py-3 bg-white border border-surface-variant rounded-sm text-sm font-body focus:outline-none focus:border-psikotes transition-colors"
                    >
                </div>
                @error('tanggal_lahir')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- Field Karyawan --}}
        <div x-show="tipe === 'karyawan'" x-cloak class="space-y-4 border-t border-surface-variant pt-4">
            <p class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant">Data Karyawan</p>

            <div class="space-y-1.5">
                <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant" for="name_karyawan">Nama Lengkap</label>
                <div class="relative focused-input group">
                    <input
                        id="name_karyawan"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        :disabled="tipe !== 'karyawan'"
                        :required="tipe === 'karyawan'"
                        placeholder="Nama lengkap sesuai data HR"
                        class="w-full px-4 py-3 bg-white border border-surface-variant rounded-sm text-sm font-body placeholder:text-outline-variant focus:outline-none focus:border-psikotes transition-colors"
                    >
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline-variant group-focus-within:text-psikotes transition-colors">person</span>
                </div>
                <p class="font-body text-xs text-on-surface-variant mt-1">Nama harus cocok dengan data HR.</p>
            </div>

            <div class="space-y-1.5">
                <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant" for="nik_karyawan">NIK</label>
                <div class="relative focused-input group">
                    <input
                        id="nik_karyawan"
                        name="nik_karyawan"
                        type="text"
                        value="{{ old('nik_karyawan') }}"
                        :disabled="tipe !== 'karyawan'"
                        :required="tipe === 'karyawan'"
                        placeholder="NIK sesuai data karyawan resmi"
                        class="w-full px-4 py-3 bg-white border rounded-sm text-sm font-body placeholder:text-outline-variant focus:outline-none focus:border-psikotes transition-colors @error('nik_karyawan') border-rose-500 @else border-surface-variant @enderror"
                    >
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline-variant group-focus-within:text-psikotes transition-colors">badge</span>
                </div>
                @error('nik_karyawan')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @else
                    <p class="font-body text-xs text-on-surface-variant mt-1">Harap isi NIK dan Nama Karyawan dengan benar.</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant" for="departemen_karyawan">Departemen</label>
                <div class="relative">
                    <select
                        id="departemen_karyawan"
                        x-model="departemenId"
                        @change="muatPosisi()"
                        class="w-full px-4 py-3 pr-10 bg-white border border-surface-variant rounded-sm text-sm font-body focus:outline-none focus:border-psikotes transition-colors appearance-none"
                    >
                        <option value="">-- Pilih Departemen --</option>
                        @foreach($departemen as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                        @endforeach
                    </select>
                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline-variant text-[18px]">expand_more</span>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant" for="posisi_karyawan">Posisi</label>
                <div class="relative">
                    <select
                        id="posisi_karyawan"
                        name="posisi_karyawan"
                        :disabled="tipe !== 'karyawan' || departemenId === ''"
                        class="w-full px-4 py-3 pr-10 bg-white border border-surface-variant rounded-sm text-sm font-body focus:outline-none focus:border-psikotes transition-colors appearance-none"
                    >
                        <option value="">-- Pilih Posisi --</option>
                        <template x-for="p in karyawanPosisi" :key="p.id">
                            <option :value="p.nama_posisi" x-text="p.nama_posisi"></option>
                        </template>
                    </select>
                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline-variant text-[18px]">expand_more</span>
                </div>
                <p class="font-body text-xs text-on-surface-variant mt-1" x-show="departemenId === ''">Pilih departemen terlebih dahulu untuk melihat posisi.</p>
            </div>

            <div class="space-y-1.5">
                <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant" for="tanggal_lahir_karyawan">Tanggal Lahir</label>
                <div class="relative focused-input group">
                    <input
                        id="tanggal_lahir_karyawan"
                        name="tanggal_lahir"
                        type="date"
                        value="{{ old('tanggal_lahir') }}"
                        :disabled="tipe !== 'karyawan'"
                        class="w-full px-4 py-3 bg-white border border-surface-variant rounded-sm text-sm font-body focus:outline-none focus:border-psikotes transition-colors"
                    >
                </div>
            </div>

        </div>

        {{-- Password --}}
        <div class="space-y-1.5">
            <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant" for="password">Password</label>
            <div class="relative focused-input group">
                <input
                    id="password"
                    name="password"
                    :type="showPwd ? 'text' : 'password'"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                    class="w-full px-4 py-3 pr-10 bg-white border rounded-sm text-sm font-body placeholder:text-outline-variant focus:outline-none focus:border-psikotes transition-colors @error('password') border-rose-500 @else border-surface-variant @enderror"
                >
                <button
                    type="button"
                    @click="showPwd = !showPwd"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-outline-variant hover:text-on-surface transition-colors flex items-center"
                >
                    <span class="material-symbols-outlined text-lg" x-text="showPwd ? 'visibility_off' : 'visibility'"></span>
                </button>
            </div>
            @error('password')
                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
            @else
                <p class="font-body text-xs text-on-surface-variant mt-1">Minimal 8 karakter.</p>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div class="space-y-1.5">
            <label class="font-body text-xs font-medium uppercase tracking-tight text-on-surface-variant" for="password_confirmation">Konfirmasi Password</label>
            <div class="relative focused-input group">
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    :type="showPwdConfirm ? 'text' : 'password'"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    placeholder="Ulangi password"
                    class="w-full px-4 py-3 pr-10 bg-white border border-surface-variant rounded-sm text-sm font-body placeholder:text-outline-variant focus:outline-none focus:border-psikotes transition-colors"
                >
                <button
                    type="button"
                    @click="showPwdConfirm = !showPwdConfirm"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-outline-variant hover:text-on-surface transition-colors flex items-center"
                >
                    <span class="material-symbols-outlined text-lg" x-text="showPwdConfirm ? 'visibility_off' : 'visibility'"></span>
                </button>
            </div>
        </div>

        <button type="submit"
            class="w-full bg-psikotes text-white font-body font-semibold text-sm py-3.5 rounded-sm hover:brightness-110 active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-sm shadow-psikotes/20">
            <span>Daftar</span>
            <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
        </button>
    </form>

    <div class="text-center mt-4 text-sm text-on-surface-variant">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-psikotes font-semibold hover:underline">Masuk di sini</a>
    </div>

    <div class="pt-6 border-t border-surface-variant">
        <p class="font-body text-sm text-on-surface-variant text-center">
            Membutuhkan bantuan akses? <a href="#" class="text-psikotes font-medium hover:underline">Hubungi IT Support</a>
        </p>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
