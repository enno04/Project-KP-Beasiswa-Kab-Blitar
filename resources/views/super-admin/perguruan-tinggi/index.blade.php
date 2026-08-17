<x-layouts.admin :title="'Perguruan Tinggi'">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">Perguruan Tinggi</h1>
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold" style="background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah PT
            </button>
        </div>
        <div class="rounded-xl border overflow-hidden" style="background-color: var(--color-surface); border-color: var(--color-border);">
            <table class="w-full text-sm">
                <thead style="background-color: #F1F5F9;"><tr>
                    <th class="text-left px-4 py-3 font-semibold">Nama</th>
                    <th class="text-center px-4 py-3 font-semibold">Jenis</th>
                    <th class="text-center px-4 py-3 font-semibold">Akreditasi</th>
                    <th class="text-center px-4 py-3 font-semibold">Lokasi</th>
                    <th class="text-center px-4 py-3 font-semibold">Aksi</th>
                </tr></thead>
                <tbody class="divide-y">
                    @forelse($pt as $item)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 font-medium">{{ $item->nama }}</td>
                        <td class="px-4 py-3 text-center"><span class="text-xs px-2 py-1 rounded-full font-semibold {{ $item->jenis === 'negeri' ? '' : '' }}" style="background: {{ $item->jenis === 'negeri' ? 'rgba(25,135,84,0.1)' : 'rgba(11,94,215,0.1)' }}; color: {{ $item->jenis === 'negeri' ? 'var(--color-success)' : 'var(--color-primary)' }};">{{ ucfirst($item->jenis) }}</span></td>
                        <td class="px-4 py-3 text-center">{{ $item->akreditasi ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">{{ $item->lokasi === 'dalam_daerah' ? 'Dalam Daerah' : 'Luar Daerah' }}</td>
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('super-admin.master.pt.destroy', $item->id) }}" onsubmit="return confirm('Yakin hapus?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg hover:bg-red-50"><i data-lucide="trash-2" class="w-4 h-4" style="color: var(--color-danger);"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center" style="color: var(--color-text-secondary);">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3">{{ $pt->links() }}</div>
        </div>
    </div>
    {{-- Modal Tambah --}}
    <div id="modalTambah" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="rounded-2xl border p-6 w-full max-w-md mx-4" style="background-color: var(--color-surface);">
            <h3 class="text-lg font-bold mb-4">Tambah Perguruan Tinggi</h3>
            <form method="POST" action="{{ route('super-admin.master.pt.store') }}">@csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-medium mb-1">Nama</label><input type="text" name="nama" required class="w-full border rounded-xl px-3 py-2 text-sm"></div>
                    <div><label class="block text-sm font-medium mb-1">Jenis</label><select name="jenis" required class="w-full border rounded-xl px-3 py-2 text-sm"><option value="negeri">Negeri</option><option value="swasta">Swasta</option></select></div>
                    <div><label class="block text-sm font-medium mb-1">Akreditasi</label><input type="text" name="akreditasi" class="w-full border rounded-xl px-3 py-2 text-sm"></div>
                    <div><label class="block text-sm font-medium mb-1">Lokasi</label><select name="lokasi" required class="w-full border rounded-xl px-3 py-2 text-sm"><option value="luar_daerah">Luar Daerah</option><option value="dalam_daerah">Dalam Daerah</option></select></div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="px-4 py-2 rounded-xl text-sm border">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-sm text-white font-semibold" style="background-color: var(--color-primary);">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
