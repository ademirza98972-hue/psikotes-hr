{{-- Tabs shadcn. Pakai: <x-ui.tabs model="tab" :items="['a' => 'A', 'b' => 'B']" />, lalu x-show="tab === 'a'" pada panel. --}}
@props(['model', 'items' => []])

<div role="tablist" {{ $attributes->class('inline-flex h-9 w-fit items-center justify-center rounded-lg bg-muted p-[3px] text-muted-foreground') }}>
    @foreach ($items as $nilai => $teks)
        <button type="button" role="tab" @click="{{ $model }} = '{{ $nilai }}'"
                :aria-selected="{{ $model }} === '{{ $nilai }}'"
                :class="{{ $model }} === '{{ $nilai }}' ? 'bg-background text-foreground shadow-sm' : 'hover:text-foreground'"
                class="inline-flex h-full flex-1 cursor-pointer items-center justify-center gap-1.5 whitespace-nowrap rounded-md px-3 text-sm font-medium transition-all outline-none focus-visible:ring-[3px] focus-visible:ring-ring/50">
            {{ $teks }}
        </button>
    @endforeach
</div>
