<x-layouts.admin :title="'Instansi OPD'">
    <x-page-header title="Instansi OPD" subtitle="Kelola instansi Organisasi Perangkat Daerah sebagai verifikator dokumen.">
        <x-slot:actions>
            <button onclick="document.getElementById('modal-tambah-opd').classList.remove('hidden')" class="btn btn-primary">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah OPD
            </button>
        </x-slot:actions>
    </x-page-header>

    <x-data-table :items="$opd" empty-icon="building-2" empty-title="Belum Ada OPD" empty-text="Belum ada instansi OPD terdaftar.">
        <x-slot:header>
            <x-table-column label="Nama OPD" sortable="nama_opd" />
            <x-table-column label="Singkatan" sortable="singkatan" />
            <x-table-column label="Aksi" align="right" />
        </x-slot:header>

        @foreach($opd as $o)
            <tr class="hover:bg-slate-50 transition-colors group">
                <td class="px-4 py-3 font-semibold text-slate-900 border-b border-slate-100">{{ $o->nama_opd }}</td>
                <td class="px-4 py-3 border-b border-slate-100"><x-badge type="muted">{{ $o->singkatan ?? '-' }}</x-badge></td>
                <td class="px-4 py-3 border-b border-slate-100 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <button type="button" onclick="openEditOpd('{{ $o->id }}', '{{ $o->nama_opd }}', '{{ $o->singkatan }}')" class="btn btn-xs btn-ghost text-amber-600 hover:bg-amber-50" title="Edit">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                        </button>
                        <form action="{{ route('super-admin.master.opd.destroy', $o->id) }}" method="POST" onsubmit="return confirm('Hapus OPD ini?');">
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
    <div id="modal-tambah-opd" class="modal-overlay hidden">
        <div class="modal-panel max-w-md mx-4">
            <form action="{{ route('super-admin.master.opd.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h3 class="text-lg font-bold">Tambah OPD Baru</h3>
                    <button type="button" onclick="document.getElementById('modal-tambah-opd').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="modal-body space-y-4">
                    <x-form-input name="nama_opd" label="Nama OPD" :required="true" placeholder="Dinas Pendidikan" />
                    <x-form-input name="singkatan" label="Singkatan" placeholder="Disdik" />
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="document.getElementById('modal-tambah-opd').classList.add('hidden')" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-primary"><i data-lucide="save" class="w-4 h-4"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="modal-edit-opd" class="modal-overlay hidden">
        <div class="modal-panel max-w-md mx-4">
            <form id="form-edit-opd" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h3 class="text-lg font-bold">Edit OPD</h3>
                    <button type="button" onclick="document.getElementById('modal-edit-opd').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="modal-body space-y-4">
                    <div><label class="form-label">Nama OPD <span class="required">*</span></label><input type="text" id="edit_nama_opd" name="nama_opd" class="form-input" required></div>
                    <div><label class="form-label">Singkatan</label><input type="text" id="edit_singkatan" name="singkatan" class="form-input"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="document.getElementById('modal-edit-opd').classList.add('hidden')" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-warning"><i data-lucide="save" class="w-4 h-4"></i> Update</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openEditOpd(id, nama_opd, singkatan) {
            document.getElementById('form-edit-opd').action = `/super-admin/master/opd/${id}`;
            document.getElementById('edit_nama_opd').value = nama_opd;
            document.getElementById('edit_singkatan').value = singkatan;
            document.getElementById('modal-edit-opd').classList.remove('hidden');
        }
    </script>
    @endpush
</x-layouts.admin>
