@extends('layouts.admin', ['judulHalaman' => 'Dashboard'])

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>

@php
    $u = auth()->user();
    $bisaKelolaTes = $u->hasIzin('soal.lihat') || $u->hasIzin('kategori_tes.kelola');
    $bisaLihatHasil = $u->hasIzin('hasil_tes.lihat');
    $labelKejadian = ['pindah_tab' => 'Pindah tab', 'keluar_fullscreen' => 'Keluar layar penuh', 'halaman_ditutup' => 'Halaman ditutup'];
    $persen = fn ($n) => $pesertaTotal > 0 ? round($n / $pesertaTotal * 100) : 0;

    $kpi = [
        ['Sesi Berjalan', $jumlahSesiBerjalan, 'event_available', "{$jumlahSesiMendatang} sesi mendatang"],
        ['Total Peserta', $pesertaTotal, 'groups', "{$pesertaBelum} belum mulai mengerjakan"],
        ['Tes Selesai', $pesertaSelesai, 'task_alt', "{$tingkatPenyelesaian}% tingkat penyelesaian"],
        ['Sedang Mengerjakan', $pesertaSedang, 'pending', 'Peserta yang sudah mulai tes'],
    ];
@endphp

<div class="mx-auto max-w-7xl space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="space-y-1">
            <p class="text-sm text-muted-foreground">{{ $tanggalHariIni }}</p>
            <h1 class="text-2xl font-semibold tracking-tight">{{ $sapaan }}, {{ $namaPengguna }}</h1>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($u->hasIzin('pengguna.tambah'))
                <x-ui.button variant="outline" size="sm" :href="route('admin.data-kandidat.tambah')">
                    <span class="material-symbols-outlined">person_add</span>Kandidat
                </x-ui.button>
            @endif
            @if ($bisaKelolaTes)
                <x-ui.button size="sm" :href="route('admin.penjadwalan-tes.tambah')">
                    <span class="material-symbols-outlined">add</span>Jadwalkan Tes
                </x-ui.button>
            @endif
        </div>
    </div>

    {{-- KPI --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($kpi as [$judul, $nilai, $ikon, $sub])
            <x-ui.card class="flex flex-col gap-2 p-5">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-muted-foreground">{{ $judul }}</p>
                    <span class="material-symbols-outlined text-[18px] text-muted-foreground">{{ $ikon }}</span>
                </div>
                <p class="text-3xl font-semibold tabular-nums tracking-tight">{{ number_format($nilai) }}</p>
                @if ($loop->index === 2)
                    <x-ui.progress :value="$tingkatPenyelesaian" class="mt-1" />
                @endif
                <p class="text-xs text-muted-foreground">{{ $sub }}</p>
            </x-ui.card>
        @endforeach
    </div>

    {{-- TREN + STATUS PENGERJAAN --}}
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <x-ui.card class="py-6 xl:col-span-2"
                   x-data="grafikTren({{ Js::from($tren) }})" x-init="mulai()">
            <x-ui.card-header>
                <x-slot:title>
                    <span x-text="seri === 'selesai' ? 'Tes Diselesaikan' : 'Pendaftaran Akun'"></span>
                </x-slot:title>
                <x-slot:description>
                    <span x-text="(seri === 'selesai' ? 'Peserta yang menyelesaikan tes' : 'Akun baru semua tipe') + ', ' + rentang + ' hari terakhir'"></span>
                </x-slot:description>
                <p class="text-right">
                    <span class="block text-2xl font-semibold tabular-nums" x-text="total"></span>
                    <span class="text-xs text-muted-foreground">total periode</span>
                </p>
            </x-ui.card-header>
            <div class="mt-4 flex flex-wrap items-center justify-between gap-2 px-6">
                <x-ui.tabs model="seri" :items="['selesai' => 'Tes selesai', 'pendaftaran' => 'Pendaftaran']" />
                <x-ui.tabs model="rentang" :items="['7' => '7 hari', '30' => '30 hari', '90' => '90 hari']" />
            </div>
            <div class="mt-4 h-64 px-4">
                <canvas x-ref="kanvas" role="img" aria-label="Grafik batang jumlah per hari"></canvas>
            </div>
        </x-ui.card>

        <x-ui.card class="flex flex-col gap-5 py-6">
            <x-ui.card-header title="Status Pengerjaan" description="Seluruh peserta di sesi tes aktif" />
            <div class="grid gap-4 px-6">
                @foreach ([['Selesai', $pesertaSelesai, 'bg-emerald-500'], ['Sedang mengerjakan', $pesertaSedang, 'bg-amber-500'], ['Belum mulai', $pesertaBelum, 'bg-zinc-300']] as [$label, $jumlah, $warna])
                    <div class="grid gap-1.5">
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2"><span class="size-2 rounded-full {{ $warna }}"></span>{{ $label }}</span>
                            <span class="tabular-nums text-muted-foreground"><span class="font-medium text-foreground">{{ number_format($jumlah) }}</span> · {{ $persen($jumlah) }}%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-muted">
                            <div class="h-full rounded-full {{ $warna }}" style="width: {{ $persen($jumlah) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <x-ui.separator />
            <div class="grid grid-cols-2 gap-3 px-6 text-sm">
                <div>
                    <p class="text-muted-foreground">Kandidat menunggu</p>
                    <p class="text-lg font-semibold tabular-nums">{{ number_format($kandidatMenunggu) }}</p>
                </div>
                <div>
                    <p class="text-muted-foreground">Peringatan 30 hari</p>
                    <p class="text-lg font-semibold tabular-nums">{{ number_format($peringatan30Hari) }}</p>
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- SESI BERJALAN + VERIFIKASI --}}
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <x-ui.card class="flex flex-col gap-4 py-6 xl:col-span-2">
            <x-ui.card-header title="Sesi Tes Aktif" description="Sesi yang sedang berjalan atau akan datang">
                @if ($bisaKelolaTes)
                    <x-ui.button variant="outline" size="sm" :href="route('admin.penjadwalan-tes.index')">Lihat semua</x-ui.button>
                @endif
            </x-ui.card-header>
            @if ($daftarSesi->isEmpty())
                <x-ui.empty icon="event_busy" title="Tidak ada sesi aktif" description="Jadwalkan sesi tes baru untuk mulai mengundang peserta." />
            @else
                <x-ui.table>
                    <thead>
                        <tr class="border-b border-border">
                            <x-ui.th class="pl-6">Sesi</x-ui.th>
                            <x-ui.th>Periode</x-ui.th>
                            <x-ui.th>Status</x-ui.th>
                            <x-ui.th class="w-48 pr-6">Progres</x-ui.th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarSesi as $sesi)
                            @php
                                $mulai = \Illuminate\Support\Carbon::parse($sesi->tanggal_mulai);
                                $selesai = \Illuminate\Support\Carbon::parse($sesi->tanggal_selesai);
                                $berjalan = $mulai->lte(today());
                                $progres = $sesi->total_peserta > 0 ? round($sesi->total_selesai / $sesi->total_peserta * 100) : 0;
                                $sisaHari = (int) today()->diffInDays($selesai, false);
                            @endphp
                            <tr class="border-b border-border transition-colors last:border-0 hover:bg-muted/50">
                                <x-ui.td class="pl-6">
                                    @if ($bisaKelolaTes)
                                        <a href="{{ route('admin.penjadwalan-tes.detail', $sesi->id) }}" class="font-medium hover:underline">{{ $sesi->nama_sesi }}</a>
                                    @else
                                        <span class="font-medium">{{ $sesi->nama_sesi }}</span>
                                    @endif
                                </x-ui.td>
                                <x-ui.td class="text-muted-foreground">
                                    {{ $mulai->translatedFormat('j M') }} – {{ $selesai->translatedFormat('j M Y') }}
                                    @if ($berjalan)<span class="block text-xs">{{ $sisaHari === 0 ? 'Berakhir hari ini' : "Sisa {$sisaHari} hari" }}</span>@endif
                                </x-ui.td>
                                <x-ui.td>
                                    <x-ui.badge :variant="$berjalan ? 'success' : 'secondary'">{{ $berjalan ? 'Berjalan' : 'Mendatang' }}</x-ui.badge>
                                </x-ui.td>
                                <x-ui.td class="pr-6">
                                    <div class="flex items-center gap-3">
                                        <x-ui.progress :value="$progres" />
                                        <span class="w-14 shrink-0 text-right text-xs tabular-nums text-muted-foreground">{{ $sesi->total_selesai }}/{{ $sesi->total_peserta }}</span>
                                    </div>
                                </x-ui.td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
            @endif
        </x-ui.card>

        <x-ui.card class="flex flex-col gap-4 py-6">
            <x-ui.card-header title="Menunggu Verifikasi" description="Kandidat perlu persetujuan">
                @if ($kandidatMenunggu > 0)
                    <x-ui.badge variant="warning">{{ $kandidatMenunggu }}</x-ui.badge>
                @endif
            </x-ui.card-header>
            @if ($kandidatMenunggu === 0)
                <x-ui.empty icon="how_to_reg" title="Semua sudah diverifikasi" description="Tidak ada kandidat yang menunggu." />
            @else
                <div class="flex flex-col gap-1 px-3">
                    @foreach ($kandidatMenungguList as $k)
                        <div class="flex items-center gap-3 rounded-md px-3 py-2 hover:bg-muted/50">
                            <x-ui.avatar :name="$k->name" size="size-9" />
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium">{{ $k->name }}</p>
                                <p class="truncate text-xs text-muted-foreground">
                                    {{ $k->profilKandidat?->posisi?->nama_posisi ?? '-' }} · {{ $k->created_at->diffForHumans(null, true) }}
                                </p>
                            </div>
                            <form action="{{ route('admin.data-kandidat.approve', $k->id) }}" method="POST">
                                @csrf
                                <x-ui.button type="submit" variant="outline" size="icon-sm" title="Aktifkan" class="text-emerald-600 hover:text-emerald-700">
                                    <span class="material-symbols-outlined">check</span>
                                </x-ui.button>
                            </form>
                            <form action="{{ route('admin.data-kandidat.tolak', $k->id) }}" method="POST" onsubmit="return confirm('Tolak kandidat {{ $k->name }}?')">
                                @csrf
                                <x-ui.button type="submit" variant="outline" size="icon-sm" title="Tolak" class="text-destructive hover:text-destructive">
                                    <span class="material-symbols-outlined">close</span>
                                </x-ui.button>
                            </form>
                        </div>
                    @endforeach
                </div>
                @if ($kandidatMenunggu > 2)
                    <div class="mt-auto px-6">
                        <x-ui.button variant="ghost" size="sm" class="w-full" :href="route('admin.data-kandidat.index')">
                            Lihat {{ $kandidatMenunggu - 2 }} kandidat lainnya
                            <span class="material-symbols-outlined">chevron_right</span>
                        </x-ui.button>
                    </div>
                @endif
            @endif
        </x-ui.card>
    </div>

    {{-- HASIL TERBARU + INTEGRITAS --}}
    @if ($bisaLihatHasil)
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <x-ui.card class="flex flex-col gap-4 py-6 xl:col-span-2">
            <x-ui.card-header title="Hasil Tes Terbaru" description="Peserta yang baru menyelesaikan tes">
                <x-ui.button variant="outline" size="sm" :href="route('admin.hasil-tes.index')">Lihat semua</x-ui.button>
            </x-ui.card-header>
            @if ($hasilTerbaru->isEmpty())
                <x-ui.empty icon="assignment" title="Belum ada hasil" description="Hasil akan muncul setelah peserta menyelesaikan tes." />
            @else
                <x-ui.table>
                    <thead>
                        <tr class="border-b border-border">
                            <x-ui.th class="pl-6">Peserta</x-ui.th>
                            <x-ui.th>Sesi</x-ui.th>
                            <x-ui.th>Alat Tes</x-ui.th>
                            <x-ui.th>Selesai</x-ui.th>
                            <x-ui.th class="pr-6 text-right"><span class="sr-only">Aksi</span></x-ui.th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($hasilTerbaru as $h)
                            <tr class="border-b border-border transition-colors last:border-0 hover:bg-muted/50">
                                <x-ui.td class="pl-6">
                                    <div class="flex items-center gap-3">
                                        <x-ui.avatar :src="$h->user->foto_profil ? asset('storage/' . $h->user->foto_profil) : null" :name="$h->user->name" />
                                        <div class="min-w-0">
                                            <p class="truncate font-medium">{{ $h->user->name }}</p>
                                            <p class="text-xs capitalize text-muted-foreground">{{ $h->user->tipe_akun }}</p>
                                        </div>
                                    </div>
                                </x-ui.td>
                                <x-ui.td class="max-w-48 truncate text-muted-foreground">{{ $h->sesiTes->nama_sesi }}</x-ui.td>
                                <x-ui.td>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($h->alatTes as $at)
                                            <x-ui.badge variant="outline" class="font-mono">{{ $at->kode }}</x-ui.badge>
                                        @endforeach
                                    </div>
                                </x-ui.td>
                                <x-ui.td class="text-muted-foreground">{{ $h->tanggal_pengerjaan?->translatedFormat('j M Y') ?? '-' }}</x-ui.td>
                                <x-ui.td class="pr-6 text-right">
                                    <x-ui.button variant="ghost" size="sm" :href="route('admin.hasil-tes.detail', [$h->sesi_tes_id, $h->user_id])">
                                        Lihat<span class="material-symbols-outlined">arrow_forward</span>
                                    </x-ui.button>
                                </x-ui.td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
            @endif
        </x-ui.card>

        <x-ui.card class="flex flex-col gap-4 py-6">
            <x-ui.card-header title="Peringatan Integritas" description="Aktivitas mencurigakan saat tes berlangsung" />
            @if ($pesertaMencurigakan->isEmpty())
                <x-ui.empty icon="verified_user" title="Tidak ada peringatan" description="Belum ada aktivitas mencurigakan yang tercatat." />
            @else
                <div class="flex flex-col gap-1 px-3">
                    @foreach ($pesertaMencurigakan as $p)
                        <a href="{{ route('admin.hasil-tes.detail', [$p->sesi_tes_id, $p->user_id]) }}" class="flex items-start gap-3 rounded-md px-3 py-2 transition-colors hover:bg-muted/50">
                            <x-ui.avatar :src="$p->user->foto_profil ? asset('storage/' . $p->user->foto_profil) : null" :name="$p->user->name" size="size-9" />
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="truncate text-sm font-medium">{{ $p->user->name }}</p>
                                    <x-ui.badge variant="danger" class="tabular-nums">{{ $p->jumlah_peringatan }}×</x-ui.badge>
                                </div>
                                <p class="truncate text-xs text-muted-foreground">{{ $p->sesiTes->nama_sesi }} · {{ \Illuminate\Support\Carbon::parse($p->peringatan_terakhir)->diffForHumans() }}</p>
                                <p class="mt-1 truncate text-xs text-muted-foreground">
                                    {{ ($rincianPeringatan[$p->id] ?? collect())->map(fn ($r) => ($labelKejadian[$r->tipe_kejadian] ?? $r->tipe_kejadian) . ' ' . $r->total)->join(' · ') }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </div>
    @endif

    {{-- RINGKASAN PENGGUNA --}}
    <x-ui.card class="py-6">
        <x-ui.card-header title="Ringkasan Pengguna" description="Akun yang terdaftar di sistem" />
        <div class="mt-5 grid grid-cols-2 gap-px overflow-hidden border-y border-border bg-border md:grid-cols-4">
            @foreach ([
                ['Akun Karyawan', $totalKaryawan, "+{$karyawanBaru7Hari} minggu ini", 'admin.akun-karyawan.index', 'pengguna.lihat'],
                ['Akun Kandidat', $totalKandidat, "{$kandidatAktif} aktif · {$kandidatDitolak} ditolak", 'admin.data-kandidat.index', 'pengguna.lihat'],
                ['Data Karyawan Tanpa Akun', $nikBelumTerpakai, "dari {$totalDataKaryawan} data master", 'admin.akun-karyawan.index', 'pengguna.lihat'],
                ['Admin & Staff', $totalAdminStaff, "{$totalPeran} peran tersedia", 'admin.pengguna-admin.index', 'pengguna_admin.kelola'],
            ] as [$judul, $nilai, $sub, $rute, $izin])
                <a href="{{ $u->hasIzin($izin) ? route($rute) : '#' }}" class="bg-card px-6 py-4 transition-colors hover:bg-muted/50">
                    <p class="text-sm text-muted-foreground">{{ $judul }}</p>
                    <p class="mt-1 text-2xl font-semibold tabular-nums">{{ number_format($nilai) }}</p>
                    <p class="mt-1 text-xs text-muted-foreground">{{ $sub }}</p>
                </a>
            @endforeach
        </div>
    </x-ui.card>
</div>

<script>
function grafikTren(data) {
    let chart = null;
    return {
        seri: 'selesai',
        rentang: '90',
        get potong() { return -Number(this.rentang); },
        get total() { return data[this.seri].slice(this.potong).reduce((a, b) => a + b, 0).toLocaleString('id-ID'); },
        mulai() {
            if (!window.Chart) return;
            Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
            chart = new Chart(this.$refs.kanvas, {
                type: 'bar',
                data: { labels: [], datasets: [{ data: [], backgroundColor: '#2a78d6', hoverBackgroundColor: '#1f5fae',
                    borderRadius: { topLeft: 4, topRight: 4 }, borderSkipped: 'bottom', maxBarThickness: 32, barPercentage: 0.8 }] },
                options: {
                    responsive: true, maintainAspectRatio: false, animation: { duration: 250 },
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#ffffff', borderColor: '#e5e5e5', borderWidth: 1, titleColor: '#0a0a0a',
                            bodyColor: '#525252', padding: 10, cornerRadius: 8, displayColors: false,
                            callbacks: { label: c => c.parsed.y + (this.seri === 'selesai' ? ' tes selesai' : ' pendaftaran') },
                        },
                    },
                    scales: {
                        x: { grid: { display: false }, border: { display: false }, ticks: { color: '#737373', font: { size: 11 }, maxRotation: 0, autoSkip: true, maxTicksLimit: 8 } },
                        y: { beginAtZero: true, border: { display: false }, grid: { color: '#f0f0f0' }, ticks: { color: '#737373', font: { size: 11 }, precision: 0 } },
                    },
                },
            });
            this.perbarui();
            this.$watch('seri', () => this.perbarui());
            this.$watch('rentang', () => this.perbarui());
        },
        perbarui() {
            chart.data.labels = data.label.slice(this.potong);
            chart.data.datasets[0].data = data[this.seri].slice(this.potong);
            chart.update();
        },
    };
}
</script>
@endsection
