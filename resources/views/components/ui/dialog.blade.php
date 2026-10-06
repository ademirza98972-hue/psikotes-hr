{{-- $show: nama variabel Alpine boolean di scope induk, mis. "modalTambah" --}}
@props(['show', 'title' => null, 'description' => null, 'maxWidth' => 'sm:max-w-lg'])

<template x-teleport="body">
    <div x-show="{{ $show }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="{{ $show }} = false">
        <div x-show="{{ $show }}" x-transition.opacity.duration.150ms class="fixed inset-0 bg-black/50" @click="{{ $show }} = false"></div>

        <div x-show="{{ $show }}"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             {{ $attributes->class('relative grid max-h-[calc(100vh-2rem)] w-full gap-4 overflow-y-auto rounded-lg border border-border bg-background p-6 shadow-lg ' . $maxWidth) }}>
            <button type="button" @click="{{ $show }} = false"
                    class="absolute right-4 top-4 rounded-xs opacity-70 transition-opacity hover:opacity-100 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">close</span>
                <span class="sr-only">Tutup</span>
            </button>

            @if ($title)
                <div class="flex flex-col gap-2 pr-6">
                    <h2 class="text-lg font-semibold leading-none">{{ $title }}</h2>
                    @if ($description)
                        <p class="text-sm text-muted-foreground">{{ $description }}</p>
                    @endif
                </div>
            @endif

            {{ $slot }}
        </div>
    </div>
</template>
