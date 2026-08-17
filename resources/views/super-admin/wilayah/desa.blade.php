<x-layouts.admin :title="'Data Desa'">
    <x-page-header title="Data Desa / Kelurahan" subtitle="Kelola data master desa dan kelurahan se-Kabupaten Blitar.">
        <x-slot:actions>
            <button onclick="document.getElementById('modal-tambah-desa').classList.remove('hidden')" class="btn btn-primary">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Desa
            </button>
        </x-slot:actions>
    </x-page-header>

    <x-data-table :items="$desa" empty-icon="map-pin" empty-title="Belum Ada Data" empty-text="Belum ada data desa untuk kriteria ini.">
        <x-slot:filter>
            <div class="flex items-center gap-2">
                <select name="kecamatan_id" onchange="this.form.submit()" class="form-select w-full sm:w-auto min-w-[200px]">
                    <option value="">Semua Kecamatan</option>
                    @foreach($kecamatanList as $kec)
                        <option value="{{ $kec->id }}" {{ request('kecamatan_id') == $kec->id ? 'selected' : '' }}>{{ $kec->nama_kecamatan }}</option>
                    @endforeach
                </select>
                @if(request('kecamatan_id'))
                    <a href="{{ route('super-admin.master.desa.index') }}" class="btn btn-outline" title="Reset Filter">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </x-slot:filter>

        <x-slot:header>
            <x-table-column label="Kode" sortable="kode_desa" />
            <x-table-column label="Nama Desa/Kelurahan" sortable="nama_desa" />
            <x-table-column label="Kecamatan" sortable="kecamatan_id" />
            <x-table-column label="Aksi" align="right" />
        </x-slot:header>

        @foreach($desa as $d)
            <tr class="hover:bg-slate-50 transition-colors group">
                <td class="px-4 py-3 border-b border-slate-100"><code class="text-xs bg-slate-100 px-2 py-1 rounded font-mono">{{ $d->kode_desa }}</code></td>
                <td class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-900">{{ $d->nama_desa }}</td>
                <td class="px-4 py-3 border-b border-slate-100"><x-badge type="warning"><i data-lucide="map" class="w-3 h-3"></i> {{ $d->kecamatan->nama_kecamatan ?? '-' }}</x-badge></td>
                <td class="px-4 py-3 border-b border-slate-100 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <button type="button" onclick="openEditDesa('{{ $d->id }}', '{{ $d->kecamatan_id }}', '{{ $d->kode_desa }}', '{{ $d->nama_desa }}')" class="btn btn-xs btn-ghost text-amber-600 hover:bg-amber-50" title="Edit">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                        </button>
                        <form action="{{ route('super-admin.master.desa.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Hapus desa ini?');">
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
    <div id="modal-tambah-desa" class="modal-overlay hidden">
        <div class="modal-panel max-w-md mx-4">
            <form action="{{ route('super-admin.master.desa.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h3 class="text-lg font-bold">Tambah Desa Baru</h3>
                    <button type="button" onclick="document.getElementById('modal-tambah-desa').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">Kecamatan <span class="required">*</span></label>
                        <select name="kecamatan_id" class="form-select" required>
                            <option value="">— Pilih Kecamatan —</option>
                            @foreach($kecamatanList as $kec)
                                <option value="{{ $kec->id }}" {{ request('kecamatan_id') == $kec->id ? 'selected' : '' }}>{{ $kec->nama_kecamatan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-form-input name="kode_desa" label="Kode Desa" :required="true" placeholder="DESA-01" />
                    <x-form-input name="nama_desa" label="Nama Desa / Kelurahan" :required="true" placeholder="Tanggungan" />
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="document.getElementById('modal-tambah-desa').classList.add('hidden')" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-primary"><i data-lucide="save" class="w-4 h-4"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="modal-edit-desa" class="modal-overlay hidden">
        <div class="modal-panel max-w-md mx-4">
            <form id="form-edit-desa" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h3 class="text-lg font-bold">Edit Desa</h3>
                    <button type="button" onclick="document.getElementById('modal-edit-desa').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">Kecamatan <span class="required">*</span></label>
                        <select id="edit_kecamatan_id" name="kecamatan_id" class="form-select" required>
                            <option value="">— Pilih Kecamatan —</option>
                            @foreach($kecamatanList as $kec)
                                <option value="{{ $kec->id }}">{{ $kec->nama_kecamatan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label class="form-label">Kode Desa <span class="required">*</span></label><input type="text" id="edit_kode_desa" name="kode_desa" class="form-input" required></div>
                    <div><label class="form-label">Nama Desa <span class="required">*</span></label><input type="text" id="edit_nama_desa" name="nama_desa" class="form-input" required></div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="document.getElementById('modal-edit-desa').classList.add('hidden')" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-warning"><i data-lucide="save" class="w-4 h-4"></i> Update</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openEditDesa(id, kecamatan_id, kode, nama) {
            document.getElementById('form-edit-desa').action = `/super-admin/master/desa/${id}`;
            document.getElementById('edit_kecamatan_id').value = kecamatan_id;
            document.getElementById('edit_kode_desa').value = kode;
            document.getElementById('edit_nama_desa').value = nama;
            document.getElementById('modal-edit-desa').classList.remove('hidden');
        }
    </script>
    @endpush
</x-layouts.admin>
