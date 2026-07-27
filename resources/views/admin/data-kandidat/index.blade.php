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

    <div class="w-full rounded-lg border border-slate-200 bg-white px-6 pt-3 pb-4 shadow-sm">

        {{-- FILTER BAR --}}
        <form method="GET" action="{{ route('admin.data-kandidat.index') }}" class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-1 flex-wrap items-end gap-2">
                {{-- Search --}}
                <input type="text" name="cari" value="{{ $kataKunci }}" placeholder="Cari nama, email, atau NIK KTP..."
                    class="block w-56 rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">

                {{-- Filter Status --}}
                <select name="status"
                    class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    <option value="">Semua Status</option>
                    <option value="menunggu_verifikasi" @if($filterStatus === 'menunggu_verifikasi') selected @endif>Menunggu Verifikasi</option>
                    <option value="aktif" @if($filterStatus === 'aktif') selected @endif>Aktif</option>
                    <option value="ditolak" @if($filterStatus === 'ditolak') selected @endif>Ditolak</option>
                </select>

                {{-- Filter Posisi --}}
                <select name="posisi"
                    class="rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
                    <option value="">Semua Posisi</option>
                    @foreach ($semuaPosisi as $p)
                        <option value="{{ $p->id }}" @if($filterPosisi == $p->id) selected @endif>{{ $p->nama_posisi }}</option>
                    @endforeach
                </select>

                <button type="submit" class="rounded-md bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
                    Terapkan Filter
                </button>

                @if ($kataKunci || $filterStatus || $filterPosisi)
                    <a href="{{ route('admin.data-kandidat.index') }}"
                        class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300 whitespace-nowrap">
                        Reset Filter
                    </a>
                @endif
            </div>

            @if (auth()->user()->hasIzin('pengguna.tambah'))
                <a href="{{ route('admin.data-kandidat.tambah') }}"
                   class="self-start inline-flex items-center rounded-md bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#234853] whitespace-nowrap">
                    + Tambah Kandidat
                </a>
            @endif
        </form>

        {{-- INDICATOR --}}
        <div class="mb-2 text-xs text-slate-500">
            Menampilkan <strong>{{ $kandidat->firstItem() ?? $kandidat->total() }}</strong> dari <strong>{{ $kandidat->total() }}</strong> kandidat
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Nama Kandidat</th>
                        <th class="px-4 py-3">NIK KTP</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Posisi yang Dilamar</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($kandidat as $item)
                        @php
                            $profil = $item->profilKandidat;
                            $namaTampil = $profil->nama_kandidat ?? $item->name;
                            $status = $item->status;
                            $warnaBadge = match($status) {
                                'aktif' => 'bg-emerald-600 text-white border-emerald-700',
                                'menunggu_verifikasi' => 'bg-amber-500 text-white border-amber-600',
                                'ditolak' => 'bg-rose-600 text-white border-rose-700',
                                
                                default => 'bg-slate-500 text-white border-slate-600',
                            };
                            $labelBadge = match($status) {
                                'aktif' => 'Aktif',
                                'menunggu_verifikasi' => 'Menunggu Verifikasi',
                                'ditolak' => 'Ditolak',
                                'nonaktif' => 'Nonaktif',
                                default => ucfirst($status ?? 'Tidak diketahui'),
                            };
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $namaTampil }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $profil->nik_kandidat ?? '—' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $item->email }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $profil->posisi_dilamar ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $warnaBadge }}">
                                    {{ $labelBadge }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center justify-end gap-2">
                                    @if ($status === 'menunggu_verifikasi' && $bisaVerifikasi)
                                        <form method="POST" action="{{ route('admin.data-kandidat.approve', $item->id) }}" class="inline">
                                            @csrf
                                            <button type="button"
                                                    @click="bukaAksi({{ $item->id }}, @js($namaTampil), 'approve')"
                                                    class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700">
                                                Aktifkan
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.data-kandidat.tolak', $item->id) }}" class="inline">
                                            @csrf
                                            <button type="button"
                                                    @click="bukaAksi({{ $item->id }}, @js($namaTampil), 'tolak')"
                                                    class="rounded-md bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-amber-700">
                                                Tolak
                                            </button>
                                        </form>
                                    @elseif (auth()->user()->hasIzin('pengguna.edit'))
                                        @if (in_array($status, ['aktif', 'nonaktif']))
                                            <form method="POST" action="{{ route('admin.data-kandidat.toggle-status', $item->id) }}" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="rounded-md px-3 py-1.5 text-xs font-semibold text-white shadow-sm {{ $item->status === 'aktif' ? 'bg-slate-600 hover:bg-slate-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                                                    {{ $item->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                            <a href="{{ route('admin.data-kandidat.ubah', $item->id) }}"
                                               class="rounded-md bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-700">Ubah</a>
                                        @elseif ($status === 'ditolak')
                                            <a href="{{ route('admin.data-kandidat.ubah', $item->id) }}"
                                               class="rounded-md bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-700">Ubah</a>
                                        @endif
                                    @endif

                                    @if (auth()->user()->hasIzin('pengguna.hapus'))
                                        <button type="button"
                                                @click="bukaHapus({{ $item->id }}, @js($namaTampil))"
                                                class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-red-700">
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center">
                                @if ($kataKunci || $filterStatus || $filterPosisi)
                                    <p class="text-sm text-slate-500">Tidak ada data kandidat yang cocok dengan filter aktif.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.data-kandidat.index') }}"
                                            class="inline-block rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">
                                            Reset Filter
                                        </a>
                                    </div>
                                @else
                                    <p class="text-sm text-slate-500">Belum ada data kandidat. Klik <span class="font-medium">+ Tambah Kandidat</span> untuk menambahkan.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($kandidat->hasPages())
            <div class="mt-4">
                {{ $kandidat->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL HAPUS --}}
    <div x-show="modalHapus.buka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4">
        <div @click.outside="tutupHapus()" class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            <h3 class="text-base font-semibold text-slate-900">Hapus Akun Kandidat?</h3>
            <p class="mt-2 text-sm text-slate-600">
                Anda yakin ingin menghapus akun kandidat <span class="font-semibold" x-text="modalHapus.nama"></span>?
                Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="tutupHapus()"
                        class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <form method="POST" :action="`{{ url('admin/data-kandidat') }}/${modalHapus.id}`" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-md bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL AKSI (approve / tolak) --}}
    <div x-show="modalAksi.buka" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4">
        <div @click.outside="tutupAksi()" class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            <h3 class="text-base font-semibold text-slate-900"
                x-text="modalAksi.tipe === 'approve' ? 'Aktifkan Kandidat?' : 'Tolak Kandidat?'"></h3>
            <p class="mt-2 text-sm text-slate-600">
                <template x-if="modalAksi.tipe === 'approve'">
                    <span>Anda akan mengaktifkan akun kandidat <span class="font-semibold" x-text="modalAksi.nama"></span>. Kandidat akan dapat login dan mengikuti tes.</span>
                </template>
                <template x-if="modalAksi.tipe === 'tolak'">
                    <span>Anda akan menolak akun kandidat <span class="font-semibold" x-text="modalAksi.nama"></span>. Data akan tetap disimpan dengan status "Ditolak".</span>
                </template>
            </p>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="tutupAksi()"
                        class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">
                    Batal
                </button>
                <form method="POST" :action="`{{ url('admin/data-kandidat') }}/${modalAksi.id}/${modalAksi.tipe}`" class="inline">
                    @csrf
                    <button type="submit"
                            :class="modalAksi.tipe === 'approve' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'"
                            class="rounded-md px-4 py-2 text-sm font-semibold text-white">
                        <span x-text="modalAksi.tipe === 'approve' ? 'Ya, Aktifkan' : 'Ya, Tolak'"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection