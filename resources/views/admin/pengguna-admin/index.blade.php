@extends('layouts.admin', ['judulHalaman' => 'Kelola Admin & Staff'])

@section('content')
<div x-data="{ modalHapus: false, idHapus: null, namaHapus: '' }">

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" action="{{ route('admin.pengguna-admin.index') }}" class="flex flex-1 gap-2">
            <input type="text" name="cari" value="{{ $kataKunci }}" placeholder="Cari nama atau email..."
                class="flex-1 rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
            <button type="submit" class="rounded-md bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Cari</button>
            @if ($kataKunci)
                <a href="{{ route('admin.pengguna-admin.index') }}" class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Reset</a>
            @endif
        </form>

        <a href="{{ route('admin.pengguna-admin.tambah') }}"
           class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">
            + Tambah Admin/Staff
        </a>
    </div>

    <div class="mb-3 border-b border-slate-200 pb-3 text-xs text-slate-500">
        Halaman ini mengelola akun internal (admin, HR, dan staff sistem lainnya). Akun karyawan dan kandidat dikelola secara terpisah di <a href="{{ route('admin.akun-karyawan.index') }}" class="text-[#2C5F6F] hover:underline">Akun Karyawan</a> dan <a href="{{ route('admin.data-kandidat.index') }}" class="text-[#2C5F6F] hover:underline">Data Kandidat</a>.
    </div>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Peran</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($pengguna as $u)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $u->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $u->email }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $u->peran->nama_peran ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $warnaStatus = match($u->status) {
                                    'aktif' => 'bg-emerald-600 text-white border-emerald-700',
                                    'nonaktif' => 'bg-slate-500 text-white border-slate-600',
                                    default => 'bg-slate-500 text-white border-slate-600',
                                };
                                $labelStatus = match($u->status) {
                                    'aktif' => 'Aktif',
                                    'nonaktif' => 'Nonaktif',
                                    default => $u->status,
                                };
                            @endphp
                            <span class="inline-block rounded-md border px-2 py-0.5 text-xs font-medium {{ $warnaStatus }}">
                                {{ $labelStatus }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.pengguna-admin.ubah', $u->id) }}"
                                   class="rounded-md bg-blue-600 px-3 py-1 text-xs font-semibold text-white shadow-sm hover:bg-blue-700">Edit</a>
                                @if ($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.pengguna-admin.toggle-status', $u->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="rounded-md px-3 py-1 text-xs font-semibold text-white shadow-sm {{ $u->status === 'aktif' ? 'bg-slate-600 hover:bg-slate-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                                            {{ $u->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                @endif
                                @if ($u->id !== auth()->id())
                                    <button type="button"
                                            @click="modalHapus = true; idHapus = {{ $u->id }}; namaHapus = '{{ addslashes($u->name) }}'"
                                            class="rounded-md bg-red-600 px-3 py-1 text-xs font-semibold text-white shadow-sm hover:bg-red-700">Hapus</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">Tidak ada data pengguna internal.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $pengguna->links() }}
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div x-show="modalHapus" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl" @click.outside="modalHapus = false">
            <h3 class="text-base font-semibold text-slate-900">Hapus Pengguna Internal</h3>
            <p class="mt-2 text-sm text-slate-600">
                Apakah Anda yakin ingin menghapus pengguna <span class="font-semibold" x-text="namaHapus"></span>?
                Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" @click="modalHapus = false"
                        class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</button>
                <form :action="`{{ url('admin/pengguna-admin') }}/${idHapus}`" method="POST">
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