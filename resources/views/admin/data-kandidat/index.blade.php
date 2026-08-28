@extends('layouts.admin', ['judulHalaman' => 'Data Kandidat'])

@section('content')
@php
    $bisaVerifikasi = auth()->user()->hasIzin('pengguna.verifikasi');
    $posisiUrlTemplate = str_replace('__D__', '{id}', route('api.posisi.daftar', ['departemen' => '__D__']));
@endphp
<div x-data="{
    modalHapus: { buka: false, id: null, nama: '' },
    modalAksi: { buka: false, id: null, nama: '', tipe: '' },
    bukaHapus(id, nama) { this.modalHapus = { buka: true, id, nama }; },
    tutupHapus() { this.modalHapus = { buka: false, id: null, nama: '' }; },
    bukaAksi(id, nama, tipe) { this.modalAksi = { buka: true, id, nama, tipe }; },
    tutupAksi() { this.modalAksi = { buka: false, id: null, nama: '', tipe: '' }; },
    modalTambah: false,
    tambahDeptId: '{{ old('departemen', '') }}',
    posisiTambah: [],
    modalUbah: false,
    ubah: { id:'', nama:'', nik:'', email:'', no_hp:'', tgl:'', jk:'', deptId:'', posisiId:'', pendidikan:'' },
    posisiUbah: [],
    posisiUrl: '{{ $posisiUrlTemplate }}',
    loadPosisi(target, deptId, selectedId) {
        if (!deptId) { this[target] = []; return; }
        const self = this;
        fetch(this.posisiUrl.replace('{id}', deptId))
            .then(r => r.json())
            .then(data => {
                self[target] = data;
                if (selectedId) {
                    self.$nextTick(() => {
                        if (target === 'posisiUbah') self.ubah.posisiId = String(selectedId);
                        if (target === 'posisiTambah') self.tambahPosisiId = String(selectedId);
                    });
                }
            });
    },
    openUbah(id, nama, nik, email, no_hp, tgl, jk, deptId, posisiId, pendidikan) {
        this.ubah = { id: String(id), nama, nik, email, no_hp, tgl, jk,
                      deptId: String(deptId || ''), posisiId: String(posisiId || ''), pendidikan };
        this.posisiUbah = [];
        if (deptId) this.loadPosisi('posisiUbah', deptId, posisiId);
        this.modalUbah = true;
    }
}"
x-init="
    @if(old('_form') === 'tambah') $nextTick(() => { modalTambah = true; }); @endif
    @if(old('_form') === 'ubah') $nextTick(() => { openUbah({{ (int)old('id',0) }}, @js(old('nama_kandidat','')), @js(old('nik_kandidat','')), @js(old('email','')), @js(old('no_hp','')), @js(old('tanggal_lahir','')), @js(old('jenis_kelamin','')), {{ (int)old('departemen',0) }}, 0, @js(old('pendidikan_terakhir',''))); }); @endif
