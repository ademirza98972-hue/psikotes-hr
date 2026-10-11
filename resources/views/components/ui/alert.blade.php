@props(['variant' => 'default', 'title' => null, 'icon' => 'info'])

@php
    $variants = [
        'default'     => 'bg-card text-card-foreground',
        'destructive' => 'bg-card text-destructive [&_p.desc]:text-destructive/90',
        'warning'     => 'border-amber-200 bg-amber-50 text-amber-900 [&_p.desc]:text-amber-800',
    ];
@endphp

<div role="alert" {{ $attributes->class('relative flex w-full gap-3 rounded-lg border border-border px-4 py-3 text-sm ' . $variants[$variant]) }}>
    @if ($icon)
        <span class="material-symbols-outlined mt-px text-[18px]">{{ $icon }}</span>
    @endif
    <div class="grid gap-1">
        @if ($title)<p class="font-medium leading-tight">{{ $title }}</p>@endif
        <p class="desc text-muted-foreground">{{ $slot }}</p>
    </div>
</div>
