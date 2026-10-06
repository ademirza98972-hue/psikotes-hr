@props(['variant' => 'default'])

@php
    $variants = [
        'default'     => 'border-transparent bg-primary text-primary-foreground',
        'secondary'   => 'border-transparent bg-secondary text-secondary-foreground',
        'destructive' => 'border-transparent bg-destructive text-white',
        'outline'     => 'border-border text-foreground',
        'success'     => 'border-emerald-200 bg-emerald-50 text-emerald-700',
        'warning'     => 'border-amber-200 bg-amber-50 text-amber-700',
        'danger'      => 'border-red-200 bg-red-50 text-red-700',
    ];
@endphp

<span {{ $attributes->class('inline-flex w-fit shrink-0 items-center justify-center gap-1 whitespace-nowrap rounded-md border px-2 py-0.5 text-xs font-medium ' . $variants[$variant]) }}>{{ $slot }}</span>
