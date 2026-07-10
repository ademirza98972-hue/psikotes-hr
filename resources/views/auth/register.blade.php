@extends('layouts.auth', ['judulHalaman' => 'Daftar Akun'])

@section('content')
<div x-data="{
    tipe: '{{ old('tipe_akun', 'kandidat') }}',
    departemenId: '',
    karyawanPosisi: [],
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
}" class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    <h2 class="mb-6 text-lg font-semibold text-slate-900">Daftar akun baru</h2>

    @if ($errors->any())
    <div class="mb-4 rounded-md border border-rose-600 bg-rose-600 px-4 py-3 text-sm text-white">
        <ul class="list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-700">Tipe Akun</label>
            <div class="mt-2 grid grid-cols-2 gap-2">
                <label class="flex cursor-pointer items-center justify-center rounded-md border px-3 py-2 text-sm"
                    :class="tipe === 'kandidat' ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-slate-300 text-slate-600'">
                    <input type="radio" name="tipe_akun" value="kandidat" x-model="tipe" class="sr-only">
                    Kandidat
                </label>
                <label class="flex cursor-pointer items-center justify-center rounded-md border px-3 py-2 text-sm"
                    :class="tipe === 'karyawan' ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-slate-300 text-slate-600'">
                    <input type="radio" name="tipe_akun" value="karyawan" x-model="tipe" class="sr-only">
                    Karyawan
                </label>
            </div>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>

        <div>
            <label for="no_hp" class="block text-sm font-medium text-slate-700">No HP</label>
            <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp') }}" required
                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>

        <div x-show="tipe === 'kandidat'" x-cloak class="space-y-4 border-t border-slate-100 pt-4">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Data Kandidat</p>

            <div>
                <label for="name_kandidat" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                <input id="name_kandidat" name="name" type="text" value="{{ old('name') }}"
                    :disabled="tipe !== 'kandidat'"
                    :required="tipe === 'kandidat'"
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label for="posisi_dilamar" class="block text-sm font-medium text-slate-700">Posisi yang Dilamar</label>
                <select id="posisi_dilamar" name="posisi_dilamar"
                    :disabled="tipe !== 'kandidat'"
                    :required="tipe === 'kandidat'"
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">-- Pilih Posisi --</option>
                    @php
                        $currentDept = null;
                    @endphp
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
                <p class="mt-1 text-xs text-slate-500">Posisi berasal dari Departemen &amp; Posisi yang dikelola Admin/HR.</p>
            </div>

            <div>
                <label for="pendidikan_terakhir" class="block text-sm font-medium text-slate-700">Pendidikan Terakhir</label>
                <input id="pendidikan_terakhir" name="pendidikan_terakhir" type="text" value="{{ old('pendidikan_terakhir') }}"
                    :disabled="tipe !== 'kandidat'"
                    :required="tipe === 'kandidat'"
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <div>
                <label for="nik_kandidat" class="block text-sm font-medium text-slate-700">NIK KTP <span class="text-slate-400">(opsional)</span></label>
                <input id="nik_kandidat" name="nik_kandidat" type="text" value="{{ old('nik_kandidat') }}"
                    :disabled="tipe !== 'kandidat'"
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>
        </div>

        <div x-show="tipe === 'karyawan'" x-cloak class="space-y-4 border-t border-slate-100 pt-4">
            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Data Karyawan</p>

            <div>
                <label for="departemen_karyawan" class="block text-sm font-medium text-slate-700">Departemen</label>
                <select id="departemen_karyawan"
                    x-model="departemenId"
                    @change="muatPosisi()"
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">-- Pilih Departemen --</option>
                    @foreach($departemen as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="posisi_karyawan" class="block text-sm font-medium text-slate-700">Posisi</label>
                <select id="posisi_karyawan" name="posisi_karyawan"
                    :disabled="tipe !== 'karyawan' || departemenId === ''"
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">-- Pilih Posisi --</option>
                    <template x-for="p in karyawanPosisi" :key="p.id">
                        <option :value="p.nama_posisi" x-text="p.nama_posisi"></option>
                    </template>
                </select>
                <p class="mt-1 text-xs text-slate-500" x-show="departemenId === ''">Pilih departemen terlebih dahulu untuk melihat posisi.</p>
            </div>

            <div>
                <label for="name_karyawan" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                <input id="name_karyawan" name="name" type="text" value="{{ old('name') }}"
                    :disabled="tipe !== 'karyawan'"
                    :required="tipe === 'karyawan'"
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-slate-500">Nama harus cocok dengan data HR.</p>
            </div>

            <div>
                <label for="nik_karyawan" class="block text-sm font-medium text-slate-700">NIK (sesuai data karyawan resmi)</label>
                <input id="nik_karyawan" name="nik_karyawan" type="text" value="{{ old('nik_karyawan') }}"
                    :disabled="tipe !== 'karyawan'"
                    :required="tipe === 'karyawan'"
                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-slate-500">Harap isi NIK dan Nama Karyawan dengan benar.</p>
            </div>
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
            <input id="password" name="password" type="password" required minlength="8"
                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            <p class="mt-1 text-xs text-slate-500">Minimal 8 karakter.</p>
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8"
                class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
        </div>

        <button type="submit"
            class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Daftar
        </button>
    </form>
</div>

<p class="mt-6 text-center text-sm text-slate-600">
    Sudah punya akun?
    <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-700">Masuk di sini</a>
</p>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>
@endsection
