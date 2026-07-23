@extends('layouts.admin', ['judulHalaman' => 'Akun Karyawan'])

@section('content')
<div x-data="{ modalHapus: false, idHapus: null, namaHapus: '' }">

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('admin.akun-karyawan.index') }}" class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
            {{-- Search --}}
            <input type="text" name="cari" value="{{ $kataKunci }}" placeholder="Cari nama, email, atau NIK..."
                class="flex-1 rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">

            {{-- Filter Departemen --}}
            <select id="filterDepartemen" name="departemen" onchange="this.form.submit()"
                class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                <option value="">— Semua Departemen —</option>
                @foreach ($semuaDepartemen as $item)
                    <option value="{{ $item->id }}" @if($filterDepartemen == $item->id) selected @endif>{{ $item->nama_departemen }}</option>
                @endforeach
            </select>

            {{-- Filter Posisi --}}
            <select id="filterPosisi" name="posisi" onchange="this.form.submit()"
                class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                <option value="">— Semua Posisi —</option>
                @foreach ($semuaPosisi as $item)
                    <option value="{{ $item->id }}" @if($filterPosisi == $item->id) selected @endif>{{ $item->nama_posisi }}</option>
                @endforeach
            </select>

            <button type="submit" class="rounded-md bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Cari</button>

            {{-- Reset Filters --}}
            @if($kataKunci || $filterDepartemen || $filterPosisi)
                <a href="{{ route('admin.akun-karyawan.index') }}" class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300 whitespace-nowrap">
                    Reset Filter
                </a>
            @endif
        </div>

        @auth
            @if(auth()->user()->hasIzin('pengguna.tambah'))
                <a href="{{ route('admin.akun-karyawan.tambah') }}"
                   class="self-start rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853]">
                    + Tambah Karyawan
                </a>
            @endif
        @endauth
    </form>

    {{-- TABLE + PAGINATION + EMPTY STATE --}}
    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <caption class="sr-only">Daftar Akun Karyawan</caption>
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                <tr>
                    <th class="px-4 py-3">NIK</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Departemen</th>
                    <th class="px-4 py-3">Posisi/Jabatan</th>
                    <th class="px-4 py-3">Peran</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($pengguna as $u)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-slate-500 font-mono text-xs">
                            {{ $u->profilKaryawan?->nik_karyawan ?? '-' }}
                        </td>
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $u->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $u->email }}</td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ $u->profilKaryawan?->departemen ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ $u->profilKaryawan?->jabatan ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $u->peran->nama_peran ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $warnaStatus = match($u->status) {
                                    'aktif' => 'bg-emerald-600 text-white border-emerald-700',
                                    'menunggu_verifikasi' => 'bg-amber-500 text-white border-amber-600',
                                    'nonaktif' => 'bg-slate-500 text-white border-slate-600',
                                    default => 'bg-slate-500 text-white border-slate-600',
                                };
                                $labelStatus = match($u->status) {
                                    'aktif' => 'Aktif',
                                    'menunggu_verifikasi' => 'Menunggu Verifikasi',
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
                                @auth
                                    @if(auth()->user()->hasIzin('pengguna.edit'))
                                        <a href="{{ route('admin.akun-karyawan.ubah', $u->id) }}"
                                           class="rounded-md bg-blue-600 px-3 py-1 text-xs font-semibold text-white shadow-sm hover:bg-blue-700">Edit</a>
                                    @endif
                                    @if(auth()->user()->hasIzin('pengguna.edit'))
                                        @if ($u->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.akun-karyawan.toggle-status', $u->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="rounded-md px-3 py-1 text-xs font-semibold text-white shadow-sm {{ $u->status === 'aktif' ? 'bg-slate-600 hover:bg-slate-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                                                    {{ $u->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                    @if(auth()->user()->hasIzin('pengguna.hapus'))
                                        @if ($u->id !== auth()->id())
                                            <button type="button"
                                                    @click="modalHapus = true; idHapus = {{ $u->id }}; namaHapus = '{{ addslashes($u->name) }}'"
                                                    class="rounded-md bg-red-600 px-3 py-1 text-xs font-semibold text-white shadow-sm hover:bg-red-700">Hapus</button>
                                        @endif
                                    @endif
                                @endauth
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center">
                            @if($kataKunci || $filterDepartemen || $filterPosisi)
                                <p class="text-sm text-slate-600">Tidak ada karyawan yang cocok dengan pencarian/filter ini.</p>
                                <a href="{{ route('admin.akun-karyawan.index') }}" class="mt-2 inline-block rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Reset Filter</a>
                            @else
                                <p class="text-sm text-slate-500">Belum ada akun karyawan yang terdaftar.</p>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="border-t border-slate-200 px-4 py-3 flex items-center justify-between">
            <div class="text-xs text-slate-500">
                Menampilkan <strong>{{ $pengguna->firstItem() ?? $pengguna->total() }}</strong> dari <strong>{{ $pengguna->total() }}</strong> karyawan
            </div>
            <div>
                {{ $pengguna->links() }}
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div x-show="modalHapus" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl" @click.outside="modalHapus = false">
            <h3 class="text-base font-semibold text-slate-900">Hapus Akun Karyawan</h3>
            <p class="mt-2 text-sm text-slate-600">
                Apakah Anda yakin ingin menghapus akun <span class="font-semibold" x-text="namaHapus"></span>?
                Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" @click="modalHapus = false"
                        class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Batal</button>
                <form :action="`{{ url('admin/akun-karyawan') }}/${idHapus}`" method="POST">
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
