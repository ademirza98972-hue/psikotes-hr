@extends('layouts.admin', ['judulHalaman' => 'Data Kandidat'])

@section('content')
@php
    $bisaVerifikasi = auth()->user()->hasIzin('pengguna.verifikasi');
@endphp
<div x-data="{
    modalHapus: { buka: false, id: null, nama: '' },
    modalAksi: { buka: false, id: null, nama: '', tipe: '' },
    bukaHapus(id, nama) { this.modalHapus = { buka: true, id, nama }; },
    tutupHapus() { this.modalHapus = { buka: false, id: null, nama: '' }; },
    bukaAksi(id, nama, tipe) { this.modalAksi = { buka: true, id, nama, tipe }; },
    tutupAksi() { this.modalAksi = { buka: false, id: null, nama: '', tipe: '' }; }
}">

    {{-- STICKY HEADER --}}
    <div class="sticky top-0 z-30 bg-[#f7f9fb] -mx-6 px-6 pt-6 pb-4 border-b border-[#e0e3e5]">

    {{-- PAGE HEADER --}}
    <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-[#191c1e]">Daftar Akun Kandidat</h1>
            <p class="mt-1 text-sm text-[#40484b]">Verifikasi dan kelola akses kandidat untuk proses rekrutmen perusahaan.</p>
        </div>
        @if (auth()->user()->hasIzin('pengguna.tambah'))
            <a href="{{ route('admin.data-kandidat.tambah') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-[#2C5F6F] px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-[#234853] transition-colors active:scale-[0.98] whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                Tambah Kandidat
            </a>
        @endif
    </div>

    {{-- FILTER BAR --}}
    <form method="GET" action="{{ route('admin.data-kandidat.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div class="flex flex-1 flex-wrap items-end gap-2">
            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px]">
                <span class="absolute inset-y-0 left-3 flex items-center text-[#71787c]">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                </span>
                <input type="text" name="cari" value="{{ $kataKunci }}"
                    placeholder="Cari nama, email, atau NIK KTP..."
                    class="w-full rounded-lg border border-[#c0c8cb] py-2 pl-9 pr-4 text-sm text-[#191c1e] placeholder-[#71787c] outline-none focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F]/30 transition-all">
            </div>

            {{-- Filter Status --}}
            <div class="relative min-w-[180px]">
                <select name="status" onchange="this.form.submit()"
                    class="appearance-none w-full rounded-lg border border-[#c0c8cb] py-2 pl-4 pr-10 text-sm text-[#191c1e] outline-none focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F]/30 transition-all cursor-pointer bg-white">
                    <option value="">Semua Status</option>
                    <option value="menunggu_verifikasi" @if($filterStatus === 'menunggu_verifikasi') selected @endif>Menunggu Verifikasi</option>
                    <option value="aktif" @if($filterStatus === 'aktif') selected @endif>Aktif</option>
                    <option value="ditolak" @if($filterStatus === 'ditolak') selected @endif>Ditolak</option>
                </select>
                <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#71787c]">
                    <span class="material-symbols-outlined text-[18px]">expand_more</span>
                </span>
            </div>

            {{-- Filter Posisi --}}
            <div class="relative min-w-[180px]">
                <select name="posisi" onchange="this.form.submit()"
                    class="appearance-none w-full rounded-lg border border-[#c0c8cb] py-2 pl-4 pr-10 text-sm text-[#191c1e] outline-none focus:border-[#2C5F6F] focus:ring-1 focus:ring-[#2C5F6F]/30 transition-all cursor-pointer bg-white">
                    <option value="">Semua Posisi</option>
                    @foreach ($semuaPosisi as $p)
                        <option value="{{ $p->id }}" @if($filterPosisi == $p->id) selected @endif>{{ $p->nama_posisi }}</option>
                    @endforeach
                </select>
                <span class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-[#71787c]">
                    <span class="material-symbols-outlined text-[18px]">expand_more</span>
                </span>
            </div>

            @if ($kataKunci || $filterStatus || $filterPosisi)
                <a href="{{ route('admin.data-kandidat.index') }}"
                   class="rounded-lg border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors whitespace-nowrap">
                    Reset Filter
                </a>
            @endif
        </div>
    </form>

    </div>{{-- /STICKY HEADER --}}

    {{-- TABLE + PAGINATION + EMPTY STATE --}}
    <div class="mt-4 rounded-xl border border-[#e0e3e5] bg-white overflow-hidden shadow-sm">

        <div style="overflow: auto; max-height: calc(100vh - 300px);">
            <table class="w-full text-left border-collapse">
                <caption class="sr-only">Daftar Akun Kandidat</caption>
                <thead style="position: sticky; top: 0; z-index: 5;">
                    <tr style="background: #0f2230; border-bottom: 1px solid #0a1a25;">
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider" style="color: #7db8c2;">NIK</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider" style="color: #7db8c2;">Nama Kandidat</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider" style="color: #7db8c2;">Email</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider" style="color: #7db8c2;">Posisi yang Dilamar</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-center" style="color: #7db8c2;">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-right" style="color: #7db8c2;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e0e3e5]">
                    @forelse ($kandidat as $item)
                        @php
                            $profil = $item->profilKandidat;
                            $namaTampil = $profil->nama_kandidat ?? $item->name;
                            $status = $item->status;
                            $inisial = strtoupper(mb_substr(explode(' ', $namaTampil)[0], 0, 2));
                            $avatarBg = match(hexdec(substr(substr(md5($namaTampil), 0, 6), 0, 6)) % 4) {
                                0 => 'bg-[#2C5F6F]/10 text-[#2C5F6F]',
                                1 => 'bg-blue-100 text-blue-700',
                                2 => 'bg-violet-100 text-violet-700',
                                default => 'bg-amber-100 text-amber-700',
                            };
                            $warnaBadge = match($status) {
                                'aktif' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'menunggu_verifikasi' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'ditolak' => 'bg-rose-50 text-rose-700 border-rose-200',
                                default => 'bg-[#f2f4f6] text-[#40484b] border-[#c0c8cb]',
                            };
                            $labelBadge = match($status) {
                                'aktif' => 'Aktif',
                                'menunggu_verifikasi' => 'Menunggu Verifikasi',
                                'ditolak' => 'Ditolak',
                                'nonaktif' => 'Nonaktif',
                                default => ucfirst($status ?? 'Tidak diketahui'),
                            };
                            $dotColor = match($status) {
                                'aktif' => 'bg-emerald-600',
                                'menunggu_verifikasi' => 'bg-amber-500',
                                'ditolak' => 'bg-rose-600',
                                'nonaktif' => 'bg-[#40484b]',
                                default => 'bg-[#40484b]',
                            };
                        @endphp
                        <tr class="hover:bg-[#f7f9fb] transition-colors group">
                            <td class="px-6 py-4 text-sm font-mono text-[#40484b]">
                                {{ $profil->nik_kandidat ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $avatarBg }}">
                                        {{ $inisial }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-[#191c1e]">{{ $namaTampil }}</p>
                                        <p class="text-[11px] text-[#40484b]">{{ $item->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-[#40484b]">
                                {{ $item->email }}
                            </td>
                            <td class="px-6 py-4 text-sm text-[#40484b]">
                                {{ $profil->posisi->nama_posisi ?? $profil->posisi_dilamar ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $warnaBadge }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} mr-1.5"></span>
                                    {{ $labelBadge }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if ($status === 'menunggu_verifikasi' && $bisaVerifikasi)
                                        <form method="POST" action="{{ route('admin.data-kandidat.approve', $item->id) }}" class="inline">
                                            @csrf
                                            <button type="button"
                                                    @click="bukaAksi({{ $item->id }}, @js($namaTampil), 'approve')"
                                                    class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700 transition-colors">
                                                <span class="material-symbols-outlined text-[14px]">check</span>
                                                Setujui
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.data-kandidat.tolak', $item->id) }}" class="inline">
                                            @csrf
                                            <button type="button"
                                                    @click="bukaAksi({{ $item->id }}, @js($namaTampil), 'tolak')"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-rose-300 bg-white px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition-colors">
                                                <span class="material-symbols-outlined text-[14px]">close</span>
                                                Tolak
                                            </button>
                                        </form>
                                    @elseif (auth()->user()->hasIzin('pengguna.edit'))
                                        @if (in_array($status, ['aktif', 'nonaktif']))
                                            <form method="POST" action="{{ route('admin.data-kandidat.toggle-status', $item->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="p-2 rounded-lg text-[#40484b] hover:text-[#2C5F6F] hover:bg-[#2C5F6F]/10 transition-colors"
                                                        title="{{ $item->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <span class="material-symbols-outlined text-[18px]">
                                                        {{ $item->status === 'aktif' ? 'block' : 'check_circle' }}
                                                    </span>
                                                </button>
                                            </form>
                                            <a href="{{ route('admin.data-kandidat.ubah', $item->id) }}"
                                               class="p-2 rounded-lg text-[#40484b] hover:text-[#2C5F6F] hover:bg-[#2C5F6F]/10 transition-colors"
                                               title="Edit">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                            </a>
                                        @elseif ($status === 'ditolak')
                                            <a href="{{ route('admin.data-kandidat.ubah', $item->id) }}"
                                               class="p-2 rounded-lg text-[#40484b] hover:text-[#2C5F6F] hover:bg-[#2C5F6F]/10 transition-colors"
                                               title="Edit">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                            </a>
                                        @endif
                                    @endif

                                    @if (auth()->user()->hasIzin('pengguna.hapus'))
                                        <button type="button"
                                                @click="bukaHapus({{ $item->id }}, @js($namaTampil))"
                                                class="p-2 rounded-lg text-[#40484b] hover:text-rose-600 hover:bg-rose-50 transition-colors"
                                                title="Hapus">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center">
                                @if ($kataKunci || $filterStatus || $filterPosisi)
                                    <p class="text-sm text-[#40484b]">Tidak ada data kandidat yang cocok dengan filter aktif.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.data-kandidat.index') }}"
                                            class="inline-block rounded-lg border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">
                                            Reset Filter
                                        </a>
                                    </div>
                                @else
                                    <p class="text-sm text-[#40484b]">Belum ada data kandidat. Klik <span class="font-semibold">Tambah Kandidat</span> untuk menambahkan.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if ($kandidat->hasPages())
            <div class="border-t border-[#e0e3e5] px-6 py-4 flex items-center justify-between">
                <p class="text-[11px] text-[#40484b]">
                    Menampilkan <span class="font-semibold text-[#191c1e]">{{ $kandidat->firstItem() ?? $kandidat->total() }}</span> -
                    <span class="font-semibold text-[#191c1e]">{{ $kandidat->lastItem() ?? $kandidat->total() }}</span> dari
                    <span class="font-semibold text-[#191c1e]">{{ $kandidat->total() }}</span> kandidat
                </p>
                <div>
                    {{ $kandidat->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- MODAL HAPUS --}}
    <div x-show="modalHapus.buka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-[#191c1e]/50 px-4">
        <div @click.outside="tutupHapus()" class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <h3 class="text-base font-semibold text-[#191c1e]">Hapus Akun Kandidat?</h3>
            <p class="mt-2 text-sm text-[#40484b]">
                Anda yakin ingin menghapus akun kandidat <span class="font-semibold" x-text="modalHapus.nama"></span>?
                Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="tutupHapus()"
                        class="rounded-lg border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">
                    Batal
                </button>
                <form method="POST" :action="`{{ url('admin/data-kandidat') }}/${modalHapus.id}`" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700 transition-colors">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL AKSI (approve / tolak) --}}
    <div x-show="modalAksi.buka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-[#191c1e]/50 px-4">
        <div @click.outside="tutupAksi()" class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <h3 class="text-base font-semibold text-[#191c1e]"
                x-text="modalAksi.tipe === 'approve' ? 'Aktifkan Kandidat?' : 'Tolak Kandidat?'"></h3>
            <p class="mt-2 text-sm text-[#40484b]">
                <template x-if="modalAksi.tipe === 'approve'">
                    <span>Anda akan mengaktifkan akun kandidat <span class="font-semibold" x-text="modalAksi.nama"></span>. Kandidat akan dapat login dan mengikuti tes.</span>
                </template>
                <template x-if="modalAksi.tipe === 'tolak'">
                    <span>Anda akan menolak akun kandidat <span class="font-semibold" x-text="modalAksi.nama"></span>. Data akan tetap disimpan dengan status "Ditolak".</span>
                </template>
            </p>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="tutupAksi()"
                        class="rounded-lg bg-slate-200 px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">
                    Batal
                </button>
                <form method="POST" :action="`{{ url('admin/data-kandidat') }}/${modalAksi.id}/${modalAksi.tipe}`" class="inline">
                    @csrf
                    <button type="submit"
                            :class="modalAksi.tipe === 'approve' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'"
                            class="rounded-lg px-4 py-2 text-sm font-semibold text-white transition-colors">
                        <span x-text="modalAksi.tipe === 'approve' ? 'Ya, Aktifkan' : 'Ya, Tolak'"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection
