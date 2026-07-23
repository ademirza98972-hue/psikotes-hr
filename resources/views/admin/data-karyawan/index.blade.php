@extends('layouts.admin', ['judulHalaman' => 'Data Karyawan'])

@section('content')
<div x-data="{ modalHapus: false, idHapus: null, namaHapus: '' }">

    {{-- OUTER WRAPPER FOR STYLING --}}
    <div class="w-full rounded-lg border border-slate-200 bg-white p-6 shadow-sm">

        {{-- HEADER --}}
        <div class="mb-4 flex flex-col items-start justify-between gap-3 md:flex-row md:items-center">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Daftar Data Karyawan</h2>
                <p class="mt-1 text-sm text-slate-500">Kelola data master karyawan yang tersedia di sistem.</p>
            </div>
            <div class="flex items-center gap-2">
                @auth
                    @if(auth()->user()->hasIzin('data_karyawan.kelola'))
                        <a href="{{ route('admin.data-karyawan.tambah') }}"
                           class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">
                            + Tambah Data Karyawan
                        </a>
                    @endif
                @endauth
            </div>
        </div>

        {{-- FILTER BAR --}}
        <form method="GET" action="{{ route('admin.data-karyawan.index') }}" class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-1 flex-wrap items-end gap-2">
                {{-- Search --}}
                <input type="text" name="cari" value="{{ $kataKunci }}" placeholder="Cari NIK atau nama..."
                    class="block w-56 rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">

                {{-- Filter Departemen --}}
                <select name="departemen"
                    class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    <option value="">Semua Departemen</option>
                    @foreach ($semuaDepartemen as $d)
                        <option value="{{ $d->id }}" @if($filterDepartemen == $d->id) selected @endif>{{ $d->nama_departemen }}</option>
                    @endforeach
                </select>

                {{-- Filter Status --}}
                <select name="status"
                    class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    <option value="">Semua Status</option>
                    <option value="belum_terpakai" @if($filterStatus === 'belum_terpakai') selected @endif>Belum Terpakai</option>
                    <option value="sudah_terpakai" @if($filterStatus === 'sudah_terpakai') selected @endif>Sudah Terpakai</option>
                </select>

                <button type="submit" class="rounded-md bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                    Terapkan Filter
                </button>

                @if ($kataKunci || $filterDepartemen || $filterStatus)
                    <a href="{{ route('admin.data-karyawan.index') }}"
                        class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300 whitespace-nowrap">
                        Reset Filter
                    </a>
                @endif
            </div>
        </form>

        {{-- INDICATOR --}}
        <div class="mb-2 text-xs text-slate-500">
            Menampilkan <strong>{{ $data->firstItem() ?? $data->total() }}</strong> dari <strong>{{ $data->total() }}</strong> data
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">NIK</th>
                        <th class="px-4 py-3">Nama Karyawan</th>
                        <th class="px-4 py-3">Departemen</th>
                        <th class="px-4 py-3">Jabatan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($data as $row)
                        @php
                            $warnaStatus = match($row->status) {
                                'belum_terpakai' => 'bg-emerald-600 text-white border-emerald-700',
                                'sudah_terpakai' => 'bg-slate-500 text-white border-slate-600',
                                default => 'bg-slate-500 text-white border-slate-600',
                            };
                            $labelStatus = match($row->status) {
                                'belum_terpakai' => 'Belum Terpakai',
                                'sudah_terpakai' => 'Sudah Terpakai',
                                default => $row->status,
                            };
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $row->nik_karyawan }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->nama_karyawan }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->departemen ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $row->jabatan ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $warnaStatus }}">
                                    {{ $labelStatus }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center justify-end gap-2">
                                    @auth
                                        @if(auth()->user()->hasIzin('data_karyawan.kelola'))
                                            <a href="{{ route('admin.data-karyawan.ubah', $row->id) }}"
                                               class="rounded-md bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-700">Edit</a>
                                            @if ($row->status === 'sudah_terpakai')
                                                <button type="button" disabled
                                                        title="NIK ini sudah dipakai untuk registrasi, tidak bisa dihapus."
                                                        class="cursor-not-allowed rounded-md bg-slate-300 px-3 py-1.5 text-xs font-semibold text-white opacity-60">Hapus</button>
                                            @else
                                                <button type="button"
                                                        @click="modalHapus = true; idHapus = {{ $row->id }}; namaHapus = '{{ addslashes($row->nama_karyawan) }}'"
                                                        class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-red-700">Hapus</button>
                                            @endif
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center">
                                @if ($kataKunci || $filterDepartemen || $filterStatus)
                                    <p class="text-sm text-slate-500">Tidak ada data karyawan yang cocok dengan filter aktif.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.data-karyawan.index') }}"
                                            class="inline-block rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">
                                            Reset Filter
                                        </a>
                                    </div>
                                @else
                                    <p class="text-sm text-slate-500">Belum ada data karyawan. Klik <span class="font-medium">+ Tambah Data Karyawan</span> untuk menambahkan.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($data->hasPages())
            <div class="mt-4">
                {{ $data->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL HAPUS --}}
    <div x-show="modalHapus" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl" @click.outside="modalHapus = false">
            <h3 class="text-base font-semibold text-slate-900">Hapus Data Karyawan</h3>
            <p class="mt-2 text-sm text-slate-600">
                Apakah Anda yakin ingin menghapus data karyawan <span class="font-semibold" x-text="namaHapus"></span>?
                Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" @click="modalHapus = false"
                        class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</button>
                <form :action="`{{ url('admin/data-karyawan') }}/${idHapus}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="rounded-md bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Hapus</button>
                </form>
            </div>
        </div>
    </div>

</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection
