{{-- Dropdown Menu shadcn. Slot "trigger" = isi tombol pemicu; slot utama = item (<x-ui.dropdown-item>). --}}
@props(['align' => 'end', 'side' => 'bottom', 'width' => 'w-56'])

@php
    $posisi = match (true) {
        $side === 'top'    => 'bottom-full mb-2',
        $side === 'right'  => 'left-full bottom-0 ml-2',
        default            => 'top-full mt-2',
    } . ' ' . ($side === 'right' ? '' : ($align === 'start' ? 'left-0' : 'right-0'));
@endphp

<div class="relative" x-data="{ buka: false }" @click.outside="buka = false" @keydown.escape.window="buka = false">
    <div @click="buka = !buka">{{ $trigger }}</div>
    <div x-show="buka" x-cloak @click="buka = false"
         x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         {{ $attributes->class("absolute z-50 $posisi $width overflow-hidden rounded-md border border-border bg-popover p-1 text-popover-foreground shadow-md") }}>
        {{ $slot }}
    </div>
</div>
