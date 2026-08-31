@extends('layouts.admin', ['judulHalaman' => 'Akun Karyawan'])

@section('content')
<div x-data="{
    modalHapus: false, idHapus: null, namaHapus: '',
    modalTambah: false,
    modalUbah: false,
    ubah: { id: '', nik: '', nama: '', jenis_kelamin: '', tanggal_lahir: '', pendidikan_terakhir: '', departemen_id: '', posisi_id: '', email: '', no_hp: '' },
    openUbah(data) {
        this.ubah = data;
        this.modalUbah = true;
    },
    posisiList: [],
    posisiUbahList: [],
    async loadPosisi(deptId, target) {
        if (!deptId) { this[target] = []; return; }
        const res = await fetch(`/api/posisi/daftar/${deptId}`);
        this[target] = await res.json();
    }
}"
x-init="
    @if(old('_form') === 'tambah') $nextTick(() => { modalTambah = true; }); @endif
    @if(old('_form') === 'ubah') $nextTick(() => { openUbah({
        id: '{{ (int)old('id',0) }}',
        nik: @js(old('nik_karyawan','')),
        nama: @js(old('nama_karyawan','')),
        jenis_kelamin: @js(old('jenis_kelamin','')),
        tanggal_lahir: @js(old('tanggal_lahir','')),
        pendidikan_terakhir: @js(old('pendidikan_terakhir','')),
        departemen_id: @js(old('departemen_id','')),
        posisi_id: @js(old('posisi_id','')),
        email: @js(old('email','')),
        no_hp: @js(old('no_hp','')),
    }); }); @endif
">

    {{-- STICKY HEADER --}}
    <div class="sticky top-0 z-30 bg-[#f7f9fb] -mx-6 px-6 pt-6 pb-4 border-b border-[#e0e3e5]">
        <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-xl font-bold text-[#191c1e]">Daftar Akun Karyawan</h1>
                <p class="mt-1 text-sm text-[#40484b]">Kelola data dan akun seluruh karyawan perusahaan.</p>
            </div>
            @if(auth()->user()->hasIzin('pengguna.tambah'))
                <button type="button" @click="modalTambah = true"
                   class="inline-flex items-center gap-2 rounded-lg bg-[#2C5F6F] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#234853] transition-colors active:scale-[0.98]">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                    Tambah Karyawan
                </button>
            @endif
        </div>

        <form method="GET" action="{{ route('admin.akun-karyawan.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex flex-1 flex-wrap items-end gap-2">
                <div class="relative flex-1 min-w-[200px]">
                    <span class="absolute inset-y-0 left-3 flex items-center text-[#71787c]">
                        <span class="material-symbols-outlined text-[18px]">search</span>
                    </span>
                    <input type="text" name="cari" value="{{ $kataKunci }}"
                        placeholder="Cari nama atau NIK..."
                        class="w-full rounded-lg border border-[#c0c8cb] py-2 pl-9 pr-4 text-sm text-[#191c1e] placeholder-[#71787c] outline-none focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F]/30 transition-all">
                </div>
                <div class="relative min-w-[180px]">
                    <select name="departemen" onchange="this.form.submit()"
                        class="appearance-none w-full rounded-lg border border-[#c0c8cb] py-2 pl-4 pr-10 text-sm text-[#191c1e] outline-none focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F]/30 transition-all cursor-pointer bg-white">
                        <option value="">Semua Departemen</option>
                        @foreach ($semuaDepartemen as $item)
                            <option value="{{ $item->id }}" @if($filterDepartemen == $item->id) selected @endif>{{ $item->nama_departemen }}</option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#71787c]">
                        <span class="material-symbols-outlined text-[18px]">expand_more</span>
                    </span>
                </div>
                <div class="relative min-w-[180px]">
                    <select name="posisi" onchange="this.form.submit()"
                        class="appearance-none w-full rounded-lg border border-[#c0c8cb] py-2 pl-4 pr-10 text-sm text-[#191c1e] outline-none focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F]/30 transition-all cursor-pointer bg-white">
                        <option value="">Semua Posisi</option>
                        @foreach ($semuaPosisi as $item)
                            <option value="{{ $item->id }}" @if($filterPosisi == $item->id) selected @endif>{{ $item->nama_posisi }}</option>
                        @endforeach
                    </select>
                    <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#71787c]">
                        <span class="material-symbols-outlined text-[18px]">expand_more</span>
                    </span>
                </div>
                @if($kataKunci || $filterDepartemen || $filterPosisi)
                    <a href="{{ route('admin.akun-karyawan.index') }}"
                       class="rounded-lg border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors whitespace-nowrap">
                        Reset Filter
                    </a>
                @endif
            </div>
        </form>
    </div>{{-- /STICKY HEADER --}}

    <div class="mt-4 rounded-xl border border-[#e0e3e5] bg-white overflow-hidden shadow-sm">
        <div style="overflow: auto; max-height: calc(100vh - 300px);">
            <table class="w-full text-left border-collapse">
                <caption class="sr-only">Daftar Akun Karyawan</caption>
                <thead style="position: sticky; top: 0; z-index: 5;">
                    <tr style="background: #0f2230; border-bottom: 1px solid #0a1a25;">
                        <th class="px-4 py-3.5 text-[11px] font-bold uppercase tracking-wider text-center" style="color:#7db8c2;width:52px">No.</th>
                        <th class="px-4 py-3.5 text-[11px] font-bold uppercase tracking-wider" style="color:#7db8c2;">ID Karyawan</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider" style="color:#7db8c2;">NIK</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider" style="color:#7db8c2;">Nama Karyawan</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider" style="color:#7db8c2;">Departemen</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider" style="color:#7db8c2;">Posisi</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-center" style="color:#7db8c2;">Status Akun</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-right" style="color:#7db8c2;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e0e3e5]">
                    @forelse ($data as $dk)
                        @php
                            $u = $dk->user;
                            $inisial = strtoupper(mb_substr(explode(' ', $u?->name ?? '?')[0], 0, 2));
                            $warnaStatus = match($u?->status) {
                                'aktif'    => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'nonaktif' => 'bg-[#f2f4f6] text-[#40484b] border-[#c0c8cb]',
                                default    => 'bg-amber-50 text-amber-700 border-amber-200',
                            };
                            $labelStatus = match($u?->status) {
                                'aktif'    => 'Aktif',
                                'nonaktif' => 'Nonaktif',
                                default    => '—',
                            };
                            $dotColor = match($u?->status) {
                                'aktif'    => 'bg-emerald-600',
                                'nonaktif' => 'bg-[#40484b]',
                                default    => 'bg-amber-500',
                            };
                            $avatarBg = match(hexdec(substr(md5($u?->name ?? ''), 0, 6)) % 4) {
                                0 => 'bg-[#2C5F6F]/10 text-[#2C5F6F]',
                                1 => 'bg-blue-100 text-blue-700',
                                2 => 'bg-violet-100 text-violet-700',
                                default => 'bg-amber-100 text-amber-700',
                            };
                        @endphp
                        <tr class="hover:bg-[#f7f9fb] transition-colors group">
                            <td class="px-4 py-4 text-sm text-[#71787c] tabular-nums text-center">{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td class="px-4 py-4 text-sm font-mono font-semibold text-[#2C5F6F]">
                                {{ $u?->id_karyawan ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-mono text-[#40484b]">
                                {{ $dk->nik_karyawan }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $avatarBg }}">
                                        {{ $inisial }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-[#191c1e]">{{ $u?->name ?? '—' }}</p>
                                        <p class="text-[11px] text-[#40484b]">{{ $u?->email ?? 'Tanpa email' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#40484b]">{{ $dk->departemen?->nama_departemen ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-[#40484b]">{{ $dk->posisi?->nama_posisi ?? '—' }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $warnaStatus }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} mr-1.5"></span>
                                        {{ $labelStatus }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if(auth()->user()->hasIzin('pengguna.edit'))
                                        <button type="button"
                                                @click="openUbah({
                                                    id: '{{ $dk->id }}',
                                                    nik: @js($dk->nik_karyawan),
                                                    nama: @js($u?->name ?? ''),
                                                    jenis_kelamin: @js($dk->jenis_kelamin ?? ''),
                                                    tanggal_lahir: @js($dk->tanggal_lahir?->format('Y-m-d') ?? ''),
                                                    pendidikan_terakhir: @js($dk->pendidikan_terakhir ?? ''),
                                                    departemen_id: '{{ $dk->departemen_id }}',
                                                    posisi_id: '{{ $dk->posisi_id }}',
                                                    email: @js($u?->email ?? ''),
                                                    no_hp: @js($dk->no_hp ?? ''),
                                                })"
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                title="Edit">
                                            <span class="material-symbols-outlined text-[20px]">edit_square</span>
                                        </button>
                                    @endif
                                    @if(auth()->user()->hasIzin('pengguna.edit') && $u?->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.akun-karyawan.toggle-status', $dk->id) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="p-2 rounded-lg text-[#40484b] hover:text-[#2C5F6F] hover:bg-[#2C5F6F]/10 transition-colors"
                                                    title="{{ $u?->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <span class="material-symbols-outlined text-[18px]">{{ $u?->status === 'aktif' ? 'block' : 'check_circle' }}</span>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.akun-karyawan.reset-password', $dk->id) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('Reset password {{ addslashes($u?->name ?? '') }} ke ID Karyawan ({{ $u?->id_karyawan ?? '' }})?')"
                                                    class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 transition-colors"
                                                    title="Reset Password ke ID Karyawan">
                                                <span class="material-symbols-outlined text-[18px]">lock_reset</span>
                                            </button>
                                        </form>
                                    @endif
                                    @if(auth()->user()->hasIzin('pengguna.hapus') && $u?->id !== auth()->id())
                                        <button type="button"
                                                @click="modalHapus = true; idHapus = {{ $dk->id }}; namaHapus = '{{ addslashes($u?->name ?? $dk->nik_karyawan) }}'"
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
                            <td colspan="8" class="px-6 py-14 text-center">
                                @if($kataKunci || $filterDepartemen || $filterPosisi)
                                    <span class="material-symbols-outlined block mb-3" style="font-size:40px; color:#c0c8cb;">search_off</span>
                                    <p class="text-[14px] font-medium text-[#40484b] mb-1">Tidak ada hasil yang cocok</p>
                                    <a href="{{ route('admin.akun-karyawan.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">Reset Filter</a>
                                @else
                                    <span class="material-symbols-outlined block mb-3" style="font-size:40px; color:#c0c8cb;">group</span>
                                    <p class="text-[14px] font-medium text-[#40484b] mb-1">Belum Ada Data Karyawan</p>
                                    @if(auth()->user()->hasIzin('pengguna.tambah'))
                                        <button type="button" @click="modalTambah = true" class="inline-flex items-center gap-2 rounded-xl bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#1E414C] transition-all active:scale-95 mt-3">
                                            <span class="material-symbols-outlined text-[16px]">add</span>
                                            Tambah Karyawan
                                        </button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-[#e0e3e5] px-6 py-4 flex flex-wrap items-center justify-between gap-3">
            <p class="text-[12px] text-[#71787c]">
                Menampilkan
                <span class="font-semibold text-[#191c1e]">{{ $data->firstItem() ?? 0 }}</span>–<span class="font-semibold text-[#191c1e]">{{ $data->lastItem() ?? 0 }}</span>
                dari <span class="font-semibold text-[#191c1e]">{{ $data->total() }}</span> data
            </p>
            @if($data->hasPages())
                {{ $data->links() }}
            @endif
        </div>
    </div>

    {{-- MODAL TAMBAH --}}
    <template x-teleport="body">
        <div x-show="modalTambah" x-cloak
             style="background:rgba(0,0,0,0.4);backdrop-filter:blur(4px)"
             class="fixed inset-0 z-50 flex items-center justify-center px-4 overflow-y-auto">
            <div class="my-6 w-full max-w-xl bg-white rounded-xl shadow-2xl overflow-hidden" @click.outside="modalTambah = false">
                <div class="flex items-center justify-between px-4 py-2.5 border-b border-[#e0e3e5]">
                    <div>
                        <h3 class="text-sm font-semibold text-[#191c1e]">Tambah Karyawan</h3>
                        <p class="text-[11px] text-[#71787c]">ID Karyawan & password dibuat otomatis.</p>
                    </div>
                    <button type="button" @click="modalTambah = false" class="text-[#71787c] hover:text-[#191c1e] p-1">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.akun-karyawan.simpan') }}">
                    @csrf
                    <input type="hidden" name="_form" value="tambah">
                    <div class="px-4 py-3 space-y-2 max-h-[75vh] overflow-y-auto">
                        @php $inp = 'w-full rounded-md border border-[#c0c8cb] px-2.5 py-1.5 text-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20'; @endphp
                        @php $lbl = 'block text-[11px] font-medium text-[#40484b] mb-0.5'; @endphp
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="{{ $lbl }}">Nama Lengkap <span class="text-rose-500">*</span></label>
                                <input name="nama_karyawan" type="text" required value="{{ old('nama_karyawan') }}" class="{{ $inp }} @error('nama_karyawan') border-rose-400 @enderror">
                                @error('nama_karyawan')<p class="text-[10px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="{{ $lbl }}">NIK Karyawan <span class="text-rose-500">*</span></label>
                                <input name="nik_karyawan" type="text" required value="{{ old('nik_karyawan') }}" class="{{ $inp }} @error('nik_karyawan') border-rose-400 @enderror">
                                @error('nik_karyawan')<p class="text-[10px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="{{ $lbl }}">Jenis Kelamin <span class="text-rose-500">*</span></label>
                                <select name="jenis_kelamin" required class="{{ $inp }} @error('jenis_kelamin') border-rose-400 @enderror">
                                    <option value="">Pilih</option>
                                    <option value="L" @if(old('jenis_kelamin')=='L') selected @endif>Laki-laki</option>
                                    <option value="P" @if(old('jenis_kelamin')=='P') selected @endif>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')<p class="text-[10px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="{{ $lbl }}">Tanggal Lahir <span class="text-rose-500">*</span></label>
                                <input name="tanggal_lahir" type="date" required value="{{ old('tanggal_lahir') }}" class="{{ $inp }} @error('tanggal_lahir') border-rose-400 @enderror">
                                @error('tanggal_lahir')<p class="text-[10px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div x-data="{ deptId: '{{ old('departemen_id') }}' }" class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="{{ $lbl }}">Departemen <span class="text-rose-500">*</span></label>
                                <select name="departemen_id" required x-model="deptId" @change="loadPosisi($event.target.value, 'posisiList')" class="{{ $inp }} @error('departemen_id') border-rose-400 @enderror">
                                    <option value="">Pilih Departemen</option>
                                    @foreach ($semuaDepartemen as $dept)
                                        <option value="{{ $dept->id }}" @if(old('departemen_id')==$dept->id) selected @endif>{{ $dept->nama_departemen }}</option>
                                    @endforeach
                                </select>
                                @error('departemen_id')<p class="text-[10px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="{{ $lbl }}">Posisi <span class="text-rose-500">*</span></label>
                                <select name="posisi_id" required class="{{ $inp }} @error('posisi_id') border-rose-400 @enderror">
                                    <option value="">Pilih Posisi</option>
                                    <template x-for="p in posisiList" :key="p.id">
                                        <option :value="p.id" x-text="p.nama_posisi"></option>
                                    </template>
                                </select>
                                @error('posisi_id')<p class="text-[10px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="{{ $lbl }}">Pendidikan Terakhir</label>
                                <input name="pendidikan_terakhir" type="text" value="{{ old('pendidikan_terakhir') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lbl }}">No. HP</label>
                                <input name="no_hp" type="text" value="{{ old('no_hp') }}" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lbl }}">Email <span class="text-[#71787c] font-normal">(opsional)</span></label>
                                <input name="email" type="email" value="{{ old('email') }}" placeholder="—" class="{{ $inp }} @error('email') border-rose-400 @enderror">
                                @error('email')<p class="text-[10px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="flex items-start gap-2 rounded-md bg-blue-50 border border-blue-200 px-3 py-2 text-[11px] text-blue-700">
                            <span class="material-symbols-outlined text-[13px] mt-0.5 shrink-0">info</span>
                            <span>ID Karyawan & password awal dibuat otomatis (contoh: <strong>2026001</strong>). Karyawan wajib ganti password saat login pertama.</span>
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
             style="background:rgba(0,0,0,0.4);backdrop-filter:blur(4px)"
             class="fixed inset-0 z-50 flex items-center justify-center px-4 overflow-y-auto">
            <div class="my-6 w-full max-w-xl bg-white rounded-xl shadow-2xl overflow-hidden" @click.outside="modalUbah = false">
                <div class="flex items-center justify-between px-4 py-2.5 border-b border-[#e0e3e5]">
                    <h3 class="text-sm font-semibold text-[#191c1e]">Edit Data Karyawan</h3>
                    <button type="button" @click="modalUbah = false" class="text-[#71787c] hover:text-[#191c1e] p-1">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
                <form method="POST" :action="`{{ url('admin/akun-karyawan') }}/${ubah.id}`">
                    @csrf @method('PUT')
                    <input type="hidden" name="_form" value="ubah">
                    <input type="hidden" name="id" :value="ubah.id">
                    <div class="px-4 py-3 space-y-2 max-h-[75vh] overflow-y-auto">
                        @php $inp = 'w-full rounded-md border border-[#c0c8cb] px-2.5 py-1.5 text-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]/20'; @endphp
                        @php $lbl = 'block text-[11px] font-medium text-[#40484b] mb-0.5'; @endphp
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="{{ $lbl }}">Nama Lengkap <span class="text-rose-500">*</span></label>
                                <input name="nama_karyawan" type="text" required :value="ubah.nama" class="{{ $inp }}">
                                @error('nama_karyawan')<p class="text-[10px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="{{ $lbl }}">NIK Karyawan <span class="text-rose-500">*</span></label>
                                <input name="nik_karyawan" type="text" required :value="ubah.nik" class="{{ $inp }}">
                                @error('nik_karyawan')<p class="text-[10px] text-rose-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="{{ $lbl }}">Jenis Kelamin <span class="text-rose-500">*</span></label>
                                <select name="jenis_kelamin" required class="{{ $inp }}">
                                    <option value="L" :selected="ubah.jenis_kelamin === 'L'">Laki-laki</option>
                                    <option value="P" :selected="ubah.jenis_kelamin === 'P'">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="{{ $lbl }}">Tanggal Lahir <span class="text-rose-500">*</span></label>
                                <input name="tanggal_lahir" type="date" required :value="ubah.tanggal_lahir" class="{{ $inp }}">
                            </div>
                        </div>
                        <div x-data="{ deptId: ubah.departemen_id }" x-init="$watch('ubah.departemen_id', v => { deptId = v; loadPosisi(v, 'posisiUbahList'); }); if(ubah.departemen_id) loadPosisi(ubah.departemen_id, 'posisiUbahList');" class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="{{ $lbl }}">Departemen <span class="text-rose-500">*</span></label>
                                <select name="departemen_id" required x-model="deptId" @change="loadPosisi($event.target.value, 'posisiUbahList')" class="{{ $inp }}">
                                    <option value="">Pilih Departemen</option>
                                    @foreach ($semuaDepartemen as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="{{ $lbl }}">Posisi <span class="text-rose-500">*</span></label>
                                <select name="posisi_id" required class="{{ $inp }}">
                                    <option value="">Pilih Posisi</option>
                                    <template x-for="p in posisiUbahList" :key="p.id">
                                        <option :value="p.id" :selected="p.id == ubah.posisi_id" x-text="p.nama_posisi"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="{{ $lbl }}">Pendidikan Terakhir</label>
                                <input name="pendidikan_terakhir" type="text" :value="ubah.pendidikan_terakhir" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lbl }}">No. HP</label>
                                <input name="no_hp" type="text" :value="ubah.no_hp" class="{{ $inp }}">
                            </div>
                            <div>
                                <label class="{{ $lbl }}">Email <span class="text-[#71787c] font-normal">(opsional)</span></label>
                                <input name="email" type="email" :value="ubah.email" placeholder="—" class="{{ $inp }}">
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
        <div x-show="modalHapus" x-cloak
             style="background:rgba(0,0,0,0.3);backdrop-filter:blur(5px)"
             class="fixed inset-0 z-50 flex items-center justify-center px-4">
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl" @click.outside="modalHapus = false">
                <h3 class="text-base font-semibold text-[#191c1e]">Hapus Data Karyawan</h3>
                <p class="mt-2 text-sm text-[#40484b]">
                    Apakah Anda yakin ingin menghapus <span class="font-semibold text-[#191c1e]" x-text="namaHapus"></span>?
                    Data dan akun karyawan ini akan dihapus.
                </p>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" @click="modalHapus = false"
                            class="rounded-lg border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">Batal</button>
                    <form :action="`{{ url('admin/akun-karyawan') }}/${idHapus}`" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700 transition-colors">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection
