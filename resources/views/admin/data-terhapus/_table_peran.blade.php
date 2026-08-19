<div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden">
        <div class="mb-2 px-6 pt-4 text-[12px] text-on-surface-variant">
            Menampilkan <strong>{{ $items->firstItem() ?? $items->total() }}</strong> dari <strong>{{ $items->total() }}</strong> peran
        </div>
        <div style="overflow: auto; max-height: calc(100vh - 300px);">
            <table class="w-full text-left border-collapse">
                <thead style="position: sticky; top: 0; z-index: 5;">
                    <tr style="background: #0f2230; border-bottom: 1px solid #0a1a25;">
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider" style="color: #7db8c2;">Nama Peran</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider" style="color: #7db8c2;">Deskripsi</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-center" style="color: #7db8c2;">Jumlah Pengguna</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider" style="color: #7db8c2;">Dihapus Pada</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider text-right" style="color: #7db8c2;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30">
                    @forelse ($items as $item)
                        <tr class="hover:bg-surface-container/30 transition-colors">
                            <td class="px-6 py-5">
                                <span class="font-semibold text-primary">{{ $item->nama_peran }}</span>
                            </td>
                            <td class="px-6 py-5 text-body-md text-on-surface-variant max-w-[200px] truncate" title="{{ $item->deskripsi }}">{{ $item->deskripsi ?? '-' }}</td>
                            <td class="px-6 py-5 text-center">
                                <div class="inline-flex items-center justify-center px-2 py-1 rounded bg-secondary-container/30 border border-secondary-container text-on-secondary-container">
                                    <span class="text-label-sm font-bold">{{ $item->pengguna_count }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-body-md text-on-surface font-medium">{{ $item->deleted_at ? $item->deleted_at->format('d M Y') : '-' }}</span>
                                    @if ($item->deleted_at)
                                        <span class="text-label-sm text-on-surface-variant">({{ $item->deleted_at->diffForHumans() }})</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <form action="{{ route('admin.data-terhapus.pulihkan', ['jenis' => 'peran', 'id' => $item->id]) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-2 px-4 py-2 border-2 border-action-teal text-action-teal rounded-xl hover:bg-action-teal/5 transition-all text-label-sm font-bold active:opacity-80">
                                            <span class="material-symbols-outlined text-[20px]">restore_from_trash</span>
                                            Pulihkan
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.data-terhapus.hapus-permanen', ['jenis' => 'peran', 'id' => $item->id]) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Peringatan: Tindakan ini akan menghapus data peran secara permanen dan tidak dapat dipulihkan. Lanjutkan?')">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-error text-on-error rounded-xl shadow-lg shadow-error/20 hover:bg-error/90 transition-all text-label-sm font-bold active:opacity-80 border-2 border-error">
                                            <span class="material-symbols-outlined text-[20px]">delete_forever</span>
                                            Hapus Permanen
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-body-md text-on-surface-variant">Tidak ada peran yang dihapus.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($items->hasPages())
            <div class="p-6 border-t border-outline-variant bg-surface-container-low flex justify-center">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>
