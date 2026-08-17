<x-layouts.admin :title="'Histori Penerima Beasiswa'">
    <x-page-header title="Histori Penerima Beasiswa" subtitle="Data penerima beasiswa">
        <x-slot:actions>
            <button onclick="document.getElementById('importModal').classList.remove('hidden')" class="btn btn-primary">
                <i data-lucide="upload" class="w-4 h-4"></i> Import Excel
            </button>
        </x-slot:actions>
    </x-page-header>

    <x-data-table :items="$histori" empty-icon="archive" empty-title="Belum Ada Data"
        empty-text="Belum ada data histori. Silakan Import Excel terlebih dahulu.">
        <x-slot:filter>
            <div class="flex items-center gap-2">
                <select name="tahun" onchange="this.form.submit()" class="form-select w-full sm:w-auto min-w-[150px]">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList as $y)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-outline" title="Filter Data">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                </button>
                @if(request('tahun'))
                    <a href="{{ route('super-admin.histori') }}" class="btn btn-outline" title="Reset Filter">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </x-slot:filter>

        <x-slot:header>
            <x-table-column label="Tahun" />
            <x-table-column label="NIK" />
            <x-table-column label="Nama Lengkap" />
            <x-table-column label="Asal PT" />
            <x-table-column label="Jenis Beasiswa" />
        </x-slot:header>

        @foreach($histori as $h)
            <tr class="hover:bg-slate-50 transition-colors group">
                <td class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-900">{{ $h->tahun }}</td>
                <td class="px-4 py-3 border-b border-slate-100 text-sm font-mono text-slate-500">{{ $h->nik ?? '-' }}</td>
                <td class="px-4 py-3 border-b border-slate-100 text-sm">{{ $h->nama_lengkap }}</td>
                <td class="px-4 py-3 border-b border-slate-100 text-sm text-slate-500">
                    {{ $h->asal_perguruan_tinggi ?? '-' }}
                </td>
                <td class="px-4 py-3 border-b border-slate-100"><x-badge
                        type="primary">{{ $h->jenis_beasiswa ?? 'BBP' }}</x-badge></td>
            </tr>
        @endforeach
    </x-data-table>

    {{-- Modal Import --}}
    <div id="importModal" class="modal-overlay hidden">
        <div class="modal-panel max-w-lg mx-4">
            <form action="{{ route('super-admin.histori.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h3 class="text-lg font-bold">Import Data Excel</h3>
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">File Excel (.xlsx, .xls) <span class="required">*</span></label>
                        <input type="file" name="file_excel" accept=".xlsx,.xls,.csv" required
                            class="form-input file:mr-4 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary-light file:text-primary-dark hover:file:bg-primary-light">
                        <div class="mt-3 p-3 bg-primary-light border border-primary-light rounded-lg text-xs text-primary-dark">
                            <strong>PENTING:</strong> Pastikan format kolom pada dile Excel persis secara berurutan dari
                            kiri ke kanan:
                            <br><code
                                class="mt-1 block font-mono">1. Tahun | 2. NIK | 3. Nama Lengkap | 4. Asal PT | 5. Jenis Beasiswa</code>
                            Baris pertama akan diabaikan (dianggap sebagai Header).
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-primary"><i data-lucide="upload" class="w-4 h-4"></i> Upload &
                        Import</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>