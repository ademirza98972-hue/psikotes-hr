@extends('layouts.admin', ['judulHalaman' => 'Kelola Admin & Staff'])

@section('content')
@php
    $bisaEdit = auth()->user()->hasIzin('pengguna.edit');
    $bisaHapus = auth()->user()->hasIzin('pengguna.hapus');
    $bisaToggle = auth()->user()->hasIzin('pengguna.edit');
@endphp
<div x-data="{ modalHapus: false, idHapus: null, namaHapus: '' }">

    {{-- STICKY HEADER --}}
    <div class="sticky top-0 z-30 bg-[#f7f9fb] -mx-6 px-6 pt-6 pb-4 border-b border-[#e0e3e5]">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-4">
        <div>
            <h2 class="text-[24px] font-bold text-[#001a22] leading-snug">Kelola Admin & Staff</h2>
            <p class="text-[14px] text-[#41484b] mt-1">Manajemen akses dan kontrol untuk seluruh administrator sistem.</p>
        </div>
        @auth
            @if(auth()->user()->hasIzin('pengguna.tambah'))
                <a href="{{ route('admin.pengguna-admin.tambah') }}"
                   class="self-start inline-flex items-center gap-2 bg-[#2C5F6F] text-white px-5 py-2.5 rounded-xl text-[14px] font-semibold hover:opacity-90 active:scale-[0.98] transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    + Tambah Admin/Staff
                </a>
            @endif
        @endauth
    </div>

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('admin.pengguna-admin.index') }}" class="bg-[#f2f4f6] p-4 rounded-xl border border-[#c0c8cb] flex flex-wrap items-center gap-3 shadow-sm">
        <div class="flex-1 min-w-[240px] relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#41484b] text-[18px]">search</span>
            <input type="text" name="cari" value="{{ $kataKunci }}" placeholder="Cari nama atau email..."
                class="w-full bg-white border border-[#c0c8cb] rounded-xl py-2 pl-10 pr-4 text-[14px] text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F] focus:border-[#2C5F6F] outline-none transition-all">
        </div>
        <div class="w-full md:w-auto">
            <select name="peran"
                class="w-full bg-white border border-[#c0c8cb] rounded-xl py-2 px-4 text-[14px] text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F] focus:border-[#2C5F6F] outline-none min-w-[160px] transition-all">
                <option value="">Semua Peran</option>
                @foreach ($semuaPeran as $r)
                    <option value="{{ $r->id }}" @if($filterPeran == $r->id) selected @endif>{{ $r->nama_peran }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full md:w-auto">
            <select name="status"
                class="w-full bg-white border border-[#c0c8cb] rounded-xl py-2 px-4 text-[14px] text-[#191c1e] focus:ring-2 focus:ring-[#2C5F6F] focus:border-[#2C5F6F] outline-none min-w-[160px] transition-all">
                <option value="">Semua Status</option>
                <option value="aktif" @if($filterStatus === 'aktif') selected @endif>Aktif</option>
                <option value="nonaktif" @if($filterStatus === 'nonaktif') selected @endif>Nonaktif</option>
            </select>
        </div>
        <button type="submit" class="w-full md:w-auto bg-[#001a22] text-white px-6 py-2 rounded-xl text-[14px] font-medium hover:opacity-90 transition-colors">
            Terapkan Filter
        </button>
        @if ($kataKunci || $filterPeran || $filterStatus)
            <a href="{{ route('admin.pengguna-admin.index') }}"
                class="w-full md:w-auto text-center rounded-xl border border-[#71787b] px-5 py-2 text-[14px] font-medium text-[#41484b] hover:bg-white transition-colors">
                Reset Filter
            </a>
        @endif
    </form>

    </div>{{-- /STICKY HEADER --}}

    {{-- DATA TABLE --}}
    <div class="mt-4 bg-white rounded-xl border border-[#c0c8cb] overflow-hidden shadow-sm">
        <div class="mb-2 px-6 pt-4 text-[12px] text-[#41484b]">
            Menampilkan <strong class="text-[#191c1e]">{{ $pengguna->firstItem() ?? $pengguna->total() }}</strong> dari <strong class="text-[#191c1e]">{{ $pengguna->total() }}</strong> pengguna
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#f2f4f6] border-b border-[#c0c8cb]">
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-wider text-[#41484b]">Nama</th>
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-wider text-[#41484b]">Email</th>
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-wider text-[#41484b]">Peran</th>
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-wider text-[#41484b]">Status</th>
                        <th class="px-6 py-4 text-[11px] font-semibold uppercase tracking-wider text-[#41484b] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c0c8cb]/30">
                    @forelse ($pengguna as $u)
                        @php
                            $initials = mb_substr(explode(' ', $u->name)[0], 0, 1, 'UTF-8') . (strlen(trim(explode(' ', $u->name)[1] ?? '')) > 0 ? mb_substr(explode(' ', $u->name)[1], 0, 1, 'UTF-8') : '');
                            $warnaStatus = match($u->status) {
                                'aktif' => 'bg-[#1B5E20] text-white',
                                'nonaktif' => 'bg-[#757575] text-white',
                                default => 'bg-[#757575] text-white',
                            };
                            $labelStatus = match($u->status) {
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Nonaktif',
                                default => $u->status,
                            };
                            $isSuperAdmin = $u->peran && $u->peran->nama_peran === 'Super Admin';
                        @endphp
                        <tr class="hover:bg-[#f2f4f6] transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full {{ $isSuperAdmin ? 'bg-[#001a22] text-white' : 'bg-[#001a22]/10 text-[#001a22]' }} flex items-center justify-center font-bold text-[14px] border {{ $isSuperAdmin ? 'border-transparent' : 'border-[#001a22]/20' }}">
                                        {{ strtoupper($initials) }}
                                    </div>
                                    <span class="font-medium text-[14px] {{ $isSuperAdmin ? 'font-bold text-[#191c1e]' : 'text-[#191c1e]' }}">{{ $u->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-[14px] text-[#41484b]">{{ $u->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 bg-[#e6e8ea] rounded-full text-[12px] font-medium text-[#41484b] border border-[#c0c8cb]">
                                    {{ $u->peran->nama_peran ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[12px] font-bold {{ $warnaStatus }}">
                                    {{ $labelStatus }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-2">
                                    @auth
                                        @if($bisaEdit)
                                            <a href="{{ route('admin.pengguna-admin.ubah', $u->id) }}"
                                               class="px-3 py-1.5 bg-[#0D47A1] text-white rounded-lg text-[12px] font-medium hover:opacity-90 transition-opacity">Edit</a>
                                            @if ($u->status !== 'aktif' && !$isSuperAdmin)
                                                @if ($u->status === 'aktif')
                                                    <form method="POST" action="{{ route('admin.pengguna-admin.toggle-status', $u->id) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                class="px-3 py-1.5 bg-[#455A64] text-white rounded-lg text-[12px] font-medium hover:opacity-90 transition-opacity">Nonaktifkan</button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.pengguna-admin.toggle-status', $u->id) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                class="px-3 py-1.5 bg-[#1B5E20] text-white rounded-lg text-[12px] font-medium hover:opacity-90 transition-opacity">Aktifkan</button>
                                                    </form>
                                                @endif
                                            @endif
                                        @endif
                                        @if($bisaHapus && $u->id !== auth()->id())
                                            <button type="button"
                                                    @click="modalHapus = true; idHapus = {{ $u->id }}; namaHapus = '{{ addslashes($u->name) }}'"
                                                    class="px-3 py-1.5 bg-[#B71C1C] text-white rounded-lg text-[12px] font-medium hover:opacity-90 transition-opacity">Hapus</button>
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center">
                                @if ($kataKunci || $filterPeran || $filterStatus)
                                    <p class="text-[14px] text-[#41484b]">Tidak ada data pengguna internal yang cocok dengan filter aktif.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.pengguna-admin.index') }}"
                                            class="inline-block border border-[#71787b] px-4 py-2 rounded-xl text-[14px] font-medium text-[#41484b] hover:bg-white transition-colors">
                                            Reset Filter
                                        </a>
                                    </div>
                                @else
                                    <p class="text-[14px] text-[#41484b]">Belum ada data pengguna internal. Klik <span class="font-semibold">+ Tambah Admin/Staff</span> untuk menambahkan.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pengguna->hasPages())
            <div class="px-6 py-4 bg-[#f2f4f6] border-t border-[#c0c8cb] flex items-center justify-between">
                <span class="text-[12px] text-[#41484b]">Menampilkan {{ $pengguna->firstItem() }}-{{ $pengguna->lastItem() }} dari {{ $pengguna->total() }} entri</span>
                <div class="flex items-center gap-2">
                    @foreach ($pengguna->links()->elements[0] as $page => $url)
                        @if ($page == $pengguna->currentPage())
                            <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#001a22] text-white text-[12px] font-medium">{{ $page }}</span>
                        @elseif ($page === '...')
                            <span class="w-8 h-8 flex items-center justify-center text-[12px] text-[#41484b]">...</span>
                        @else
                            <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#c0c8cb] bg-white text-[#41484b] text-[12px] font-medium hover:bg-[#e6e8ea] transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- MODAL KONFIRMASI HAPUS --}}
    <div x-show="modalHapus" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-[#191c1e]/50 px-4">
        <div class="w-full max-w-md bg-white p-6 rounded-xl shadow-xl" @click.outside="modalHapus = false">
            <h3 class="text-[16px] font-semibold text-[#191c1e]">Hapus Pengguna Internal</h3>
            <p class="mt-2 text-[14px] text-[#41484b]">
                Apakah Anda yakin ingin menghapus pengguna <span class="font-semibold text-[#191c1e]" x-text="namaHapus"></span>?
                Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" @click="modalHapus = false"
                        class="px-5 py-2.5 rounded-xl border border-[#71787b] text-[14px] font-medium text-[#41484b] hover:bg-[#f2f4f6] transition-colors">Batal</button>
                <form :action="`{{ url('admin/pengguna-admin') }}/${idHapus}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-[#B71C1C] text-white text-[14px] font-semibold hover:opacity-90 transition-opacity">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection
