@extends('layouts.admin', ['judulHalaman' => 'Kelola Peran'])

@section('content')
<div x-data="{ modalHapus: false, idHapus: null, namaHapus: '' }">

    <div class="w-full rounded-lg border border-slate-200 bg-white px-6 pt-3 pb-4 shadow-sm">

        {{-- FILTER BAR --}}
        <form method="GET" action="{{ route('admin.peran.index') }}" class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-1 flex-wrap items-end gap-2">
                {{-- Search --}}
                <input type="text" name="cari" value="{{ $kataKunci }}" placeholder="Cari nama peran..."
                    class="block w-56 rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">

                <button type="submit" class="rounded-md bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                    Terapkan Filter
                </button>

                @if ($kataKunci)
                    <a href="{{ route('admin.peran.index') }}"
                        class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300 whitespace-nowrap">
                        Reset Filter
                    </a>
                @endif
            </div>

            @auth
                @if(auth()->user()->hasIzin('peran.kelola'))
                    <a href="{{ route('admin.peran.tambah') }}"
                       class="self-start rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853] whitespace-nowrap">
                        + Tambah Peran
                    </a>
                @endif
            @endauth
        </form>

        {{-- INDICATOR --}}
        <div class="mb-2 text-xs text-slate-500">
            Menampilkan <strong>{{ $peran->firstItem() ?? $peran->total() }}</strong> dari <strong>{{ $peran->total() }}</strong> peran
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Nama Peran</th>
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3 text-center">Jumlah Izin</th>
                        <th class="px-4 py-3 text-center">Jumlah Pengguna</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($peran as $p)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900">
                                {{ $p->nama_peran }}
                                @if ($p->nama_peran === 'Super Admin')
                                    <span class="ml-2 inline-block rounded-md border border-indigo-700 bg-indigo-600 px-2 py-0.5 text-xs font-medium text-white">Protected</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $p->deskripsi ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-slate-600">{{ $p->izin_count }}</td>
                            <td class="px-4 py-3 text-center text-slate-600">{{ $p->pengguna_count }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex justify-end gap-2">
                                    @auth
                                        @if(auth()->user()->hasIzin('peran.kelola'))
                                            <a href="{{ route('admin.peran.ubah', $p->id) }}"
                                               class="rounded-md bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-700">Edit</a>
                                            @if ($p->nama_peran === 'Super Admin')
                                                <button type="button" disabled
                                                        title="Peran Super Admin tidak dapat dihapus."
                                                        class="cursor-not-allowed rounded-md bg-slate-300 px-3 py-1.5 text-xs font-semibold text-white opacity-60">
                                                    Hapus
                                                </button>
                                            @else
                                                <button type="button"
                                                        @click="modalHapus = true; idHapus = {{ $p->id }}; namaHapus = '{{ addslashes($p->nama_peran) }}'"
                                                        class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-red-700">Hapus</button>
                                            @endif
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center">
                                @if ($kataKunci)
                                    <p class="text-sm text-slate-500">Tidak ada peran yang cocok dengan pencarian "{{ $kataKunci }}".</p>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.peran.index') }}"
                                            class="inline-block rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">
                                            Reset Filter
                                        </a>
                                    </div>
                                @else
                                    <p class="text-sm text-slate-500">Belum ada data peran.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($peran->hasPages())
            <div class="mt-4">
                {{ $peran->links() }}
            </div>
        @endif
    </div>

    <div x-show="modalHapus" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl" @click.outside="modalHapus = false">
            <h3 class="text-base font-semibold text-slate-900">Hapus Peran</h3>
            <p class="mt-2 text-sm text-slate-600">
                Apakah Anda yakin ingin menghapus peran <span class="font-semibold" x-text="namaHapus"></span>?
                Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" @click="modalHapus = false"
                        class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</button>
                <form :action="`{{ url('admin/peran') }}/${idHapus}`" method="POST">
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
