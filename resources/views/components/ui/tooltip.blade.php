{{-- Tooltip shadcn (CSS-only, muncul saat hover/fokus). --}}
@props(['text', 'side' => 'top'])

@php
    $posisi = [
        'top'    => 'bottom-full left-1/2 mb-2 -translate-x-1/2',
        'bottom' => 'top-full left-1/2 mt-2 -translate-x-1/2',
        'right'  => 'left-full top-1/2 ml-2 -translate-y-1/2',
    ][$side];
@endphp

<span {{ $attributes->class('group/tooltip relative inline-flex') }}>
    {{ $slot }}
    <span role="tooltip"
          class="pointer-events-none absolute z-50 {{ $posisi }} w-max max-w-xs rounded-md bg-primary px-3 py-1.5 text-xs text-primary-foreground opacity-0 transition-opacity delay-100 group-hover/tooltip:opacity-100 group-focus-within/tooltip:opacity-100">
        {{ $text }}
    </span>
</span>
