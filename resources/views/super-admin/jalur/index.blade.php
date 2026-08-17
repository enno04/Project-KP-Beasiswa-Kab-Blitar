<x-layouts.admin :title="'Jalur — ' . $program->nama">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-sm mb-1" style="color: var(--color-text-secondary);">
                    <a href="{{ route('super-admin.master.program.index') }}" class="hover:underline">Program</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span>{{ $program->nama }}</span>
                </div>
                <h1 class="text-2xl font-bold">Jalur Beasiswa</h1>
            </div>
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold" style="background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Jalur
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($program->jalurs as $jalur)
            <div class="rounded-xl border p-5 space-y-4" style="background-color: var(--color-surface); border-color: var(--color-border);">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-lg">{{ $jalur->nama }}</h3>
                        <code class="text-xs bg-gray-100 px-2 py-0.5 rounded">{{ $jalur->kode }}</code>
                    </div>
                    @if($jalur->aktif)
                        <span class="text-xs px-2.5 py-1 rounded-full font-semibold" style="background: rgba(25,135,84,0.1); color: var(--color-success);">Aktif</span>
                    @else
                        <span class="text-xs px-2.5 py-1 rounded-full font-semibold" style="background: rgba(108,117,125,0.1); color: var(--color-text-secondary);">Nonaktif</span>
                    @endif
                </div>
                <p class="text-sm" style="color: var(--color-text-secondary);">{{ $jalur->deskripsi ?: 'Belum ada deskripsi' }}</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('super-admin.master.kriteria.index', $jalur->id) }}" class="text-xs px-3 py-1.5 rounded-lg border font-medium hover:bg-gray-50 flex items-center gap-1">
                        <i data-lucide="bar-chart-3" class="w-3 h-3"></i> Kriteria & Bobot
                    </a>
                    <a href="{{ route('super-admin.master.dokumen.index', $jalur->id) }}" class="text-xs px-3 py-1.5 rounded-lg border font-medium hover:bg-gray-50 flex items-center gap-1">
                        <i data-lucide="file-text" class="w-3 h-3"></i> Dokumen
                    </a>
                </div>
                <div class="flex items-center gap-1 pt-2 border-t" style="border-color: var(--color-border);">
                    <form method="POST" action="{{ route('super-admin.master.jalur.destroy', [$program->id, $jalur->id]) }}" onsubmit="return confirm('Yakin hapus jalur ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 rounded-lg hover:bg-red-50"><i data-lucide="trash-2" class="w-4 h-4" style="color: var(--color-danger);"></i></button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-8" style="color: var(--color-text-secondary);">Belum ada jalur untuk program ini.</div>
            @endforelse
        </div>
    </div>

    {{-- Modal Tambah --}}
    <div id="modalTambah" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="rounded-2xl border p-6 w-full max-w-md mx-4" style="background-color: var(--color-surface);">
            <h3 class="text-lg font-bold mb-4">Tambah Jalur</h3>
            <form method="POST" action="{{ route('super-admin.master.jalur.store', $program->id) }}">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-medium mb-1">Nama</label><input type="text" name="nama" required class="w-full border rounded-xl px-3 py-2 text-sm" placeholder="Reguler"></div>
                    <div><label class="block text-sm font-medium mb-1">Kode</label><input type="text" name="kode" required class="w-full border rounded-xl px-3 py-2 text-sm" placeholder="reguler"></div>
                    <div><label class="block text-sm font-medium mb-1">Deskripsi</label><textarea name="deskripsi" rows="2" class="w-full border rounded-xl px-3 py-2 text-sm"></textarea></div>
                    <div class="grid grid-cols-2 gap-3">
                        <div><label class="block text-sm font-medium mb-1">Urutan</label><input type="number" name="urutan" value="1" required class="w-full border rounded-xl px-3 py-2 text-sm"></div>
                        <div><label class="block text-sm font-medium mb-1">Status</label><select name="aktif" class="w-full border rounded-xl px-3 py-2 text-sm"><option value="1">Aktif</option><option value="0">Nonaktif</option></select></div>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="px-4 py-2 rounded-xl text-sm border">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-sm text-white font-semibold" style="background-color: var(--color-primary);">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
