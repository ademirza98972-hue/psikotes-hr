{{-- Kepala bersama halaman Dimensi & Norma: identitas alat tes + tab pindah halaman. Butuh $alatTes, $deskripsi. --}}
<div class="space-y-4">
    <a href="{{ route('admin.alat-tes.index') }}" class="inline-flex items-center gap-1 text-sm text-muted-foreground transition-colors hover:text-foreground">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>Semua alat tes
    </a>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="flex min-w-0 items-start gap-4">
            <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-brand-ink font-mono text-sm font-semibold text-white">{{ $alatTes->kode }}</span>
            <div class="min-w-0 space-y-1">
                <h1 class="text-2xl font-semibold tracking-tight">{{ $alatTes->nama }}</h1>
                <p class="text-sm text-muted-foreground">{{ $deskripsi }}</p>
            </div>
        </div>
        <nav class="inline-flex h-9 w-fit shrink-0 items-center rounded-lg bg-muted p-[3px] text-sm font-medium text-muted-foreground" aria-label="Pengaturan alat tes">
            @foreach (['dimensi' => ['Dimensi & level', 'psychology'], 'norma' => ['Norma', 'table_chart']] as $rute => [$label, $ikon])
                @php $aktif = request()->routeIs("admin.alat-tes.$rute"); @endphp
                <a href="{{ route("admin.alat-tes.$rute", $alatTes->id) }}" @if ($aktif) aria-current="page" @endif
                   class="inline-flex h-full items-center gap-1.5 rounded-md px-3 transition-all {{ $aktif ? 'bg-background text-foreground shadow-sm' : 'hover:text-foreground' }}">
                    <span class="material-symbols-outlined text-[18px]">{{ $ikon }}</span>{{ $label }}
                </a>
            @endforeach
        </nav>
    </div>
</div>
