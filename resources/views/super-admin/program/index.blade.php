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
            <x-table-column label="Urutan" sortable="urutan" align="center" />
            <x-table-column label="Periode" sortable="periode_id" />
            <x-table-column label="Jalur" align="center" />
            <x-table-column label="Status" sortable="aktif" align="center" />
            <x-table-column label="Aksi" align="center" />
        </x-slot:header>

        @foreach($programs as $prog)
            <tr class="hover:bg-slate-50 transition-colors group">
                <td class="px-4 py-3 font-semibold text-slate-900 border-b border-slate-100">{{ $prog->nama }}</td>
                <td class="px-4 py-3 border-b border-slate-100"><code class="text-xs bg-slate-100 px-2 py-1 rounded font-mono">{{ $prog->kode }}</code></td>
                <td class="px-4 py-3 border-b border-slate-100 text-center font-bold text-slate-600">{{ $prog->urutan }}</td>
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
                        <button onclick="editProgram({{ $prog->id }}, '{{ $prog->nama }}', '{{ $prog->kode }}', '{{ $prog->deskripsi }}', '{{ $prog->tanggal_buka ? \Carbon\Carbon::parse($prog->tanggal_buka)->format('Y-m-d') : '' }}', '{{ $prog->tanggal_tutup ? \Carbon\Carbon::parse($prog->tanggal_tutup)->format('Y-m-d') : '' }}', {{ $prog->urutan }}, {{ $prog->aktif ? 'true' : 'false' }}, {{ $prog->kunci_hitung_nilai ? 'true' : 'false' }})" class="btn btn-xs btn-ghost text-amber-600 hover:bg-amber-50" title="Edit">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                        </button>
                        @if(strtolower($prog->kode) !== 'sdss')
                        <form method="POST" action="{{ route('super-admin.master.program.destroy', $prog->id) }}" onsubmit="return confirm('Hapus program ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-ghost text-red-500 hover:bg-red-50" title="Hapus">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                        @endif
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
                <div class="modal-body space-y-4 overflow-y-auto max-h-[70vh]">
                    <div>
                        <label class="form-label">Periode <span class="required">*</span></label>
                        <select name="periode_id" required class="form-select">
                            @foreach($periodeList as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-form-input name="nama" label="Nama Program" :required="true" placeholder="Contoh: Satu Desa Satu Sarjana (SDSS)" />
                    
                    <div class="mb-4">
                        <label class="form-label">Kode Program <span class="required">*</span></label>
                        <input type="text" name="kode" class="form-input" required placeholder="contoh: sdss" onkeyup="checkKodeSdss(this.value)">
                        <span class="text-xs text-slate-500 mt-1 block">Gunakan huruf kecil tanpa spasi. Kode <strong>sdss</strong> akan mengaktifkan fitur khusus.</span>
                    </div>

                    <div>
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" rows="2" class="form-input" placeholder="Tuliskan deskripsi singkat mengenai program beasiswa ini..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <x-form-input name="tanggal_buka" type="date" label="Tanggal Buka Pendaftaran" :required="true" />
                        <x-form-input name="tanggal_tutup" type="date" label="Tanggal Tutup Pendaftaran" :required="true" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <x-form-input name="urutan" type="number" label="Urutan" :required="true" placeholder="1" />
                        <div>
                            <label class="form-label">Status</label>
                            <select name="aktif" class="form-select">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <!-- Peringatan Dinamis SDSS -->
                    <div id="sdss-note-tambah" class="hidden p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                        <div class="flex items-start gap-2">
                            <i data-lucide="info" class="w-5 h-5 text-indigo-600 shrink-0 mt-0.5"></i>
                            <div>
                                <h4 class="font-semibold text-indigo-800 text-sm">Fitur Khusus SDSS Aktif</h4>
                                <p class="text-xs text-indigo-600 mt-1">Karena Anda menggunakan kode <strong>sdss</strong>, sistem akan secara otomatis mengaktifkan fitur <em>Kunci Penilaian Desa</em> untuk program ini setelah disimpan.</p>
                            </div>
                        </div>
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
        function editProgram(id, nama, kode, deskripsi, buka, tutup, urutan, aktif, kunci) {
            const html = `<div id="modalEdit" class="modal-overlay">
                <div class="modal-panel max-w-lg mx-4">
                    <form method="POST" action="/super-admin/master/program/${id}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <div class="modal-header">
                            <h3 class="text-lg font-bold">Edit Program</h3>
                            <button type="button" onclick="document.getElementById('modalEdit').remove()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                        </div>
                        <div class="modal-body space-y-4 overflow-y-auto max-h-[70vh]">
                            <div><label class="form-label">Nama <span class="required">*</span></label><input type="text" name="nama" value="${nama}" required class="form-input"></div>
                            <div><label class="form-label">Kode <span class="required">*</span></label><input type="text" name="kode" value="${kode}" required class="form-input"></div>
                            <div><label class="form-label">Deskripsi</label><textarea name="deskripsi" rows="2" class="form-input">${deskripsi || ''}</textarea></div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="form-label">Tanggal Buka <span class="required">*</span></label><input type="date" name="tanggal_buka" value="${buka || ''}" class="form-input" required></div>
                                <div><label class="form-label">Tanggal Tutup <span class="required">*</span></label><input type="date" name="tanggal_tutup" value="${tutup || ''}" class="form-input" required></div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div><label class="form-label">Urutan <span class="required">*</span></label><input type="number" name="urutan" value="${urutan || 1}" class="form-input" required min="1"></div>
                                <div><label class="form-label">Status</label><select name="aktif" class="form-select"><option value="1" ${aktif ? 'selected' : ''}>Aktif</option><option value="0" ${!aktif ? 'selected' : ''}>Nonaktif</option></select></div>
                            </div>
                            ${kode.toLowerCase() === 'sdss' ? `
                            <div>
                                <label class="flex items-center gap-2 cursor-pointer bg-slate-50 p-3 rounded-lg border border-slate-200 hover:bg-slate-100 transition-colors">
                                    <input type="checkbox" name="kunci_hitung_nilai" value="1" ${kunci ? 'checked' : ''} class="form-checkbox text-rose-500 rounded focus:ring-rose-500 w-5 h-5">
                                    <div>
                                        <span class="font-semibold text-slate-800 block">Kunci Penilaian Desa <span class="text-xs bg-rose-100 text-rose-700 px-2 py-0.5 rounded ml-1">Khusus SDSS</span></span>
                                        <span class="text-xs text-slate-500">Mencegah Admin Desa melakukan perhitungan nilai dan perankingan sebelum pendaftaran ditutup.</span>
                                    </div>
                                </label>
                            </div>
                            ` : ''}
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

        function checkKodeSdss(val) {
            const note = document.getElementById('sdss-note-tambah');
            if (note) {
                if (val.toLowerCase() === 'sdss') {
                    note.classList.remove('hidden');
                } else {
                    note.classList.add('hidden');
                }
            }
        }
    </script>
    @endpush
</x-layouts.admin>
