<x-layouts.admin :title="'Dokumen — ' . $jalur->nama">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-sm mb-1" style="color: var(--color-text-secondary);">
                    <a href="{{ route('super-admin.master.program.index') }}" class="hover:underline">Program</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <a href="{{ route('super-admin.master.jalur.index', $jalur->program_id) }}" class="hover:underline">{{ $jalur->program->nama }}</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span>{{ $jalur->nama }}</span>
                </div>
                <h1 class="text-2xl font-bold">Dokumen Persyaratan</h1>
            </div>
            <button onclick="document.getElementById('modal-tambah-dokumen').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-semibold transition-all hover:opacity-90" style="background-color: var(--color-primary);">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Dokumen
            </button>
        </div>

        <div class="rounded-2xl border overflow-hidden" style="background-color: var(--color-surface); border-color: var(--color-border);">
            <table class="w-full text-left text-sm">
                <thead style="background-color: #F1F5F9; border-bottom: 1px solid var(--color-border);">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-center w-16">No</th>
                        <th class="px-6 py-4 font-semibold">Nama Dokumen</th>
                        <th class="px-6 py-4 font-semibold text-center">Wajib</th>
                        <th class="px-6 py-4 font-semibold">OPD Verifikator</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--color-border);">
                    @forelse($jalur->dokumens()->orderBy('urutan')->get() as $dok)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-center font-medium text-gray-500">{{ $dok->urutan }}</td>
                            <td class="px-6 py-4">
                                <p class="font-bold">{{ $dok->nama }}</p>
                                <p class="text-xs" style="color: var(--color-text-secondary);">{{ $dok->deskripsi ?: '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($dok->wajib)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Wajib</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Opsional</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($dok->opd)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold" style="background: rgba(111,66,193,0.1); color: #6F42C1;">
                                        <i data-lucide="building-2" class="w-3 h-3"></i> {{ $dok->opd->nama_opd }}
                                    </span>
                                @else
                                    <span class="text-xs italic" style="color: var(--color-text-secondary);">Tanpa Verifikasi OPD</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" onclick="openEditDokumen('{{ $dok->id }}', '{{ $dok->nama }}', '{{ $dok->deskripsi }}', '{{ $dok->wajib }}', '{{ $dok->opd_id }}', '{{ $dok->urutan }}')" class="p-2 rounded-lg hover:bg-yellow-50" title="Edit">
                                        <i data-lucide="pencil" class="w-4 h-4" style="color: var(--color-warning);"></i>
                                    </button>
                                    <form action="{{ route('super-admin.master.dokumen.destroy', [$jalur->id, $dok->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg hover:bg-red-50" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4" style="color: var(--color-danger);"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center" style="color: var(--color-text-secondary);">
                                <i data-lucide="file-x" class="w-10 h-10 mx-auto mb-3 opacity-50"></i>
                                Belum ada dokumen persyaratan untuk jalur ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Tambah --}}
    <div id="modal-tambah-dokumen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
        <div class="rounded-2xl w-full max-w-md mx-4 overflow-hidden" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            <form action="{{ route('super-admin.master.dokumen.store', $jalur->id) }}" method="POST">
                @csrf
                <div class="p-6 border-b flex items-center justify-between sticky top-0" style="background-color: var(--color-surface); border-color: var(--color-border);">
                    <h3 class="text-lg font-bold">Tambah Dokumen</h3>
                    <button type="button" onclick="document.getElementById('modal-tambah-dokumen').classList.add('hidden')" class="hover:opacity-70">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Nama Dokumen</label>
                        <input type="text" name="nama" class="w-full px-4 py-2 rounded-xl border text-sm" placeholder="KTP / Kartu Keluarga" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Deskripsi</label>
                        <textarea name="deskripsi" class="w-full px-4 py-2 rounded-xl border text-sm" rows="2"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Urutan</label>
                            <input type="number" name="urutan" value="1" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Wajib?</label>
                            <select name="wajib" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                                <option value="1">Wajib</option>
                                <option value="0">Opsional</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">OPD Verifikator</label>
                        <select name="opd_id" class="w-full px-4 py-2 rounded-xl border text-sm">
                            <option value="">-- Tanpa Verifikasi OPD Khusus --</option>
                            @foreach($opdList as $opd)
                                <option value="{{ $opd->id }}">{{ $opd->nama_opd }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="p-6 border-t flex justify-end gap-3" style="background-color: var(--color-bg); border-color: var(--color-border);">
                    <button type="button" onclick="document.getElementById('modal-tambah-dokumen').classList.add('hidden')" class="px-4 py-2 rounded-xl border font-semibold hover:bg-gray-100">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white font-semibold" style="background-color: var(--color-primary);">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- Modal Edit --}}
    <div id="modal-edit-dokumen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
        <div class="rounded-2xl w-full max-w-md mx-4 overflow-hidden" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            <form id="form-edit-dokumen" method="POST">
                @csrf @method('PUT')
                <div class="p-6 border-b flex items-center justify-between sticky top-0" style="background-color: var(--color-surface); border-color: var(--color-border);">
                    <h3 class="text-lg font-bold">Edit Dokumen</h3>
                    <button type="button" onclick="document.getElementById('modal-edit-dokumen').classList.add('hidden')" class="hover:opacity-70">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Nama Dokumen</label>
                        <input type="text" id="edit_nama" name="nama" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Deskripsi</label>
                        <textarea id="edit_deskripsi" name="deskripsi" class="w-full px-4 py-2 rounded-xl border text-sm" rows="2"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Urutan</label>
                            <input type="number" id="edit_urutan" name="urutan" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Wajib?</label>
                            <select id="edit_wajib" name="wajib" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                                <option value="1">Wajib</option>
                                <option value="0">Opsional</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">OPD Verifikator</label>
                        <select id="edit_opd_id" name="opd_id" class="w-full px-4 py-2 rounded-xl border text-sm">
                            <option value="">-- Tanpa Verifikasi OPD Khusus --</option>
                            @foreach($opdList as $opd)
                                <option value="{{ $opd->id }}">{{ $opd->nama_opd }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="p-6 border-t flex justify-end gap-3" style="background-color: var(--color-bg); border-color: var(--color-border);">
                    <button type="button" onclick="document.getElementById('modal-edit-dokumen').classList.add('hidden')" class="px-4 py-2 rounded-xl border font-semibold hover:bg-gray-100">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white font-semibold" style="background-color: var(--color-warning);">Update</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openEditDokumen(id, nama, deskripsi, wajib, opd_id, urutan) {
            document.getElementById('form-edit-dokumen').action = `/super-admin/master/jalur/{{ $jalur->id }}/dokumen/${id}`;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_deskripsi').value = deskripsi;
            document.getElementById('edit_wajib').value = wajib ? '1' : '0';
            document.getElementById('edit_urutan').value = urutan;
            document.getElementById('edit_opd_id').value = opd_id || '';
            document.getElementById('modal-edit-dokumen').classList.remove('hidden');
        }
    </script>
    @endpush
</x-layouts.admin>
