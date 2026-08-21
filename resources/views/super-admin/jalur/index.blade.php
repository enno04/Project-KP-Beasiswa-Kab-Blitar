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
                    <a href="{{ route('super-admin.master.custom-fields.index', $jalur->id) }}" class="text-xs px-3 py-1.5 rounded-lg border font-medium hover:bg-indigo-50 text-indigo-600 flex items-center gap-1">
                        <i data-lucide="layout-template" class="w-3 h-3"></i> Formulir
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
    <div id="modalTambah" class="modal-overlay hidden">
        <div class="modal-panel max-w-lg mx-4">
            <form method="POST" action="{{ route('super-admin.master.jalur.store', $program->id) }}">
                @csrf
                <div class="modal-header">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-primary/10 text-primary">
                            <i data-lucide="git-branch" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-lg font-bold">Tambah Jalur Baru</h3>
                    </div>
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="modal-body space-y-4">
                    <x-form-input name="nama" label="Nama Jalur" :required="true" placeholder="Contoh: Reguler / Prestasi" />
                    <div>
                        <label class="form-label">Kode Jalur <span class="required">*</span></label>
                        <input type="text" name="kode" class="form-input" required placeholder="contoh: reguler">
                        <span class="text-[11px] text-slate-500 mt-1 block">Gunakan huruf kecil tanpa spasi. Ini akan digunakan oleh sistem.</span>
                    </div>
                    <div>
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="form-input" placeholder="Tuliskan penjelasan singkat mengenai jalur ini..."></textarea>
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="aktif" class="form-select">
                            <option value="1">Aktif (Ditampilkan)</option>
                            <option value="0">Nonaktif (Disembunyikan)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-slate-50/80">
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-primary"><i data-lucide="plus" class="w-4 h-4"></i> Tambah Jalur</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
