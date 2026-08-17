<x-layouts.admin :title="'Program Beasiswa'">
    <x-page-header title="Program Beasiswa" subtitle="Kelola program beasiswa per periode.">
        <x-slot:actions>
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="btn btn-primary">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Program
            </button>
        </x-slot:actions>
    </x-page-header>

    <x-data-table :items="$programs" empty-title="Belum Ada Program" empty-text="Belum ada program beasiswa yang dikonfigurasi.">
        <x-slot:header>
            <x-table-column label="Nama Program" sortable="nama" />
            <x-table-column label="Kode" sortable="kode" />
            <x-table-column label="Periode" sortable="periode_id" />
            <x-table-column label="Jalur" align="center" />
            <x-table-column label="Status" sortable="aktif" align="center" />
            <x-table-column label="Aksi" align="center" />
        </x-slot:header>

        @foreach($programs as $prog)
            <tr class="hover:bg-slate-50 transition-colors group">
                <td class="px-4 py-3 font-semibold text-slate-900 border-b border-slate-100">{{ $prog->nama }}</td>
                <td class="px-4 py-3 border-b border-slate-100"><code class="text-xs bg-slate-100 px-2 py-1 rounded font-mono">{{ $prog->kode }}</code></td>
                <td class="px-4 py-3 border-b border-slate-100 text-sm">{{ $prog->periode->nama ?? '-' }}</td>
                <td class="px-4 py-3 border-b border-slate-100 text-center">
                    <a href="{{ route('super-admin.master.jalur.index', $prog->id) }}" class="badge badge-primary">
                        {{ $prog->jalurs->count() }} Jalur <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    </a>
                </td>
                <td class="px-4 py-3 border-b border-slate-100 text-center">
                    @if($prog->aktif)
                        <x-badge type="success" dot>Aktif</x-badge>
                    @else
                        <x-badge type="muted">Nonaktif</x-badge>
                    @endif
                </td>
                <td class="px-4 py-3 border-b border-slate-100 text-center">
                    <div class="flex items-center justify-center gap-1">
                        <a href="{{ route('super-admin.master.jalur.index', $prog->id) }}" class="btn btn-xs btn-ghost text-primary hover:bg-primary-light" title="Kelola Jalur">
                            <i data-lucide="git-branch" class="w-4 h-4"></i>
                        </a>
                        <button onclick="editProgram({{ $prog->id }}, '{{ $prog->nama }}', '{{ $prog->kode }}', '{{ $prog->deskripsi }}', '{{ $prog->tanggal_buka }}', '{{ $prog->tanggal_tutup }}', {{ $prog->aktif ? 'true' : 'false' }})" class="btn btn-xs btn-ghost text-amber-600 hover:bg-amber-50" title="Edit">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                        </button>
                        <form method="POST" action="{{ route('super-admin.master.program.destroy', $prog->id) }}" onsubmit="return confirm('Hapus program ini?')">
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
    <div id="modalTambah" class="modal-overlay hidden">
        <div class="modal-panel max-w-lg mx-4">
            <form method="POST" action="{{ route('super-admin.master.program.store') }}">
                @csrf
                <div class="modal-header">
                    <h3 class="text-lg font-bold">Tambah Program</h3>
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">Periode <span class="required">*</span></label>
                        <select name="periode_id" required class="form-select">
                            @foreach($periodeList as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-form-input name="nama" label="Nama Program" :required="true" placeholder="Satu Desa Satu Sarjana (SDSS)" />
                    <x-form-input name="kode" label="Kode" :required="true" placeholder="sdss" />
                    <div>
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" rows="2" class="form-input"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <x-form-input name="tanggal_buka" type="date" label="Tanggal Buka" />
                        <x-form-input name="tanggal_tutup" type="date" label="Tanggal Tutup" />
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="aktif" class="form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-primary"><i data-lucide="save" class="w-4 h-4"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function editProgram(id, nama, kode, deskripsi, buka, tutup, aktif) {
            const html = `<div id="modalEdit" class="modal-overlay">
                <div class="modal-panel max-w-lg mx-4">
                    <form method="POST" action="/super-admin/master/program/${id}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <div class="modal-header">
                            <h3 class="text-lg font-bold">Edit Program</h3>
                            <button type="button" onclick="document.getElementById('modalEdit').remove()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                        </div>
                        <div class="modal-body space-y-4">
                            <div><label class="form-label">Nama <span class="required">*</span></label><input type="text" name="nama" value="${nama}" required class="form-input"></div>
                            <div><label class="form-label">Kode <span class="required">*</span></label><input type="text" name="kode" value="${kode}" required class="form-input"></div>
                            <div><label class="form-label">Deskripsi</label><textarea name="deskripsi" rows="2" class="form-input">${deskripsi || ''}</textarea></div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="form-label">Tanggal Buka</label><input type="date" name="tanggal_buka" value="${buka || ''}" class="form-input"></div>
                                <div><label class="form-label">Tanggal Tutup</label><input type="date" name="tanggal_tutup" value="${tutup || ''}" class="form-input"></div>
                            </div>
                            <div><label class="form-label">Status</label><select name="aktif" class="form-select"><option value="1" ${aktif ? 'selected' : ''}>Aktif</option><option value="0" ${!aktif ? 'selected' : ''}>Nonaktif</option></select></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" onclick="document.getElementById('modalEdit').remove()" class="btn btn-outline">Batal</button>
                            <button type="submit" class="btn btn-warning"><i data-lucide="save" class="w-4 h-4"></i> Update</button>
                        </div>
                    </form>
                </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', html);
            lucide.createIcons();
        }
    </script>
    @endpush
</x-layouts.admin>
