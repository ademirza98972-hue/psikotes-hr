@extends('layouts.admin', ['judulHalaman' => 'Kelola Peran & Izin'])

@section('content')
<div x-data="{ modalHapus: false, idHapus: null, namaHapus: '' }">

    {{-- STICKY HEADER --}}
    <div class="sticky top-0 z-30 bg-[#f7f9fb] -mx-6 px-6 pt-6 pb-4 border-b border-[#e0e3e5]">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-0">
        <div>
            <nav class="flex items-center gap-2 mb-2 text-[#40484b] text-sm">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#2C5F6F] transition-colors">Dashboard</a>
                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                <span class="font-semibold text-[#2C5F6F]">Kelola Peran & Izin</span>
            </nav>
            <h2 class="text-[32px] leading-[40px] font-bold text-[#001a22]">Kelola Peran & Izin</h2>
            <p class="text-[14px] leading-[20px] text-[#40484b] mt-1">Kelola hak akses dan tanggung jawab pengguna dalam sistem.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" action="{{ route('admin.peran.index') }}" class="flex items-center gap-2">
                <div class="relative group">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#40484b] text-[20px] transition-colors group-focus-within:text-[#2C5F6F]">search</span>
                    <input type="text" name="cari" value="{{ $kataKunci }}" placeholder="Cari nama peran..."
                        class="pl-10 pr-4 py-2.5 w-64 bg-white border border-[#c0c8cb] rounded-xl focus:ring-2 focus:ring-[#2C5F6F] focus:border-[#2C5F6F] outline-none text-[14px] leading-[20px] transition-all shadow-sm">
                </div>
                <button type="submit" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-[#c0c8cb] rounded-xl text-[12px] leading-[16px] font-medium text-[#191c1e] hover:bg-[#f2f4f6] transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">filter_list</span>
                    Terapkan Filter
                </button>
                @if ($kataKunci)
                    <a href="{{ route('admin.peran.index') }}"
                        class="px-4 py-2.5 bg-white border border-[#c0c8cb] rounded-xl text-[12px] leading-[16px] font-medium text-[#191c1e] hover:bg-[#f2f4f6] transition-colors shadow-sm">
                        Reset
                    </a>
                @endif
            </form>
            @auth
                @if(auth()->user()->hasIzin('peran.kelola'))
                    <a href="{{ route('admin.peran.tambah') }}"
                       class="flex items-center gap-2 px-5 py-2.5 bg-[#2C5F6F] text-white rounded-xl text-[12px] leading-[16px] font-medium hover:opacity-90 active:scale-[0.98] transition-all shadow-md shadow-[#2C5F6F]/20">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        Tambah Peran
                    </a>
                @endif
            @endauth
        </div>
    </div>

    </div>{{-- /STICKY HEADER --}}

    {{-- Warning Info --}}
    <div class="mb-4 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 flex items-start gap-3">
        <span class="material-symbols-outlined text-[#d97706] text-[20px] mt-0.5 shrink-0">warning</span>
        <div>
            <p class="text-sm font-medium text-amber-800">Perubahan izin berlaku langsung</p>
            <p class="text-xs text-amber-700 mt-0.5">Perubahan hak akses pada peran akan langsung mempengaruhi semua pengguna dengan peran tersebut.</p>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white border border-[#c0c8cb] rounded-xl overflow-hidden shadow-sm">
        <div style="overflow: auto; max-height: calc(100vh - 300px);">
            <table class="w-full text-left border-collapse">
                <thead style="position: sticky; top: 0; z-index: 5;">
                    <tr style="background: #0f2230; border-bottom: 1px solid #0a1a25;">
                        <th class="px-6 py-4 font-[11px] leading-[16px] font-semibold uppercase tracking-[0.05em]" style="color: #7db8c2;">Nama Peran</th>
                        <th class="px-6 py-4 font-[11px] leading-[16px] font-semibold uppercase tracking-[0.05em]" style="color: #7db8c2;">Deskripsi</th>
                        <th class="px-6 py-4 font-[11px] leading-[16px] font-semibold uppercase tracking-[0.05em] text-center" style="color: #7db8c2;">Jumlah Izin</th>
                        <th class="px-6 py-4 font-[11px] leading-[16px] font-semibold uppercase tracking-[0.05em] text-center" style="color: #7db8c2;">Jumlah Pengguna</th>
                        <th class="px-6 py-4 font-[11px] leading-[16px] font-semibold uppercase tracking-[0.05em] text-right" style="color: #7db8c2;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#c0c8cb]/30">
                    @forelse ($peran as $p)
                        <tr class="hover:bg-[#f2f4f6]/50 transition-colors group">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <span class="text-[18px] leading-[24px] font-semibold text-[#001a22]">{{ $p->nama_peran }}</span>
                                    @if ($p->nama_peran === 'Super Admin')
                                        <span class="px-2 py-0.5 rounded-md bg-purple-50 border border-purple-200 text-[10px] font-bold text-purple-700 uppercase tracking-tight">Protected</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5 max-w-xs">
                                <p class="text-[14px] leading-[20px] text-[#40484b] line-clamp-2">{{ $p->deskripsi ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#eceef0] text-[#001a22] font-semibold text-[14px] leading-[20px]">{{ $p->izin_count }}</span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                @if ($p->pengguna_count > 0)
                                    <div class="flex items-center justify-center -space-x-2">
                                        @foreach($p->pengguna()->limit(3)->get() as $user)
                                            <div class="w-8 h-8 rounded-full border-2 border-white {{ $loop->first ? 'bg-[#c0e9f9]' : 'bg-[#eceef0]' }} flex items-center justify-center overflow-hidden">
                                                @if($user->foto_profil)
                                                    <img src="{{ asset('storage/' . $user->foto_profil) }}" class="w-full h-full object-cover" alt="">
                                                @else
                                                    <span class="text-[10px] font-bold text-[#39485e]">{{ strtoupper(mb_substr(explode(' ', $user->name)[0], 0, 2)) }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if ($p->pengguna_count > 3)
                                            <div class="w-8 h-8 rounded-full border-2 border-white bg-[#eceef0] text-[10px] font-medium text-[#40484b] flex items-center justify-center">
                                                +{{ $p->pengguna_count - 3 }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-[11px] font-bold text-[#40484b] bg-[#eceef0] rounded-lg px-2 py-1">0 Users</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @auth
                                        @if(auth()->user()->hasIzin('peran.kelola'))
                                            <a href="{{ route('admin.peran.ubah', $p->id) }}"
                                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                                <span class="material-symbols-outlined text-[20px]">edit_square</span>
                                            </a>
                                            @if ($p->nama_peran === 'Super Admin')
                                                <button type="button" disabled
                                                        title="Peran Super Admin tidak dapat dihapus."
                                                        class="p-2 text-[#40484b] cursor-not-allowed" title="System protected">
                                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                                </button>
                                            @else
                                                <button type="button"
                                                        @click="modalHapus = true; idHapus = {{ $p->id }}; namaHapus = '{{ addslashes($p->nama_peran) }}'"
                                                        class="p-2 text-[#ba1a1a] hover:bg-[#fef2f2] rounded-lg transition-colors" title="Hapus">
                                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                                </button>
                                            @endif
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center">
                                @if ($kataKunci)
                                    <p class="text-[14px] text-[#40484b]">Tidak ada peran yang cocok dengan pencarian "{{ $kataKunci }}".</p>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.peran.index') }}"
                                            class="inline-block rounded-xl border border-[#c0c8cb] bg-white px-4 py-2 text-[12px] font-medium text-[#191c1e] hover:bg-[#f2f4f6] transition-colors">
                                            Reset Filter
                                        </a>
                                    </div>
                                @else
                                    <p class="text-[14px] text-[#40484b]">Belum ada data peran.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-[#c0c8cb] px-6 py-4 flex items-center justify-between gap-4">
            <p class="text-[11px] text-[#40484b]">
                Menampilkan <span class="font-semibold text-[#191c1e]">{{ $peran->firstItem() ?? $peran->total() }}</span> -
                <span class="font-semibold text-[#191c1e]">{{ $peran->lastItem() ?? $peran->total() }}</span> dari
                <span class="font-semibold text-[#191c1e]">{{ $peran->total() }}</span> peran
            </p>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-purple-500"></div>
                        <span class="text-[11px] text-[#40484b] uppercase tracking-wider">System Role</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-2.5 h-2.5 rounded-full bg-[#2C5F6F]"></div>
                        <span class="text-[11px] text-[#40484b] uppercase tracking-wider">Custom Role</span>
                    </div>
                </div>
                @if ($peran->hasPages())
                    {{ $peran->links() }}
                @endif
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="modalHapus" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-[#191c1e]/50 px-4">
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl" @click.outside="modalHapus = false">
            <h3 class="text-[16px] leading-[24px] font-semibold text-[#191c1e]">Hapus Peran</h3>
            <p class="mt-2 text-[14px] leading-[20px] text-[#40484b]">
                Apakah Anda yakin ingin menghapus peran <span class="font-semibold text-[#191c1e]" x-text="namaHapus"></span>?
                Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" @click="modalHapus = false"
                        class="rounded-xl border border-[#c0c8cb] bg-white px-4 py-2.5 text-[14px] leading-[20px] font-medium text-[#191c1e] hover:bg-[#f2f4f6] transition-all">
                    Batal
                </button>
                <form :action="`{{ url('admin/peran') }}/${idHapus}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="rounded-xl bg-[#ba1a1a] px-4 py-2.5 text-[14px] leading-[20px] font-semibold text-white hover:brightness-110 transition-all">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection
