@extends('layouts.admin', ['judulHalaman' => 'Aktivitas Terbaru'])

@section('content')

    {{-- STICKY HEADER --}}
    <div class="sticky top-0 z-30 bg-[#f7f9fb] -mx-6 px-6 pt-6 pb-4 border-b border-[#e0e3e5]">
        <div class="mb-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-[28px] leading-9 font-semibold text-[#00303c]">Aktivitas Terbaru</h1>
                <p class="mt-1 text-sm text-[#40484b]">Daftar seluruh pengguna sistem dari semua tipe akun.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}"
               class="self-start inline-flex items-center gap-1.5 rounded-xl border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">
                ← Kembali ke Dashboard
            </a>
        </div>
        <form method="GET" action="{{ route('admin.aktivitas.index') }}" class="flex gap-2">
            <input type="text" name="cari" value="{{ $kataKunci }}" placeholder="Cari nama atau email..."
                class="flex-1 rounded-xl border border-[#c0c8cb] bg-white px-3 py-2 text-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-2 focus:ring-[#2C5F6F]/20">
            <button type="submit" class="rounded-xl bg-[#2C5F6F] px-4 py-2 text-sm font-semibold text-white hover:bg-[#1E414C] transition-all">Cari</button>
            @if ($kataKunci)
                <a href="{{ route('admin.aktivitas.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">Reset</a>
            @endif
        </form>
    </div>{{-- /STICKY HEADER --}}

    <div class="mt-4 rounded-xl border border-[#e0e3e5] bg-white overflow-hidden shadow-sm">
        <div style="overflow: auto; max-height: calc(100vh - 300px);">
        <table class="w-full divide-y divide-[#e0e3e5] text-sm">
            <thead style="position: sticky; top: 0; z-index: 5;"><tr style="background: #0f2230; border-bottom: 1px solid #0a1a25;">
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #7db8c2;">Nama</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #7db8c2;">Email</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #7db8c2;">Tipe Akun</th>
                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: #7db8c2;">Status</th>
                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: #7db8c2;">Dibuat</th>
            </tr></thead>
            <tbody class="divide-y divide-[#e0e3e5] bg-white">
                @forelse ($aktivitas as $u)
                    <tr class="transition-colors" onmouseover="this.style.background='#f0f9ff'" onmouseout="this.style.background=''">
                        <td class="px-4 py-3 font-medium text-[#191c1e]">{{ $u->name }}</td>
                        <td class="px-4 py-3 text-[#40484b]">{{ $u->email }}</td>
                        <td class="px-4 py-3">
                            @php
                                $badgeClass = match($u->tipe_akun) {
                                    'karyawan' => 'bg-[#2C5F6F]/10 text-[#2C5F6F] border border-[#2C5F6F]/20',
                                    'kandidat' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                    'custom' => 'bg-violet-50 text-violet-700 border border-violet-200',
                                    default => 'bg-[#f2f4f6] text-[#40484b] border border-[#c0c8cb]',
                                };
                                $labelTipe = match($u->tipe_akun) {
                                    'karyawan' => 'Karyawan',
                                    'kandidat' => 'Kandidat',
                                    'custom' => 'Admin/Staff',
                                    default => ucfirst($u->tipe_akun ?? '-'),
                                };
                            @endphp
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $badgeClass }}">
                                {{ $labelTipe }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $warnaStatus = match($u->status) {
                                    'aktif' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                                    'menunggu_verifikasi' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                    'ditolak' => 'bg-rose-50 text-rose-700 border border-rose-200',
                                    'nonaktif' => 'bg-[#f2f4f6] text-[#40484b] border border-[#c0c8cb]',
                                    default => 'bg-[#f2f4f6] text-[#40484b] border border-[#c0c8cb]',
                                };
                                $labelStatus = match($u->status) {
                                    'aktif' => 'Aktif',
                                    'menunggu_verifikasi' => 'Menunggu',
                                    'ditolak' => 'Ditolak',
                                    'nonaktif' => 'Nonaktif',
                                    default => $u->status,
                                };
                            @endphp
                            <span class="inline-block rounded-md border px-2 py-0.5 text-xs font-medium {{ $warnaStatus }}">
                                {{ $labelStatus }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-xs text-[#40484b]">
                            <div class="font-medium text-[#191c1e]">{{ $u->created_at->format('d M Y') }}</div>
                            <div class="text-[#71787c]">{{ $u->created_at->format('H:i') }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-14 text-center">
                            <span class="material-symbols-outlined block mb-3" style="font-size:40px; color:#c0c8cb;">search_off</span>
                            <p class="text-[14px] font-medium text-[#40484b] mb-1">Tidak ada hasil yang cocok</p>
                            <p class="text-[12px] text-[#71787c] mb-4">Coba ubah kata kunci pencarian.</p>
                            <a href="{{ route('admin.aktivitas.index') }}"
                               class="inline-flex items-center gap-1.5 rounded-xl border border-[#c0c8cb] px-4 py-2 text-sm font-medium text-[#40484b] hover:bg-[#f2f4f6] transition-colors">
                                Reset Filter
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        <div class="border-t border-[#e0e3e5] px-6 py-4 flex justify-end">
            {{ $aktivitas->links() }}
        </div>
    </div>

@endsection
