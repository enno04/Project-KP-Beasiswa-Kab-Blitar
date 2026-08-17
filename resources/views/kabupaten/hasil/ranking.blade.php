<x-layouts.admin :title="'Hasil Ranking (Weighted Score)'">
    <div class="space-y-6 pb-20">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">Hasil Ranking Seleksi</h1>
                <p style="color: var(--color-text-secondary);">Kalkulasi pemeringkatan dan penetapan daftar pendaftar terpilih.</p>
            </div>
        </div>

        {{-- Filter & Generate Panel --}}
        <div class="rounded-2xl border p-5 bg-white" style="border-color: var(--color-border);">
            <form method="GET" class="flex flex-col md:flex-row gap-4 items-end" x-data="filterRanking()">
                <div class="w-full md:w-1/3">
                    <label class="block text-sm font-semibold mb-1 text-gray-700">Periode Pendaftaran</label>
                    <select name="periode_id" class="w-full px-4 py-2.5 rounded-xl border text-sm" required>
                        <option value="">-- Pilih Periode --</option>
                        @foreach($periodeList as $p)
                            <option value="{{ $p->id }}" {{ request('periode_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }} ({{ $p->tahun }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-1/3">
                    <label class="block text-sm font-semibold mb-1 text-gray-700">Program</label>
                    <select id="program_select" class="w-full px-4 py-2.5 rounded-xl border text-sm" required @change="updateJalur()">
                        <option value="">-- Pilih Program --</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog->id }}" data-jalurs="{{ json_encode($prog->jalurs) }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>{{ $prog->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-1/3">
                    <label class="block text-sm font-semibold mb-1 text-gray-700">Jalur</label>
                    <select name="jalur_id" id="jalur_select" class="w-full px-4 py-2.5 rounded-xl border text-sm" required>
                        <option value="">-- Pilih Jalur --</option>
                    </select>
                </div>
                <div class="w-full md:w-auto flex gap-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-white font-semibold transition-all hover:opacity-90 flex items-center gap-2" style="background-color: var(--color-primary);">
                        <i data-lucide="search" class="w-4 h-4"></i> Tampilkan
                    </button>
                </div>
            </form>

            @if(request('jalur_id') && request('periode_id'))
                @php
                    $selectedJalur = \App\Models\Jalur::with('program')->find(request('jalur_id'));
                    $isSdss = $selectedJalur && $selectedJalur->program->isSdss();
                @endphp
                
                @if(!$isSdss)
                <div class="mt-4 pt-4 border-t flex flex-col sm:flex-row justify-between items-center gap-4" style="border-color: var(--color-border);">
                    <div class="text-sm text-gray-600">
                        Data yang tampil adalah hasil proses ranking. Jika ada perubahan nilai/data baru, silakan lakukan proses ulang (Generate Ranking).
                    </div>
                    <div class="flex gap-2">
                        <form action="{{ route('kabupaten.penilaian.hitung') }}" method="POST" onsubmit="return confirm('Kalkulasi nilai pendaftar yang lolos verifikasi?');">
                            @csrf
                            <input type="hidden" name="jalur_id" value="{{ request('jalur_id') }}">
                            <input type="hidden" name="periode_id" value="{{ request('periode_id') }}">
                            <button type="submit" class="whitespace-nowrap px-4 py-2 rounded-xl text-primary-dark font-semibold bg-primary-light hover:bg-primary-light transition-all flex items-center gap-2 text-sm shadow-sm border border-primary-light">
                                <i data-lucide="calculator" class="w-4 h-4"></i> Hitung Penilaian
                            </button>
                        </form>
                        <form action="{{ route('kabupaten.ranking.generate') }}" method="POST" onsubmit="return confirm('Proses ini akan mengurutkan pendaftar berdasarkan nilai akhir administrasi. Lanjutkan?');">
                            @csrf
                            <input type="hidden" name="jalur_id" value="{{ request('jalur_id') }}">
                            <input type="hidden" name="periode_id" value="{{ request('periode_id') }}">
                            <button type="submit" class="whitespace-nowrap px-4 py-2 rounded-xl text-white font-semibold bg-orange-600 hover:bg-orange-700 transition-all flex items-center gap-2 text-sm shadow-md">
                                <i data-lucide="bar-chart-2" class="w-4 h-4"></i> Generate Ranking
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <div class="mt-4 pt-4 border-t text-sm text-gray-600" style="border-color: var(--color-border);">
                    Untuk program SDSS, proses penilaian dan perangkingan dilakukan oleh <strong>Admin Desa</strong>. Anda hanya dapat melihat hasilnya di sini dan memprosesnya di tahap Penetapan.
                </div>
                @endif
            @endif
        </div>

        {{-- Tabel Ranking --}}
        @if(request('jalur_id') && request('periode_id'))
            <div class="rounded-2xl border bg-white overflow-hidden" style="border-color: var(--color-border);">
                <div class="p-4 border-b bg-gray-50 flex justify-between items-center" style="border-color: var(--color-border);">
                    <h3 class="font-bold text-gray-700">Daftar Peringkat</h3>
                    <a href="{{ route('kabupaten.hasil.penetapan', ['jalur_id' => request('jalur_id'), 'periode_id' => request('periode_id')]) }}" class="px-3 py-1.5 rounded-lg text-sm font-semibold bg-primary-light text-primary-dark hover:bg-primary-light transition-all flex items-center gap-2 border border-primary-light">
                        Lanjut ke Penetapan <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead style="background-color: var(--color-bg); border-bottom: 1px solid var(--color-border);">
                            <tr>
                                <th class="px-6 py-4 font-semibold text-gray-700 w-16 text-center">Rank</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Pendaftar</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Wilayah</th>
                                <th class="px-6 py-4 font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-4 font-bold text-primary-dark text-right">Nilai Administrasi</th>
                                @if(isset($isBerdayaBerjaya) && $isBerdayaBerjaya)
                                    <th class="px-6 py-4 font-bold text-green-700 text-right">Nilai Wawancara</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color: var(--color-border);">
                            @forelse($pendaftars as $p)
                                <tr class="hover:bg-gray-50 transition-colors {{ $loop->iteration <= 10 ? 'bg-yellow-50/30' : '' }}">
                                    <td class="px-6 py-4 text-center">
                                        @if($p->ranking == 1)
                                            <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 font-bold mx-auto border border-yellow-200 shadow-sm" title="Peringkat 1">1</div>
                                        @elseif($p->ranking == 2)
                                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold mx-auto border border-gray-300 shadow-sm">2</div>
                                        @elseif($p->ranking == 3)
                                            <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-700 font-bold mx-auto border border-orange-200 shadow-sm">3</div>
                                        @else
                                            <span class="font-bold text-gray-500">{{ $p->ranking ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="font-bold">{{ $p->identitas->nama_lengkap ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $p->nomor_pendaftaran }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p>Desa {{ $p->identitas->desa->nama_desa ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">Kec. {{ $p->identitas->kecamatan->nama_kecamatan ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $p->status_color }}">
    {{ strtoupper($p->status_label) }}
</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="font-bold text-lg" style="color: var(--color-primary);">{{ number_format($p->total_nilai, 4) }}</span>
                                    </td>
                                    @if(isset($isBerdayaBerjaya) && $isBerdayaBerjaya)
                                    <td class="px-6 py-4 text-right">
                                        @if($p->status === 'gugur_wawancara')
                                            <span class="px-2 py-1 bg-red-50 text-red-600 rounded text-xs font-semibold">Tidak Hadir</span>
                                            <button type="button" onclick="openWawancaraModal({{ $p->id }}, '')" class="ml-2 text-xs text-primary hover:underline">Revisi</button>
                                        @elseif($p->nilai_wawancara !== null)
                                            <div class="font-bold text-lg text-green-700 mb-1">{{ number_format($p->nilai_wawancara, 2) }}</div>
                                            <button type="button" onclick="openWawancaraModal({{ $p->id }}, {{ $p->nilai_wawancara }})" class="text-xs text-primary hover:underline">Edit Nilai</button>
                                        @else
                                            <button type="button" onclick="openWawancaraModal({{ $p->id }}, '')" class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-xs font-semibold hover:bg-green-200 border border-green-200 transition-all">
                                                Input Wawancara
                                            </button>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <i data-lucide="bar-chart-3" class="w-10 h-10 mx-auto mb-3 opacity-50"></i>
                                        Tidak ada data pendaftar pada filter ini, atau belum dilakukan Generate Ranking.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
                        <label class="block text-sm font-semibold mb-1 text-gray-700">Nilai Wawancara <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" min="0" max="100" name="nilai_wawancara" id="input_nilai_wawancara" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all text-lg font-bold text-green-700" required placeholder="0.00">
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                    <button type="button" onclick="gugurkanWawancara()" class="px-4 py-2 rounded-xl font-semibold text-red-600 hover:bg-red-50 border border-red-200 transition-colors text-sm">Tidak Hadir (Gugurkan)</button>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeWawancaraModal()" class="px-4 py-2 rounded-xl font-semibold text-gray-600 hover:bg-gray-200 transition-colors text-sm">Batal</button>
                        <button type="submit" onclick="document.getElementById('action_type').value='simpan'" class="px-5 py-2.5 rounded-xl font-semibold bg-green-600 text-white hover:bg-green-700 transition-colors shadow-sm text-sm">Simpan Nilai</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('filterRanking', () => ({
                init() {
                    this.updateJalur('{{ request('jalur_id') }}');
                },
                updateJalur(selectedJalur = '') {
                    const progSelect = document.getElementById('program_select');
                    const jalurSelect = document.getElementById('jalur_select');
                    
                    if(!progSelect.value) {
                        jalurSelect.innerHTML = '<option value="">-- Pilih Jalur --</option>';
                        return;
                    }

                    const selectedOption = progSelect.options[progSelect.selectedIndex];
                    const jalurs = JSON.parse(selectedOption.getAttribute('data-jalurs'));
                    
                    jalurSelect.innerHTML = '<option value="">-- Pilih Jalur --</option>';
                    jalurs.forEach(j => {
                        const isSelected = selectedJalur == j.id ? 'selected' : '';
                        jalurSelect.innerHTML += `<option value="${j.id}" ${isSelected}>${j.nama}</option>`;
                    });
                }
            }));
        });

        function openWawancaraModal(id, nilai) {
            const form = document.getElementById('wawancaraForm');
            const input = document.getElementById('input_nilai_wawancara');
            
            form.action = `/kabupaten/wawancara/${id}`;
            input.value = nilai !== null ? nilai : '';
            
            // Reset required attribute
            input.required = true;
            document.getElementById('action_type').value = 'simpan';
            
            document.getElementById('wawancaraModal').classList.remove('hidden');
        }
        
        function closeWawancaraModal() {
            document.getElementById('wawancaraModal').classList.add('hidden');
        }

        function gugurkanWawancara() {
            if(confirm('Yakin ingin menggugurkan pendaftar ini karena tidak hadir wawancara? (Aksi ini dapat direvisi)')) {
                document.getElementById('action_type').value = 'gugurkan';
                document.getElementById('input_nilai_wawancara').required = false;
                document.getElementById('wawancaraForm').submit();
            }
        }
    </script>
    @endpush
</x-layouts.admin>
