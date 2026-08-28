<x-layouts.admin :title="'Formulir - ' . $jalur->nama">
    <x-page-header :title="'Formulir: ' . $jalur->nama" subtitle="Kelola isian tambahan (custom fields) untuk formulir pendaftaran program ini.">
        <x-slot:actions>
            <a href="{{ route('super-admin.master.jalur.index', $jalur->program_id) }}" class="btn btn-outline mr-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="btn btn-primary">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Field
            </button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 p-4 rounded-xl flex gap-3 shadow-sm">
        <i data-lucide="info" class="w-5 h-5 text-blue-500 shrink-0 mt-0.5"></i>
        <div>
            <h4 class="font-bold text-blue-900 mb-1">Informasi Penggunaan Formulir Tambahan</h4>
            <p class="text-sm leading-relaxed">Fitur formulir ini ditujukan secara spesifik untuk mengumpulkan data administratif atau informasi tambahan dari peserta. Data yang diisikan pada formulir ini sifatnya informatif dan <strong>tidak akan memengaruhi perhitungan skor atau hasil perangkingan seleksi sistem</strong>. Untuk mengatur parameter penilaian yang berdampak pada perangkingan, silakan kelola pada menu <a href="{{ route('super-admin.master.kriteria.index', $jalur->id) }}" class="underline font-semibold hover:text-blue-700">Kriteria & Bobot</a>.</p>
        </div>
    </div>

    <x-data-table :items="$customFields" empty-title="Belum Ada Field Tambahan" empty-text="Belum ada isian tambahan yang dikonfigurasi untuk program beasiswa ini.">
        <x-slot:header>
            <x-table-column label="Nama Field" />
            <x-table-column label="Penempatan" />
            <x-table-column label="Tipe Input" />
            <x-table-column label="Wajib" align="center" />
            <x-table-column label="Status" align="center" />
            <x-table-column label="Urutan" align="center" />
            <x-table-column label="Aksi" align="center" />
        </x-slot:header>

        @foreach($customFields as $field)
            <tr class="hover:bg-slate-50 transition-colors group">
                <td class="px-4 py-3 font-semibold text-slate-900 border-b border-slate-100">
                    {{ $field->nama_field }}
                    @if($field->tipe_field === 'select' && $field->options)
                        <br><span class="text-xs font-normal text-slate-500">Pilihan: {{ implode(', ', $field->options) }}</span>
                    @endif
                </td>
                <td class="px-4 py-3 border-b border-slate-100">
                    @php
                        $penempatan = [
                            'identitas_diri' => ['label' => 'Identitas Diri', 'color' => 'blue'],
                            'orang_tua' => ['label' => 'Data Orang Tua', 'color' => 'indigo'],
                            'akademik' => ['label' => 'Data Akademik', 'color' => 'emerald'],
                            'tambahan' => ['label' => 'Informasi Tambahan', 'color' => 'amber'],
                        ][$field->penempatan] ?? ['label' => '-', 'color' => 'gray'];
                    @endphp
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-{{ $penempatan['color'] }}-100 text-{{ $penempatan['color'] }}-700">
                        {{ $penempatan['label'] }}
                    </span>
                </td>
                <td class="px-4 py-3 border-b border-slate-100 text-sm capitalize">{{ $field->tipe_field }}</td>
                <td class="px-4 py-3 border-b border-slate-100 text-center">
                    @if($field->is_required)
                        <x-badge type="success">Wajib</x-badge>
                    @else
                        <x-badge type="muted">Opsional</x-badge>
                    @endif
                </td>
                <td class="px-4 py-3 border-b border-slate-100 text-center">
                    @if($field->is_active)
                        <x-badge type="success">Aktif</x-badge>
                    @else
                        <x-badge type="danger">Tidak Aktif</x-badge>
                    @endif
                </td>
                <td class="px-4 py-3 border-b border-slate-100 text-center">{{ $field->urutan }}</td>
                <td class="px-4 py-3 border-b border-slate-100 text-center">
                    <div class="flex items-center justify-center gap-1">
                        <button onclick="editField({{ $field->id }}, '{{ addslashes($field->nama_field) }}', '{{ $field->penempatan }}', '{{ $field->tipe_field }}', '{{ $field->tipe_field === 'select' ? addslashes(implode(', ', $field->options ?? [])) : '' }}', {{ $field->is_required ? 'true' : 'false' }}, {{ $field->is_active ? 'true' : 'false' }}, {{ $field->urutan }})" class="btn btn-xs btn-ghost text-amber-600 hover:bg-amber-50" title="Edit">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                        </button>
                        <form method="POST" action="{{ route('super-admin.master.custom-fields.destroy', $field->id) }}" onsubmit="return confirm('Hapus field ini? Form pendaftar lama tidak akan kehilangan datanya (Soft Delete).')">
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
            <form id="formTambah" action="{{ route('super-admin.master.custom-fields.store', $jalur->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h3 class="text-lg font-bold">Tambah Field Baru</h3>
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="modal-body space-y-4">
                    <x-form-input name="nama_field" label="Nama Field" :required="true" placeholder="Contoh: Pekerjaan Sampingan Ayah" />
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Tipe Input <span class="required">*</span></label>
                            <select name="tipe_field" id="tipe_field_tambah" required class="form-select" onchange="toggleOptions('tambah')">
                                <option value="text">Teks Singkat (Text)</option>
                                <option value="textarea">Teks Panjang (Textarea)</option>
                                <option value="number">Angka (Number)</option>
                                <option value="rupiah">Uang / Rupiah (Rupiah)</option>
                                <option value="date">Tanggal (Date)</option>
                                <option value="select">Pilihan Ganda (Dropdown)</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Penempatan Form <span class="required">*</span></label>
                            <select name="penempatan" required class="form-select">
                                <option value="identitas_diri">Identitas Diri (Step 1)</option>
                                <option value="orang_tua">Data Orang Tua (Step 2)</option>
                                <option value="akademik">Data Akademik (Step 4)</option>
                                <option value="tambahan">Informasi Tambahan (Step Khusus)</option>
                            </select>
                        </div>
                    </div>

                    <div id="options_container_tambah" class="hidden">
                        <label class="form-label">Opsi Pilihan <span class="required">*</span></label>
                        <input type="text" name="options" id="options_tambah" class="form-input" placeholder="Opsi A, Opsi B, Opsi C (Pisahkan dengan koma)">
                        <p class="text-xs text-slate-500 mt-1">Gunakan tanda koma (,) untuk memisahkan antar opsi.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Sifat Isian</label>
                            <label class="flex items-center gap-2 mt-2">
                                <input type="checkbox" name="is_required" value="1" class="form-checkbox text-primary rounded" checked>
                                <span class="text-sm">Wajib Diisi</span>
                            </label>
                            <label class="flex items-center gap-2 mt-2">
                                <input type="checkbox" name="is_active" value="1" class="form-checkbox text-primary rounded" checked>
                                <span class="text-sm">Aktif Digunakan</span>
                            </label>
                        </div>
                        <div>
                            <label class="form-label">Urutan Tampil</label>
                            <input type="number" name="urutan" class="form-input" value="1" min="1">
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
        function toggleOptions(mode) {
            const tipe = document.getElementById('tipe_field_' + mode).value;
            const container = document.getElementById('options_container_' + mode);
            const input = document.getElementById('options_' + mode);
            
            if (tipe === 'select') {
                container.classList.remove('hidden');
                input.required = true;
            } else {
                container.classList.add('hidden');
                input.required = false;
                input.value = '';
            }
        }

        function editField(id, nama, penempatan, tipe, options, required, active, urutan) {
            const html = `<div id="modalEdit" class="modal-overlay">
                <div class="modal-panel max-w-lg mx-4">
                    <form method="POST" action="/super-admin/master/custom-fields/${id}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PUT">
                        <div class="modal-header">
                            <h3 class="text-lg font-bold">Edit Field</h3>
                            <button type="button" onclick="document.getElementById('modalEdit').remove()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                        </div>
                        <div class="modal-body space-y-4">
                            <div><label class="form-label">Nama Field <span class="required">*</span></label><input type="text" name="nama_field" value="${nama}" required class="form-input"></div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">Tipe Input <span class="required">*</span></label>
                                    <select name="tipe_field" id="tipe_field_edit" required class="form-select" onchange="toggleOptions('edit')">
                                        <option value="text" ${tipe === 'text' ? 'selected' : ''}>Teks Singkat</option>
                                        <option value="textarea" ${tipe === 'textarea' ? 'selected' : ''}>Teks Panjang</option>
                                        <option value="number" ${tipe === 'number' ? 'selected' : ''}>Angka</option>
                                        <option value="rupiah" ${tipe === 'rupiah' ? 'selected' : ''}>Uang / Rupiah</option>
                                        <option value="date" ${tipe === 'date' ? 'selected' : ''}>Tanggal</option>
                                        <option value="select" ${tipe === 'select' ? 'selected' : ''}>Pilihan Ganda</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label">Penempatan <span class="required">*</span></label>
                                    <select name="penempatan" required class="form-select">
                                        <option value="identitas_diri" ${penempatan === 'identitas_diri' ? 'selected' : ''}>Identitas Diri</option>
                                        <option value="orang_tua" ${penempatan === 'orang_tua' ? 'selected' : ''}>Data Orang Tua</option>
                                        <option value="akademik" ${penempatan === 'akademik' ? 'selected' : ''}>Data Akademik</option>
                                        <option value="tambahan" ${penempatan === 'tambahan' ? 'selected' : ''}>Informasi Tambahan</option>
                                    </select>
                                </div>
                            </div>
                            <div id="options_container_edit" class="${tipe === 'select' ? '' : 'hidden'}">
                                <label class="form-label">Opsi Pilihan <span class="required">*</span></label>
                                <input type="text" name="options" id="options_edit" class="form-input" value="${options}" placeholder="Opsi A, Opsi B" ${tipe === 'select' ? 'required' : ''}>
                                <p class="text-xs text-slate-500 mt-1">Gunakan tanda koma (,) untuk memisahkan.</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">Sifat Isian</label>
                                    <label class="flex items-center gap-2 mt-2">
                                        <input type="checkbox" name="is_required" value="1" class="form-checkbox text-primary rounded" ${required ? 'checked' : ''}>
                                        <span class="text-sm">Wajib Diisi</span>
                                    </label>
                                    <label class="flex items-center gap-2 mt-2">
                                        <input type="checkbox" name="is_active" value="1" class="form-checkbox text-primary rounded" ${active ? 'checked' : ''}>
                                        <span class="text-sm">Aktif Digunakan</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="form-label">Urutan</label>
                                    <input type="number" name="urutan" class="form-input" value="${urutan}" min="1">
                                </div>
                            </div>
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
