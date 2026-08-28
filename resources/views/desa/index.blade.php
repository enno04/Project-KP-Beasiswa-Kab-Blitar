<x-layouts.admin :title="'Data Pendaftar — ' . $program->nama">
    <x-page-header :title="'Data Pendaftar: ' . $program->nama" :subtitle="$jalur ? 'Jalur: ' . $jalur->nama : null">
        <x-slot:actions>
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button type="button" @click="open = !open" class="btn bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm flex items-center h-[54px] rounded-xl px-5 font-semibold transition-all duration-200" title="Export Excel">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2"></i> Export Data <i data-lucide="chevron-down" class="w-4 h-4 ml-2"></i>
                </button>
                <div x-show="open" x-transition style="display: none;" class="absolute left-0 sm:right-0 sm:left-auto top-full mt-2 w-64 bg-white border border-slate-200 rounded-lg shadow-lg z-50 overflow-hidden">
                    <a href="{{ route('desa.program.export', ['programSlug' => $program->slug, 'jalurSlug' => $jalur?->slug] + request()->except('status') + ['export_type' => 'lolos_saja']) }}" class="flex items-start px-4 py-3 text-sm font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 border-b border-slate-100 transition-colors">
                        <i data-lucide="check-circle" class="w-4 h-4 mr-2.5 shrink-0 text-emerald-600 mt-0.5"></i>
                        <span>Hanya Yang Lolos</span>
                    </a>
                    <a href="{{ route('desa.program.export', ['programSlug' => $program->slug, 'jalurSlug' => $jalur?->slug] + request()->except('status') + ['export_type' => 'tidak_lolos']) }}" class="flex items-start px-4 py-3 text-sm font-medium text-slate-700 hover:bg-rose-50 hover:text-rose-700 border-b border-slate-100 transition-colors">
                        <i data-lucide="x-circle" class="w-4 h-4 mr-2.5 shrink-0 text-rose-500 mt-0.5"></i>
                        <span>Tidak Dipilih Desa</span>
                    </a>
                    <a href="{{ route('desa.program.export', ['programSlug' => $program->slug, 'jalurSlug' => $jalur?->slug] + request()->except('status') + ['export_type' => 'sudah_dinilai']) }}" class="flex items-start px-4 py-3 text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 border-b border-slate-100 transition-colors">
                        <i data-lucide="clipboard-check" class="w-4 h-4 mr-2.5 shrink-0 text-blue-500 mt-0.5"></i>
                        <span>Sudah Dinilai</span>
                    </a>
                    <a href="{{ route('desa.program.export', ['programSlug' => $program->slug, 'jalurSlug' => $jalur?->slug] + request()->except('status') + ['export_type' => 'belum_dinilai']) }}" class="flex items-start px-4 py-3 text-sm font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-700 border-b border-slate-100 transition-colors">
                        <i data-lucide="clipboard-x" class="w-4 h-4 mr-2.5 shrink-0 text-amber-500 mt-0.5"></i>
                        <span>Belum Dinilai</span>
                    </a>
                    <a href="{{ route('desa.program.export', ['programSlug' => $program->slug, 'jalurSlug' => $jalur?->slug] + request()->query()) }}" class="flex items-start px-4 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                        <i data-lucide="users" class="w-4 h-4 mr-2.5 shrink-0 text-indigo-500 mt-0.5"></i>
                        <span>Semua Pendaftar (Sesuai Filter Data)</span>
                    </a>
                </div>
            </div>

            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-xl bg-primary text-white shadow-lg shadow-primary/30 transform transition-transform hover:-translate-y-0.5 h-[54px]">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center backdrop-blur-sm">
                    <i data-lucide="database" class="w-4 h-4 text-white"></i>
                </div>
                <div class="flex flex-col text-left">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-white/90 leading-none mb-0.5">Total Data</span>
                    <span class="text-base font-black leading-none">{{ number_format($pendaftar->total(), 0, ',', '.') }}</span>
                </div>
            </div>
        </x-slot:actions>
    </x-page-header>

    {{-- Filter --}}
    <div class="card mb-6">
        <div class="card-body py-4">
            <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 items-end">
                <div class="flex-1 w-full min-w-[200px]">
                    <label class="form-label">Cari Pendaftar</label>
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full" style="padding-left: 2.5rem !important;" placeholder="Nama/NIK...">
                    </div>
                </div>
                <div class="w-full sm:w-32">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        @for($i = date('Y'); $i >= 2024; $i--)
                            <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="w-full sm:w-48">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="menunggu_verifikasi" {{ request('status') === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="lolos_verifikasi" {{ request('status') === 'lolos_verifikasi' ? 'selected' : '' }}>Lolos Verifikasi</option>
                        <option value="ditolak_verifikasi" {{ request('status') === 'ditolak_verifikasi' ? 'selected' : '' }}>Ditolak Verifikasi</option>
                        <option value="menunggu_penetapan" {{ request('status') === 'menunggu_penetapan' ? 'selected' : '' }}>Menunggu Penetapan</option>
                        <option value="lulus" {{ request('status') === 'lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="tidak_lulus" {{ request('status') === 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                    </select>
                </div>
                <div class="w-full sm:w-48">
                    <label class="form-label">Urutkan</label>
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="">Default (Peringkat)</option>
                        <option value="terbaru" {{ request('sort') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                        <option value="nilai_tertinggi" {{ request('sort') === 'nilai_tertinggi' ? 'selected' : '' }}>Nilai Tertinggi</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary"><i data-lucide="filter" class="w-4 h-4"></i> Filter</button>
                    @if(request()->hasAny(['tahun', 'status', 'search', 'sort']))
                        <a href="{{ url()->current() }}" class="btn btn-outline" title="Reset"><i data-lucide="x" class="w-4 h-4"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- SDSS Penilaian Panel --}}
    @if($program->isSdss() && $jalur && $periodeAktif)
    <div class="card mb-6">
        <div class="card-body flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="font-bold text-slate-900">Penilaian & Pemeringkatan ({{ $periodeAktif->nama }})</h3>
                <p class="text-xs text-slate-400 mt-1">Kalkulasi nilai dan ranking untuk pendaftar yang lolos verifikasi OPD.</p>
            </div>
            <div class="flex items-center gap-2">
                @php
                    $tutup = $program->tanggal_tutup ?? ($periodeAktif ? $periodeAktif->tanggal_selesai : null);
                @endphp
                @if($program && $program->kunci_hitung_nilai && $tutup && \Carbon\Carbon::now()->startOfDay()->lte(\Carbon\Carbon::parse($tutup)->endOfDay()))
                    <div class="text-sm text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200">
                        <i data-lucide="lock" class="w-4 h-4 inline-block mr-1"></i> Penilaian terkunci. Dibuka setelah pendaftaran ditutup pada <strong>{{ \Carbon\Carbon::parse($tutup)->format('d M Y') }}</strong>.
                    </div>
                @elseif(isset($sudahDitetapkan) && $sudahDitetapkan)
                    <div class="text-sm text-emerald-700 bg-emerald-50 px-4 py-2 rounded-lg border border-emerald-200">
                        <i data-lucide="check-circle" class="w-4 h-4 inline-block mr-1"></i> Perwakilan telah ditetapkan. Penilaian dikunci.
                    </div>
                @elseif(isset($sudahDinilai) && $sudahDinilai && !$belumDinilai)
                    <div class="text-sm text-emerald-700 bg-emerald-50 px-4 py-2 rounded-lg border border-emerald-200">
                        <i data-lucide="check-circle" class="w-4 h-4 inline-block mr-1"></i> Seluruh pendaftar lolos verifikasi dari desa Anda sudah dinilai.
                    </div>
                @else
                    <form id="form-hitung-penilaian" action="{{ route('desa.penilaian.hitung') }}" method="POST">
                        @csrf
                        <input type="hidden" name="jalur_id" value="{{ $jalur->id }}">
                        <input type="hidden" name="periode_id" value="{{ $periodeAktif->id }}">
                        <button type="button" onclick="konfirmasiHitung()" class="btn btn-sm btn-primary"><i data-lucide="calculator" class="w-4 h-4"></i> Hitung Penilaian & Ranking</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        function konfirmasiHitung() {
            Swal.fire({
                title: 'Proses Penilaian & Ranking?',
                text: "Sistem akan mengkalkulasi ulang seluruh nilai pendaftar yang lolos verifikasi dari desa Anda.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fas fa-calculator mr-1"></i> Ya, Hitung Sekarang!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu, sistem sedang menghitung nilai.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    document.getElementById('form-hitung-penilaian').submit();
                }
            });
        }

        function konfirmasiTetapkan(e, formId, namaPendaftar) {
            e.preventDefault();
            Swal.fire({
                title: 'Tetapkan Perwakilan?',
                html: 'Anda akan menetapkan <b>' + namaPendaftar + '</b> sebagai perwakilan desa.<br><br><span class="text-rose-500 text-sm">⚠️ Perhatian: Pendaftar lain dari desa ini akan otomatis digugurkan dan aksi ini tidak dapat dibatalkan.</span>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981', // emerald-500
                cancelButtonColor: '#64748b', // slate-500
                confirmButtonText: 'Ya, Tetapkan!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Menyimpan data penetapan.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
    @endpush

    {{-- Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>No. Pendaftaran</th>
                        <th>Nama Lengkap</th>
                        <th>Skor & Rank</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftar as $p)
                        <tr class="{{ $p->ranking === 1 && $p->total_nilai > 0 ? 'bg-amber-50' : '' }}">
                            <td><span class="text-slate-500 font-medium">{{ $pendaftar->firstItem() + $loop->index }}</span></td>
                            <td><span class="font-bold text-primary-dark">{{ $p->nomor_pendaftaran }}</span></td>
                            <td>
                                <p class="font-semibold text-slate-900">{{ $p->identitas->nama_lengkap ?? '-' }}</p>
                                <p class="text-xs text-slate-400">NIK: {{ $p->identitas->nik ?? '-' }}</p>
                            </td>
                            <td>
                                @if($p->total_nilai > 0)
                                    <span class="font-bold text-primary-dark">{{ number_format($p->total_nilai, 4) }}</span>
                                    @if($p->ranking === 1)
                                        <p class="text-xs font-bold text-amber-600">Rank: {{ $p->ranking ?? '-' }} (Peringkat 1)</p>
                                    @else
                                        <p class="text-xs text-slate-400">Rank: {{ $p->ranking ?? '-' }}</p>
                                    @endif
                                @else
                                    <span class="text-xs italic text-slate-400">Belum dinilai</span>
                                @endif
                            </td>
                            <td><span class="badge {{ $p->status_color }}">{{ $p->status_label }}</span></td>
                            <td class="text-right">
                                <div class="flex justify-end gap-2">
                                    @if($program->isSdss() && $p->ranking === 1 && $p->status === 'lolos_verifikasi' && $p->total_nilai > 0)
                                        @if(!$p->rekomendasiDesa)
                                            <form id="form-tetapkan-{{ $p->id }}" action="{{ route('desa.tetapkan', $p->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="button" onclick="konfirmasiTetapkan(event, 'form-tetapkan-{{ $p->id }}', '{{ addslashes($p->identitas->nama_lengkap ?? 'Pendaftar') }}')" class="btn btn-xs btn-success"><i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Tetapkan</button>
                                            </form>
                                            <a href="{{ route('desa.show', $p->id) }}" class="btn btn-xs btn-outline"><i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail</a>
                                        @else
                                            <a href="{{ route('desa.show', $p->id) }}#form-rekomendasi" class="btn btn-xs btn-primary bg-indigo-600 hover:bg-indigo-700 text-white border-none"><i data-lucide="upload-cloud" class="w-3.5 h-3.5"></i> Unggah Rekomendasi</a>
                                        @endif
                                    @else
                                        <a href="{{ route('desa.show', $p->id) }}" class="btn btn-xs btn-outline"><i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-empty-state icon="folder-open" title="Tidak Ada Data" text="Tidak ada data pendaftar pada filter ini." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $pendaftar->links() }}</div>
    </div>
</x-layouts.admin>
