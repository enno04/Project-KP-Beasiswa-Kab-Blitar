<x-layouts.admin :title="'Kriteria & Bobot — ' . $jalur->nama">
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm mb-1" style="color: var(--color-text-secondary);">
                    <a href="{{ route('super-admin.master.program.index') }}" class="hover:underline">Program</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <a href="{{ route('super-admin.master.jalur.index', $jalur->program_id) }}" class="hover:underline">{{ $jalur->program->nama }}</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span>{{ $jalur->nama }}</span>
                </div>
                <h1 class="text-2xl font-bold">Kriteria & Bobot Penilaian</h1>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="document.getElementById('modal-tambah-kelompok').classList.remove('hidden')" class="px-4 py-2 rounded-xl border bg-white font-semibold flex items-center gap-2 hover:bg-gray-50 transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kelompok
                </button>
                <button onclick="document.getElementById('modal-tambah-kriteria').classList.remove('hidden')" class="px-4 py-2 rounded-xl text-white font-semibold flex items-center gap-2 transition-all hover:opacity-90" style="background-color: var(--color-primary);">
                    <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kriteria
                </button>
            </div>
        </div>

        {{-- Ringkasan Bobot (Model Penilaian Berbobot) --}}
        <div class="rounded-2xl border p-5" style="background-color: var(--color-surface); border-color: var(--color-border);">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(25,135,84,0.1); color: var(--color-success);">
                    <i data-lucide="pie-chart" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold">Model Penilaian Berbobot</h3>
                    <p class="text-sm" style="color: var(--color-text-secondary);">Total bobot seluruh kriteria harus tepat 100%. Bobot diatur per kriteria.</p>
                </div>
            </div>
            @php
                $grandTotalBobot = 0;
                $kelompokSummary = [];
                foreach($jalur->kelompokKriterias as $kelompok) {
                    $subTotal = $kelompok->kriterias->sum('bobot');
                    $grandTotalBobot += $subTotal;
                    $kelompokSummary[] = ['nama' => $kelompok->nama, 'bobot' => $subTotal, 'count' => $kelompok->kriterias->count()];
                }
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($kelompokSummary as $ks)
                    <div class="p-4 rounded-xl border bg-white" style="border-color: var(--color-border);">
                        <div class="text-sm font-semibold mb-1">{{ $ks['nama'] }}</div>
                        <div class="text-2xl font-bold" style="color: var(--color-primary);">{{ number_format($ks['bobot'], 0) }}%</div>
                        <div class="text-xs" style="color: var(--color-text-secondary);">{{ $ks['count'] }} kriteria</div>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between pt-4 border-t mt-4" style="border-color: var(--color-border);">
                <div class="font-bold text-sm">
                    Total Bobot: <span class="{{ abs($grandTotalBobot - 100) < 0.01 ? 'text-green-600' : 'text-red-600' }} text-lg">{{ number_format($grandTotalBobot, 0) }}%</span>
                    @if(abs($grandTotalBobot - 100) >= 0.01)
                        <span class="text-red-500 text-xs ml-2">⚠ Harus tepat 100% agar penilaian dapat dilakukan.</span>
                    @else
                        <span class="text-green-600 text-xs ml-2">✓ Siap digunakan untuk penilaian.</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kelompok & Kriteria --}}
        <div class="space-y-6">
            @forelse($jalur->kelompokKriterias()->orderBy('urutan')->get() as $kelompok)
            <div class="rounded-2xl border overflow-hidden" style="background-color: var(--color-surface); border-color: var(--color-border);">
                <div class="p-4 border-b flex items-center justify-between bg-gray-50" style="border-color: var(--color-border);">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-white border flex items-center justify-center font-bold text-sm">{{ $kelompok->urutan }}</span>
                        <div>
                            <h3 class="font-bold text-lg">{{ $kelompok->nama }}</h3>
                            <code class="text-xs bg-gray-200 px-2 py-0.5 rounded">{{ $kelompok->kode }}</code>
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <button type="button" onclick="openEditKelompok({{ json_encode($kelompok) }})" class="p-2 rounded-lg hover:bg-primary-light text-primary" title="Edit Kelompok">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                        </button>
                        <form action="{{ route('super-admin.master.kelompok.destroy', [$jalur->id, $kelompok->id]) }}" method="POST" onsubmit="return confirm('Hapus kelompok ini? Semua kriteria didalamnya akan terhapus.');">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2 rounded-lg hover:bg-red-50 text-red-600" title="Hapus Kelompok"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                        </form>
                    </div>
                </div>
                <div class="p-0">
                    <table class="w-full text-left text-sm border-collapse">
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($kelompok->kriterias()->orderBy('urutan')->get() as $kriteria)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-6 py-4 w-12 text-center text-slate-400 font-medium border-b border-slate-100">{{ $kriteria->urutan }}</td>
                                <td class="px-6 py-4 border-b border-slate-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="font-bold">{{ $kriteria->nama }} <code class="text-xs font-normal bg-gray-100 px-1.5 py-0.5 rounded text-gray-500 ml-1">{{ $kriteria->kode }}</code></div>
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-primary-light text-primary-dark">Bobot: {{ number_format($kriteria->bobot, 0) }}%</span>
                                            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-600">Max: {{ number_format($kriteria->nilai_max, 0) }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if($kriteria->tipe_input === 'pilihan')
                                                <button onclick="openTambahPilihan({{ $kriteria->id }}, '{{ $kriteria->nama }}')" class="text-xs px-2.5 py-1 rounded-lg border border-slate-200 font-semibold hover:bg-slate-100 flex items-center gap-1">
                                                    <i data-lucide="plus" class="w-3 h-3"></i> Opsi
                                                </button>
                                            @endif
                                            <button onclick="openEditKriteria({{ json_encode($kriteria) }})" class="text-primary hover:bg-primary-light p-1 rounded" title="Edit Kriteria">
                                                <i data-lucide="pencil" class="w-4 h-4"></i>
                                            </button>
                                            <form action="{{ route('super-admin.master.kriteria.destroy', [$jalur->id, $kriteria->id]) }}" method="POST" onsubmit="return confirm('Hapus kriteria ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:bg-red-50 p-1 rounded"><i data-lucide="x" class="w-4 h-4"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    @if($kriteria->tipe_input === 'angka')
                                        <div class="text-xs" style="color: var(--color-text-secondary);">
                                            Tipe: Angka Dinamis (Min: {{ $kriteria->nilai_min ?? 0 }}, Max: {{ $kriteria->nilai_max ?? '~' }})
                                        </div>
                                    @else
                                        <div class="space-y-1 mt-2">
                                            @forelse($kriteria->pilihans()->orderBy('urutan')->get() as $pilihan)
                                                <div class="flex items-center justify-between bg-white border border-slate-200 px-3 py-2 rounded-lg">
                                                    <span class="text-xs font-medium">{{ $pilihan->label }}</span>
                                                    <div class="flex items-center gap-3">
                                                        <span class="text-xs px-2 py-0.5 rounded-full bg-primary-light text-primary-dark font-bold">Skor: {{ $pilihan->skor }}</span>
                                                        <form action="{{ route('super-admin.master.pilihan.destroy', [$jalur->id, $pilihan->id]) }}" method="POST" onsubmit="return confirm('Hapus opsi ini?');">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="text-red-400 hover:text-red-600"><i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @empty
                                                <span class="text-xs italic text-red-500">Belum ada opsi pilihan (Dropdown kosong).</span>
                                            @endforelse
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="px-6 py-4 text-center text-xs italic text-slate-500">Belum ada kriteria di kelompok ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @empty
            <div class="rounded-2xl border p-12 text-center" style="background-color: var(--color-surface); border-color: var(--color-border);">
                <i data-lucide="layout-grid" class="w-12 h-12 mx-auto mb-3 opacity-30"></i>
                <p style="color: var(--color-text-secondary);">Belum ada kelompok kriteria (Misal: Prestasi Akademik, Kondisi Ekonomi, dll).<br>Silakan tambah kelompok kriteria terlebih dahulu.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Modal Tambah Kelompok --}}
    <div id="modal-tambah-kelompok" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
        <div class="rounded-2xl w-full max-w-md mx-4 overflow-hidden" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            <form action="{{ route('super-admin.master.kelompok.store', $jalur->id) }}" method="POST">
                @csrf
                <div class="p-6 border-b flex items-center justify-between sticky top-0" style="background-color: var(--color-surface); border-color: var(--color-border);">
                    <h3 class="text-lg font-bold">Tambah Kelompok</h3>
                    <button type="button" onclick="document.getElementById('modal-tambah-kelompok').classList.add('hidden')" class="hover:opacity-70"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Nama Kelompok</label>
                        <input type="text" name="nama" class="w-full px-4 py-2 rounded-xl border text-sm" placeholder="Contoh: Kondisi Ekonomi" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Kode</label>
                        <input type="text" name="kode" class="w-full px-4 py-2 rounded-xl border text-sm" placeholder="ekonomi" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Urutan Tampil</label>
                        <input type="number" name="urutan" value="{{ $jalur->kelompokKriterias->count() + 1 }}" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                    </div>
                </div>
                <div class="p-6 border-t flex justify-end gap-3" style="background-color: var(--color-bg); border-color: var(--color-border);">
                    <button type="button" onclick="document.getElementById('modal-tambah-kelompok').classList.add('hidden')" class="px-4 py-2 rounded-xl border font-semibold hover:bg-gray-100">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white font-semibold" style="background-color: var(--color-primary);">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Tambah Kriteria --}}
    <div id="modal-tambah-kriteria" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
        <div class="rounded-2xl w-full max-w-md mx-4 overflow-hidden" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            <form action="{{ route('super-admin.master.kriteria.store', $jalur->id) }}" method="POST">
                @csrf
                <div class="p-6 border-b flex items-center justify-between sticky top-0" style="background-color: var(--color-surface); border-color: var(--color-border);">
                    <h3 class="text-lg font-bold">Tambah Kriteria</h3>
                    <button type="button" onclick="document.getElementById('modal-tambah-kriteria').classList.add('hidden')" class="hover:opacity-70"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Pilih Kelompok</label>
                        <select name="kelompok_kriteria_id" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                            @foreach($jalur->kelompokKriterias as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Nama Kriteria</label>
                        <input type="text" name="nama" class="w-full px-4 py-2 rounded-xl border text-sm" placeholder="Pendapatan Ayah" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Kode / Key</label>
                        <input type="text" name="kode" class="w-full px-4 py-2 rounded-xl border text-sm" placeholder="pendapatan_ayah" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Tipe Input Pendaftar</label>
                        <select name="tipe_input" id="tipe_input_select" class="w-full px-4 py-2 rounded-xl border text-sm" required onchange="document.getElementById('angka_options').classList.toggle('hidden', this.value !== 'angka')">
                            <option value="pilihan">Pilihan Ganda (Dropdown)</option>
                            <option value="angka">Input Angka Dinamis (Misal: Rapor/IPK)</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4 pt-2 border-t mt-2">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Bobot (%) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="bobot" class="w-full px-4 py-2 rounded-xl border text-sm" placeholder="15" required>
                            <p class="text-xs text-gray-500 mt-1">Total semua kriteria harus 100%</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Nilai Maksimal <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="nilai_max" class="w-full px-4 py-2 rounded-xl border text-sm" placeholder="100" required>
                            <p class="text-xs text-gray-500 mt-1">Skor tertinggi yang bisa diperoleh</p>
                        </div>
                    </div>
                    <div id="angka_options" class="hidden grid grid-cols-2 gap-4 pt-2 border-t mt-2">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Nilai Min (Input)</label>
                            <input type="number" step="0.01" name="nilai_min" class="w-full px-4 py-2 rounded-xl border text-sm" placeholder="0">
                        </div>
                        <p class="text-xs text-gray-500">Batas bawah angka yang boleh dimasukkan pendaftar.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Urutan Tampil</label>
                        <input type="number" name="urutan" value="1" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                    </div>
                </div>
                <div class="p-6 border-t flex justify-end gap-3" style="background-color: var(--color-bg); border-color: var(--color-border);">
                    <button type="button" onclick="document.getElementById('modal-tambah-kriteria').classList.add('hidden')" class="px-4 py-2 rounded-xl border font-semibold hover:bg-gray-100">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white font-semibold" style="background-color: var(--color-primary);">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Tambah Pilihan --}}
    <div id="modal-tambah-pilihan" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
        <div class="rounded-2xl w-full max-w-md mx-4 overflow-hidden" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            <form action="{{ route('super-admin.master.pilihan.store', $jalur->id) }}" method="POST">
                @csrf
                <div class="p-6 border-b flex items-center justify-between sticky top-0" style="background-color: var(--color-surface); border-color: var(--color-border);">
                    <h3 class="text-lg font-bold">Tambah Opsi Pilihan</h3>
                    <button type="button" onclick="document.getElementById('modal-tambah-pilihan').classList.add('hidden')" class="hover:opacity-70"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="text-sm px-3 py-2 bg-primary-light text-primary-dark rounded-lg font-medium mb-2">Kriteria: <span id="nama_kriteria_label"></span></div>
                    <input type="hidden" name="kriteria_id" id="pilihan_kriteria_id">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Label / Teks Opsi</label>
                        <input type="text" name="label" class="w-full px-4 py-2 rounded-xl border text-sm" placeholder="< Rp 1.000.000" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Nilai Skor / Point (Skala 1-100)</label>
                        <input type="number" step="0.01" name="skor" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Urutan Tampil (Dropdown)</label>
                        <input type="number" name="urutan" value="1" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                    </div>
                </div>
                <div class="p-6 border-t flex justify-end gap-3" style="background-color: var(--color-bg); border-color: var(--color-border);">
                    <button type="button" onclick="document.getElementById('modal-tambah-pilihan').classList.add('hidden')" class="px-4 py-2 rounded-xl border font-semibold hover:bg-gray-100">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white font-semibold" style="background-color: var(--color-primary);">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Kriteria --}}
    <div id="modal-edit-kriteria" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
        <div class="rounded-2xl w-full max-w-md mx-4 overflow-hidden" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            <form id="form-edit-kriteria" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 border-b flex items-center justify-between sticky top-0" style="background-color: var(--color-surface); border-color: var(--color-border);">
                    <h3 class="text-lg font-bold">Edit Kriteria</h3>
                    <button type="button" onclick="document.getElementById('modal-edit-kriteria').classList.add('hidden')" class="hover:opacity-70"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Kelompok</label>
                        <select name="kelompok_kriteria_id" id="edit_kelompok" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                            @foreach($jalur->kelompokKriterias as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Nama Kriteria</label>
                        <input type="text" name="nama" id="edit_nama" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Kode</label>
                        <input type="text" name="kode" id="edit_kode" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Tipe Input</label>
                        <select name="tipe_input" id="edit_tipe_input" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                            <option value="pilihan">Pilihan Ganda (Dropdown)</option>
                            <option value="angka">Input Angka Dinamis</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Bobot (%)</label>
                            <input type="number" step="0.01" name="bobot" id="edit_bobot" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Nilai Maksimal</label>
                            <input type="number" step="0.01" name="nilai_max" id="edit_nilai_max" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1">Nilai Min (Input)</label>
                            <input type="number" step="0.01" name="nilai_min" id="edit_nilai_min" class="w-full px-4 py-2 rounded-xl border text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">Urutan Tampil</label>
                            <input type="number" name="urutan" id="edit_urutan" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t flex justify-end gap-3" style="background-color: var(--color-bg); border-color: var(--color-border);">
                    <button type="button" onclick="document.getElementById('modal-edit-kriteria').classList.add('hidden')" class="px-4 py-2 rounded-xl border font-semibold hover:bg-gray-100">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white font-semibold" style="background-color: var(--color-primary);">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Kelompok --}}
    <div id="modal-edit-kelompok" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
        <div class="rounded-2xl w-full max-w-md mx-4 overflow-hidden" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            <form id="form-edit-kelompok" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 border-b flex items-center justify-between sticky top-0" style="background-color: var(--color-surface); border-color: var(--color-border);">
                    <h3 class="text-lg font-bold">Edit Kelompok</h3>
                    <button type="button" onclick="document.getElementById('modal-edit-kelompok').classList.add('hidden')" class="hover:opacity-70"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1">Nama Kelompok</label>
                        <input type="text" name="nama" id="edit_kelompok_nama" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Kode</label>
                        <input type="text" name="kode" id="edit_kelompok_kode" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">Urutan Tampil</label>
                        <input type="number" name="urutan" id="edit_kelompok_urutan" class="w-full px-4 py-2 rounded-xl border text-sm" required>
                    </div>
                </div>
                <div class="p-6 border-t flex justify-end gap-3" style="background-color: var(--color-bg); border-color: var(--color-border);">
                    <button type="button" onclick="document.getElementById('modal-edit-kelompok').classList.add('hidden')" class="px-4 py-2 rounded-xl border font-semibold hover:bg-gray-100">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-xl text-white font-semibold" style="background-color: var(--color-primary);">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openEditKelompok(kelompok) {
            const jalurId = {{ $jalur->id }};
            document.getElementById('form-edit-kelompok').action = `/super-admin/master/jalur/${jalurId}/kelompok/${kelompok.id}`;
            document.getElementById('edit_kelompok_nama').value = kelompok.nama;
            document.getElementById('edit_kelompok_kode').value = kelompok.kode;
            document.getElementById('edit_kelompok_urutan').value = kelompok.urutan;
            document.getElementById('modal-edit-kelompok').classList.remove('hidden');
        }

        function openTambahPilihan(kriteriaId, namaKriteria) {
            document.getElementById('pilihan_kriteria_id').value = kriteriaId;
            document.getElementById('nama_kriteria_label').innerText = namaKriteria;
            document.getElementById('modal-tambah-pilihan').classList.remove('hidden');
        }

        function openEditKriteria(kriteria) {
            const jalurId = {{ $jalur->id }};
            document.getElementById('form-edit-kriteria').action = `/super-admin/master/jalur/${jalurId}/kriteria/${kriteria.id}`;
            document.getElementById('edit_kelompok').value = kriteria.kelompok_kriteria_id;
            document.getElementById('edit_nama').value = kriteria.nama;
            document.getElementById('edit_kode').value = kriteria.kode;
            document.getElementById('edit_tipe_input').value = kriteria.tipe_input;
            document.getElementById('edit_bobot').value = kriteria.bobot;
            document.getElementById('edit_nilai_max').value = kriteria.nilai_max;
            document.getElementById('edit_nilai_min').value = kriteria.nilai_min || '';
            document.getElementById('edit_urutan').value = kriteria.urutan;
            document.getElementById('modal-edit-kriteria').classList.remove('hidden');
        }
    </script>
    @endpush
</x-layouts.admin>
