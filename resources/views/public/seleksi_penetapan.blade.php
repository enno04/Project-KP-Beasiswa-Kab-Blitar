<x-layouts.public :title="'Seleksi & Penetapan — Beasiswa Blitar Mengabdi'">
    {{-- Page Hero --}}
    <section class="bg-cover bg-center bg-no-repeat border-b border-slate-200" style="background-image: url('{{ asset('images/seleksi.png') }}');">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <p class="text-sm font-bold text-white uppercase tracking-widest mb-3">Transparansi & Pengumuman Resmi</p>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">Hasil Seleksi & Alur Tahapan Penetapan</h1>
            <p class="text-white mt-3 max-w-2xl mx-auto">Lihat daftar resmi peserta yang dinyatakan Lulus berdasarkan SK Bupati Blitar serta transparansi alur tahapan seleksi dari Desa hingga Kabupaten.</p>
        </div>
    </section>

    <div class="bg-cover bg-center md:bg-fixed" style="background-image: url('{{ asset('images/background.jpeg') }}')">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
            {{-- PENGUMUMAN PENERIMA --}}
            <div class="space-y-8">
                <div class="text-center">
                    <h2 class="text-2xl font-extrabold text-slate-900">Pengumuman Hasil Penetapan</h2>
                    <div class="w-16 h-1 mx-auto mt-3 rounded-full" style="background-color: #FFD800;"></div>
                    @if($periodeAktif)
                        <p class="text-sm text-slate-500 mt-4">Daftar penerima beasiswa Kabupaten Blitar Periode Tahun
                            {{ $periodeAktif->tahun }}.
                        </p>
                    @else
                        <p class="text-sm text-slate-500 mt-4">Belum ada periode pendaftaran beasiswa yang aktif.</p>
                    @endif
                </div>

                @if($periodeAktif && $programs->count() > 0)
                    <div x-data="{ activeTab: '{{ $programs->first()->slug ?? 'none' }}' }">
                        {{-- Tabs --}}
                        <div class="flex flex-wrap justify-center gap-2 mb-8">
                            @foreach($programs as $prog)
                                <button @click="activeTab = '{{ $prog->slug }}'"
                                    :class="activeTab === '{{ $prog->slug }}' ? 'bg-primary text-white shadow-lg' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'"
                                    class="px-6 py-3 rounded-xl font-bold text-sm transition-all focus:outline-none">
                                    {{ $prog->nama }}
                                </button>
                            @endforeach
                        </div>

                        {{-- Tab Content --}}
                        <div>
                            @foreach($programs as $prog)
                                <div x-show="activeTab === '{{ $prog->slug }}'" x-transition style="display: none;">
                                    @foreach($prog->jalurs as $jalur)
                                        <div class="mb-8 card overflow-hidden">
                                            <div
                                                class="card-body py-4 bg-slate-50/50 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                                <div>
                                                    <h3 class="font-extrabold text-lg text-slate-900">Jalur {{ $jalur->nama }}</h3>
                                                    <p class="text-sm text-slate-500 mt-0.5">Daftar penerima yang dinyatakan <span
                                                            class="font-bold text-green-600">LULUS</span>.</p>
                                                </div>
                                                <x-badge type="primary">Total:
                                                    {{ isset($penerima[$jalur->id]) ? $penerima[$jalur->id]->count() : 0 }}
                                                    Penerima</x-badge>
                                            </div>

                                            <div class="card-body">
                                                @if(isset($penerima[$jalur->id]) && $penerima[$jalur->id]->count() > 0)
                                                    <div class="overflow-x-auto" x-data="{ page: 1, totalPages: {{ ceil($penerima[$jalur->id]->count() / 10) }} }">
                                                        <table class="data-table w-full">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center w-16">Peringkat</th>
                                                                    <th>No. Pendaftaran</th>
                                                                    <th>Nama Lengkap</th>
                                                                    <th>Asal Wilayah</th>
                                                                    <th>Perguruan Tinggi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($penerima[$jalur->id] as $index => $p)
                                                                    @php $itemPage = floor($index / 10) + 1; @endphp
                                                                    <tr x-show="page === {{ $itemPage }}" {!! $itemPage === 1 ? '' : 'style="display: none;"' !!}>
                                                                        <td class="text-center">
                                                                            <span
                                                                                class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs mx-auto {{ $loop->iteration <= 3 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600' }}">
                                                                                {{ $loop->iteration }}
                                                                            </span>
                                                                        </td>
                                                                        <td class="font-mono font-bold text-primary-dark">
                                                                            {{ $p->nomor_pendaftaran }}</td>
                                                                        <td class="font-semibold text-slate-900">
                                                                            {{ $p->identitas->nama_lengkap ?? '-' }}</td>
                                                                        <td>
                                                                            <p class="text-sm">Desa
                                                                                {{ $p->identitas->desa->nama_desa ?? '-' }}</p>
                                                                            <p class="text-xs text-slate-400">Kec.
                                                                                {{ $p->identitas->kecamatan->nama_kecamatan ?? '-' }}</p>
                                                                        </td>
                                                                        <td class="text-sm text-slate-600">
                                                                            {{ $p->identitas->asal_perguruan_tinggi ?? '-' }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>

                                                        @if($penerima[$jalur->id]->count() > 10)
                                                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-4 px-2 py-3 border-t border-slate-100">
                                                                <div class="text-sm text-slate-500">
                                                                    Menampilkan <span class="font-bold text-slate-700" x-text="(page - 1) * 10 + 1"></span> - 
                                                                    <span class="font-bold text-slate-700" x-text="Math.min(page * 10, {{ $penerima[$jalur->id]->count() }})"></span> 
                                                                    dari <span class="font-bold text-slate-700">{{ $penerima[$jalur->id]->count() }}</span> penerima
                                                                </div>
                                                                <div class="flex items-center gap-1">
                                                                    <button @click="if(page > 1) page--" :disabled="page === 1" 
                                                                        class="px-3 py-1.5 text-sm rounded-lg border border-slate-200 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium text-slate-600">
                                                                        Sebelumnya
                                                                    </button>
                                                                    <div class="px-3 py-1.5 text-sm font-bold text-slate-700 bg-slate-50 rounded-lg border border-slate-100">
                                                                        <span x-text="page"></span> / <span x-text="totalPages"></span>
                                                                    </div>
                                                                    <button @click="if(page < totalPages) page++" :disabled="page === totalPages" 
                                                                        class="px-3 py-1.5 text-sm rounded-lg border border-slate-200 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium text-slate-600">
                                                                        Selanjutnya
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <x-empty-state icon="award" title="Belum Ada Penetapan"
                                                        text="Belum ada penetapan penerima untuk jalur ini." />
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Alur Seleksi --}}
            <div class="space-y-8">
                <div class="text-center">
                    <h2 class="text-2xl font-extrabold text-slate-900">Alur Proses Seleksi</h2>
                    <div class="w-16 h-1 mx-auto mt-3 rounded-full" style="background-color: #FFD800;"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto">
                    @foreach([
                            [
                                'title' => 'Satu Desa Satu Sarjana (SDSS)',
                                'subtitle' => 'Melibatkan Desa, Kecamatan & DPMD',
                                'color' => '#2B5C92',
                                'bg' => 'bg-primary-light',
                                'text' => 'text-primary-dark',
                                'steps' => [
                                    'Calon mahasiswa mendaftar online melalui portal Beasiswa.',
                                    'Verifikasi berkas & dokumen oleh Tim OPD terkait.',
                                    'Penilaian SPK, Perangkingan, & Penetapan calon terbaik tingkat Desa.',
                                    'Unggah Surat Rekomendasi Kades & Persetujuan Paralel Kecamatan / DPMD.',
                                    'Penetapan akhir & penerbitan SK Penerima oleh Bupati Blitar.',
                                ]
                            ],
                            [
                                'title' => 'Berdaya Berjaya',
                                'subtitle' => 'Mahasiswa Baru & Lama + Wawancara',
                                'color' => '#059669',
                                'bg' => 'bg-green-100',
                                'text' => 'text-green-700',
                                'steps' => [
                                    'Calon mendaftar online (Jalur Mahasiswa Baru / Lama).',
                                    'Verifikasi berkas & validasi data oleh Tim OPD / Dispora.',
                                    'Pelaksanaan tes wawancara & input nilai oleh Tim Seleksi.',
                                    'Perhitungan skor SPK kumulatif (Kriteria Penilaian + Wawancara).',
                                    'Pemeringkatan akhir & penetapan SK oleh Bupati Blitar.',
                                ]
                            ],
                            [
                                'title' => 'Bantuan Biaya Pendidikan (BBP)',
                                'subtitle' => 'Jalur Prestasi & Kurang Mampu',
                                'color' => '#7C3AED',
                                'bg' => 'bg-purple-100',
                                'text' => 'text-purple-700',
                                'steps' => [
                                    'Calon mendaftar online melalui Jalur Prestasi atau Kurang Mampu.',
                                    'Verifikasi kelengkapan dokumen persyaratan oleh Tim Seleksi.',
                                    'Penilaian dan perhitungan skor akhir peserta.',
                                    'Penyusunan peringkat (ranking) berdasarkan total nilai tertinggi.',
                                    'Penetapan akhir & penerbitan SK Penerima oleh Bupati Blitar.',
                                ]
                            ],
                        ] as $alur)
                        <div class="card h-full flex flex-col justify-between">
                            <div class="card-body space-y-5">
                                <div class="border-b border-slate-100 pb-3">
                                    <h3 class="font-extrabold text-lg" style="color: {{ $alur['color'] }};">
                                        {{ $alur['title'] }}</h3>
                                    <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $alur['subtitle'] }}</p>
                                </div>
                                <ol class="space-y-4 text-sm text-slate-600">
                                    @foreach($alur['steps'] as $i => $step)
                                        <li class="flex items-start gap-3">
                                            <span
                                                class="w-6 h-6 rounded-full {{ $alur['bg'] }} {{ $alur['text'] }} font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">{{ $i + 1 }}</span>
                                            <span>{{ $step }}</span>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>