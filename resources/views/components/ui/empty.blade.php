@props(['icon' => 'inbox', 'title', 'description' => null])

<div {{ $attributes->class('flex flex-col items-center justify-center gap-2 px-6 py-10 text-center') }}>
    <div class="mb-1 flex size-10 items-center justify-center rounded-lg bg-muted">
        <span class="material-symbols-outlined text-[20px] text-muted-foreground">{{ $icon }}</span>
    </div>
    <p class="text-sm font-medium">{{ $title }}</p>
    @if ($description)
        <p class="max-w-xs text-sm text-muted-foreground">{{ $description }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-2">{{ $slot }}</div>
    @endif
</div>
