@props(['src' => null, 'name' => '?', 'size' => 'size-8'])

<span {{ $attributes->class("relative flex shrink-0 overflow-hidden rounded-full $size") }} x-data="{ gagal: false }">
    @if ($src)
        <img src="{{ $src }}" alt="{{ $name }}" class="aspect-square size-full object-cover" x-show="!gagal" @@error="gagal = true">
    @endif
    <span class="flex size-full items-center justify-center rounded-full bg-muted text-xs font-medium" @if ($src) x-show="gagal" x-cloak @endif>
        {{ mb_strtoupper(mb_substr(trim($name), 0, 1, 'UTF-8'), 'UTF-8') }}
    </span>
</span>
