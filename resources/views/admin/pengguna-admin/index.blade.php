@extends('layouts.admin', ['judulHalaman' => 'Kelola Admin & Staff'])

@section('content')
@php
    $bisaEdit = auth()->user()->hasIzin('pengguna.edit');
    $bisaHapus = auth()->user()->hasIzin('pengguna.hapus');
    $bisaToggle = auth()->user()->hasIzin('pengguna.edit');
@endphp
<div x-data="{ modalHapus: false, idHapus: null, namaHapus: '' }">

    <div class="w-full rounded-lg border border-slate-200 bg-white px-6 pt-3 pb-4 shadow-sm">

        {{-- FILTER BAR --}}
        <form method="GET" action="{{ route('admin.pengguna-admin.index') }}" class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-1 flex-wrap items-end gap-2">
                {{-- Search --}}
                <input type="text" name="cari" value="{{ $kataKunci }}" placeholder="Cari nama atau email..."
                    class="block w-56 rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">

                {{-- Filter Peran --}}
                <select name="peran"
                    class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    <option value="">Semua Peran</option>
                    @foreach ($semuaPeran as $r)
                        <option value="{{ $r->id }}" @if($filterPeran == $r->id) selected @endif>{{ $r->nama_peran }}</option>
                    @endforeach
                </select>

                {{-- Filter Status --}}
                <select name="status"
                    class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    <option value="">Semua Status</option>
                    <option value="aktif" @if($filterStatus === 'aktif') selected @endif>Aktif</option>
                    <option value="nonaktif" @if($filterStatus === 'nonaktif') selected @endif>Nonaktif</option>
                </select>

                <button type="submit" class="rounded-md bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                    Terapkan Filter
                </button>

                @if ($kataKunci || $filterPeran || $filterStatus)
                    <a href="{{ route('admin.pengguna-admin.index') }}"
                        class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300 whitespace-nowrap">
                        Reset Filter
                    </a>
                @endif
            </div>

            @auth
                @if(auth()->user()->hasIzin('pengguna.tambah'))
                    <a href="{{ route('admin.pengguna-admin.tambah') }}"
                       class="self-start rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853] whitespace-nowrap">
                        + Tambah Admin/Staff
                    </a>
                @endif
            @endauth
        </form>

        {{-- INDICATOR --}}
        <div class="mb-2 text-xs text-slate-500">
            Menampilkan <strong>{{ $pengguna->firstItem() ?? $pengguna->total() }}</strong> dari <strong>{{ $pengguna->total() }}</strong> pengguna
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Peran</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($pengguna as $u)
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
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $u->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $u->email }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $u->peran->nama_peran ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $warnaStatus }}">
                                    {{ $labelStatus }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center justify-end gap-2">
                                    @auth
                                        @if($bisaEdit)
                                            <a href="{{ route('admin.pengguna-admin.ubah', $u->id) }}"
                                               class="rounded-md bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-700">Edit</a>
                                            @if ($u->status !== 'aktif' || $u->peran->nama_peran !== 'Super Admin')
                                                @if ($u->status === 'aktif')
                                                    <form method="POST" action="{{ route('admin.pengguna-admin.toggle-status', $u->id) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                class="rounded-md px-3 py-1.5 text-xs font-semibold text-white shadow-sm bg-slate-600 hover:bg-slate-700">Nonaktifkan</button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.pengguna-admin.toggle-status', $u->id) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                class="rounded-md px-3 py-1.5 text-xs font-semibold text-white shadow-sm bg-emerald-600 hover:bg-emerald-700">Aktifkan</button>
                                                    </form>
                                                @endif
                                            @endif
                                        @endif
                                        @if($bisaHapus && $u->id !== auth()->id())
                                            <button type="button"
                                                    @click="modalHapus = true; idHapus = {{ $u->id }}; namaHapus = '{{ addslashes($u->name) }}'"
                                                    class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-red-700">Hapus</button>
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center">
                                @if ($kataKunci || $filterPeran || $filterStatus)
                                    <p class="text-sm text-slate-500">Tidak ada data pengguna internal yang cocok dengan filter aktif.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.pengguna-admin.index') }}"
                                            class="inline-block rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">
                                            Reset Filter
                                        </a>
                                    </div>
                                @else
                                    <p class="text-sm text-slate-500">Belum ada data pengguna internal. Klik <span class="font-medium">+ Tambah Admin/Staff</span> untuk menambahkan.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pengguna->hasPages())
            <div class="mt-4">
                {{ $pengguna->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL KONFIRMASI HAPUS --}}
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
