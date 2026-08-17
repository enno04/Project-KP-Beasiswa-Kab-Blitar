<x-layouts.admin :title="'Periode Beasiswa'">
    <x-page-header title="Periode Beasiswa" subtitle="Kelola master periode pendaftaran beasiswa.">
        <x-slot:actions>
            <button onclick="document.getElementById('modal-tambah-periode').classList.remove('hidden')" class="btn btn-primary">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Periode
            </button>
        </x-slot:actions>
    </x-page-header>

    <x-data-table :items="$periode" empty-icon="calendar" empty-title="Belum Ada Periode" empty-text="Belum ada periode yang ditambahkan.">
        <x-slot:header>
            <x-table-column label="Tahun" sortable="tahun" />
            <x-table-column label="Nama Periode" sortable="nama" />
            <x-table-column label="Jadwal" />
            <x-table-column label="Status" sortable="status" />
            <x-table-column label="Aksi" align="right" />
        </x-slot:header>

        @foreach($periode as $p)
            <tr class="hover:bg-slate-50 transition-colors group">
                <td class="px-4 py-3 border-b border-slate-100 font-extrabold text-lg text-slate-900">{{ $p->tahun }}</td>
                <td class="px-4 py-3 border-b border-slate-100 font-semibold">{{ $p->nama }}</td>
                <td class="px-4 py-3 border-b border-slate-100">
                    @if($p->tanggal_mulai && $p->tanggal_selesai)
                        <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                            <i data-lucide="play" class="w-3 h-3 text-green-500"></i>
                            {{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }}
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <i data-lucide="square" class="w-3 h-3 text-red-500"></i>
                            {{ \Carbon\Carbon::parse($p->tanggal_selesai)->format('d M Y') }}
                        </div>
                    @else
                        <span class="text-xs italic text-slate-400">Belum diatur</span>
                    @endif
                </td>
                <td class="px-4 py-3 border-b border-slate-100">
                    @if($p->status === 'aktif')
                        <x-badge type="success" dot>Aktif</x-badge>
                    @elseif($p->status === 'draft')
                        <x-badge type="muted">Draft</x-badge>
                    @else
                        <x-badge type="danger">Selesai</x-badge>
                    @endif
                </td>
                <td class="px-4 py-3 border-b border-slate-100 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <button type="button" onclick="openEditPeriode('{{ $p->id }}', '{{ $p->tahun }}', '{{ $p->nama }}', '{{ $p->tanggal_mulai ? \Carbon\Carbon::parse($p->tanggal_mulai)->format('Y-m-d') : '' }}', '{{ $p->tanggal_selesai ? \Carbon\Carbon::parse($p->tanggal_selesai)->format('Y-m-d') : '' }}', '{{ $p->status }}')" class="btn btn-xs btn-ghost text-amber-600 hover:bg-amber-50" title="Edit">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                        </button>
                        <form action="{{ route('super-admin.master.periode.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus periode ini?');">
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
    <div id="modal-tambah-periode" class="modal-overlay hidden">
        <div class="modal-panel max-w-md mx-4">
            <form action="{{ route('super-admin.master.periode.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h3 class="text-lg font-bold">Tambah Periode Baru</h3>
                    <button type="button" onclick="document.getElementById('modal-tambah-periode').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="modal-body space-y-4">
                    <x-form-input name="tahun" type="number" label="Tahun" :required="true" :value="date('Y')" />
                    <x-form-input name="nama" label="Nama Periode" :required="true" placeholder="Periode 2026 Gelombang 1" />
                    <div class="grid grid-cols-2 gap-4">
                        <x-form-input name="tanggal_mulai" type="date" label="Tanggal Mulai" />
                        <x-form-input name="tanggal_selesai" type="date" label="Tanggal Selesai" />
                    </div>
                    <div>
                        <label class="form-label">Status <span class="required">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="draft">Draft</option>
                            <option value="aktif">Aktif</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="document.getElementById('modal-tambah-periode').classList.add('hidden')" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-primary"><i data-lucide="save" class="w-4 h-4"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="modal-edit-periode" class="modal-overlay hidden">
        <div class="modal-panel max-w-md mx-4">
            <form id="form-edit-periode" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h3 class="text-lg font-bold">Edit Periode</h3>
                    <button type="button" onclick="document.getElementById('modal-edit-periode').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="modal-body space-y-4">
                    <div><label class="form-label">Tahun <span class="required">*</span></label><input type="number" id="edit_tahun" name="tahun" class="form-input" required></div>
                    <div><label class="form-label">Nama Periode <span class="required">*</span></label><input type="text" id="edit_nama" name="nama" class="form-input" required></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="form-label">Tanggal Mulai</label><input type="date" id="edit_tanggal_mulai" name="tanggal_mulai" class="form-input"></div>
                        <div><label class="form-label">Tanggal Selesai</label><input type="date" id="edit_tanggal_selesai" name="tanggal_selesai" class="form-input"></div>
                    </div>
                    <div>
                        <label class="form-label">Status <span class="required">*</span></label>
                        <select id="edit_status" name="status" class="form-select" required>
                            <option value="draft">Draft</option>
                            <option value="aktif">Aktif</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="document.getElementById('modal-edit-periode').classList.add('hidden')" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-warning"><i data-lucide="save" class="w-4 h-4"></i> Update</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openEditPeriode(id, tahun, nama, mulai, selesai, status) {
            document.getElementById('form-edit-periode').action = `/super-admin/master/periode/${id}`;
            document.getElementById('edit_tahun').value = tahun;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_tanggal_mulai').value = mulai;
            document.getElementById('edit_tanggal_selesai').value = selesai;
            document.getElementById('edit_status').value = status;
            document.getElementById('modal-edit-periode').classList.remove('hidden');
        }
    </script>
    @endpush
</x-layouts.admin>
