<div>
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden">
        <div class="mb-2 px-6 pt-4 text-[12px] text-on-surface-variant">
            Menampilkan <strong>{{ $items->firstItem() ?? $items->total() }}</strong> dari <strong>{{ $items->total() }}</strong> alat tes
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low border-b border-outline-variant">
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-wider">Nama Alat Tes</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-wider">Format Dasar</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-wider">Dihapus Pada</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-on-surface-variant uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/30">
                    @forelse ($items as $item)
                        <tr class="hover:bg-surface-container/30 transition-colors">
                            <td class="px-6 py-5">
                                <span class="font-mono text-body-md text-on-surface font-medium">{{ $item->kode }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <span class="font-semibold text-primary">{{ $item->nama }}</span>
                            </td>
                            <td class="px-6 py-5 text-body-md text-on-surface-variant">{{ $item->format_dasar ?? '-' }}</td>
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
                                    <form action="{{ route('admin.data-terhapus.pulihkan', ['jenis' => 'alat_tes', 'id' => $item->id]) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-2 px-4 py-2 border-2 border-action-teal text-action-teal rounded-xl hover:bg-action-teal/5 transition-all text-label-sm font-bold active:opacity-80">
                                            <span class="material-symbols-outlined text-[20px]">restore_from_trash</span>
                                            Pulihkan
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.data-terhapus.hapus-permanen', ['jenis' => 'alat_tes', 'id' => $item->id]) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Peringatan: Tindakan ini akan menghapus data alat tes secara permanen dan tidak dapat dipulihkan. Lanjutkan?')">
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
                            <td colspan="5" class="px-6 py-10 text-center text-body-md text-on-surface-variant">Tidak ada alat tes yang dihapus.</td>
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
