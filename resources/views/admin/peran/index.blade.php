@extends('layouts.admin', ['judulHalaman' => 'Kelola Peran'])

@section('content')
<div x-data="{ modalHapus: false, idHapus: null, namaHapus: '' }">

    <div class="mb-6 flex items-center justify-between">
        <p class="text-sm text-slate-600">Kelola peran dan izin yang berlaku di sistem.</p>
        @auth
            @if(auth()->user()->hasIzin('peran.kelola'))
                <a href="{{ route('admin.peran.tambah') }}"
                   class="rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">
                    + Tambah Peran
                </a>
            @endif
        @endauth
    </div>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                <tr>
                    <th class="px-4 py-3">Nama Peran</th>
                    <th class="px-4 py-3">Deskripsi</th>
                    <th class="px-4 py-3 text-center">Jumlah Izin</th>
                    <th class="px-4 py-3 text-center">Jumlah Pengguna</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
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
                            <div class="flex justify-end gap-2">
                                @auth
                                    @if(auth()->user()->hasIzin('peran.kelola'))
                                        <a href="{{ route('admin.peran.ubah', $p->id) }}"
                                           class="rounded-md bg-blue-600 px-3 py-1 text-xs font-semibold text-white shadow-sm hover:bg-blue-700">Edit</a>
                                        @if ($p->nama_peran === 'Super Admin')
                                            <button type="button" disabled
                                                    title="Peran Super Admin tidak dapat dihapus."
                                                    class="cursor-not-allowed rounded-md bg-slate-300 px-3 py-1 text-xs font-semibold text-white opacity-60">
                                                Hapus
                                            </button>
                                        @else
                                            <button type="button"
                                                    @click="modalHapus = true; idHapus = {{ $p->id }}; namaHapus = '{{ addslashes($p->nama_peran) }}'"
                                                    class="rounded-md bg-red-600 px-3 py-1 text-xs font-semibold text-white shadow-sm hover:bg-red-700">Hapus</button>
                                        @endif
                                    @endif
                                @endauth
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">Tidak ada data peran.</td></tr>
                @endforelse
            </tbody>
        </table>
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