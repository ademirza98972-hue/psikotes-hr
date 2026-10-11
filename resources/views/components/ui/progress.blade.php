@props(['value' => 0])

@php $nilai = max(0, min(100, (float) $value)); @endphp

<div role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $nilai }}"
     {{ $attributes->class('relative h-2 w-full overflow-hidden rounded-full bg-primary/15') }}>
    <div class="h-full rounded-full bg-primary transition-all" style="width: {{ $nilai }}%"></div>
</div>
