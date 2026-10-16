@props(['title' => null, 'description' => null])

<div {{ $attributes->class('flex items-start justify-between gap-4 px-6') }}>
    <div class="grid gap-1.5">
        @if ($title)<h3 class="font-semibold leading-none">{{ $title }}</h3>@endif
        @if ($description)<p class="text-sm text-muted-foreground">{{ $description }}</p>@endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="shrink-0">{{ $slot }}</div>
    @endif
</div>
