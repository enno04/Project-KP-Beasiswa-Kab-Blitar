<x-layouts.admin :title="'Histori Penerima Beasiswa'">
    <x-page-header title="Histori Penerima Beasiswa" subtitle="Data penerima beasiswa tahun-tahun sebelumnya">
        <x-slot:actions>
            @if($tab === 'lama')
                <button onclick="document.getElementById('importModalLama').classList.remove('hidden')" class="btn btn-outline">
                    <i data-lucide="upload" class="w-4 h-4"></i> Import Data Lama
                </button>
            @else
                <button onclick="document.getElementById('importModalBaru').classList.remove('hidden')" class="btn btn-primary">
                    <i data-lucide="upload-cloud" class="w-4 h-4"></i> Import (Admin Kab)
                </button>
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 border-b border-slate-200">
        <nav class="flex gap-4" aria-label="Tabs">
            <a href="{{ route('super-admin.histori', ['tab' => 'lama']) }}"
                class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors {{ $tab === 'lama' ? 'border-primary-main text-primary-main' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                <i data-lucide="history" class="w-4 h-4 inline-block mr-1"></i> Data Penerima Lama
            </a>
            <a href="{{ route('super-admin.histori', ['tab' => 'baru']) }}"
                class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors {{ $tab === 'baru' ? 'border-primary-main text-primary-main' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                <i data-lucide="folder-clock" class="w-4 h-4 inline-block mr-1"></i> Data Baru (Sistem Admin Kab)
            </a>
        </nav>
    </div>

    @if($tab === 'baru')
    {{-- Info Card Import Data Baru --}}
    <div class="mb-6 p-4 rounded-xl border shadow-sm" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-color: #bfdbfe;">
        <div class="flex gap-3">
            <div class="shrink-0 mt-0.5">
                <i data-lucide="info" class="w-5 h-5 text-blue-600"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-blue-900 mb-1">Informasi Import Data Penetapan</h4>
                <p class="text-sm text-blue-800 leading-relaxed">
                    Untuk melakukan pemasukan data penerima beasiswa baru, gunakan <em>file</em> Excel hasil dari <strong>Export Data Sekaligus</strong> yang diunduh melalui halaman <strong>Riwayat Penetapan Beasiswa</strong> pada akun <strong>Admin Kabupaten</strong>. Sistem akan membaca format <em>file</em> tersebut secara otomatis.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Modern Stat Card for Total History --}}
    <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex items-center justify-between relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-primary-light/30 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
            <div>
                <p class="text-sm font-medium text-slate-500 mb-1">Total Data Histori</p>
                <h3 class="text-3xl font-bold text-slate-800">{{ number_format($histori->total(), 0, ',', '.') }} <span class="text-sm font-normal text-slate-500">Penerima</span></h3>
            </div>
            <div class="w-12 h-12 rounded-lg bg-primary-light text-primary flex items-center justify-center z-10 shadow-inner">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <x-data-table :items="$histori" :search="false" empty-icon="archive" empty-title="Belum Ada Data"
        empty-text="Belum ada data histori. Silakan Import Excel terlebih dahulu.">
        <x-slot:filter>
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <input type="hidden" name="tab" value="{{ $tab }}">
                
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / NIK / PT..." 
                        class="form-input w-full sm:w-auto sm:min-w-[200px]" onkeypress="if(event.key === 'Enter') this.form.submit()">

                <select name="jenis_beasiswa" onchange="this.form.submit()" class="form-select w-full sm:w-auto sm:min-w-[150px]">
                    <option value="">Semua Beasiswa</option>
                    @foreach($programList as $p)
                        <option value="{{ $p }}" {{ request('jenis_beasiswa') == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>

                <select name="tahun" onchange="this.form.submit()" class="form-select w-full sm:w-auto sm:min-w-[130px]">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList as $y)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>

                <select name="sort_added" onchange="this.form.submit()" class="form-select w-full sm:w-auto sm:min-w-[150px]">
                    <option value="">Urutkan (Default)</option>
                    <option value="terbaru" {{ request('sort_added') == 'terbaru' ? 'selected' : '' }}>Baru Di-import</option>
                    <option value="terlama" {{ request('sort_added') == 'terlama' ? 'selected' : '' }}>Terlama Di-import</option>
                </select>

                <select name="per_page" onchange="this.form.submit()" class="form-select w-full sm:w-auto">
                    <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 baris</option>
                    <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ request('per_page', 50) == '50' ? 'selected' : '' }}>50 baris</option>
                    <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100 baris</option>
                </select>

                <button type="submit" class="btn btn-outline w-full sm:w-auto" title="Cari Data">
                    <i data-lucide="search" class="w-4 h-4 mr-1"></i> Cari
                </button>
                @if(request('tahun') || request('jenis_beasiswa') || request('search'))
                    <a href="{{ route('super-admin.histori', ['tab' => $tab]) }}" class="btn btn-outline text-red-500 hover:bg-red-50 hover:border-red-200 w-full sm:w-auto" title="Reset Pencarian">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </x-slot:filter>

        <x-slot:header>
            <x-table-column label="No" class="w-16 text-center" />
            <x-table-column label="Tahun" />
            <x-table-column label="NIK" />
            <x-table-column label="Nama Lengkap" />
            @if($tab === 'baru')
                <x-table-column label="No. Pendaftaran" />
                <x-table-column label="Kecamatan" />
                <x-table-column label="Desa" />
                <x-table-column label="Nilai" />
            @endif
            <x-table-column label="Asal PT" />
            <x-table-column label="Jenis Beasiswa" />
            @if($tab === 'baru')
                <x-table-column label="Jalur" />
                <x-table-column label="Ditetapkan" />
            @endif
        </x-slot:header>

        @foreach($histori as $h)
            <tr class="hover:bg-slate-50 transition-colors group whitespace-nowrap">
                <td class="px-4 py-3 border-b border-slate-100 text-sm text-slate-500 text-center">{{ $histori->firstItem() + $loop->index }}</td>
                <td class="px-4 py-3 border-b border-slate-100 font-semibold text-slate-900">{{ $h->tahun }}</td>
                <td class="px-4 py-3 border-b border-slate-100 text-sm font-mono text-slate-500">{{ $h->nik ?? '-' }}</td>
                <td class="px-4 py-3 border-b border-slate-100 text-sm">{{ $h->nama_lengkap }}</td>
                @if($tab === 'baru')
                    <td class="px-4 py-3 border-b border-slate-100 text-sm font-mono text-slate-500">{{ $h->nomor_pendaftaran ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-slate-100 text-sm text-slate-500">{{ $h->kecamatan ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-slate-100 text-sm text-slate-500">{{ $h->desa ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-slate-100 text-sm font-semibold text-amber-600">{{ $h->ipk_nilai ?? '-' }}</td>
                @endif
                <td class="px-4 py-3 border-b border-slate-100 text-sm text-slate-500 max-w-[250px] truncate" title="{{ $h->asal_perguruan_tinggi }}">
                    {{ $h->asal_perguruan_tinggi ?? '-' }}
                </td>
                <td class="px-4 py-3 border-b border-slate-100"><x-badge
                        type="primary">{{ $h->jenis_beasiswa ?? 'BBP' }}</x-badge></td>
                @if($tab === 'baru')
                    <td class="px-4 py-3 border-b border-slate-100 text-sm text-slate-500">{{ $h->jalur_beasiswa ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-slate-100 text-xs text-slate-500">
                        {{ $h->waktu_penetapan ? (is_numeric($h->waktu_penetapan) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($h->waktu_penetapan)->format('d M Y') : (strtotime($h->waktu_penetapan) ? \Carbon\Carbon::parse($h->waktu_penetapan)->format('d M Y') : $h->waktu_penetapan)) : '-' }}
                    </td>
                @endif
            </tr>
        @endforeach
    </x-data-table>

    @if($tab === 'lama')
    {{-- Modal Import LAMA --}}
    <div id="importModalLama" class="modal-overlay hidden">
        <div class="modal-panel max-w-lg mx-4">
            <form action="{{ route('super-admin.histori.import.lama') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h3 class="text-lg font-bold">Import Excel (Format Lama)</h3>
                    <button type="button" onclick="document.getElementById('importModalLama').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">File Excel (.xlsx, .xls) <span class="required">*</span></label>
                        <input type="file" name="file_excel" accept=".xlsx,.xls,.csv" required
                            class="form-input file:mr-4 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary-light file:text-primary-dark hover:file:bg-primary-light">
                        <div class="mt-3 p-3 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-600">
                            Digunakan untuk mengimpor data lawas dari Dinas. Format kolom:
                            <br><code
                                class="mt-1 block font-mono">1. Tahun | 2. NIK | 3. Nama Lengkap | 4. Asal PT | 5. Jenis Beasiswa</code>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="document.getElementById('importModalLama').classList.add('hidden')"
                        class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-primary"><i data-lucide="upload" class="w-4 h-4"></i> Upload</button>
                </div>
            </form>
        </div>
    </div>
    @else
    {{-- Modal Import BARU --}}
    <div id="importModalBaru" class="modal-overlay hidden">
        <div class="modal-panel max-w-lg mx-4">
            <form action="{{ route('super-admin.histori.import.baru') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h3 class="text-lg font-bold">Import Excel (Format Admin Kab)</h3>
                    <button type="button" onclick="document.getElementById('importModalBaru').classList.add('hidden')"
                        class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="modal-body space-y-4">
                    <div>
                        <label class="form-label">File Excel Hasil Export Admin Kab <span class="required">*</span></label>
                        <input type="file" name="file_excel" accept=".xlsx,.xls,.csv" required
                            class="form-input file:mr-4 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary-light file:text-primary-dark hover:file:bg-primary-light">
                        <div class="mt-3 p-3 bg-primary-light border border-primary-light/50 rounded-lg text-xs text-primary-dark">
                            <strong>PENTING:</strong> Gunakan file Excel asli hasil unduhan dari fitur <strong>Riwayat Penetapan</strong> di panel Admin Kabupaten. Sistem akan mendeteksi tahun, nama, dan jalur beasiswa secara otomatis.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="document.getElementById('importModalBaru').classList.add('hidden')"
                        class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-primary"><i data-lucide="upload-cloud" class="w-4 h-4"></i> Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</x-layouts.admin>
