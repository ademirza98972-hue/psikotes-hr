@extends('layouts.admin', ['judulHalaman' => 'Data Karyawan'])

@section('content')
<div x-data="{ modalHapus: false, idHapus: null, namaHapus: '' }">

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" action="{{ route('admin.data-karyawan.index') }}" class="flex gap-2">
            <input type="text" name="cari" value="{{ $kataKunci }}" placeholder="Cari NIK atau nama..."
                class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
            <button type="submit" class="rounded-md bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Cari</button>
            @if ($kataKunci)
                <a href="{{ route('admin.data-karyawan.index') }}" class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Reset</a>
            @endif
        </form>

        @auth
            @if(auth()->user()->hasIzin('data_karyawan.kelola'))
                <a href="{{ route('admin.data-karyawan.tambah') }}"
                   class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">
                    + Tambah Data Karyawan
                </a>
            @endif
        @endauth
    </div>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                <tr>
                    <th class="px-4 py-3">NIK</th>
                    <th class="px-4 py-3">Nama Karyawan</th>
                    <th class="px-4 py-3">Departemen</th>
                    <th class="px-4 py-3">Jabatan</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($data as $row)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $row->nik_karyawan }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $row->nama_karyawan }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $row->departemen ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $row->jabatan ?? '-' }}</td>
                        <td class="px-4 py-3">
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
                            <span class="inline-block rounded-md border px-2 py-0.5 text-xs font-medium {{ $warnaStatus }}">
                                {{ $labelStatus }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                @auth
                                    @if(auth()->user()->hasIzin('data_karyawan.kelola'))
                                        <a href="{{ route('admin.data-karyawan.ubah', $row->id) }}"
                                           class="rounded-md bg-blue-600 px-3 py-1 text-xs font-semibold text-white shadow-sm hover:bg-blue-700">Edit</a>
                                        @if ($row->status === 'sudah_terpakai')
                                            <button type="button" disabled
                                                    title="NIK ini sudah dipakai untuk registrasi, tidak bisa dihapus. Hapus dulu akun user terkait jika ingin melepas NIK ini."
                                                    class="cursor-not-allowed rounded-md bg-slate-300 px-3 py-1 text-xs font-semibold text-white opacity-60">
                                                Hapus
                                            </button>
                                        @else
                                            <button type="button"
                                                    @click="modalHapus = true; idHapus = {{ $row->id }}; namaHapus = '{{ addslashes($row->nama_karyawan) }}'"
                                                    class="rounded-md bg-red-600 px-3 py-1 text-xs font-semibold text-white shadow-sm hover:bg-red-700">Hapus</button>
                                        @endif
                                    @endif
                                @endauth
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">Tidak ada data karyawan.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $data->links() }}
        </div>
    </div>

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