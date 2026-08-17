<x-layouts.admin :title="'Data Pendaftar — ' . $program->nama">
    <x-page-header :title="'Data Pendaftar: ' . $program->nama" :subtitle="$jalur ? 'Jalur: ' . $jalur->nama : 'Semua jalur'">
        <x-slot:actions>
            <span class="badge badge-primary text-xs">{{ $pendaftar->total() }} data</span>
        </x-slot:actions>
    </x-page-header>

    {{-- Filter --}}
    <div class="card mb-6">
        <div class="card-body py-4">
            <form method="GET" class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="w-full sm:w-48">
                    <label class="form-label">Periode</label>
                    <select name="periode_id" class="form-select" onchange="this.form.submit()">
                        @foreach($periodeList as $p)
                            <option value="{{ $p->id }}" {{ ($periodeId ?? '') == $p->id ? 'selected' : '' }}>{{ $p->nama }} ({{ $p->tahun }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-64">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="lolos_verifikasi" {{ request('status') === 'lolos_verifikasi' ? 'selected' : '' }}>Lolos Verifikasi</option>
                        <option value="proses_penilaian" {{ request('status') === 'proses_penilaian' ? 'selected' : '' }}>Proses Penilaian</option>
                        <option value="menunggu_penetapan" {{ request('status') === 'menunggu_penetapan' ? 'selected' : '' }}>Menunggu Penetapan</option>
                        <option value="lulus" {{ request('status') === 'lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="tidak_lulus" {{ request('status') === 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                    </select>
                </div>
                @if(request()->hasAny(['status']))
                    <a href="{{ url()->current() }}?periode_id={{ $periodeId }}" class="btn btn-outline btn-sm">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i> Reset
                    </a>
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
                <form action="{{ route('kabupaten.penilaian.ranking') }}" method="POST" onsubmit="return confirm('Hitung penilaian & generate ranking untuk semua pendaftar yang lolos verifikasi pada jalur ini?');">
                    @csrf
                    <input type="hidden" name="jalur_id" value="{{ $jalur->id }}">
                    <input type="hidden" name="periode_id" value="{{ $periodeId }}">
                    <button type="submit" class="btn btn-primary whitespace-nowrap">
                        <i data-lucide="calculator" class="w-4 h-4"></i> Hitung Penilaian & Generate Ranking
                    </button>
                </form>
            </div>
        </div>
    @elseif($isSdss)
        <div class="card mb-6">
            <div class="card-body py-4">
                <div class="flex items-center gap-3 text-sm text-slate-600">
                    <i data-lucide="info" class="w-4 h-4 text-primary shrink-0"></i>
                    Untuk program SDSS, proses penilaian dan perangkingan dilakukan oleh <strong class="text-primary-dark">Admin Desa</strong>. Kabupaten hanya melihat hasil dan melakukan penetapan.
                </div>
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
                                    @if($p->status === 'gugur_wawancara')
                                        <span class="px-2 py-1 bg-red-50 text-red-600 rounded text-xs font-semibold">Tidak Hadir</span>
                                        <button type="button" onclick="openWawancaraModal({{ $p->id }}, '')" class="ml-1 text-xs text-primary hover:underline">Revisi</button>
                                    @elseif($p->nilai_wawancara !== null)
                                        <div class="font-bold text-sm text-green-700">{{ number_format($p->nilai_wawancara, 2) }}</div>
                                        <button type="button" onclick="openWawancaraModal({{ $p->id }}, {{ $p->nilai_wawancara }})" class="text-xs text-primary hover:underline">Edit Nilai</button>
                                    @else
                                        <button type="button" onclick="openWawancaraModal({{ $p->id }}, '')" class="btn btn-xs bg-green-100 text-green-700 border-green-200 hover:bg-green-200">
                                            Input Wawancara
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
                                    {{-- Tombol Tetapkan: muncul jika status menunggu_penetapan, atau lolos_verifikasi tapi sudah ada ranking (SDSS) --}}
                                    @if($p->status === 'menunggu_penetapan' || ($p->status === 'lolos_verifikasi' && $p->ranking !== null))
                                        <form action="{{ route('kabupaten.penetapan.satu', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Terbitkan SK untuk pendaftar ini?');">
                                            @csrf
                                            <input type="hidden" name="keputusan" value="lulus">
                                            <button type="submit" class="btn btn-xs text-white bg-green-600 hover:bg-green-700 border-green-600" title="Terbitkan SK">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i> Terbitkan SK
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
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-md text-[11px] font-bold">✓ SK TERBIT</span>
                                    @elseif($p->status === 'tidak_lulus')
                                        <span class="px-2 py-1 bg-red-100 text-red-600 rounded-md text-[11px] font-bold">✗ TIDAK LULUS</span>
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
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-800">Input Nilai Wawancara</h3>
                <button type="button" onclick="closeWawancaraModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form id="wawancaraForm" method="POST" action="">
                @csrf
                <input type="hidden" name="action_type" id="action_type" value="simpan">
                
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">Masukkan nilai akhir wawancara (skala 0 - 100).</p>
                    
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nilai Wawancara</label>
                        <input type="number" step="0.01" min="0" max="100" name="nilai_wawancara" id="input_nilai_wawancara" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-lg font-bold text-green-700" required placeholder="0.00">
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                    <button type="button" onclick="gugurkanWawancara()" class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl text-xs font-bold transition-all">
                        Tidak Hadir (Gugurkan)
                    </button>
                    <div class="flex gap-2">
                        <button type="button" onclick="closeWawancaraModal()" class="px-4 py-2 bg-gray-200 text-gray-700 hover:bg-gray-300 rounded-xl text-xs font-bold transition-all">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white hover:bg-primary-dark rounded-xl text-xs font-bold transition-all shadow-md">
                            Simpan Nilai
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openWawancaraModal(id, val) {
            const form = document.getElementById('wawancaraForm');
            const input = document.getElementById('input_nilai_wawancara');
            document.getElementById('action_type').value = 'simpan';
            form.action = `/kabupaten/wawancara/${id}`;
            input.value = val !== undefined ? val : '';
            input.required = true;
            document.getElementById('wawancaraModal').classList.remove('hidden');
        }

        function closeWawancaraModal() {
            document.getElementById('wawancaraModal').classList.add('hidden');
        }

        function gugurkanWawancara() {
            if(confirm('Yakin ingin menggugurkan pendaftar ini karena tidak hadir wawancara?')) {
                document.getElementById('action_type').value = 'gugurkan';
                document.getElementById('input_nilai_wawancara').required = false;
                document.getElementById('wawancaraForm').submit();
            }
        }
    </script>
    @endpush
    @endif
</x-layouts.admin>
