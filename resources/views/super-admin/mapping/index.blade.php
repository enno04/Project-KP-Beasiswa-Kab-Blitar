<x-admin-layout>
    <x-slot name="title">Mapping Kriteria</x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Mapping Kriteria SPK</h1>
            <p style="color: var(--color-text-secondary);">Pemetaan kriteria SPK ke field pada form pendaftaran untuk penilaian otomatis.</p>
        </div>
        <button onclick="document.getElementById('modal-tambah-mapping').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-white text-sm font-semibold transition-all hover:opacity-90" style="background-color: var(--color-primary);">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Mapping
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

    {{-- Table --}}
    <div class="rounded-2xl border bg-white overflow-hidden" style="border-color: var(--color-border); box-shadow: 0 2px 8px rgba(0,0,0,.05);">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead style="background-color: var(--color-bg); border-bottom: 1px solid var(--color-border);">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-gray-700">Kategori Beasiswa</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Kriteria SPK</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Field Form Pendaftaran</th>
                        <th class="px-6 py-4 font-semibold text-gray-700 text-right w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="divide-color: var(--color-border);">
                    @forelse($mapping as $m)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-800">
                                {{ $m->kriteria->kategori->nama ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-indigo-700">{{ $m->kriteria->nama_kriteria ?? '-' }}</div>
                                <div class="text-xs text-gray-500">Kode: {{ $m->kriteria->kode ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-gray-100 text-gray-700 border">
                                    {{ $m->field_pendaftaran }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" onclick="openEditMapping('{{ $m->id }}', '{{ $m->kriteria->nama_kriteria ?? '' }}', '{{ addslashes($m->field_pendaftaran) }}')" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition-colors" title="Edit Mapping">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <form action="{{ route('super-admin.master.mapping.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Yakin hapus mapping ini? Penilaian otomatis untuk field ini tidak akan berjalan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="Hapus Mapping">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="git-merge" class="w-10 h-10 mx-auto mb-3 opacity-50"></i>
                                Belum ada Mapping Kriteria yang dikonfigurasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($mapping->hasPages())
            <div class="px-6 py-4 border-t" style="border-color: var(--color-border);">
                {{ $mapping->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Tambah --}}
    <div id="modal-tambah-mapping" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-2xl w-full max-w-md mx-4 overflow-hidden">
            <form action="{{ route('super-admin.master.mapping.store') }}" method="POST">
                @csrf
                <div class="p-6 border-b flex items-center justify-between bg-white">
                    <h3 class="text-lg font-bold">Tambah Mapping Kriteria</h3>
                    <button type="button" onclick="document.getElementById('modal-tambah-mapping').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-gray-700">Pilih Kriteria <span class="text-red-500">*</span></label>
                        <select name="kriteria_id" class="w-full px-4 py-2 rounded-xl border focus:ring-2 outline-none border-gray-300 focus:ring-primary" required>
                            <option value="">-- Pilih Kriteria --</option>
                            @foreach($kriteriaList as $kr)
                                <option value="{{ $kr->id }}">[{{ $kr->kategori->nama ?? '?' }}] {{ $kr->nama_kriteria }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-gray-700">Nama Field Form (di Database) <span class="text-red-500">*</span></label>
                        <input type="text" name="field_pendaftaran" class="w-full px-4 py-2 rounded-xl border font-mono focus:ring-2 outline-none border-gray-300 focus:ring-primary" required placeholder="Misal: status_rumah">
                    </div>
                </div>
                <div class="p-6 border-t bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modal-tambah-mapping').classList.add('hidden')" class="px-4 py-2 rounded-xl text-gray-600 font-semibold hover:bg-gray-200 transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white font-semibold transition-all hover:opacity-90" style="background-color: var(--color-primary);">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="modal-edit-mapping" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-2xl w-full max-w-md mx-4 overflow-hidden">
            <form id="form-edit-mapping" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 border-b flex items-center justify-between bg-white">
                    <h3 class="text-lg font-bold">Edit Mapping Kriteria</h3>
                    <button type="button" onclick="document.getElementById('modal-edit-mapping').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-gray-700">Kriteria</label>
                        <input type="text" id="edit_nama_kriteria" class="w-full px-4 py-2 rounded-xl border bg-gray-100 text-gray-600" disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1 text-gray-700">Nama Field Form <span class="text-red-500">*</span></label>
                        <input type="text" id="edit_field_pendaftaran" name="field_pendaftaran" class="w-full px-4 py-2 rounded-xl border font-mono focus:ring-2 outline-none border-gray-300 focus:ring-primary" required>
                    </div>
                </div>
                <div class="p-6 border-t bg-gray-50 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modal-edit-mapping').classList.add('hidden')" class="px-4 py-2 rounded-xl text-gray-600 font-semibold hover:bg-gray-200 transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white font-semibold transition-all hover:opacity-90 bg-yellow-500">Update</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openEditMapping(id, kriteriaNama, field) {
            document.getElementById('form-edit-mapping').action = `/super-admin/master/mapping/${id}`;
            document.getElementById('edit_nama_kriteria').value = kriteriaNama;
            document.getElementById('edit_field_pendaftaran').value = field;
            document.getElementById('modal-edit-mapping').classList.remove('hidden');
        }
    </script>
    @endpush
</x-admin-layout>
