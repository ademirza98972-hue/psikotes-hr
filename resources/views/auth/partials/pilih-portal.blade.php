{{-- Pindah portal login: Peserta / Admin --}}
<nav class="grid grid-cols-2 gap-1 rounded-lg bg-muted p-1 text-sm font-medium" aria-label="Pilih portal login">
    @foreach ([['login', 'Peserta', 'person'], ['login.admin', 'Admin / HR', 'shield_person']] as [$rute, $label, $ikon])
        <a href="{{ route($rute) }}" @if (request()->routeIs($rute)) aria-current="page" @endif
           class="flex h-8 items-center justify-center gap-1.5 rounded-md transition-colors {{ request()->routeIs($rute) ? 'bg-background text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground' }}">
            <span class="material-symbols-outlined text-[18px]">{{ $ikon }}</span>{{ $label }}
        </a>
    @endforeach
</nav>
