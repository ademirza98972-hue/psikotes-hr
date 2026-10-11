@props(['href' => null, 'icon' => null, 'variant' => 'default'])

@php
    $classes = 'relative flex w-full cursor-pointer select-none items-center gap-2 rounded-sm px-2 py-1.5 text-sm outline-none transition-colors '
        . ($variant === 'destructive'
            ? 'text-destructive hover:bg-destructive/10 focus:bg-destructive/10'
            : 'hover:bg-accent hover:text-accent-foreground focus:bg-accent');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        @if ($icon)<span class="material-symbols-outlined text-[18px] opacity-70">{{ $icon }}</span>@endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button'])->class($classes) }}>
        @if ($icon)<span class="material-symbols-outlined text-[18px] opacity-70">{{ $icon }}</span>@endif
        {{ $slot }}
    </button>
@endif