">

    {{-- STICKY HEADER --}}
    <div class="sticky top-0 z-30 bg-[#f7f9fb] -mx-6 px-6 pt-6 pb-4 border-b border-[#e0e3e5]">

    {{-- PAGE HEADER --}}
    <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-[#191c1e]">Daftar Akun Kandidat</h1>
            <p class="mt-1 text-sm text-[#40484b]">Verifikasi dan kelola akses kandidat untuk proses rekrutmen perusahaan.</p>
        </div>
        @if (auth()->user()->hasIzin('pengguna.tambah'))
            <button type="button" @click="modalTambah = true"
               class="inline-flex items-center gap-2 rounded-lg bg-[#2C5F6F] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#234853] transition-colors active:scale-[0.98] whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                Tambah Kandidat
            </button>
        @endif
    </div>

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('admin.data-kandidat.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div class="flex flex-1 flex-wrap items-end gap-2">
            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px]">
                <span class="absolute inset-y-0 left-3 flex items-center text-[#71787c]">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                </span>
                <input type="text" name="cari" value="{{ $kataKunci }}"
                    placeholder="Cari nama, email, atau NIK KTP..."
                    class="w-full rounded-lg border border-[#c0c8cb] py-2 pl-9 pr-4 text-sm text-[#191c1e] placeholder-[#71787c] outline-none focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F]/30 transition-all">
            </div>

            {{-- Filter Status --}}
            <div class="relative min-w-[180px]">
                <select name="status" onchange="this.form.submit()"
                    class="appearance-none w-full rounded-lg border border-[#c0c8cb] py-2 pl-4 pr-10 text-sm text-[#191c1e] outline-none focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F]/30 transition-all cursor-pointer bg-white">
                    <option value="">Semua Status</option>
                    <option value="menunggu_verifikasi" @if($filterStatus === 'menunggu_verifikasi') selected @endif>Menunggu Verifikasi</option>
                    <option value="aktif" @if($filterStatus === 'aktif') selected @endif>Aktif</option>
                    <option value="ditolak" @if($filterStatus === 'ditolak') selected @endif>Ditolak</option>
                </select>
                <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#71787c]">
                    <span class="material-symbols-outlined text-[18px]">expand_more</span>
                </span>
            </div>

            {{-- Filter Posisi --}}
            <div class="relative min-w-[180px]">
                <select name="posisi" onchange="this.form.submit()"
                    class="appearance-none w-full rounded-lg border border-[#c0c8cb] py-2 pl-4 pr-10 text-sm text-[#191c1e] outline-none focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F]/30 transition-all cursor-pointer bg-white">
                    <option value="">Semua Posisi</option>
                    @foreach ($semuaPosisi as $p)
                        <option value="{{ $p->id }}" @if($filterPosisi == $p->id) selected @endif>{{ $p->nama_posisi }}</option>
                    @endforeach
                </select>
                <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#71787c]">
                    <span class="material-symbols-outlined text-[18px]">expand_more</span>
                </span>
            </div>

            @if ($kataKunci || $filterStatus || $filterPosisi)
                <a href="{{ route('admin.data-kandidat.index') }}"
                   class="rounded-lg border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors whitespace-nowrap">
                    Reset Filter
                </a>
            @endif
        </div>
    </form>

    </div>{{-- /STICKY HEADER --}}

    {{-- TABLE + PAGINATION + EMPTY STATE --}}
    <div class="mt-4 rounded-xl border border-[#e0e3e5] bg-white overflow-hidden shadow-sm">

        <div style="overflow: auto; max-height: calc(100vh - 300px);">
            <table class="w-full text-left border-collapse">
                <caption class="sr-only">Daftar Akun Kandidat</caption>
                <thead style="position: sticky; top: 0; z-index: 5;">
                    <tr style="background: #0f2230; border-bottom: 1px solid #0a1a25;">
                        <th class="px-4 py-3.5 text-[11px] font-bold uppercase tracking-wider text-center" style="color:#7db8c2;width:52px">No.</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider" style="color: #7db8c2;">NIK</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider" style="color: #7db8c2;">Nama Kandidat</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider" style="color: #7db8c2;">Email</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider" style="color: #7db8c2;">Posisi yang Dilamar</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-center" style="color: #7db8c2;">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-right" style="color: #7db8c2;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e0e3e5]">
                    @forelse ($kandidat as $item)
                        @php
                            $profil = $item->profilKandidat;
                            $profilDeptId = $profil ? ($departemenMap[$profil->departemen] ?? 0) : 0;
                            $profilPosisiId = $profil ? ($posisiMap[$profil->posisi_dilamar] ?? 0) : 0;
                            $namaTampil = $profil->nama_kandidat ?? $item->name;
                            $status = $item->status;
                            $inisial = strtoupper(mb_substr(explode(' ', $namaTampil)[0], 0, 2));
                            $avatarBg = match(hexdec(substr(substr(md5($namaTampil), 0, 6), 0, 6)) % 4) {
                                0 => 'bg-[#2C5F6F]/10 text-[#2C5F6F]',
                                1 => 'bg-blue-100 text-blue-700',
                                2 => 'bg-violet-100 text-violet-700',
                                default => 'bg-amber-100 text-amber-700',
                            };
                            $warnaBadge = match($status) {
                                'aktif' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'menunggu_verifikasi' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'ditolak' => 'bg-rose-50 text-rose-700 border-rose-200',
                                default => 'bg-[#f2f4f6] text-[#40484b] border-[#c0c8cb]',
                            };
                            $labelBadge = match($status) {
                                'aktif' => 'Aktif',
                                'menunggu_verifikasi' => 'Menunggu Verifikasi',
                                'ditolak' => 'Ditolak',
                                'nonaktif' => 'Nonaktif',
                                default => ucfirst($status ?? 'Tidak diketahui'),
                            };
                            $dotColor = match($status) {
                                'aktif' => 'bg-emerald-600',
                                'menunggu_verifikasi' => 'bg-amber-500',
                                'ditolak' => 'bg-rose-600',
                                'nonaktif' => 'bg-[#40484b]',
                                default => 'bg-[#40484b]',
                            };
                        @endphp
                        <tr class="hover:bg-[#f7f9fb] transition-colors group">
                            <td class="px-4 py-4 text-sm text-[#71787c] tabular-nums text-center">{{ ($kandidat->currentPage() - 1) * $kandidat->perPage() + $loop->iteration }}</td>
                            <td class="px-6 py-4 text-sm font-mono text-[#40484b]">
                                {{ $profil->nik_kandidat ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $avatarBg }}">
                                        {{ $inisial }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-[#191c1e]">{{ $namaTampil }}</p>
                                        <p class="text-[11px] text-[#40484b]">{{ $item->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#40484b]">
                                {{ $item->email }}
                            </td>
                            <td class="px-6 py-4 text-sm text-[#40484b]">
                                {{ $profil->posisi->nama_posisi ?? $profil->posisi_dilamar ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $warnaBadge }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} mr-1.5"></span>
                                    {{ $labelBadge }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if ($status === 'menunggu_verifikasi' && $bisaVerifikasi)
                                        <form method="POST" action="{{ route('admin.data-kandidat.approve', $item->id) }}" class="inline">
                                            @csrf
                                            <button type="button"
                                                    @click="bukaAksi({{ $item->id }}, @js($namaTampil), 'approve')"
                                                    class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700 transition-colors">
                                                <span class="material-symbols-outlined text-[14px]">check</span>
                                                Setujui
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.data-kandidat.tolak', $item->id) }}" class="inline">
                                            @csrf
                                            <button type="button"
                                                    @click="bukaAksi({{ $item->id }}, @js($namaTampil), 'tolak')"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-rose-300 bg-white px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition-colors">
                                                <span class="material-symbols-outlined text-[14px]">close</span>
                                                Tolak
                                            </button>
                                        </form>
                                    @elseif (auth()->user()->hasIzin('pengguna.edit'))
                                        @if (in_array($status, ['aktif', 'nonaktif']))
                                            <form method="POST" action="{{ route('admin.data-kandidat.toggle-status', $item->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="p-2 rounded-lg text-[#40484b] hover:text-[#2C5F6F] hover:bg-[#2C5F6F]/10 transition-colors"
                                                        title="{{ $item->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <span class="material-symbols-outlined text-[18px]">
                                                        {{ $item->status === 'aktif' ? 'block' : 'check_circle' }}
                                                    </span>
                                                </button>
                                            </form>
                                            <button type="button"
                                                    @click="openUbah({{ $item->id }}, @js($namaTampil), @js($profil->nik_kandidat ?? ''), @js($item->email), @js($item->no_hp ?? ''), @js((string)($profil->tanggal_lahir ?? '')), @js($item->jenis_kelamin ?? ''), {{ $profilDeptId }}, {{ $profilPosisiId }}, @js($profil->pendidikan_terakhir ?? ''))"
                                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                    title="Edit">
                                                <span class="material-symbols-outlined text-[20px]">edit_square</span>
                                            </button>
                                        @elseif ($status === 'ditolak')
                                            <button type="button"
                                                    @click="openUbah({{ $item->id }}, @js($namaTampil), @js($profil->nik_kandidat ?? ''), @js($item->email), @js($item->no_hp ?? ''), @js((string)($profil->tanggal_lahir ?? '')), @js($item->jenis_kelamin ?? ''), {{ $profilDeptId }}, {{ $profilPosisiId }}, @js($profil->pendidikan_terakhir ?? ''))"
                                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                    title="Edit">
                                                <span class="material-symbols-outlined text-[20px]">edit_square</span>
                                            </button>
                                        @endif
                                    @endif

                                    @if (auth()->user()->hasIzin('pengguna.hapus'))
                                        <button type="button"
                                                @click="bukaHapus({{ $item->id }}, @js($namaTampil))"
                                                class="p-2 text-[#ba1a1a] hover:bg-[#fef2f2] rounded-lg transition-colors"
                                                title="Hapus">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-14 text-center">
                                @if ($kataKunci || $filterStatus || $filterPosisi)
                                    <span class="material-symbols-outlined block mb-3" style="font-size:40px; color:#c0c8cb;">search_off</span>
                                    <p class="text-[14px] font-medium text-[#40484b] mb-1">Tidak ada hasil yang cocok</p>
                                    <p class="text-[12px] text-[#71787c] mb-4">Coba ubah kata kunci atau hapus filter aktif.</p>
                                    <a href="{{ route('admin.data-kandidat.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">close</span>
                                        Reset Filter
                                    </a>
                                @else
                                    <span class="material-symbols-outlined block mb-3" style="font-size:40px; color:#c0c8cb;">person_search</span>
                                    <p class="text-[14px] font-medium text-[#40484b] mb-1">Belum Ada Data Kandidat</p>
                                    <p class="text-[12px] text-[#71787c] mb-4">Tambahkan kandidat baru untuk memulai proses seleksi.</p>
                                    @if (auth()->user()->hasIzin('pengguna.tambah'))
                                        <button type="button" @click="modalTambah = true" class="inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#1E414C] transition-all active:scale-95">
                                            <span class="material-symbols-outlined text-[16px]">add</span>
                                            Tambah Kandidat
                                        </button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER --}}
        <div class="border-t border-[#e0e3e5] px-6 py-4 flex flex-wrap items-center justify-between gap-3">
            <p class="text-[12px] text-[#71787c]">
                Menampilkan
                <span class="font-semibold text-[#191c1e]">{{ $kandidat->firstItem() ?? 0 }}</span>–<span class="font-semibold text-[#191c1e]">{{ $kandidat->lastItem() ?? 0 }}</span>
                dari <span class="font-semibold text-[#191c1e]">{{ $kandidat->total() }}</span> data
            </p>
            @if($kandidat->hasPages())
                {{ $kandidat->links() }}
            @endif
        </div>
    </div>

    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
        <div x-show="modalTambah" x-cloak
             style="background:rgba(0,0,0,0.3);backdrop-filter:blur(5px)"
             class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="w-full max-w-md bg-white rounded-xl shadow-2xl overflow-hidden" @click.outside="modalTambah = false">
                <div class="flex items-center justify-between px-4 py-2.5 border-b border-[#e0e3e5]">
                    <h3 class="text-sm font-semibold text-[#191c1e]">Tambah Akun Kandidat</h3>
                    <button type="button" @click="modalTambah = false" class="text-[#71787c] hover:text-[#191c1e]">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.data-kandidat.simpan') }}">
                    @csrf
                    <input type="hidden" name="_form" value="tambah">
                    <div class="px-4 py-3 space-y-2 max-h-[75vh] overflow-y-auto">
                        {{-- Nama + NIK --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">Nama Kandidat <span class="text-rose-500">*</span></label>
                                <input name="nama_kandidat" type="text" required value="{{ old('nama_kandidat') }}"
                                       class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 @error('nama_kandidat') border-rose-400 @enderror">
                                @error('nama_kandidat')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">NIK KTP <span class="text-rose-500">*</span></label>
                                <input name="nik_kandidat" type="text" maxlength="16" value="{{ old('nik_kandidat') }}"
                                       class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 @error('nik_kandidat') border-rose-400 @enderror">
                                @error('nik_kandidat')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        {{-- Email + No HP --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">Email <span class="text-rose-500">*</span></label>
                                <input name="email" type="email" required value="{{ old('email') }}"
                                       class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 @error('email') border-rose-400 @enderror">
                                @error('email')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">No. HP <span class="text-rose-500">*</span></label>
                                <input name="no_hp" type="text" required value="{{ old('no_hp') }}"
                                       class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 @error('no_hp') border-rose-400 @enderror">
                                @error('no_hp')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        {{-- Tgl Lahir + Jenis Kelamin --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">Tgl. Lahir <span class="text-rose-500">*</span></label>
                                <input name="tanggal_lahir" type="date" required value="{{ old('tanggal_lahir') }}"
                                       class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 @error('tanggal_lahir') border-rose-400 @enderror">
                                @error('tanggal_lahir')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">Jenis Kelamin <span class="text-rose-500">*</span></label>
                                <select name="jenis_kelamin" required
                                        class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 appearance-none bg-white cursor-pointer @error('jenis_kelamin') border-rose-400 @enderror">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" @if(old('jenis_kelamin') === 'L') selected @endif>Laki-laki</option>
                                    <option value="P" @if(old('jenis_kelamin') === 'P') selected @endif>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        {{-- Departemen + Posisi (AJAX) --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">Departemen <span class="text-rose-500">*</span></label>
                                <select id="tambah-kandidat-dept" name="departemen" required
                                        x-model="tambahDeptId"
                                        @change="loadPosisi('posisiTambah', tambahDeptId, null)"
                                        class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 appearance-none bg-white cursor-pointer @error('departemen') border-rose-400 @enderror">
                                    <option value="">-- Pilih --</option>
                                    @foreach($daftarDepartemen as $dept)
                                        <option value="{{ $dept->id }}" @if(old('departemen') == $dept->id) selected @endif>{{ $dept->nama_departemen }}</option>
                                    @endforeach
                                </select>
                                @error('departemen')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">Posisi Dilamar <span class="text-rose-500">*</span></label>
                                <select id="tambah-kandidat-posisi" name="posisi_dilamar" required
                                        class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 appearance-none bg-white cursor-pointer @error('posisi_dilamar') border-rose-400 @enderror">
                                    <option value="">-- Pilih --</option>
                                    <template x-for="p in posisiTambah" :key="p.id">
                                        <option :value="String(p.id)" x-text="p.nama_posisi"></option>
                                    </template>
                                </select>
                                @error('posisi_dilamar')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        {{-- Pendidikan --}}
                        <div>
                            <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">Pendidikan Terakhir <span class="text-rose-500">*</span></label>
                            <input name="pendidikan_terakhir" type="text" required value="{{ old('pendidikan_terakhir') }}" placeholder="contoh: S1 Psikologi"
                                   class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 @error('pendidikan_terakhir') border-rose-400 @enderror">
                            @error('pendidikan_terakhir')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                        </div>
                        {{-- Password --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">Password <span class="text-rose-500">*</span></label>
                                <input name="password" type="password" required
                                       class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 @error('password') border-rose-400 @enderror">
                                @error('password')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">Konfirmasi Password <span class="text-rose-500">*</span></label>
                                <input name="password_confirmation" type="password" required
                                       class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 px-4 py-2.5 bg-[#f7f9fb] border-t border-[#e0e3e5]">
                        <button type="button" @click="modalTambah = false"
                                class="rounded-lg border border-[#c0c8cb] px-3 py-1.5 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">Batal</button>
                        <button type="submit"
                                class="rounded-lg bg-[#2C5F6F] px-3 py-1.5 text-sm font-semibold text-white hover:bg-[#234853] transition-colors">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- MODAL UBAH --}}
    <template x-teleport="body">
        <div x-show="modalUbah" x-cloak
             style="background:rgba(0,0,0,0.3);backdrop-filter:blur(5px)"
             class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="w-full max-w-md bg-white rounded-xl shadow-2xl overflow-hidden" @click.outside="modalUbah = false">
                <div class="flex items-center justify-between px-4 py-2.5 border-b border-[#e0e3e5]">
                    <h3 class="text-sm font-semibold text-[#191c1e]">Edit Data Kandidat</h3>
                    <button type="button" @click="modalUbah = false" class="text-[#71787c] hover:text-[#191c1e]">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>
                <form method="POST" :action="`{{ url('admin/data-kandidat') }}/${ubah.id}`">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form" value="ubah">
                    <input type="hidden" name="id" :value="ubah.id">
                    <div class="px-4 py-3 space-y-2 max-h-[75vh] overflow-y-auto">
                        {{-- Nama + NIK --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">Nama Kandidat <span class="text-rose-500">*</span></label>
                                <input name="nama_kandidat" type="text" required :value="ubah.nama"
                                       class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 @error('nama_kandidat') border-rose-400 @enderror">
                                @error('nama_kandidat')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">NIK KTP</label>
                                <input name="nik_kandidat" type="text" maxlength="16" :value="ubah.nik"
                                       class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 @error('nik_kandidat') border-rose-400 @enderror">
                                @error('nik_kandidat')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        {{-- Email + No HP --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">Email <span class="text-rose-500">*</span></label>
                                <input name="email" type="email" required :value="ubah.email"
                                       class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 @error('email') border-rose-400 @enderror">
                                @error('email')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">No. HP <span class="text-rose-500">*</span></label>
                                <input name="no_hp" type="text" required :value="ubah.no_hp"
                                       class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 @error('no_hp') border-rose-400 @enderror">
                                @error('no_hp')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        {{-- Tgl Lahir + Jenis Kelamin --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">Tgl. Lahir <span class="text-rose-500">*</span></label>
                                <input name="tanggal_lahir" type="date" required :value="ubah.tgl"
                                       class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 @error('tanggal_lahir') border-rose-400 @enderror">
                                @error('tanggal_lahir')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">Jenis Kelamin <span class="text-rose-500">*</span></label>
                                <select name="jenis_kelamin" required
                                        class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 appearance-none bg-white cursor-pointer @error('jenis_kelamin') border-rose-400 @enderror">
                                    <option value="">-- Pilih --</option>
                                    <option value="L" :selected="ubah.jk === 'L'">Laki-laki</option>
                                    <option value="P" :selected="ubah.jk === 'P'">Perempuan</option>
                                </select>
                                @error('jenis_kelamin')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        {{-- Departemen + Posisi (AJAX) --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">Departemen <span class="text-rose-500">*</span></label>
                                <select name="departemen" required
                                        x-model="ubah.deptId"
                                        @change="loadPosisi('posisiUbah', ubah.deptId, null); ubah.posisiId = ''"
                                        class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 appearance-none bg-white cursor-pointer @error('departemen') border-rose-400 @enderror">
                                    <option value="">-- Pilih --</option>
                                    @foreach($daftarDepartemen as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                                    @endforeach
                                </select>
                                @error('departemen')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">Posisi Dilamar <span class="text-rose-500">*</span></label>
                                <select name="posisi_dilamar" required
                                        class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 appearance-none bg-white cursor-pointer @error('posisi_dilamar') border-rose-400 @enderror">
                                    <option value="">-- Pilih --</option>
                                    <template x-for="p in posisiUbah" :key="p.id">
                                        <option :value="String(p.id)" x-text="p.nama_posisi" :selected="String(p.id) === ubah.posisiId"></option>
                                    </template>
                                </select>
                                @error('posisi_dilamar')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        {{-- Pendidikan --}}
                        <div>
                            <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">Pendidikan Terakhir <span class="text-rose-500">*</span></label>
                            <input name="pendidikan_terakhir" type="text" required :value="ubah.pendidikan" placeholder="contoh: S1 Psikologi"
                                   class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 @error('pendidikan_terakhir') border-rose-400 @enderror">
                            @error('pendidikan_terakhir')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                        </div>
                        {{-- Password (opsional) --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">Password Baru <span class="text-[#71787c] font-normal">(opsional)</span></label>
                                <input name="password" type="password"
                                       class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20 @error('password') border-rose-400 @enderror">
                                @error('password')<p class="mt-0.5 text-[11px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-[12px] font-medium text-[#40484b] mb-0.5">Konfirmasi Password</label>
                                <input name="password_confirmation" type="password"
                                       class="w-full rounded-lg border border-[#c0c8cb] px-2.5 py-1.5 text-sm text-[#191c1e] focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 px-4 py-2.5 bg-[#f7f9fb] border-t border-[#e0e3e5]">
                        <button type="button" @click="modalUbah = false"
                                class="rounded-lg border border-[#c0c8cb] px-3 py-1.5 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">Batal</button>
                        <button type="submit"
                                class="rounded-lg bg-[#2C5F6F] px-3 py-1.5 text-sm font-semibold text-white hover:bg-[#234853] transition-colors">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- MODAL HAPUS --}}
    <template x-teleport="body">
        <div x-show="modalHapus.buka" x-cloak
             style="background:rgba(0,0,0,0.3);backdrop-filter:blur(5px)"
             class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div @click.outside="tutupHapus()" class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                <h3 class="text-base font-semibold text-[#191c1e]">Hapus Akun Kandidat?</h3>
                <p class="mt-2 text-sm text-[#40484b]">
                    Anda yakin ingin menghapus akun kandidat <span class="font-semibold" x-text="modalHapus.nama"></span>?
                    Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" @click="tutupHapus()"
                            class="rounded-lg border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">
                        Batal
                    </button>
                    <form method="POST" :action="`{{ url('admin/data-kandidat') }}/${modalHapus.id}`" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700 transition-colors">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL AKSI (approve / tolak) --}}
    <template x-teleport="body">
        <div x-show="modalAksi.buka" x-cloak
             style="background:rgba(0,0,0,0.3);backdrop-filter:blur(5px)"
             class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div @click.outside="tutupAksi()" class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                <h3 class="text-base font-semibold text-[#191c1e]"
                    x-text="modalAksi.tipe === 'approve' ? 'Aktifkan Kandidat?' : 'Tolak Kandidat?'"></h3>
                <p class="mt-2 text-sm text-[#40484b]">
                    <template x-if="modalAksi.tipe === 'approve'">
                        <span>Anda akan mengaktifkan akun kandidat <span class="font-semibold" x-text="modalAksi.nama"></span>. Kandidat akan dapat login dan mengikuti tes.</span>
                    </template>
                    <template x-if="modalAksi.tipe === 'tolak'">
                        <span>Anda akan menolak akun kandidat <span class="font-semibold" x-text="modalAksi.nama"></span>. Data akan tetap disimpan dengan status "Ditolak".</span>
                    </template>
                </p>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" @click="tutupAksi()"
                            class="rounded-lg bg-slate-200 px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">
                        Batal
                    </button>
                    <form method="POST" :action="`{{ url('admin/data-kandidat') }}/${modalAksi.id}/${modalAksi.tipe}`" class="inline">
                        @csrf
                        <button type="submit"
                                :class="modalAksi.tipe === 'approve' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'"
                                class="rounded-lg px-4 py-2 text-sm font-semibold text-white transition-colors">
                            <span x-text="modalAksi.tipe === 'approve' ? 'Ya, Aktifkan' : 'Ya, Tolak'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection
