<x-layouts.public :title="'Informasi Umum — Beasiswa Blitar Mengabdi'">
    {{-- Page Hero --}}
    <section class="bg-cover bg-center bg-no-repeat border-b border-slate-200"
        style="background-image: url('{{ asset('images/informasi.png') }}');">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <p class="text-sm font-bold text-white uppercase tracking-widest mb-3">Panduan & Juknis Resmi</p>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">Informasi General & Ketentuan Program</h1>
            <p class="text-white mt-3 max-w-2xl mx-auto">Pelajari maksud, tujuan, kriteria sasaran penerima, besaran bantuan, hingga pengunduhan berkas Juknis & Surat Pernyataan resmi Beasiswa Blitar Mengabdi.</p>
        </div>
    </section>

    <div class="bg-cover bg-center md:bg-fixed" style="background-image: url('{{ asset('images/background.jpeg') }}')">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
            {{-- Maksud & Tujuan --}}
            <div class="card">
                <div class="card-body grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                    <div class="space-y-5">
                        <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900">Maksud & Tujuan</h2>
                        <div class="w-16 h-1 rounded-full" style="background-color: #FFD800;"></div>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Meningkatkan kualitas Sumber Daya Manusia Kabupaten Blitar yang inklusif, berakhlak, sehat
                            jasmani
                            dan rohani, berpendidikan baik, serta berdaya saing guna optimalisasi potensi pemuda
                            menyongsong
                            Indonesia Emas.
                        </p>
                    </div>
                    <div class="bg-primary-light rounded-xl p-7 border border-primary-light">
                        <h4 class="font-bold text-primary-dark mb-4 uppercase tracking-widest text-xs">Tujuan Khusus
                        </h4>
                        <ul class="space-y-3.5 text-sm text-slate-700">
                            @foreach([
                                    'Memperluas kesempatan belajar dan meningkatkan mutu relevansi lulusan perguruan tinggi.',
                                    'Meningkatkan motivasi pemuda Kabupaten Blitar untuk kuliah.',
                                    'Mendorong kontribusi nyata pemuda dalam pembangunan daerah.',
                                ] as $tujuan)
                                <li class="flex items-start gap-3">
                                    <i data-lucide="check" class="w-5 h-5 text-primary shrink-0 mt-0.5"></i>
                                    <span>{{ $tujuan }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Grid: Bidang & Sasaran --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="card">
                    <div class="card-body space-y-5">
                        <h3 class="text-xl font-extrabold text-slate-900">Bidang Non-Akademik Terkait</h3>
                        <div class="w-12 h-1 rounded-full" style="background-color: #FFD800;"></div>
                        <p class="text-xs text-slate-500 leading-relaxed">Meliputi prestasi di bidang-bidang berikut:
                        </p>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach(['Penelitian', 'Teknologi', 'Agama', 'Kebudayaan', 'Olahraga', 'Sosial', 'Lingkungan', 'Nasionalisme'] as $bidang)
                                <div class="flex items-center gap-2.5 text-sm text-slate-700">
                                    <span class="w-2.5 h-2.5 rounded-full bg-primary-light0 shrink-0"></span>
                                    <span>{{ $bidang }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body space-y-5">
                        <h3 class="text-xl font-extrabold text-slate-900">Sasaran Penerima Beasiswa</h3>
                        <div class="w-12 h-1 rounded-full" style="background-color: #FFD800;"></div>
                        <ul class="space-y-3 text-sm text-slate-600">
                            @foreach([
                                    'Warga Kabupaten Blitar (terbukti KTP/KK).',
                                    'Kuliah di PTN/PTS terakreditasi minimal B.',
                                    'Usia maksimal 26 tahun saat mendaftar.',
                                    'Bukan putra/putri ASN, TNI, POLRI, & BUMN/BUMD.',
                                    'Memiliki keterbatasan ekonomi ATAU prestasi yang memadai.',
                                    'Berprestasi.',
                                    'Tidak sedang menerima Beasiswa dari Pemerintah Pusat/Daerah.',
                                    'Belum pernah lulus program studi sarjana.',
                                ] as $s)
                                <li class="flex items-center gap-3">
                                    <i data-lucide="check-circle" class="w-4 h-4 text-green-500 shrink-0"></i>
                                    <span>{{ $s }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Jangka Waktu, Kuota & Besaran --}}
            <div class="card">
                <div class="card-body text-center space-y-8">
                    <div>
                        <h3 class="text-2xl font-extrabold text-slate-900">Jangka Waktu, Kuota & Besaran</h3>
                        <div class="w-16 h-1 mx-auto mt-3 rounded-full" style="background-color: #FFD800;"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        @foreach([
                                ['icon' => 'calendar', 'color' => '#2B5C92', 'bg' => 'bg-primary-light', 'title' => 'Jangka Waktu Bantuan', 'desc' => 'Program berkelanjutan diberikan maksimal 8 semester (4 tahun) masa studi normal. Bantuan Biaya Pendidikan diberikan sekali.'],
                                ['icon' => 'users', 'color' => '#059669', 'bg' => 'bg-green-50', 'title' => 'Kuota Penerima', 'desc' => 'Disesuaikan dengan kapasitas APBD tiap tahunnya. Jalur SDSS dialokasikan 1 orang per desa/kelurahan.'],
                                ['icon' => 'coins', 'color' => '#D97706', 'bg' => 'bg-amber-50', 'title' => 'Besaran Nominal', 'desc' => 'Bantuan UKT maksimal menyesuaikan ketentuan yang berlaku tiap periode, sesuai jenis program beasiswa.'],
                            ] as $item)
                            <div class="space-y-4">
                                <div class="w-14 h-14 {{ $item['bg'] }} rounded-2xl flex items-center justify-center mx-auto"
                                    style="color: {{ $item['color'] }};">
                                    <i data-lucide="{{ $item['icon'] }}" class="w-7 h-7"></i>
                                </div>
                                <h4 class="font-bold text-slate-900">{{ $item['title'] }}</h4>
                                <p class="text-xs text-slate-600 leading-relaxed">{{ $item['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ═══ DOKUMEN PENTING ═══ --}}
            @if(isset($dokumenPubliks) && $dokumenPubliks->count() > 0)
                <div class="space-y-6 pt-6">
                    <div class="text-center">
                        <p class="text-xs font-bold text-primary uppercase tracking-widest mb-2">Unduhan Resmi</p>
                        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Dokumen Penting</h2>
                        <div class="w-16 h-1 mx-auto mt-3 rounded-full" style="background-color: #FFD800;"></div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-200 bg-white">
                                        <th
                                            class="py-4 px-6 text-xs font-extrabold text-slate-900 uppercase tracking-wider">
                                            NAMA DOKUMEN</th>
                                        <th
                                            class="py-4 px-6 text-xs font-extrabold text-slate-900 uppercase tracking-wider hidden sm:table-cell">
                                            FORMAT</th>
                                        <th
                                            class="py-4 px-6 text-xs font-extrabold text-slate-900 uppercase tracking-wider text-right">
                                            AKSI</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($dokumenPubliks as $index => $doc)
                                        @php
                                            $isEven = $index % 2 === 1;
                                            $ext = strtolower($doc->format_file);
                                            $iconBg = match ($ext) {
                                                'pdf' => 'bg-red-50 text-red-500 border-red-100',
                                                'doc', 'docx' => 'bg-blue-50 text-blue-500 border-blue-100',
                                                default => 'bg-slate-50 text-slate-500 border-slate-100',
                                            };
                                        @endphp
                                        <tr
                                            class="{{ $isEven ? 'bg-slate-100/70' : 'bg-white' }} hover:bg-slate-100/90 transition-colors">
                                            <td class="py-4 px-6">
                                                <div class="flex items-center gap-3.5">
                                                    <div
                                                        class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center border shadow-2xs {{ $iconBg }}">
                                                        <i data-lucide="file-text" class="w-5 h-5"></i>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-bold text-sm text-slate-900 leading-snug">
                                                            {{ $doc->nama }}</h4>
                                                        <p
                                                            class="text-xs text-slate-400 font-semibold uppercase tracking-wider mt-0.5">
                                                            {{ strtoupper($doc->format_file) }} File</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 hidden sm:table-cell">
                                                <span class="text-sm text-slate-500 font-medium">Document
                                                    (.{{ strtolower($doc->format_file) }})</span>
                                            </td>
                                            <td class="py-4 px-6 text-right">
                                                <a href="{{ route('dokumen.publik.download', $doc->id) }}"
                                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition-all shadow-xs"
                                                    target="_blank">
                                                    <i data-lucide="download" class="w-3.5 h-3.5 text-slate-500"></i> Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Contact Person Card --}}
            <div id="pusat-bantuan" class="card bg-white/95 backdrop-blur-md border border-white/80 shadow-md scroll-mt-24">
                <div class="card-body p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-primary-light text-primary flex items-center justify-center shrink-0">
                            <i data-lucide="phone-call" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Pusat Informasi & Layanan Bantuan</p>
                            <div class="text-sm font-semibold text-slate-700 mt-1 flex flex-col gap-1">
                                @foreach($contactPersons ?? [] as $cp)
                                    <div>
                                        {{ $cp['name'] }}: <a href="{{ $cp['wa_link'] ?? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $cp['phone'] ?? '') }}" target="_blank" class="text-primary hover:underline font-bold">{{ $cp['phone'] }}</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row gap-3">
                        @foreach($contactPersons ?? [] as $cp)
                            <a href="{{ $cp['wa_link'] ?? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $cp['phone'] ?? '') }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold text-sm shadow-md transition-all shrink-0">
                                <i data-lucide="message-circle" class="w-4 h-4"></i> Chat {{ $cp['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('scroll') === 'bantuan') {
                // Beri sedikit jeda agar halaman selesai me-render komponen visual lainnya (seperti gambar)
                setTimeout(() => {
                    const target = document.getElementById('pusat-bantuan');
                    if (target) {
                        // Hilangkan ?scroll=bantuan dari URL address bar tanpa me-refresh halaman agar rapi
                        window.history.replaceState({}, document.title, window.location.pathname);
                        // Mulai gulir pelan-pelan ke target
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }, 400); 
            }
        });
    </script>
    @endpush
</x-layouts.public>