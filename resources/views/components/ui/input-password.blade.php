{{-- Input password dengan tombol tampil/sembunyi --}}
<div x-data="{ lihat: false }" class="relative">
    <x-ui.input type="password" x-bind:type="lihat ? 'text' : 'password'" {{ $attributes->class('pr-10') }} />
    <button type="button" @click="lihat = !lihat" :aria-label="lihat ? 'Sembunyikan password' : 'Tampilkan password'"
            class="absolute inset-y-0 right-0 flex w-10 cursor-pointer items-center justify-center rounded-r-md text-muted-foreground transition-colors hover:text-foreground focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-ring/50">
        <span class="material-symbols-outlined text-[18px]" x-text="lihat ? 'visibility_off' : 'visibility'">visibility</span>
    </button>
</div>
