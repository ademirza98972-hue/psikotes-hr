<div class="rounded-lg border border-slate-200">
    <div class="mb-2 text-xs text-slate-500">
        Menampilkan <strong>{{ $items->firstItem() ?? $items->total() }}</strong> dari <strong>{{ $items->total() }}</strong> akun kandidat
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">No HP</th>
                    <th class="px-4 py-3">Dihapus Pada</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($items as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-900">{{ $item->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $item->email }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $item->no_hp ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $item->deleted_at ? $item->deleted_at->format('d M Y, H:i') : '-' }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="inline-flex justify-end gap-2">
                                <form action="{{ route('admin.data-terhapus.pulihkan', ['jenis' => 'kandidat', 'id' => $item->id]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700">Pulihkan</button>
                                </form>
                                <form action="{{ route('admin.data-terhapus.hapus-permanen', ['jenis' => 'kandidat', 'id' => $item->id]) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Data ini akan dihapus PERMANEN dan tidak bisa dikembalikan. Lanjutkan?')">
                                    @csrf
                                    <button type="submit" class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-red-700">
                                        <span class="inline-flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="h-3.5 w-3.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                            </svg>
                                            Hapus Permanen
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">Tidak ada akun kandidat yang dihapus.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($items->hasPages())
        <div class="mt-3">
            {{ $items->links() }}
        </div>
    @endif
</div>
