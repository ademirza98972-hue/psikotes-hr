@extends('layouts.admin', ['judulHalaman' => 'Aktivitas Terbaru'])

@section('content')

    {{-- STICKY HEADER --}}
    <div class="sticky top-0 z-30 bg-[#f7f9fb] -mx-6 px-6 pt-6 pb-4 border-b border-[#e0e3e5]">
        <div class="mb-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-bold text-[#191c1e]">Aktivitas Terbaru</h1>
                <p class="mt-1 text-sm text-[#40484b]">Daftar seluruh pengguna sistem dari semua tipe akun.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}"
               class="self-start rounded-md bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-200">
                ← Kembali ke Dashboard
            </a>
        </div>
        <form method="GET" action="{{ route('admin.aktivitas.index') }}" class="flex gap-2">
            <input type="text" name="cari" value="{{ $kataKunci }}" placeholder="Cari nama atau email..."
                class="flex-1 rounded-md border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-[#2C5F6F] focus:outline-none focus:ring-1 focus:ring-[#2C5F6F]">
            <button type="submit" class="rounded-md bg-slate-700 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Cari</button>
            @if ($kataKunci)
                <a href="{{ route('admin.aktivitas.index') }}" class="rounded-md bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300">Reset</a>
            @endif
        </form>
    </div>{{-- /STICKY HEADER --}}

    <div class="mt-4 mb-3 border-b border-slate-200 pb-3 text-xs text-slate-500">
        Daftar seluruh pengguna sistem yang terdaftar di semua tipe akun (karyawan, kandidat, admin/staff), diurutkan dari yang paling baru.
    </div>

    <div class="mt-4 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Tipe Akun</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Dibuat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($aktivitas as $u)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $u->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $u->email }}</td>
                        <td class="px-4 py-3">
                            @php
                                $badgeClass = match($u->tipe_akun) {
                                    'karyawan' => 'bg-[#2C5F6F] text-white',
                                    'kandidat' => 'bg-emerald-600 text-white',
                                    'custom' => 'bg-purple-600 text-white',
                                    default => 'bg-slate-500 text-white',
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
                                    'aktif' => 'bg-emerald-600 text-white border-emerald-700',
                                    'menunggu_verifikasi' => 'bg-amber-500 text-white border-amber-600',
                                    'ditolak' => 'bg-rose-600 text-white border-rose-700',
                                    'nonaktif' => 'bg-slate-500 text-white border-slate-600',
                                    default => 'bg-slate-500 text-white border-slate-600',
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
                        <td class="px-4 py-3 text-right text-xs text-slate-500">
                            <div class="font-medium text-slate-700">{{ $u->created_at->format('d M Y') }}</div>
                            <div class="text-slate-400">{{ $u->created_at->format('H:i') }}</div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">Tidak ada aktivitas ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="border-t border-slate-200 px-4 py-3">
            {{ $aktivitas->links() }}
        </div>
    </div>

@endsection
