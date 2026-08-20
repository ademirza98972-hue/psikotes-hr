@extends('layouts.admin', ['judulHalaman' => 'Dashboard'])

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>

<div class="max-w-[1440px] mx-auto space-y-6">

    {{-- ── WELCOME BANNER ── --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#2C5F6F] via-[#1E414C] to-[#0f2a31] px-7 py-6 shadow-md">
        {{-- decorative circles --}}
        <div class="pointer-events-none absolute -right-10 -top-10 h-52 w-52 rounded-full bg-white/5"></div>
        <div class="pointer-events-none absolute -bottom-8 right-32 h-36 w-36 rounded-full bg-white/5"></div>

        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-white/60">{{ $tanggalHariIni }}</p>
                <h1 class="mt-0.5 text-2xl font-bold text-white">{{ $sapaan }}, {{ $namaPengguna }} 👋</h1>
                <p class="mt-1 text-sm text-white/60">Selamat datang di panel admin Psikotes HR.</p>
            </div>
            {{-- Quick actions --}}
            <div class="flex flex-wrap gap-2">
                @auth
                    @if(auth()->user()->hasIzin('pengguna.tambah'))
                        <a href="{{ route('admin.akun-karyawan.tambah') }}"
                           class="inline-flex items-center gap-1.5 rounded-lg bg-white/15 px-3.5 py-2 text-xs font-semibold text-white backdrop-blur transition hover:bg-white/25">
                            <span class="material-symbols-outlined text-[15px]">add</span>
                            Karyawan
                        </a>
                        <a href="{{ route('admin.data-kandidat.tambah') }}"
                           class="inline-flex items-center gap-1.5 rounded-lg bg-white/15 px-3.5 py-2 text-xs font-semibold text-white backdrop-blur transition hover:bg-white/25">
                            <span class="material-symbols-outlined text-[15px]">add</span>
                            Kandidat
                        </a>
                    @endif
                    @if(auth()->user()->hasIzin('peran.kelola'))
                        <a href="{{ route('admin.peran.tambah') }}"
                           class="inline-flex items-center gap-1.5 rounded-lg bg-white/15 px-3.5 py-2 text-xs font-semibold text-white backdrop-blur transition hover:bg-white/25">
                            <span class="material-symbols-outlined text-[15px]">add</span>
                            Peran
                        </a>
                    @endif
                @endauth
            </div>
        </div>

        {{-- mini stat strip --}}
        <div class="relative mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
            @php
                $strips = [
                    ['label' => 'Akun Karyawan', 'sub' => 'Karyawan aktif di sistem', 'val' => $totalKaryawan, 'trend' => $karyawanBaru7Hari, 'icon' => 'group'],
                    ['label' => 'Akun Kandidat', 'sub' => 'Kandidat terdaftar', 'val' => $totalKandidat, 'trend' => $kandidatBaru7Hari, 'icon' => 'person_search'],
                    ['label' => 'Perlu Verifikasi', 'sub' => 'Kandidat menunggu disetujui', 'val' => $kandidatMenunggu, 'trend' => null, 'icon' => 'pending_actions'],
                    ['label' => 'NIK Tersedia', 'sub' => 'Slot karyawan belum punya akun', 'val' => $nikBelumTerpakai, 'trend' => null, 'icon' => 'badge'],
                ];
            @endphp
            @foreach($strips as $s)
                <div class="rounded-xl bg-white/10 px-4 py-3 backdrop-blur">
                    <div class="flex items-center justify-between">
                        <span class="material-symbols-outlined text-[18px] text-white/70">{{ $s['icon'] }}</span>
                        @if($s['trend'] !== null)
                            <span class="rounded-full bg-emerald-400/20 px-2 py-0.5 text-[10px] font-semibold text-emerald-300">
                                +{{ $s['trend'] }} baru
                            </span>
                        @endif
                    </div>
                    <p class="mt-2 text-xl font-bold text-white">{{ number_format($s['val']) }}</p>
                    <p class="text-[12px] font-medium text-white/90">{{ $s['label'] }}</p>
                    <p class="text-[10px] text-white/50 mt-0.5">{{ $s['sub'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── STAT CARDS (detail) ── --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

        {{-- Kandidat breakdown --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-500">
                    <span class="material-symbols-outlined text-[22px]">person_search</span>
                </div>
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wider text-slate-400">Total Kandidat</p>
                    <p class="text-2xl font-bold text-slate-800">{{ number_format($totalKandidat) }}</p>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-3 divide-x divide-slate-100 rounded-lg bg-slate-50 text-center text-xs">
                <div class="py-2">
                    <p class="font-bold text-amber-600">{{ $kandidatMenunggu }}</p>
                    <p class="text-slate-400">Menunggu</p>
                </div>
                <div class="py-2">
                    <p class="font-bold text-emerald-600">{{ $kandidatAktif }}</p>
                    <p class="text-slate-400">Aktif</p>
                </div>
                <div class="py-2">
                    <p class="font-bold text-rose-500">{{ $kandidatDitolak }}</p>
                    <p class="text-slate-400">Ditolak</p>
                </div>
            </div>
        </div>

        {{-- Data Karyawan --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-cyan-50 text-cyan-600">
                    <span class="material-symbols-outlined text-[22px]">database</span>
                </div>
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wider text-slate-400">Data Karyawan</p>
                    <p class="text-2xl font-bold text-slate-800">{{ number_format($totalDataKaryawan) }}</p>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-2 divide-x divide-slate-100 rounded-lg bg-slate-50 text-center text-xs">
                <div class="py-2">
                    <p class="font-bold text-[#2C5F6F]">{{ number_format($totalKaryawan) }}</p>
                    <p class="text-slate-400">Punya Akun</p>
                </div>
                <div class="py-2">
                    <p class="font-bold text-amber-600">{{ number_format($nikBelumTerpakai) }}</p>
                    <p class="text-slate-400">NIK Bebas</p>
                </div>
            </div>
        </div>

        {{-- Admin & Peran --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-50 text-violet-600">
                    <span class="material-symbols-outlined text-[22px]">admin_panel_settings</span>
                </div>
                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wider text-slate-400">Admin & Staff</p>
                    <p class="text-2xl font-bold text-slate-800">{{ number_format($totalAdminStaff) }}</p>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between rounded-lg bg-slate-50 px-4 py-2">
                <p class="text-xs text-slate-400">Peran tersedia</p>
                <p class="text-sm font-bold text-violet-600">{{ number_format($totalPeran) }} peran</p>
            </div>
        </div>

    </div>

    {{-- ── LOWER SECTION: Chart + Pending ── --}}
    <div class="grid gap-4 xl:grid-cols-3">

        {{-- Chart: 2/3 lebar --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white p-6 shadow-sm xl:col-span-2">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Pendaftaran 7 Hari Terakhir</h2>
                    <p class="text-[12px] text-slate-400">Semua tipe akun baru per hari</p>
                </div>
                <div class="flex items-center gap-2 rounded-lg bg-[#2C5F6F]/8 px-3 py-1.5">
                    <span class="material-symbols-outlined text-[18px] text-[#2C5F6F]">bar_chart</span>
                    <span class="text-xs font-semibold text-[#2C5F6F]">7 hari</span>
                </div>
            </div>
            <div class="h-60">
                <canvas id="barPendaftaran"></canvas>
            </div>
        </div>

        {{-- Pending verifikasi: 1/3 lebar --}}
        <div class="rounded-xl border border-[#e0e3e5] bg-white shadow-sm xl:col-span-1">
            <div class="flex items-center justify-between border-b border-[#f2f4f6] px-5 py-4">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800">Menunggu Verifikasi</h2>
                    <p class="text-[11px] text-slate-400">Kandidat perlu persetujuan</p>
                </div>
                @if($kandidatMenunggu > 0)
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-amber-500 text-xs font-bold text-white">
                        {{ $kandidatMenunggu }}
                    </span>
                @endif
            </div>

            @if($kandidatMenunggu === 0)
                <div class="flex flex-col items-center justify-center gap-2 py-12 text-center text-slate-400">
                    <span class="material-symbols-outlined text-[40px]">check_circle</span>
                    <p class="text-sm">Tidak ada yang menunggu</p>
                </div>
            @else
                <div class="divide-y divide-[#f2f4f6]">
                    @foreach($kandidatMenungguList as $k)
                        @php
                            $lama = $k->created_at->diffForHumans(null, true);
                            $profil = $k->profilKandidat;
                            $posisi = $profil ? $profil->posisi_dilamar : '-';
                            $inisial = mb_strtoupper(mb_substr($k->name, 0, 1, 'UTF-8'), 'UTF-8');
                        @endphp
                        <div class="px-5 py-4">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#2C5F6F]/10 text-[#2C5F6F] text-xs font-bold">
                                    {{ $inisial }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-800">{{ $k->name }}</p>
                                    <p class="text-[11px] text-slate-400 truncate">{{ $posisi }} · {{ $lama }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <form action="{{ route('admin.data-kandidat.approve', $k->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-1 rounded-lg bg-emerald-50 px-3 py-1.5 text-[11px] font-semibold text-emerald-700 transition hover:bg-emerald-100">
                                        <span class="material-symbols-outlined text-[14px]">check</span>
                                        Aktifkan
                                    </button>
                                </form>
                                <form action="{{ route('admin.data-kandidat.tolak', $k->id) }}" method="POST" onsubmit="return confirm('Tolak kandidat {{ $k->name }}?')" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-1 rounded-lg bg-rose-50 px-3 py-1.5 text-[11px] font-semibold text-rose-600 transition hover:bg-rose-100">
                                        <span class="material-symbols-outlined text-[14px]">close</span>
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($kandidatMenunggu > 2)
                    <div class="border-t border-[#f2f4f6] px-5 py-3">
                        <a href="{{ route('admin.data-kandidat.index') }}"
                           class="flex items-center justify-center gap-1 text-xs font-semibold text-[#2C5F6F] hover:underline">
                            Lihat {{ $kandidatMenunggu - 2 }} kandidat lainnya
                            <span class="material-symbols-outlined text-[15px]">chevron_right</span>
                        </a>
                    </div>
                @endif
            @endif
        </div>

    </div>

</div>

<script>
(function() {
    const ctx = document.getElementById('barPendaftaran');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($labelTanggal),
            datasets: [{
                label: 'Pendaftaran',
                data: @json($pendaftaran7Hari),
                backgroundColor: 'rgba(44, 95, 111, 0.15)',
                hoverBackgroundColor: 'rgba(44, 95, 111, 0.85)',
                borderColor: 'rgba(44, 95, 111, 0.9)',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                barPercentage: 0.6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleColor: '#fff',
                    bodyColor: '#cbd5e1',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: ctx => ctx.parsed.y + ' pendaftaran'
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8', font: { size: 11 } },
                    border: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: { color: '#94a3b8', font: { size: 11 }, stepSize: 1 },
                    grid: { color: '#f1f5f9' },
                    border: { display: false }
                }
            }
        }
    });
})();
</script>
@endsection
