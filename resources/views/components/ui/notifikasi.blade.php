{{-- Toast (Sonner) + Alert Dialog global ala shadcn.
     JS: toast({type, title, text}), konfirmasi({title, text, confirmButtonText}) → Promise<bool>.
     window.Swal.fire() dipetakan ke keduanya agar halaman lama tetap jalan tanpa SweetAlert. --}}
@props(['flash' => []])

<div x-data class="pointer-events-none fixed bottom-4 right-4 z-[100] flex w-[calc(100%-2rem)] max-w-sm flex-col gap-2">
    <template x-for="t in $store.toast.items" :key="t.id">
        <div x-show="t.show"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-y-2 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="pointer-events-auto flex items-start gap-3 rounded-lg border border-border bg-popover p-4 text-sm text-popover-foreground shadow-lg"
             role="status">
            <span class="material-symbols-outlined mt-px text-[18px]" :class="$store.toast.warna(t.type)" x-text="$store.toast.ikon(t.type)"></span>
            <div class="grid min-w-0 flex-1 gap-1">
                <p class="font-medium leading-tight" x-text="t.title"></p>
                <p class="text-muted-foreground" x-show="t.text" x-text="t.text"></p>
                <div class="text-muted-foreground [&_ul]:list-disc [&_ul]:pl-4" x-show="t.html" x-html="t.html"></div>
            </div>
            <button type="button" class="cursor-pointer text-muted-foreground transition-colors hover:text-foreground" @click="$store.toast.tutup(t.id)">
                <span class="material-symbols-outlined text-[16px]">close</span>
                <span class="sr-only">Tutup</span>
            </button>
        </div>
    </template>
</div>

<div x-data x-show="$store.konfirmasi.buka" x-cloak class="fixed inset-0 z-[90] flex items-center justify-center p-4"
     @keydown.escape.window="$store.konfirmasi.jawab(false)">
    <div x-show="$store.konfirmasi.buka" x-transition.opacity.duration.150ms class="fixed inset-0 bg-black/50"></div>
    <div x-show="$store.konfirmasi.buka" role="alertdialog" aria-modal="true"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         class="relative grid w-full max-w-lg gap-4 rounded-lg border border-border bg-background p-6 shadow-lg">
        <div class="grid gap-2">
            <h2 class="text-lg font-semibold" x-text="$store.konfirmasi.title"></h2>
            <p class="text-sm text-muted-foreground" x-show="$store.konfirmasi.text" x-text="$store.konfirmasi.text"></p>
            <div class="text-sm text-muted-foreground [&_ul]:list-disc [&_ul]:pl-4" x-show="$store.konfirmasi.html" x-html="$store.konfirmasi.html"></div>
        </div>
        <div class="flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
            <x-ui.button variant="outline" ::hidden="!$store.konfirmasi.showCancel" @click="$store.konfirmasi.jawab(false)"
                         x-text="$store.konfirmasi.cancelText"></x-ui.button>
            <button type="button" @click="$store.konfirmasi.jawab(true)" x-text="$store.konfirmasi.confirmText"
                    :class="$store.konfirmasi.destructive ? 'bg-destructive text-white hover:bg-destructive/90' : 'bg-primary text-primary-foreground hover:bg-primary/90'"
                    class="inline-flex h-9 cursor-pointer items-center justify-center rounded-md px-4 text-sm font-medium shadow-xs outline-none transition-all focus-visible:ring-[3px] focus-visible:ring-ring/50"></button>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    let urut = 0;
    Alpine.store('toast', {
        items: [],
        add({ type = 'success', title = '', text = '', html = '', duration = 4000 } = {}) {
            const id = ++urut;
            this.items.push({ id, type, title, text, html, show: true });
            if (duration > 0) setTimeout(() => this.tutup(id), duration);
        },
        tutup(id) {
            const t = this.items.find(i => i.id === id);
            if (!t) return;
            t.show = false;
            setTimeout(() => this.items = this.items.filter(i => i.id !== id), 200);
        },
        ikon: t => ({ success: 'check_circle', error: 'error', warning: 'warning', info: 'info', question: 'help' }[t] || 'info'),
        warna: t => ({ success: 'text-emerald-600', error: 'text-destructive', warning: 'text-amber-600' }[t] || 'text-foreground'),
    });

    Alpine.store('konfirmasi', {
        buka: false, title: '', text: '', html: '', confirmText: 'Lanjutkan', cancelText: 'Batal',
        showCancel: true, destructive: false, _resolve: null,
        ask(o = {}) {
            const label = `${o.confirmButtonText || ''} ${o.title || ''} ${o.text || ''}`;
            Object.assign(this, {
                title: o.title || 'Konfirmasi', text: o.text || '', html: o.html || '',
                confirmText: o.confirmButtonText || (o.showCancelButton === false ? 'Mengerti' : 'Lanjutkan'),
                cancelText: o.cancelButtonText || 'Batal',
                showCancel: o.showCancelButton !== false,
                destructive: o.destructive ?? /hapus|tolak|reset|nonaktif/i.test(label),
                buka: true,
            });
            return new Promise(r => this._resolve = r);
        },
        jawab(ok) {
            if (!this.buka) return;
            this.buka = false;
            this._resolve?.(ok);
        },
    });

    @json($flash).forEach(t => Alpine.store('toast').add(t));
});

window.toast = (o) => Alpine.store('toast').add(o);
window.konfirmasi = (o) => Alpine.store('konfirmasi').ask(o);
window.Swal = {
    fire(o = {}) {
        if (typeof o === 'string') o = { title: arguments[0], text: arguments[1], icon: arguments[2] };
        if (o.timer || o.toast) {
            toast({ type: o.icon, title: o.title, text: o.text, html: o.html, duration: o.timer || 4000 });
            return Promise.resolve({ isConfirmed: false, isDismissed: true });
        }
        return konfirmasi({ ...o, showCancelButton: !!o.showCancelButton })
            .then(ok => ({ isConfirmed: ok, isDismissed: !ok }));
    },
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[onsubmit]').forEach(form => {
        const match = (form.getAttribute('onsubmit') || '').match(/confirm\(['"`]([\s\S]*?)['"`]\)/);
        if (!match) return;
        const pesan = match[1].replace(/\\'/g, "'").replace(/\\"/g, '"');
        form.removeAttribute('onsubmit');
        form.addEventListener('submit', e => {
            e.preventDefault();
            konfirmasi({ title: 'Konfirmasi', text: pesan, showCancelButton: true, confirmButtonText: 'Ya, lanjutkan' })
                .then(ok => ok && form.submit());
        });
    });
});
</script>
