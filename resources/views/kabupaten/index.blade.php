<x-layouts.admin :title="'Data Pendaftar — ' . $program->nama">
    <x-page-header :title="'Data Pendaftar: ' . $program->nama" :subtitle="$jalur ? 'Jalur: ' . $jalur->nama : 'Semua jalur'">
        <x-slot:actions>
            <div class="flex items-center gap-2.5 px-4 py-2 rounded-xl border shadow-sm"
                 style="background: linear-gradient(135deg, #f0f7ff 0%, #e8f4fd 100%); border-color: #bfdbfe;">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg"
                     style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                    <i data-lucide="users" class="w-4 h-4 text-white"></i>
                </div>
                <div class="leading-tight">
                    <p class="text-xs font-medium text-blue-500 uppercase tracking-wide">Total Pendaftar</p>
                    <p class="text-lg font-bold text-blue-900">{{ number_format($pendaftar->total(), 0, ',', '.') }}</p>
                </div>
            </div>
        </x-slot:actions>
    </x-page-header>

    {{-- Filter Kompleks --}}
    <div class="card mb-6 overflow-hidden">
        <div class="bg-slate-50/50 border-b border-slate-100 px-5 py-3">
            <h3 class="text-sm font-semibold text-slate-700 flex items-center gap-2">
                <i data-lucide="filter" class="w-4 h-4 text-slate-400"></i> Filter Data
            </h3>
        </div>
        <div class="card-body p-5">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                {{-- Pencarian --}}
                <div class="w-full sm:w-64">
                    <label class="form-label text-xs font-semibold text-slate-500 mb-1.5 block">Pencarian</label>
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none z-10"></i>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full" style="padding-left: 2.5rem;" placeholder="Cari Nama / NIK..." onblur="this.form.submit()">
                    </div>
                </div>

                {{-- Periode --}}
                <div class="w-full sm:w-48">
                    <label class="form-label text-xs font-semibold text-slate-500 mb-1.5 block">Periode</label>
                    <select name="periode_id" class="form-select w-full" onchange="this.form.submit()">
                        @foreach($periodeList as $p)
                            <option value="{{ $p->id }}" {{ ($periodeId ?? '') == $p->id ? 'selected' : '' }}>{{ $p->nama }} ({{ $p->tahun }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Kecamatan --}}
                <div class="w-full sm:w-48">
                    <label class="form-label text-xs font-semibold text-slate-500 mb-1.5 block">Kecamatan</label>
                    <select name="kecamatan_id" class="form-select w-full" onchange="this.form.submit()">
                        <option value="">Semua Kecamatan</option>
                        @foreach($kecamatanList as $kec)
                            <option value="{{ $kec->id }}" {{ request('kecamatan_id') == $kec->id ? 'selected' : '' }}>{{ $kec->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Desa (Hanya muncul jika Kecamatan dipilih) --}}
                @if(request()->filled('kecamatan_id'))
                <div class="w-full sm:w-48 animate-in fade-in slide-in-from-left-4 duration-300">
                    <label class="form-label text-xs font-semibold text-slate-500 mb-1.5 block">Desa / Kelurahan</label>
                    <select name="desa_id" class="form-select w-full" onchange="this.form.submit()">
                        <option value="">Semua Desa</option>
                        @foreach($desaList as $desa)
                            <option value="{{ $desa->id }}" {{ request('desa_id') == $desa->id ? 'selected' : '' }}>{{ $desa->nama_desa }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Status --}}
                <div class="w-full sm:w-48">
                    <label class="form-label text-xs font-semibold text-slate-500 mb-1.5 block">Status</label>
                    <select name="status" class="form-select w-full" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="lolos_verifikasi" {{ request('status') === 'lolos_verifikasi' ? 'selected' : '' }}>Lolos Verifikasi</option>
                        <option value="proses_penilaian" {{ request('status') === 'proses_penilaian' ? 'selected' : '' }}>Proses Penilaian</option>
                        
                        @if(isset($isBerdayaBerjaya) && $isBerdayaBerjaya)
                            <option value="menunggu_wawancara" {{ request('status') === 'menunggu_wawancara' ? 'selected' : '' }}>Menunggu Wawancara</option>
                            <option value="sudah_wawancara" {{ request('status') === 'sudah_wawancara' ? 'selected' : '' }}>Tahap Wawancara: Lolos</option>
                            <option value="gugur_wawancara" {{ request('status') === 'gugur_wawancara' ? 'selected' : '' }}>Tahap Wawancara: Tidak Lolos / Gugur</option>
                        @endif

                        <option value="menunggu_penetapan" {{ request('status') === 'menunggu_penetapan' ? 'selected' : '' }}>Menunggu Penetapan</option>
                        <option value="lulus" {{ request('status') === 'lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="tidak_lulus" {{ request('status') === 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                    </select>
                </div>
                
                {{-- Pagination Limit --}}
                <div class="w-full sm:w-24">
                    <label class="form-label text-xs font-semibold text-slate-500 mb-1.5 block">Tampil</label>
                    <select name="per_page" class="form-select w-full" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                @if(request()->hasAny(['search', 'kecamatan_id', 'desa_id', 'status']) || request('per_page') != 50)
                    <div class="pb-0.5">
                        <a href="{{ url()->current() }}?periode_id={{ $periodeId }}" class="btn btn-outline border-slate-200 text-slate-600 hover:bg-slate-100 h-[42px]">
                            <i data-lucide="x" class="w-4 h-4"></i> Reset
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Tombol Hitung Penilaian & Generate Ranking (non-SDSS) --}}
    @if(!$isSdss && $jalur && $periodeId)
        <div class="card mb-6">
            <div class="card-body py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-slate-600">
                    <i data-lucide="info" class="w-4 h-4 inline -mt-0.5 text-primary"></i>
                    Klik tombol untuk menghitung penilaian berbobot dan menghasilkan peringkat otomatis untuk semua pendaftar yang lolos verifikasi.
                </div>
                <form id="form-generate-ranking" action="{{ route('kabupaten.penilaian.ranking') }}" method="POST">
                    @csrf
                    <input type="hidden" name="jalur_id" value="{{ $jalur->id }}">
                    <input type="hidden" name="periode_id" value="{{ $periodeId }}">
                    <button type="button" onclick="confirmGenerateRanking()" class="btn btn-primary whitespace-nowrap shadow-md">
                        <i data-lucide="calculator" class="w-4 h-4"></i> Hitung Penilaian & Generate Ranking
                    </button>
                </form>
            </div>
        </div>
    @endif

    @if(isset($adaBelumDinilai) && $adaBelumDinilai && !$isSdss)
        <div class="bg-orange-50 border border-orange-200 text-orange-800 px-4 py-3 rounded-xl mb-6 flex items-start gap-3 shadow-sm">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-orange-500 shrink-0 mt-0.5"></i>
            <div>
                <strong class="font-bold block">Peringatan: Ada Pendaftar Baru!</strong>
                <span class="text-sm">Terdapat pendaftar baru yang masuk dan belum memiliki skor/peringkat. Anda diwajibkan mengklik tombol <b>Hitung Penilaian & Generate Ranking</b> di atas agar data mereka ikut terhitung sebelum Anda bisa memproses Wawancara atau Penetapan.</span>
            </div>
        </div>
    @endif

    {{-- Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="text-center w-16">Rank</th>
                        <th>No. Pendaftaran</th>
                        <th>Nama Lengkap</th>
                        <th>Wilayah</th>
                        <th class="text-right">Skor Akhir</th>
                        @if(isset($isBerdayaBerjaya) && $isBerdayaBerjaya)
                            <th class="text-right">Nilai Wawancara</th>
                        @endif
                        <th class="text-center">Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftar as $p)
                        <tr class="{{ $p->status === 'lulus' ? 'bg-green-50/30' : ($p->status === 'tidak_lulus' ? 'bg-red-50/30' : '') }}">
                            <td class="text-center">
                                @if($p->ranking == 1)
                                    <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 font-bold mx-auto border border-yellow-200 shadow-sm">1</div>
                                @elseif($p->ranking == 2)
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold mx-auto border border-gray-300 shadow-sm">2</div>
                                @elseif($p->ranking == 3)
                                    <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-700 font-bold mx-auto border border-orange-200 shadow-sm">3</div>
                                @elseif($p->ranking)
                                    <span class="font-bold text-slate-500">{{ $p->ranking }}</span>
                                @else
                                    <span class="text-xs text-slate-300">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="font-bold text-primary-dark">{{ $p->nomor_pendaftaran }}</span>
                            </td>
                            <td>
                                <p class="font-semibold text-slate-900">{{ $p->identitas->nama_lengkap ?? '-' }}</p>
                                <p class="text-xs text-slate-400">NIK: {{ $p->identitas->nik ?? '-' }}</p>
                            </td>
                            <td>
                                <p class="text-sm">{{ $p->identitas->desa->nama_desa ?? '-' }}</p>
                                <p class="text-xs text-slate-400">Kec. {{ $p->identitas->kecamatan->nama_kecamatan ?? '-' }}</p>
                            </td>
                            <td class="text-right">
                                @if($p->total_nilai !== null)
                                    <span class="font-bold text-lg text-primary">{{ number_format($p->total_nilai, 4) }}</span>
                                @else
                                    <span class="text-xs italic text-slate-400">Belum dinilai</span>
                                @endif
                            </td>
                            @if(isset($isBerdayaBerjaya) && $isBerdayaBerjaya)
                                <td class="text-right whitespace-nowrap">
                                    @if($p->ranking === null)
                                        <div class="flex flex-col items-end gap-0.5">
                                            <button disabled class="btn btn-xs bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed w-full">
                                                Hasil Wawancara
                                            </button>
                                            <span class="text-[10px] text-orange-500 italic font-medium mt-0.5">Generate nilai total dahulu</span>
                                        </div>
                                    @elseif(isset($adaBelumDinilai) && $adaBelumDinilai)
                                        <div class="flex flex-col items-end gap-0.5">
                                            <button disabled class="btn btn-xs bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed w-full">
                                                Hasil Wawancara
                                            </button>
                                        </div>
                                    @elseif($p->status === 'gugur_wawancara')
                                        <span class="px-2 py-1 bg-red-50 text-red-600 rounded text-xs font-semibold">Tidak Lolos</span>
                                        <button type="button" onclick="openWawancaraModal({{ $p->id }})" class="ml-1 text-xs text-primary hover:underline">Ubah Hasil</button>
                                    @elseif($p->nilai_wawancara !== null && $p->nilai_wawancara == 100)
                                        <span class="px-2 py-1 bg-green-50 text-green-700 rounded text-xs font-semibold">Lolos Wawancara</span>
                                        <button type="button" onclick="openWawancaraModal({{ $p->id }})" class="ml-1 text-xs text-primary hover:underline">Ubah Hasil</button>
                                    @else
                                        <button type="button" onclick="openWawancaraModal({{ $p->id }})" class="btn btn-xs bg-blue-100 text-blue-700 border-blue-200 hover:bg-blue-200">
                                            Hasil Wawancara
                                        </button>
                                    @endif
                                </td>
                            @endif
                            <td class="text-center">
                                <span class="badge {{ $p->status_color }}">{{ $p->status_label }}</span>
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('kabupaten.show', $p->id) }}" class="btn btn-xs btn-outline" title="Detail">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </a>
                                    {{-- Tombol Tetapkan: hanya muncul jika status menunggu_penetapan. Khusus Berdaya Berjaya wajib lolos wawancara dulu --}}
                                    @php
                                        $canTetapkan = false;
                                        if (!isset($adaBelumDinilai) || !$adaBelumDinilai) {
                                            if ($p->status === 'menunggu_penetapan') {
                                                if (isset($isBerdayaBerjaya) && $isBerdayaBerjaya) {
                                                    // Berdaya Berjaya wajib lolos wawancara dulu
                                                    if ($p->nilai_wawancara !== null && $p->nilai_wawancara == 100) {
                                                        $canTetapkan = true;
                                                    }
                                                } else {
                                                    // Selain Berdaya Berjaya (termasuk SDSS), langsung bisa ditetapkn
                                                    $canTetapkan = true;
                                                }
                                            }
                                        }
                                    @endphp

                                    @if($canTetapkan)
                                        <form action="{{ route('kabupaten.penetapan.satu', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Tetapkan pendaftar ini?');">
                                            @csrf
                                            <input type="hidden" name="keputusan" value="lulus">
                                            <button type="submit" class="btn btn-xs text-white bg-green-600 hover:bg-green-700 border-green-600" title="Tetapkan">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i> Tetapkan
                                            </button>
                                        </form>
                                        <form action="{{ route('kabupaten.penetapan.satu', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Tolak pendaftar ini (TIDAK LULUS)?');">
                                            @csrf
                                            <input type="hidden" name="keputusan" value="tidak_lulus">
                                            <button type="submit" class="btn btn-xs text-white bg-red-500 hover:bg-red-600 border-red-500" title="Tidak Lulus">
                                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    @endif
                                    {{-- Status akhir badges --}}
                                    @if($p->status === 'lulus')
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-md text-[11px] font-bold">✓ SK TERBIT</span>
                                            <form action="{{ route('kabupaten.penetapan.satu', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan penetapan beasiswa untuk pendaftar ini? Status akan kembali menjadi Menunggu Penetapan.');">
                                                @csrf
                                                <input type="hidden" name="keputusan" value="batal">
                                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 underline" title="Batalkan Penetapan">Batal</button>
                                            </form>
                                        </div>
                                    @elseif($p->status === 'tidak_lulus')
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 bg-red-100 text-red-600 rounded-md text-[11px] font-bold">✗ TIDAK LULUS</span>
                                            <form action="{{ route('kabupaten.penetapan.satu', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan penolakan beasiswa untuk pendaftar ini? Status akan kembali menjadi Menunggu Penetapan.');">
                                                @csrf
                                                <input type="hidden" name="keputusan" value="batal">
                                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 underline" title="Batalkan Penolakan">Batal</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ (isset($isBerdayaBerjaya) && $isBerdayaBerjaya) ? 8 : 7 }}">
                                <x-empty-state icon="folder-open" title="Tidak Ada Data" text="Tidak ada pendaftar yang sudah diteruskan dari kecamatan pada periode dan program ini." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pendaftar->hasPages())
            <div class="card-footer">
                {{ $pendaftar->links() }}
            </div>
        @endif
    </div>

    @if(isset($isBerdayaBerjaya) && $isBerdayaBerjaya)
    <!-- Modal Input Wawancara -->
    <div id="wawancaraModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden" style="backdrop-filter: blur(4px);">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-800">Hasil Wawancara</h3>
                <button type="button" onclick="closeWawancaraModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form id="wawancaraForm" method="POST" action="">
                @csrf
                <input type="hidden" name="action_type" id="action_type" value="">
                
                <div class="p-6">
                    <p class="text-sm text-center text-gray-600 mb-5">Tentukan hasil wawancara untuk pendaftar ini.</p>
                    
                    <div class="mb-5 text-left">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Lampirkan Catatan (Opsional)</label>
                        <textarea name="catatan" id="wawancara_catatan" rows="3" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-sm" placeholder="Tuliskan catatan khusus (jika ada)..."></textarea>
                    </div>

                    <div class="flex flex-col gap-3">
                        <button type="button" onclick="submitWawancara('lolos')" class="w-full px-4 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl text-sm font-bold transition-all shadow-md flex items-center justify-center gap-2">
                            <i data-lucide="check-circle" class="w-5 h-5"></i> Lolos Wawancara
                        </button>
                        <button type="button" onclick="submitWawancara('gugurkan')" class="w-full px-4 py-3 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-xl text-sm font-bold transition-all flex items-center justify-center gap-2">
                            <i data-lucide="x-circle" class="w-5 h-5"></i> Tidak Lolos / Gugur
                        </button>
                        <button type="button" onclick="submitWawancara('batalkan')" class="w-full px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-500 border border-slate-200 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 mt-2">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Batalkan Pilih (Reset Status)
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-right">
                    <button type="button" onclick="closeWawancaraModal()" class="px-4 py-2 bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-xl text-xs font-bold transition-all">
                        Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openWawancaraModal(id) {
            const form = document.getElementById('wawancaraForm');
            form.action = `/kabupaten/wawancara/${id}`;
            document.getElementById('wawancara_catatan').value = ''; // Kosongkan form saat buka
            document.getElementById('wawancaraModal').classList.remove('hidden');
        }

        function closeWawancaraModal() {
            document.getElementById('wawancaraModal').classList.add('hidden');
        }

        function submitWawancara(action) {
            let title = '';
            let text = '';
            let confirmColor = '';
            let icon = 'question';
            let confirmText = '';

            if (action === 'lolos') {
                title = 'Lolos Wawancara?';
                text = 'Tetapkan pendaftar ini sebagai Lolos Wawancara?';
                confirmColor = '#10B981'; // Green
                confirmText = 'Ya, Loloskan';
            } else if (action === 'gugurkan') {
                title = 'Tidak Lolos / Gugur?';
                text = 'Tetapkan pendaftar ini sebagai Tidak Lolos / Gugur Wawancara?';
                confirmColor = '#EF4444'; // Red
                icon = 'warning';
                confirmText = 'Ya, Gugurkan';
            } else {
                title = 'Batalkan Pilihan?';
                text = 'Reset status wawancara pendaftar ini ke keadaan awal (Belum dinilai)?';
                confirmColor = '#6B7280'; // Gray
                icon = 'warning';
                confirmText = 'Ya, Batalkan Pilihan';
            }

            closeWawancaraModal(); // Sembunyikan modal dasar saat SweetAlert muncul

            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#d33',
                confirmButtonText: confirmText,
                cancelButtonText: 'Kembali',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('action_type').value = action;
                    document.getElementById('wawancaraForm').submit();
                } else {
                    // Tampilkan kembali modal dasar jika dibatalkan
                    document.getElementById('wawancaraModal').classList.remove('hidden');
                }
            });
        }
    </script>
    @endpush
    @endif

    @push('scripts')
    <script>
        function confirmGenerateRanking() {
            let adaBelumDinilai = {{ isset($adaBelumDinilai) && $adaBelumDinilai ? 'true' : 'false' }};
            
            if (!adaBelumDinilai) {
                // Semua pendaftar sudah dinilai
                Swal.fire({
                    title: 'Tidak Ada Data Baru',
                    text: 'Seluruh pendaftar pada jalur ini telah memiliki skor dan peringkat. Proses perhitungan ulang hanya dapat dilakukan jika terdapat data pendaftar baru.',
                    icon: 'info',
                    confirmButtonColor: '#3B82F6',
                    confirmButtonText: 'Mengerti'
                });
                return;
            }

            Swal.fire({
                title: 'Mulai Perhitungan Nilai?',
                text: 'Proses ini akan menghitung nilai berbobot dan menetapkan peringkat untuk semua pendaftar yang Lolos Verifikasi pada jalur ini.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3B82F6', // Blue
                cancelButtonColor: '#9CA3AF', // Gray
                confirmButtonText: 'Ya, Hitung & Ranking!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sedang Memproses...',
                        html: 'Mohon tunggu sebentar, sistem sedang melakukan perhitungan otomatis.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                    document.getElementById('form-generate-ranking').submit();
                }
            });
        }
    </script>
    @endpush
</x-layouts.admin>
