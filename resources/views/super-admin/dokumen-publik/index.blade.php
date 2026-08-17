<x-layouts.admin title="Master Dokumen Publik">
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Dokumen Publik</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola dokumen/informasi penting yang dapat dibaca dan diunduh oleh masyarakat umum.</p>
            </div>
            <button type="button" onclick="openCreateModal()" class="btn btn-primary shadow-md flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Dokumen
            </button>
        </div>

        {{-- Filter & Search Card --}}
        <div class="card p-4">
            <form method="GET" action="{{ route('super-admin.master.dokumen-publik.index') }}" class="flex flex-col sm:flex-row gap-3 items-center justify-between">
                <div class="relative w-full sm:w-80">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, deskripsi, atau file..."
                        class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select name="status" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 text-sm text-slate-700 focus:ring-2 focus:ring-primary outline-none">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('super-admin.master.dokumen-publik.index') }}" class="btn btn-outline btn-sm text-slate-500">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table Card --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="text-center w-16">Urutan</th>
                            <th>Nama & Deskripsi Dokumen</th>
                            <th>Informasi File</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dokumens as $doc)
                            <tr class="{{ !$doc->status_aktif ? 'bg-slate-50/70 opacity-75' : '' }}">
                                <td class="text-center font-bold text-slate-500">
                                    {{ $doc->urutan }}
                                </td>
                                <td>
                                    <p class="font-bold text-slate-900">{{ $doc->nama }}</p>
                                    @if($doc->deskripsi)
                                        <p class="text-xs text-slate-500 mt-0.5 max-w-md">{{ $doc->deskripsi }}</p>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[11px] font-extrabold uppercase border {{ $doc->extension_badge_color }}">
                                            {{ $doc->format_file }}
                                        </span>
                                        <div class="text-xs">
                                            <p class="font-medium text-slate-700 truncate max-w-[200px]" title="{{ $doc->file_name }}">{{ $doc->file_name }}</p>
                                            <p class="text-[11px] text-slate-400">{{ $doc->ukuran_formatted }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('super-admin.master.dokumen-publik.toggle', $doc->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ $doc->status_aktif ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-slate-200 text-slate-600 hover:bg-slate-300' }}">
                                            {{ $doc->status_aktif ? '✓ Aktif' : '✗ Nonaktif' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('dokumen.publik.download', $doc->id) }}" class="btn btn-xs bg-blue-50 text-blue-600 hover:bg-blue-100 border-blue-200" title="Unduh File" target="_blank">
                                            <i data-lucide="download" class="w-3.5 h-3.5"></i> Unduh
                                        </a>
                                        <button type="button" onclick="openEditModal({{ json_encode($doc) }})" class="btn btn-xs btn-outline" title="Edit">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Edit
                                        </button>
                                        <form action="{{ route('super-admin.master.dokumen-publik.destroy', $doc->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus dokumen {{ $doc->nama }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs text-red-600 hover:bg-red-50 border-red-200" title="Hapus">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <x-empty-state icon="file-text" title="Belum Ada Dokumen Publik" text="Klik tombol Tambah Dokumen untuk mengunggah dokumen publik pertama." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($dokumens->hasPages())
                <div class="card-footer">
                    {{ $dokumens->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL TAMBAH DOKUMEN --}}
    <div id="createModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden" style="backdrop-filter: blur(4px);">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800">Tambah Dokumen Publik</h3>
                <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form action="{{ route('super-admin.master.dokumen-publik.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Dokumen <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" required placeholder="Contoh: Ebook Panduan Pendaftaran 2026"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-primary focus:border-primary text-sm outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Singkat</label>
                        <textarea name="deskripsi" rows="2" placeholder="Penjelasan singkat mengenai isi dokumen..."
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-primary focus:border-primary text-sm outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Upload File <span class="text-red-500">*</span></label>
                        <input type="file" name="file" required accept=".pdf,.doc,.docx"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-primary outline-none">
                        <p class="text-[11px] text-slate-400 mt-1">Format: PDF, DOC, DOCX. Maksimal 10 MB.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Urutan Tampil</label>
                            <input type="number" name="urutan" value="1" min="0" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none focus:ring-2 focus:ring-primary">
                        </div>

                        <div class="flex items-center pt-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="status_aktif" value="1" checked class="w-4 h-4 rounded text-primary focus:ring-primary">
                                <span class="text-sm font-semibold text-slate-700">Status Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" onclick="closeCreateModal()" class="px-4 py-2 bg-slate-200 text-slate-700 hover:bg-slate-300 rounded-xl text-xs font-bold transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white hover:bg-primary-dark rounded-xl text-xs font-bold transition-all shadow-md">Simpan Dokumen</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT DOKUMEN --}}
    <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden" style="backdrop-filter: blur(4px);">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800">Edit Dokumen Publik</h3>
                <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form id="editForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Dokumen <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" id="edit_nama" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-primary focus:border-primary text-sm outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Singkat</label>
                        <textarea name="deskripsi" id="edit_deskripsi" rows="2"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-primary focus:border-primary text-sm outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Ganti File (Opsional)</label>
                        <input type="file" name="file" accept=".pdf,.doc,.docx"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-primary outline-none">
                        <p id="edit_current_file" class="text-[11px] text-slate-500 mt-1 italic"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Urutan Tampil</label>
                            <input type="number" name="urutan" id="edit_urutan" min="0" required
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm outline-none focus:ring-2 focus:ring-primary">
                        </div>

                        <div class="flex items-center pt-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="status_aktif" id="edit_status_aktif" value="1" class="w-4 h-4 rounded text-primary focus:ring-primary">
                                <span class="text-sm font-semibold text-slate-700">Status Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-slate-200 text-slate-700 hover:bg-slate-300 rounded-xl text-xs font-bold transition-all">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white hover:bg-primary-dark rounded-xl text-xs font-bold transition-all shadow-md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
        }
        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
        }
        function openEditModal(doc) {
            document.getElementById('editForm').action = `/super-admin/master/dokumen-publik/${doc.id}`;
            document.getElementById('edit_nama').value = doc.nama;
            document.getElementById('edit_deskripsi').value = doc.deskripsi || '';
            document.getElementById('edit_urutan').value = doc.urutan;
            document.getElementById('edit_status_aktif').checked = Boolean(doc.status_aktif);
            document.getElementById('edit_current_file').innerText = `File saat ini: ${doc.file_name} (${doc.format_file.toUpperCase()})`;
            document.getElementById('editModal').classList.remove('hidden');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
    @endpush
</x-layouts.admin>
