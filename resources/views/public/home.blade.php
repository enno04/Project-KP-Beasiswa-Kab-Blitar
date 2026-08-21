<x-layouts.public :title="'Beranda — Beasiswa Blitar Mengabdi'">

    {{-- ═══ HERO SECTION ═══ --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-50 via-primary-light/30 to-white">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute inset-0 bg-no-repeat bg-center bg-cover opacity-100 pointer-events-none"
                style="background-image: url('{{ asset('images/bg-beranda.png') }}');">
            </div>
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary-light/40 rounded-full"></div>
            <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-amber-100/30 rounded-full"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Text Column --}}
                <div class="animate-fade-in">
                    <h1
                        class="text-4xl lg:text-5xl xl:text-6xl font-extrabold text-slate-900 leading-tight tracking-tight mb-6">
                        Beasiswa<br>
                        <span class="bg-gradient-to-r from-primary to-primary-dark bg-clip-text text-transparent">Blitar
                            Mengabdi</span>
                    </h1>

                    <p class="text-lg text-white leading-relaxed mb-8 max-w-xl">
                        Meningkatkan kualitas sumber daya manusia Kabupaten Blitar yang berpendidikan baik dan berdaya
                        saing untuk menyongsong Indonesia Emas.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 mb-8">
                        <a href="{{ route('pendaftaran.index') }}" class="btn btn-lg btn-primary shadow-lg">
                            <i data-lucide="edit-3" class="w-5 h-5"></i> Daftar Sekarang
                        </a>
                        <a href="{{ route('cek.status') }}"
                            class="btn btn-lg bg-white/20 hover:bg-white/30 text-white border-2 border-white/70 font-bold shadow-xl flex items-center gap-2.5">
                            <i data-lucide="search" class="w-5 h-5 text-white"></i> Cek Status & Cetak Bukti
                        </a>
                    </div>
                </div>

                {{-- Visual Column: Transparent Full-Image Slider --}}
                <div class="hidden lg:flex items-center justify-center animate-slide-up">
                    <div class="relative w-full max-w-xl" x-data="{ 
                            activeSlide: 0, 
                            slides: [
                                '{{ asset('images/slide1.png') }}',
                                '{{ asset('images/slide2.png') }}',
                                '{{ asset('images/slide3.png') }}'
                            ],
                            timer: null,
                            startAutoPlay() {
                                this.timer = setInterval(() => {
                                    this.activeSlide = (this.activeSlide + 1) % this.slides.length;
                                }, 4000);
                            },
                            stopAutoPlay() {
                                clearInterval(this.timer);
                            }
                         }" x-init="startAutoPlay()" @mouseenter="stopAutoPlay()" @mouseleave="startAutoPlay()">

                        {{-- Transparent Main Slider Container --}}
                        <div class="relative w-full overflow-hidden">
                            <div
                                class="relative h-[420px] w-full overflow-hidden rounded-2xl flex items-center justify-center">
                                <template x-for="(slide, index) in slides" :key="index">
                                    <div x-show="activeSlide === index"
                                        x-transition:enter="transition ease-out duration-700 transform"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-500 transform absolute inset-0"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-105"
                                        class="absolute inset-0 w-full h-full flex items-center justify-center">
                                        <img :src="slide" :alt="'Slide Beasiswa Blitar ' + (index + 1)"
                                            class="w-full h-full object-contain drop-shadow-xl rounded-2xl">
                                    </div>
                                </template>

                                {{-- Navigation Arrows --}}
                                <button @click="activeSlide = (activeSlide - 1 + slides.length) % slides.length"
                                    class="absolute left-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-slate-900/50 hover:bg-slate-900/80 text-white flex items-center justify-center transition-all z-10 shadow-lg border border-white/20">
                                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                                </button>
                                <button @click="activeSlide = (activeSlide + 1) % slides.length"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-slate-900/50 hover:bg-slate-900/80 text-white flex items-center justify-center transition-all z-10 shadow-lg border border-white/20">
                                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                                </button>
                            </div>

                            {{-- Dot Indicators --}}
                            <div class="flex items-center justify-center gap-2 pt-4">
                                <template x-for="(slide, index) in slides" :key="index">
                                    <button @click="activeSlide = index"
                                        class="h-2.5 rounded-full transition-all duration-300 shadow-sm"
                                        :class="activeSlide === index ? 'w-8 bg-amber-400' : 'w-2.5 bg-white/60 hover:bg-white'">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ STATS BAR ═══ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 relative z-10">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-md p-6 sm:p-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                @php
                    $statItems = [
                        ['value' => $programs->count(), 'label' => 'Program Beasiswa', 'icon' => 'tag'],
                        ['value' => $programs->flatMap->jalurs->count(), 'label' => 'Jalur Tersedia', 'icon' => 'git-branch'],
                        ['value' => $periodeAktif ? $periodeAktif->tahun : date('Y'), 'label' => 'Periode Aktif', 'icon' => 'calendar'],
                        ['value' => 'Online', 'label' => 'Sistem Pendaftaran', 'icon' => 'globe'],
                    ];
                @endphp
                @foreach($statItems as $stat)
                    <div class="text-center group">
                        <div
                            class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-primary-light text-primary mb-3 group-hover:scale-110 transition-transform">
                            <i data-lucide="{{ $stat['icon'] }}" class="w-5 h-5"></i>
                        </div>
                        <p class="text-2xl lg:text-3xl font-extrabold text-slate-900">{{ $stat['value'] }}</p>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">{{ $stat['label'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══ MAIN CONTENT WITH BACKGROUND ═══ --}}
    <div class="bg-cover bg-center bg-fixed" style="background-image: url('{{ asset('images/background.jpeg') }}')">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 space-y-20">

            {{-- ═══ PROGRAM BEASISWA ═══ --}}
            <section class="lenis-reveal">
                <div class="text-center mb-14">
                    <p class="text-sm font-bold text-primary uppercase tracking-widest mb-3">Program Kami</p>
                    <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900">Program Beasiswa</h2>
                    <p class="text-slate-600 mt-3 max-w-2xl mx-auto">Pilih program beasiswa yang sesuai dengan
                        kualifikasi Anda</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($programs as $index => $prog)
                        <div
                            class="card group hover:shadow-xl hover:-translate-y-1 transition-all duration-300 lenis-reveal lenis-delay-{{ min($index + 1, 4) }}">
                            <div class="card-body">
                                <div class="flex items-start gap-5">
                                    <div
                                        class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 bg-primary-light text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                                        <i data-lucide="{{ $prog->isSdss() ? 'graduation-cap' : 'award' }}"
                                            class="w-7 h-7"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2 mb-2">
                                            <h3 class="font-bold text-lg text-slate-900">{{ $prog->nama }}</h3>
                                            @if($prog->status_pendaftaran === 'Sedang Dibuka')
                                                <x-badge type="success" dot>Buka</x-badge>
                                            @else
                                                <x-badge type="muted">Tutup</x-badge>
                                            @endif
                                        </div>
                                        <p class="text-sm text-slate-500 mb-4 line-clamp-2">
                                            {{ $prog->deskripsi ?? 'Program Beasiswa Pemerintah Kabupaten Blitar.' }}
                                        </p>

                                        <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2">
                                                Jalur Tersedia</p>
                                            <div class="flex flex-wrap gap-2">
                                                @forelse($prog->jalurs as $jalur)
                                                    <span
                                                        class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-medium text-slate-700 shadow-xs">{{ $jalur->nama }}</span>
                                                @empty
                                                    <span class="text-xs italic text-slate-400">Belum ada jalur</span>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full">
                            <x-empty-state icon="folder-open" title="Belum Ada Program"
                                text="Program beasiswa belum dikonfigurasi untuk periode ini." />
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- ═══ STATISTIK PENDAFTAR ═══ --}}
            <section class="lenis-reveal">
                <div class="text-center mb-14">
                    <p class="text-sm font-bold text-primary uppercase tracking-widest mb-3">Statistik</p>
                    <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900">TOTAL PENDAFTAR SAAT INI</h2>
                    <p class="text-slate-600 mt-3 max-w-2xl mx-auto">Jumlah pendaftar yang telah menyelesaikan pendaftaran pada masing-masing program beasiswa (Periode {{ $periodeAktif->tahun ?? date('Y') }})</p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($programs as $index => $prog)
                        <div class="card relative overflow-hidden group hover:-translate-y-1 hover:shadow-xl transition-all duration-300 lenis-reveal lenis-delay-{{ min($index + 1, 4) }}">
                            <div class="absolute -right-6 -top-6 w-32 h-32 bg-gradient-to-br from-primary-light/50 to-primary/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                            <div class="absolute -left-6 -bottom-6 w-24 h-24 bg-amber-100/40 rounded-full blur-xl group-hover:scale-150 transition-transform duration-700"></div>
                            
                            <div class="card-body p-6 flex flex-col items-center text-center relative z-10 h-full">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-white mb-5 shadow-lg shadow-primary/30 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                                    <i data-lucide="{{ $prog->isSdss() ? 'users' : 'award' }}" class="w-8 h-8"></i>
                                </div>
                                <h3 class="font-bold text-slate-800 mb-2 leading-snug flex-1">{{ $prog->nama }}</h3>
                                <div class="pt-5 mt-auto flex flex-col items-center w-full border-t border-slate-100 border-dashed">
                                    <span class="text-4xl font-extrabold bg-gradient-to-r from-primary-dark to-primary bg-clip-text text-transparent">{{ number_format($prog->pendaftarans_count) }}</span>
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1">Mahasiswa / Siswa</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full">
                            <x-empty-state icon="bar-chart-2" title="Belum Ada Data" text="Belum ada data pendaftar untuk ditampilkan." />
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- ═══ ALUR SELEKSI ═══ --}}
            <section class="lenis-reveal">
                <div class="text-center mb-14">
                    <p class="text-sm font-bold text-primary uppercase tracking-widest mb-3">Tahapan</p>
                    <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900">Alur Seleksi</h2>
                    <p class="text-slate-600 mt-3 max-w-2xl mx-auto">Tahapan seleksi pendaftaran Beasiswa Blitar
                        Mengabdi</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                    @foreach([
                            ['icon' => 'laptop', 'title' => 'Pendaftaran Online', 'desc' => 'Peserta mengisi formulir dan mengunggah dokumen persyaratan.', 'num' => '01'],
                            ['icon' => 'shield-check', 'title' => 'Verifikasi Dokumen', 'desc' => 'Pemeriksaan keabsahan dokumen oleh Tim Verifikator.', 'num' => '02'],
                            ['icon' => 'clipboard-list', 'title' => 'Penilaian Kriteria', 'desc' => 'Pemberian bobot nilai terhadap kriteria SPK.', 'num' => '03'],
                            ['icon' => 'bar-chart-2', 'title' => 'Pemeringkatan', 'desc' => 'Perankingan otomatis menggunakan Model Penilaian Berbobot.', 'num' => '04'],
                            ['icon' => 'award', 'title' => 'Penetapan', 'desc' => 'Penetapan penerima berdasarkan kuota dan peringkat.', 'num' => '05'],
                        ] as $i => $step)
                        <div
                            class="card group text-center hover:-translate-y-2 hover:shadow-xl transition-all duration-300 relative overflow-hidden lenis-reveal lenis-delay-{{ min($i + 1, 5) }}">
                            <div
                                class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary to-primary-dark transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-500">
                            </div>
                            <div class="card-body py-8">
                                <span class="text-xs font-extrabold text-slate-300 mb-3 block">{{ $step['num'] }}</span>
                                <div
                                    class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-5 bg-primary-light text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                                    <i data-lucide="{{ $step['icon'] }}" class="w-7 h-7"></i>
                                </div>
                                <h3 class="font-bold text-sm text-slate-900 mb-2">{{ $step['title'] }}</h3>
                                <p class="text-xs text-slate-500 leading-relaxed">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

        </div>
    </div>

    {{-- ═══ CTA SECTION ═══ --}}
    <section class="py-16 relative overflow-hidden lenis-reveal"
        style="background: linear-gradient(135deg, #2B5C92, #0C1446);">
        <div class="animate-fade-in">
            <div class="absolute -top-20 -right-20 w-72 h-72 bg-white/5 rounded-full"></div>
            <div class="absolute -bottom-20 -left-20 w-72 h-72 bg-white/5 rounded-full"></div>
            <!-- Icon PNG kiri -->
            <img src="{{ asset('images/icon2.png') }}" class="absolute left-10 bottom-0 w-80 opacity-100" alt="">

            <!-- Icon PNG kanan -->
            <img src="{{ asset('images/icon-1.png') }}" class="absolute right-10 bottom-0 w-80 opacity-100" alt="">
        </div>

        <div class="relative max-w-3xl mx-auto px-4 text-center text-white">
            <h2 class="text-3xl lg:text-4xl font-extrabold mb-4">Jangan Lewatkan Kesempatan Ini</h2>
            <p class="text-primary-light mb-8 text-lg">Daftarkan diri Anda sekarang dan raih masa depan lebih cerah
                bersama Beasiswa Blitar Mengabdi.</p>
            <a href="{{ route('pendaftaran.index') }}"
                class="btn btn-lg font-extrabold shadow-xl hover:scale-105 transition-all"
                style="background-color: #FFD800; color: #0C1446;">
                <i data-lucide="edit-3" class="w-5 h-5"></i> Daftar Sekarang
            </a>
        </div>
    </section>

</x-layouts.public>