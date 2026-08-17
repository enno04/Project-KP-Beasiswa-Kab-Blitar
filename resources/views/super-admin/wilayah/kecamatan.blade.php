<x-layouts.admin :title="'Data Kecamatan'">
    <x-page-header title="Data Kecamatan" subtitle="Kelola data master kecamatan se-Kabupaten Blitar.">
        <x-slot:actions>
            <button onclick="document.getElementById('modal-tambah-kecamatan').classList.remove('hidden')" class="btn btn-primary">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kecamatan
            </button>
        </x-slot:actions>
    </x-page-header>

    <x-data-table :items="$kecamatan" empty-icon="map" empty-title="Belum Ada Data" empty-text="Belum ada data kecamatan.">
        <x-slot:header>
            <x-table-column label="Kode" sortable="kode_kecamatan" />
            <x-table-column label="Nama Kecamatan" sortable="nama_kecamatan" />
            <x-table-column label="Jumlah Desa" align="center" />
            <x-table-column label="Aksi" align="right" />
        </x-slot:header>

        @foreach($kecamatan as $kec)
            <tr class="hover:bg-slate-50 transition-colors group">
                <td class="px-4 py-3 border-b border-slate-100"><code class="text-xs bg-slate-100 px-2 py-1 rounded font-mono">{{ $kec->kode_kecamatan }}</code></td>
                <td class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-900">{{ $kec->nama_kecamatan }}</td>
                <td class="px-4 py-3 border-b border-slate-100 text-center">
                    <a href="{{ route('super-admin.master.desa.index', ['kecamatan_id' => $kec->id]) }}" class="badge badge-primary">
                        {{ $kec->desa_count ?? 0 }} Desa <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    </a>
                </td>
                <td class="px-4 py-3 border-b border-slate-100 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <button type="button" onclick="openEditKecamatan('{{ $kec->id }}', '{{ $kec->kode_kecamatan }}', '{{ $kec->nama_kecamatan }}')" class="btn btn-xs btn-ghost text-amber-600 hover:bg-amber-50" title="Edit">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                        </button>
                        <form action="{{ route('super-admin.master.kecamatan.destroy', $kec->id) }}" method="POST" onsubmit="return confirm('Hapus kecamatan ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-ghost text-red-500 hover:bg-red-50" title="Hapus">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-data-table>

    {{-- Modal Tambah --}}
    <div id="modal-tambah-kecamatan" class="modal-overlay hidden">
        <div class="modal-panel max-w-md mx-4">
            <form action="{{ route('super-admin.master.kecamatan.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h3 class="text-lg font-bold">Tambah Kecamatan</h3>
                    <button type="button" onclick="document.getElementById('modal-tambah-kecamatan').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="modal-body space-y-4">
                    <x-form-input name="kode_kecamatan" label="Kode Kecamatan" :required="true" placeholder="KEC-01" />
                    <x-form-input name="nama_kecamatan" label="Nama Kecamatan" :required="true" placeholder="Wlingi" />
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="document.getElementById('modal-tambah-kecamatan').classList.add('hidden')" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-primary"><i data-lucide="save" class="w-4 h-4"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="modal-edit-kecamatan" class="modal-overlay hidden">
        <div class="modal-panel max-w-md mx-4">
            <form id="form-edit-kecamatan" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h3 class="text-lg font-bold">Edit Kecamatan</h3>
                    <button type="button" onclick="document.getElementById('modal-edit-kecamatan').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="modal-body space-y-4">
                    <div><label class="form-label">Kode Kecamatan <span class="required">*</span></label><input type="text" id="edit_kode_kecamatan" name="kode_kecamatan" class="form-input" required></div>
                    <div><label class="form-label">Nama Kecamatan <span class="required">*</span></label><input type="text" id="edit_nama_kecamatan" name="nama_kecamatan" class="form-input" required></div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="document.getElementById('modal-edit-kecamatan').classList.add('hidden')" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-warning"><i data-lucide="save" class="w-4 h-4"></i> Update</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openEditKecamatan(id, kode, nama) {
            document.getElementById('form-edit-kecamatan').action = `/super-admin/master/kecamatan/${id}`;
            document.getElementById('edit_kode_kecamatan').value = kode;
            document.getElementById('edit_nama_kecamatan').value = nama;
            document.getElementById('modal-edit-kecamatan').classList.remove('hidden');
        }
    </script>
    @endpush
</x-layouts.admin>
