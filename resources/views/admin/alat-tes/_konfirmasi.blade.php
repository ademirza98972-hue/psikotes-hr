{{-- Dialog konfirmasi hapus. Butuh state Alpine: konfirmasi = { buka, judul, pesan, url, label } --}}
<x-ui.dialog show="konfirmasi.buka" maxWidth="sm:max-w-md">
    <div class="flex gap-4">
        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-destructive/10 text-destructive">
            <span class="material-symbols-outlined text-[20px]">delete</span>
        </span>
        <div class="grid gap-1.5 pr-4">
            <h2 class="text-lg font-semibold leading-tight" x-text="konfirmasi.judul"></h2>
            <p class="text-sm text-muted-foreground" x-text="konfirmasi.pesan"></p>
        </div>
    </div>
    <form method="POST" :action="konfirmasi.url" class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
        @csrf @method('DELETE')
        <x-ui.button variant="outline" @click="konfirmasi.buka = false">Batal</x-ui.button>
        <x-ui.button type="submit" variant="destructive" x-text="konfirmasi.label"></x-ui.button>
    </form>
</x-ui.dialog>
