@extends('layouts.admin', ['judulHalaman' => 'Akun Karyawan'])

@section('content')
<div x-data="{ modalHapus: false, idHapus: null, namaHapus: '' }">

    {{-- PAGE HEADER --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-[#191c1e]">Daftar Akun Karyawan</h1>
            <p class="mt-1 text-sm text-[#40484b]">Kelola akses dan informasi akun seluruh karyawan perusahaan.</p>
        </div>
        @auth
            @if(auth()->user()->hasIzin('pengguna.tambah'))
                <a href="{{ route('admin.akun-karyawan.tambah') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-[#2C5F6F] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#234853] transition-colors active:scale-[0.98]">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                    Tambah Akun Karyawan
                </a>
            @endif
        @endauth
    </div>

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('admin.akun-karyawan.index') }}" class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div class="flex flex-1 flex-wrap items-end gap-2">
            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px]">
                <span class="absolute inset-y-0 left-3 flex items-center text-[#71787c]">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                </span>
                <input type="text" name="cari" value="{{ $kataKunci }}"
                    placeholder="Cari Nama atau NIK..."
                    class="w-full rounded-lg border border-[#c0c8cb] py-2 pl-9 pr-4 text-sm text-[#191c1e] placeholder-[#71787c] outline-none focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F]/30 transition-all">
            </div>

            {{-- Filter Departemen --}}
            <div class="relative min-w-[180px]">
                <select id="filterDepartemen" name="departemen" onchange="this.form.submit()"
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

            {{-- Filter Posisi --}}
            <div class="relative min-w-[180px]">
                <select id="filterPosisi" name="posisi" onchange="this.form.submit()"
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

    {{-- TABLE + PAGINATION + EMPTY STATE --}}
    <div class="rounded-xl border border-[#e0e3e5] bg-white overflow-hidden shadow-sm">

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <caption class="sr-only">Daftar Akun Karyawan</caption>
                <thead>
                    <tr class="bg-[#f2f4f6] border-b border-[#e0e3e5]">
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-[#40484b]">Nama Karyawan</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-[#40484b]">NIK</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-[#40484b]">Departemen</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-[#40484b]">Posisi</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-[#40484b] text-center">Status Akun</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-[#40484b] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e0e3e5]">
                    @forelse ($pengguna as $u)
                        @php
                            $inisial = strtoupper(mb_substr(explode(' ', $u->name)[0], 0, 2));
                            $warnaStatus = match($u->status) {
                                'aktif' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'menunggu_verifikasi' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'nonaktif' => 'bg-[#f2f4f6] text-[#40484b] border-[#c0c8cb]',
                                default => 'bg-[#f2f4f6] text-[#40484b] border-[#c0c8cb]',
                            };
                            $labelStatus = match($u->status) {
                                'aktif' => 'Aktif',
                                'menunggu_verifikasi' => 'Menunggu Verifikasi',
                                'nonaktif' => 'Nonaktif',
                                default => $u->status,
                            };
                            $dotColor = match($u->status) {
                                'aktif' => 'bg-emerald-600',
                                'menunggu_verifikasi' => 'bg-amber-500',
                                'nonaktif' => 'bg-[#40484b]',
                                default => 'bg-[#40484b]',
                            };
                            $avatarBg = match(hexdec(substr(substr(md5($u->name), 0, 6), 0, 6)) % 4) {
                                0 => 'bg-[#2C5F6F]/10 text-[#2C5F6F]',
                                1 => 'bg-blue-100 text-blue-700',
                                2 => 'bg-violet-100 text-violet-700',
                                default => 'bg-amber-100 text-amber-700',
                            };
                        @endphp
                        <tr class="hover:bg-[#f7f9fb] transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $avatarBg }}">
                                        {{ $inisial }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-[#191c1e]">{{ $u->name }}</p>
                                        <p class="text-[11px] text-[#40484b]">{{ $u->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-mono text-[#40484b]">
                                {{ $u->profilKaryawan?->nik_karyawan ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-[#40484b]">
                                {{ $u->profilKaryawan?->departemen ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-[#40484b]">
                                {{ $u->profilKaryawan?->jabatan ?? '-' }}
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
                                        @if(auth()->user()->hasIzin('pengguna.edit'))
                                            <a href="{{ route('admin.akun-karyawan.ubah', $u->id) }}"
                                               class="p-2 rounded-lg text-[#40484b] hover:text-[#2C5F6F] hover:bg-[#2C5F6F]/10 transition-colors"
                                               title="Edit">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                            </a>
                                        @endif
                                        @if(auth()->user()->hasIzin('pengguna.edit'))
                                            @if ($u->id !== auth()->id())
                                                <form method="POST" action="{{ route('admin.akun-karyawan.toggle-status', $u->id) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="p-2 rounded-lg text-[#40484b] hover:text-[#2C5F6F] hover:bg-[#2C5F6F]/10 transition-colors"
                                                            title="{{ $u->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                        <span class="material-symbols-outlined text-[18px]">
                                                            {{ $u->status === 'aktif' ? 'block' : 'check_circle' }}
                                                        </span>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                        @if(auth()->user()->hasIzin('pengguna.hapus'))
                                            @if ($u->id !== auth()->id())
                                                <button type="button"
                                                        @click="modalHapus = true; idHapus = {{ $u->id }}; namaHapus = '{{ addslashes($u->name) }}'"
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
                                @if($kataKunci || $filterDepartemen || $filterPosisi)
                                    <p class="text-sm text-[#40484b]">Tidak ada karyawan yang cocok dengan pencarian/filter ini.</p>
                                    <a href="{{ route('admin.akun-karyawan.index') }}"
                                       class="mt-3 inline-block rounded-lg border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">
                                        Reset Filter
                                    </a>
                                @else
                                    <p class="text-sm text-[#40484b]">Belum ada akun karyawan yang terdaftar.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if ($pengguna->hasPages())
            <div class="border-t border-[#e0e3e5] px-6 py-4 flex items-center justify-between">
                <p class="text-[11px] text-[#40484b]">
                    Menampilkan <span class="font-semibold text-[#191c1e]">{{ $pengguna->firstItem() }}</span> -
                    <span class="font-semibold text-[#191c1e]">{{ $pengguna->lastItem() }}</span> dari
                    <span class="font-semibold text-[#191c1e]">{{ $pengguna->total() }}</span> karyawan
                </p>
                <div>
                    {{ $pengguna->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div x-show="modalHapus" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-[#191c1e]/50 px-4">
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl" @click.outside="modalHapus = false">
            <h3 class="text-base font-semibold text-[#191c1e]">Hapus Akun Karyawan</h3>
            <p class="mt-2 text-sm text-[#40484b]">
                Apakah Anda yakin ingin menghapus akun <span class="font-semibold text-[#191c1e]" x-text="namaHapus"></span>?
                Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" @click="modalHapus = false"
                        class="rounded-lg border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">Batal</button>
                <form :action="`{{ url('admin/akun-karyawan') }}/${idHapus}`" method="POST">
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
