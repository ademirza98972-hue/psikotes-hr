@extends('layouts.admin', ['judulHalaman' => 'Data Karyawan'])

@section('content')
<div x-data="{ modalHapus: false, idHapus: null, namaHapus: '' }">

    {{-- PAGE HEADER --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-[#191c1e]">Daftar Data Karyawan</h1>
            <p class="mt-1 text-sm text-[#40484b]">Kelola master data karyawan — NIK, departemen, dan jabatan.</p>
        </div>
        @auth
            @if(auth()->user()->hasIzin('data_karyawan.kelola'))
                <a href="{{ route('admin.data-karyawan.tambah') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-[#2C5F6F] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#234853] transition-colors active:scale-[0.98]">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                    Tambah Data Karyawan
                </a>
            @endif
        @endauth
    </div>

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('admin.data-karyawan.index') }}" class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div class="flex flex-1 flex-wrap items-end gap-2">
            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px]">
                <span class="absolute inset-y-0 left-3 flex items-center text-[#71787c]">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                </span>
                <input type="text" name="cari" value="{{ $kataKunci }}"
                    placeholder="Cari NIK atau nama..."
                    class="w-full rounded-lg border border-[#c0c8cb] py-2 pl-9 pr-4 text-sm text-[#191c1e] placeholder-[#71787c] outline-none focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F]/30 transition-all">
            </div>

            {{-- Filter Departemen --}}
            <div class="relative min-w-[180px]">
                <select name="departemen" onchange="this.form.submit()"
                    class="appearance-none w-full rounded-lg border border-[#c0c8cb] py-2 pl-4 pr-10 text-sm text-[#191c1e] outline-none focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F]/30 transition-all cursor-pointer bg-white">
                    <option value="">Semua Departemen</option>
                    @foreach ($semuaDepartemen as $d)
                        <option value="{{ $d->id }}" @if($filterDepartemen == $d->id) selected @endif>{{ $d->nama_departemen }}</option>
                    @endforeach
                </select>
                <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#71787c]">
                    <span class="material-symbols-outlined text-[18px]">expand_more</span>
                </span>
            </div>
            {{-- Filter Status --}}
            <div class="relative min-w-[140px]">
                <select name="status" onchange="this.form.submit()"
                    class="appearance-none w-full rounded-lg border border-[#c0c8cb] py-2 pl-4 pr-10 text-sm text-[#191c1e] outline-none focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F]/30 transition-all cursor-pointer bg-white">
                    <option value="">Semua Status</option>
                    <option value="belum_terpakai" @if($filterStatus === 'belum_terpakai') selected @endif>Belum Terpakai</option>
                    <option value="sudah_terpakai" @if($filterStatus === 'sudah_terpakai') selected @endif>Sudah Terpakai</option>
                </select>
                <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#71787c]">
                    <span class="material-symbols-outlined text-[18px]">expand_more</span>
                </span>
            </div>

            @if ($kataKunci || $filterDepartemen || $filterStatus)
                <a href="{{ route('admin.data-karyawan.index') }}"
                   class="rounded-lg border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors whitespace-nowrap">
                    Reset Filter
                </a>
            @endif
        </div>
    </form>

    {{-- TABLE + PAGINATION + EMPTY STATE --}}
    <div class="rounded-xl border border-[#e0e3e5] bg-white overflow-hidden shadow-sm">

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <caption class="sr-only">Daftar Data Karyawan</caption>
                <thead>
                    <tr class="bg-[#f2f4f6] border-b border-[#e0e3e5]">
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-[#40484b]">NIK</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-[#40484b]">Nama Karyawan</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-[#40484b]">Departemen</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-[#40484b]">Jabatan</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-[#40484b] text-center">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-[#40484b] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e0e3e5]">
                    @forelse ($data as $row)
                        @php
                            $inisial = strtoupper(mb_substr(explode(' ', $row->nama_karyawan)[0], 0, 2));
                            $warnaStatus = match($row->status) {
                                'belum_terpakai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'sudah_terpakai' => 'bg-[#f2f4f6] text-[#40484b] border-[#c0c8cb]',
                                default => 'bg-[#f2f4f6] text-[#40484b] border-[#c0c8cb]',
                            };
                            $labelStatus = match($row->status) {
                                'belum_terpakai' => 'Belum Terpakai',
                                'sudah_terpakai' => 'Sudah Terpakai',
                                default => $row->status,
                            };
                            $dotColor = match($row->status) {
                                'belum_terpakai' => 'bg-emerald-600',
                                'sudah_terpakai' => 'bg-[#40484b]',
                                default => 'bg-[#40484b]',
                            };
                            $avatarBg = match(hexdec(substr(substr(md5($row->nama_karyawan), 0, 6), 0, 6)) % 4) {
                                0 => 'bg-[#2C5F6F]/10 text-[#2C5F6F]',
                                1 => 'bg-blue-100 text-blue-700',
                                2 => 'bg-violet-100 text-violet-700',
                                default => 'bg-amber-100 text-amber-700',
                            };
                        @endphp
                        <tr class="hover:bg-[#f7f9fb] transition-colors group">
                            <td class="px-6 py-4 text-sm font-mono text-[#40484b]">
                                {{ $row->nik_karyawan }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $avatarBg }}">
                                        {{ $inisial }}
                                    </div>
                                    <p class="text-sm font-semibold text-[#191c1e]">{{ $row->nama_karyawan }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#40484b]">
                                {{ $row->departemenRel?->nama_departemen ?? $row->departemen ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-[#40484b]">
                                {{ $row->posisi?->nama_posisi ?? $row->jabatan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $warnaStatus }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} mr-1.5"></span>
                                    {{ $labelStatus }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @auth
                                        @if(auth()->user()->hasIzin('data_karyawan.kelola'))
                                            <a href="{{ route('admin.data-karyawan.ubah', $row->id) }}"
                                               class="p-2 rounded-lg text-[#40484b] hover:text-[#2C5F6F] hover:bg-[#2C5F6F]/10 transition-colors"
                                               title="Edit">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                            </a>
                                            @if ($row->status === 'sudah_terpakai')
                                                <button type="button" disabled
                                                        title="NIK ini sudah dipakai untuk registrasi, tidak bisa dihapus."
                                                        class="p-2 rounded-lg text-[#40484b] opacity-40 cursor-not-allowed"
                                                        aria-disabled="true">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            @else
                                                <button type="button"
                                                        @click="modalHapus = true; idHapus = {{ $row->id }}; namaHapus = '{{ addslashes($row->nama_karyawan) }}'"
                                                        class="p-2 rounded-lg text-[#40484b] hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                                        title="Hapus">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            @endif
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center">
                                @if ($kataKunci || $filterDepartemen || $filterStatus)
                                    <p class="text-sm text-[#40484b]">Tidak ada data karyawan yang cocok dengan filter aktif.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.data-karyawan.index') }}"
                                            class="inline-block rounded-lg border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">
                                            Reset Filter
                                        </a>
                                    </div>
                                @else
                                    <p class="text-sm text-[#40484b]">Belum ada data karyawan. Klik <span class="font-semibold">Tambah Data Karyawan</span> untuk menambahkan.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if ($data->hasPages())
            <div class="border-t border-[#e0e3e5] px-6 py-4 flex items-center justify-between">
                <p class="text-[11px] text-[#40484b]">
                    Menampilkan <span class="font-semibold text-[#191c1e]">{{ $data->firstItem() ?? $data->total() }}</span> -
                    <span class="font-semibold text-[#191c1e]">{{ $data->lastItem() ?? $data->total() }}</span> dari
                    <span class="font-semibold text-[#191c1e]">{{ $data->total() }}</span> data
                </p>
                <div>
                    {{ $data->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- MODAL HAPUS --}}
    <div x-show="modalHapus" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-[#191c1e]/50 px-4">
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl" @click.outside="modalHapus = false">
            <h3 class="text-base font-semibold text-[#191c1e]">Hapus Data Karyawan</h3>
            <p class="mt-2 text-sm text-[#40484b]">
                Apakah Anda yakin ingin menghapus data karyawan <span class="font-semibold text-[#191c1e]" x-text="namaHapus"></span>?
                Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" @click="modalHapus = false"
                        class="rounded-lg border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">Batal</button>
                <form :action="`{{ url('admin/data-karyawan') }}/${idHapus}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700 transition-colors">Hapus</button>
                </form>
            </div>
        </div>
    </div>

</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection
