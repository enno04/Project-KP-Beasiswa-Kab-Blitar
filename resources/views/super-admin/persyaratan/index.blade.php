<x-admin-layout>
    <x-slot name="title">Persyaratan Beasiswa</x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Persyaratan Dokumen</h1>
            <p style="color: var(--color-text-secondary);">Pemetaan dokumen wajib untuk setiap kategori beasiswa.</p>
        </div>
        <button onclick="document.getElementById('modal-tambah-syarat').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white text-sm font-semibold transition-all hover:opacity-90" style="background-color: var(--color-primary);">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Persyaratan
        </button>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Content --}}
    <div class="space-y-8">
        @forelse($persyaratan as $kategori_id => $syarats)
            <div class="rounded-2xl border bg-white overflow-hidden" style="border-color: var(--color-border); box-shadow: 0 2px 8px rgba(0,0,0,.05);">
                <div class="px-6 py-4 border-b flex items-center gap-3" style="background-color: var(--color-bg); border-color: var(--color-border);">
                    <i data-lucide="folder-check" class="w-5 h-5" style="color: var(--color-primary);"></i>
                    <h2 class="text-lg font-bold text-gray-800">
                        Kategori: {{ $syarats->first()->kategori->nama ?? 'Unknown' }}
                    </h2>
                </div>
                <div class="p-0">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 font-semibold text-gray-700 w-20 text-center sticky top-0 bg-gray-50 z-10 shadow-[0_1px_0_0_#e2e8f0]">Urutan</th>
                                <th class="px-6 py-3 font-semibold text-gray-700 sticky top-0 bg-gray-50 z-10 shadow-[0_1px_0_0_#e2e8f0]">Jenis Dokumen</th>
                                <th class="px-6 py-3 font-semibold text-gray-700 text-center sticky top-0 bg-gray-50 z-10 shadow-[0_1px_0_0_#e2e8f0]">Sifat</th>
                                <th class="px-6 py-3 font-semibold text-gray-700 text-right w-32 sticky top-0 bg-gray-50 z-10 shadow-[0_1px_0_0_#e2e8f0]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach($syarats as $s)
                                <tr class="hover:bg-slate-50 transition-colors group">
                                    <td class="px-6 py-3 text-center font-bold text-gray-500 border-b border-gray-100">
                                        {{ $s->urutan }}
                                    </td>
                                    <td class="px-6 py-3 border-b border-gray-100">
                                        <div class="font-bold text-gray-800">{{ $s->jenisDokumen->nama_dokumen }}</div>
                                        <div class="text-xs text-gray-500">{{ Str::limit($s->jenisDokumen->deskripsi, 60) }}</div>
                                    </td>
                                    <td class="px-6 py-3 text-center border-b border-gray-100">
                                        @if($s->wajib)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-red-100 text-red-700 border border-red-200">
                                                Wajib
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                                Opsional
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-right border-b border-gray-100">
                                        <div class="flex items-center justify-end gap-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                            <button type="button" onclick="openEditSyarat('{{ $s->id }}', '{{ $s->urutan }}', {{ $s->wajib ? 'true' : 'false' }}, '{{ $s->jenisDokumen->nama_dokumen }}')" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition-colors" title="Edit Syarat">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </button>
                                            <form action="{{ route('super-admin.master.persyaratan.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Yakin hapus persyaratan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="Hapus Syarat">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="text-center py-12 rounded-2xl border border-dashed border-gray-300 bg-gray-50">
                <i data-lucide="list-x" class="w-12 h-12 mx-auto mb-3 text-gray-400"></i>
                <p class="text-gray-500 font-medium">Belum ada persyaratan dokumen yang diatur.</p>
                <p class="text-sm text-gray-400 mt-1">Klik tombol Tambah Persyaratan untuk mulai memetakan dokumen.</p>
            </div>
        @endforelse
    </div>

    {{-- Modal Tambah --}}
    <div id="modal-tambah-syarat" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-2xl w-full max-w-md mx-4 overflow-hidden">
            <form action="{{ route('super-admin.master.persyaratan.store') }}" method="POST">
                @csrf
                <div class="p-6 border-b flex items-center justify-between bg-white">
                    <h3 class="text-lg font-bold">Tambah Persyaratan</h3>
                    <button type="button" onclick="document.getElementById('modal-tambah-syarat').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-gray-700">Kategori Beasiswa <span class="text-red-500">*</span></label>
                        <select name="kategori_id" class="w-full px-4 py-2 rounded-xl border focus:ring-2 outline-none border-gray-300 focus:ring-primary" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-gray-700">Jenis Dokumen <span class="text-red-500">*</span></label>
                        <select name="jenis_dokumen_id" class="w-full px-4 py-2 rounded-xl border focus:ring-2 outline-none border-gray-300 focus:ring-primary" required>
                            <option value="">-- Pilih Dokumen --</option>
                            @foreach($jenisDokumenList as $dok)
                                <option value="{{ $dok->id }}">{{ $dok->nama_dokumen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1 text-gray-700">Sifat <span class="text-red-500">*</span></label>
                            <select name="wajib" class="w-full px-4 py-2 rounded-xl border focus:ring-2 outline-none border-gray-300 focus:ring-primary" required>
                                <option value="1">Wajib</option>
                                <option value="0">Opsional</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1 text-gray-700">Urutan Tampil <span class="text-red-500">*</span></label>
                            <input type="number" name="urutan" value="1" class="w-full px-4 py-2 rounded-xl border focus:ring-2 outline-none border-gray-300 focus:ring-primary" required min="1">
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modal-tambah-syarat').classList.add('hidden')" class="px-4 py-2 rounded-xl text-gray-600 font-semibold hover:bg-gray-200 transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white font-semibold transition-all hover:opacity-90" style="background-color: var(--color-primary);">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="modal-edit-syarat" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-2xl w-full max-w-md mx-4 overflow-hidden">
            <form id="form-edit-syarat" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 border-b flex items-center justify-between bg-white">
                    <h3 class="text-lg font-bold">Edit Persyaratan</h3>
                    <button type="button" onclick="document.getElementById('modal-edit-syarat').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-gray-700">Dokumen</label>
                        <input type="text" id="edit_nama_dok" class="w-full px-4 py-2 rounded-xl border bg-gray-100 text-gray-600" disabled>
                        <p class="text-xs text-gray-400 mt-1">Dokumen tidak dapat diubah. Hapus dan buat baru jika salah.</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1 text-gray-700">Sifat <span class="text-red-500">*</span></label>
                            <select id="edit_wajib" name="wajib" class="w-full px-4 py-2 rounded-xl border focus:ring-2 outline-none border-gray-300 focus:ring-primary" required>
                                <option value="1">Wajib</option>
                                <option value="0">Opsional</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1 text-gray-700">Urutan Tampil <span class="text-red-500">*</span></label>
                            <input type="number" id="edit_urutan" name="urutan" class="w-full px-4 py-2 rounded-xl border focus:ring-2 outline-none border-gray-300 focus:ring-primary" required min="1">
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modal-edit-syarat').classList.add('hidden')" class="px-4 py-2 rounded-xl text-gray-600 font-semibold hover:bg-gray-200 transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white font-semibold transition-all hover:opacity-90 bg-yellow-500">Update</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openEditSyarat(id, urutan, wajib, namaDokumen) {
            document.getElementById('form-edit-syarat').action = `/super-admin/master/persyaratan/${id}`;
            document.getElementById('edit_urutan').value = urutan;
            document.getElementById('edit_wajib').value = wajib ? '1' : '0';
            document.getElementById('edit_nama_dok').value = namaDokumen;
            document.getElementById('modal-edit-syarat').classList.remove('hidden');
        }
    </script>
    @endpush
</x-admin-layout>
