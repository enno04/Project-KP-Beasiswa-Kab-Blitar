@props(['title' => 'Beasiswa Blitar Mengabdi'])
<!DOCTYPE html>
<html lang="id" class="overflow-x-hidden w-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="description" content="Sistem Beasiswa Blitar Mengabdi - Program beasiswa Pemerintah Kabupaten Blitar">
    <title>{{ $title }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-kab-blitar.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-kab-blitar.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/lenis@1.1.20/dist/lenis.css">
    <script src="https://unpkg.com/lenis@1.1.20/dist/lenis.min.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Mencegah preloader muncul saat pindah halaman biasa (hanya muncul saat Reload/F5)
        if (performance.getEntriesByType('navigation')[0]?.type !== 'reload') {
            document.documentElement.classList.add('skip-preloader');
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .skip-preloader #global-preloader { display: none !important; }
    </style>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="min-h-screen flex flex-col overflow-x-hidden" x-data="{ 
    mobileMenu: false, 
    loaded: false,
    initPreloader() {
        if (document.documentElement.classList.contains('skip-preloader')) {
            this.loaded = true;
            return;
        }
        if (document.readyState === 'complete') { 
            setTimeout(() => this.loaded = true, 200); 
        } else { 
            window.addEventListener('load', () => setTimeout(() => this.loaded = true, 200)); 
        }
    }
}" x-init="initPreloader()"
   @pageshow.window="if ($event.persisted) loaded = true"
   @submit.document="if ($event.target && $event.target.target !== '_blank') loaded = false">

    {{-- ═══ PRELOADER ═══ --}}
    <div id="global-preloader" x-show="!loaded" 
         x-transition:leave="transition-opacity duration-700 ease-in-out" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 flex items-center justify-center bg-white/80 backdrop-blur-md"
         style="z-index: 100;">
        <div class="flex flex-col items-center gap-6">
            <!-- Animation Wrapper -->
            <div class="relative flex items-center justify-center w-24 h-24">
                <!-- Outer glowing ring (Pulse) -->
                <div class="absolute inset-0 rounded-full animate-ping" style="background-color: rgba(225, 235, 245, 0.6); animation-duration: 2s;"></div>
                <!-- Inner spinning dashed ring -->
                <div class="absolute inset-[-8px] rounded-full border-dashed border-blue-600" style="border-width: 3px; animation: spin 3s linear infinite;"></div>
                <!-- Center Logo -->
                <img src="{{ asset('images/logo-kab-blitar.png') }}" class="w-14 h-14 object-contain relative z-10 animate-pulse" alt="Loading">
            </div>
            <!-- Loading Text -->
            <div class="flex flex-col items-center gap-1.5 mt-2">
                <p class="text-xs font-extrabold text-blue-900 uppercase animate-pulse" style="letter-spacing: 0.25em;">Memuat Halaman</p>
                <div class="flex gap-1.5 mt-1">
                    <div class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-bounce" style="animation-delay: 0s"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-bounce" style="animation-delay: 0.15s"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-bounce" style="animation-delay: 0.3s"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ NAVBAR ═══ --}}
    <nav class="sticky top-0 z-50 bg-white border-b border-slate-200/80"
        style="box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Beasiswa Blitar Mengabdi"
                        class="h-12 sm:h-12 lg:h-12 w-auto object-contain">
                </a>

                {{-- Wrapper Kanan untuk Nav & Actions --}}
                <div class="flex items-center gap-2 lg:gap-6">
                    {{-- Desktop Nav --}}
                    <div class="hidden lg:flex items-center gap-1">
                    @php
                        $navItems = [
                            ['route' => 'home', 'label' => 'Beranda', 'is' => 'home'],
                            ['route' => 'informasi', 'label' => 'Informasi & Juknis', 'is' => 'informasi'],
                            ['route' => 'kriteria.persyaratan', 'label' => 'Persyaratan & SPK', 'is' => 'kriteria.persyaratan'],
                            ['route' => 'seleksi.penetapan', 'label' => 'Hasil Seleksi', 'is' => 'seleksi.penetapan'],
                        ];
                    @endphp
                    @foreach($navItems as $nav)
                        <a href="{{ route($nav['route']) }}"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs($nav['is']) ? 'bg-primary-light text-primary-dark font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            {{ $nav['label'] }}
                        </a>
                    @endforeach

                    {{-- Dropdown Pendaftaran --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                            class="px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-1.5 transition-all duration-200 {{ request()->routeIs('pendaftaran.*') || request()->routeIs('cek.status') ? 'bg-primary-light text-primary-dark font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            Pendaftaran
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 transition-transform"
                                :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-slate-100 py-2 z-50">
                            <a href="{{ route('pendaftaran.index') }}"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-primary">
                                <i data-lucide="edit-3" class="w-4 h-4"></i> Formulir Pendaftaran
                            </a>
                            <a href="{{ route('cek.status') }}"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-primary">
                                <i data-lucide="search" class="w-4 h-4"></i> Cek Status & Cetak Bukti
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Right Actions --}}
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-primary">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                class="btn btn-sm btn-outline text-red-500 border-red-200 hover:bg-red-50 hover:border-red-300">
                                Keluar
                            </button>
                        </form>
                    @endauth

                    <button @click="mobileMenu = !mobileMenu"
                        class="lg:hidden p-2 rounded-lg hover:bg-slate-100 text-slate-500">
                        <i data-lucide="menu" class="w-5 h-5" x-show="!mobileMenu"></i>
                        <i data-lucide="x" class="w-5 h-5" x-show="mobileMenu" x-cloak></i>
                    </button>
                </div>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            class="lg:hidden border-t border-slate-100 bg-white" x-cloak>
            <div class="px-4 py-4 space-y-1">
                <a href="{{ route('home') }}"
                    class="block px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('home') ? 'bg-primary-light text-primary-dark' : 'text-slate-600 hover:bg-slate-50' }}">Beranda</a>
                <a href="{{ route('informasi') }}"
                    class="block px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('informasi') ? 'bg-primary-light text-primary-dark' : 'text-slate-600 hover:bg-slate-50' }}">Informasi & Juknis</a>
                <a href="{{ route('kriteria.persyaratan') }}"
                    class="block px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('kriteria.persyaratan') ? 'bg-primary-light text-primary-dark' : 'text-slate-600 hover:bg-slate-50' }}">Persyaratan & SPK</a>
                <a href="{{ route('seleksi.penetapan') }}"
                    class="block px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('seleksi.penetapan') ? 'bg-primary-light text-primary-dark' : 'text-slate-600 hover:bg-slate-50' }}">Hasil Seleksi</a>
                <div class="border-t border-slate-100 pt-2 mt-2">
                    <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Pendaftaran</p>
                    <a href="{{ route('pendaftaran.index') }}"
                        class="block px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('pendaftaran.*') ? 'bg-primary-light text-primary-dark' : 'text-slate-600 hover:bg-slate-50' }}">Formulir Pendaftaran</a>
                    <a href="{{ route('cek.status') }}"
                        class="block px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('cek.status') ? 'bg-primary-light text-primary-dark' : 'text-slate-600 hover:bg-slate-50' }}">Cek Status & Cetak Bukti</a>
                </div>

            </div>
        </div>
    </nav>

    {{-- ═══ GLOBAL ANNOUNCEMENT BANNER (MARQUEE) ═══ --}}
    @if(isset($announcementActive) && $announcementActive == '1' && !empty($announcementText))
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-600 text-white border-b border-indigo-700 overflow-hidden relative z-40 shadow-md">
        <div class="flex items-center w-full py-2">
            <div class="px-3 sm:px-5 shrink-0 border-r border-white/20 bg-black/10 backdrop-blur-sm z-10 flex items-center gap-2 font-bold uppercase tracking-widest text-xs h-full">
                <i data-lucide="megaphone" class="w-4 h-4 animate-pulse text-amber-300"></i> <span class="text-white drop-shadow-sm hidden sm:inline">INFO PENTING</span>
            </div>
            {{-- Seamless Infinite Marquee --}}
            <style>
                .marquee-wrapper { display: flex; overflow: hidden; width: 100%; white-space: nowrap; align-items: center; }
                .marquee-content { display: flex; flex-shrink: 0; animation: scroll-left 90s linear infinite; cursor: default; }
                .marquee-wrapper:hover .marquee-content { animation-play-state: paused; }
                @keyframes scroll-left { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
            </style>
            <div class="marquee-wrapper pl-4">
                <div class="marquee-content font-semibold text-sm tracking-wide text-white/90 drop-shadow-sm">
                    @php
                        $announcementLines = array_filter(array_map('trim', explode("\n", $announcementText)));
                    @endphp
                    @for($i = 0; $i < 10; $i++)
                        @foreach($announcementLines as $line)
                            <span class="flex items-center">
                                {{ $line }}
                                <span class="mx-8 text-white/30">•</span>
                            </span>
                        @endforeach
                    @endfor
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══ FLASH MESSAGES ═══ --}}
    @if(session('success') || session('error') || session('info') || session('warning'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            @if(session('success')) <x-alert type="success" :dismissible="true">{{ session('success') }}</x-alert> @endif
            @if(session('error')) <x-alert type="danger" :dismissible="true">{{ session('error') }}</x-alert> @endif
            @if(session('info')) <x-alert type="info" :dismissible="true">{{ session('info') }}</x-alert> @endif
            @if(session('warning')) <x-alert type="warning" :dismissible="true">{{ session('warning') }}</x-alert> @endif
        </div>
    @endif

    {{-- ═══ MAIN CONTENT ═══ --}}
    <main class="flex-1 bg-cover bg-center md:bg-fixed" style="background-image: url('{{ asset('images/background.jpeg') }}');">
        {{ $slot }}
    </main>

    {{-- ═══ FOOTER ═══ --}}
    <footer class="bg-slate-900 text-white mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                {{-- Brand --}}
                <div>
                    <a href="{{ route('home') }}" class="flex items-center gap-3.5 mb-5 group">
                        <img src="{{ asset('images/logo-kab-blitar.png') }}" alt="Logo Kabupaten Blitar" class="h-11 w-auto object-contain shrink-0">
                        <div class="flex flex-col">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pemerintah Kabupaten Blitar</span>
                            <span class="text-base sm:text-lg font-extrabold text-white tracking-tight group-hover:text-primary-light transition-colors">Beasiswa Blitar Mengabdi</span>
                        </div>
                    </a>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Program beasiswa Pemerintah Kabupaten Blitar untuk mendukung pendidikan putra-putri terbaik
                        daerah demi menyongsong Indonesia Emas.
                    </p>
                </div>

                {{-- Links --}}
                <div>
                    <h3 class="font-bold text-base mb-5">Tautan</h3>
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li><a href="{{ route('home') }}"
                                class="hover:text-white transition-colors flex items-center gap-2"><i
                                    data-lucide="chevron-right" class="w-3 h-3"></i> Beranda</a></li>
                        <li><a href="{{ route('informasi') }}"
                                class="hover:text-white transition-colors flex items-center gap-2"><i
                                    data-lucide="chevron-right" class="w-3 h-3"></i> Informasi & Juknis</a></li>
                        <li><a href="{{ route('kriteria.persyaratan') }}"
                                class="hover:text-white transition-colors flex items-center gap-2"><i
                                    data-lucide="chevron-right" class="w-3 h-3"></i> Persyaratan Berkas & SPK</a></li>
                        <li><a href="{{ route('seleksi.penetapan') }}"
                                class="hover:text-white transition-colors flex items-center gap-2"><i
                                    data-lucide="chevron-right" class="w-3 h-3"></i> Hasil Seleksi & Penetapan</a></li>
                        <li><a href="{{ route('pendaftaran.index') }}"
                                class="hover:text-white transition-colors flex items-center gap-2"><i
                                    data-lucide="chevron-right" class="w-3 h-3"></i> Formulir Pendaftaran</a></li>
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h3 class="font-bold text-base mb-5">Kantor Sekretariat</h3>
                    <div class="space-y-4 text-sm text-slate-400">
                        <div class="flex items-start gap-3">
                            <i data-lucide="map-pin" class="w-4 h-4 shrink-0 mt-0.5 text-primary"></i>
                            <div>
                                <p>Kantor Dinas Kepemudaan dan Olahraga Kab. Blitar</p>
                                <p>{{ $webAddress }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <i data-lucide="clock" class="w-4 h-4 shrink-0 text-primary"></i>
                            <span>{{ $webOperationalHours }}</span>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center shrink-0 border border-slate-700">
                                <i data-lucide="phone" class="w-5 h-5 text-amber-400"></i>
                            </div>
                            <div>
                                <p class="text-sm text-slate-400 mb-1">WhatsApp Only</p>
                                <div class="flex flex-col gap-2">
                                    @foreach($contactPersons ?? [] as $cp)
                                        <a href="{{ $cp['wa_link'] ?? 'https://wa.me/'.preg_replace('/[^0-9]/', '', $cp['phone']) }}" target="_blank" class="text-white hover:text-amber-400 font-bold flex flex-col gap-0.5 transition-colors">
                                            <span>{{ $cp['phone'] }}</span>
                                            <span class="text-xs font-normal text-slate-300">Contact Person: {{ $cp['name'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <a href="{{ $webMapsLink }}" target="_blank"
                            class="inline-flex items-center gap-2 text-primary hover:text-primary-light transition-colors mt-1">
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i> Buka di Google Maps
                        </a>
                        
                        {{-- Social Media Links --}}
                        <div class="flex items-center gap-3 mt-4 pt-4 border-t border-slate-800">
                            @if(!empty($webInstagram))
                                <a href="{{ $webInstagram }}" target="_blank" class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-primary hover:text-white transition-all shadow-sm" title="Instagram">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                                </a>
                            @endif
                            @if(!empty($webLink))
                                <a href="{{ $webLink }}" target="_blank" class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-primary hover:text-white transition-all shadow-sm" title="Website Resmi">
                                    <i data-lucide="globe" class="w-4 h-4"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Copyright Bar --}}
        <div class="border-t border-slate-800">
            <div
                class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row justify-between items-center gap-3">
                <p class="text-xs text-slate-500">
                    &copy; {{ date('Y') }} Pemerintah Kabupaten Blitar. Hak cipta dilindungi.
                </p>
                <div class="flex items-center gap-4">
                    <p class="text-xs text-slate-600">
                        Sistem Beasiswa Blitar Mengabdi
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        lucide.createIcons();
        document.addEventListener('alpine:init', () => { setTimeout(() => lucide.createIcons(), 100); });

        // Initialize Lenis Smooth Scroll Engine
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Lenis !== 'undefined') {
                const lenis = new Lenis({
                    duration: 1.2,
                    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                    smoothWheel: true,
                    touchMultiplier: 2,
                });

                function raf(time) {
                    lenis.raf(time);
                    requestAnimationFrame(raf);
                }

                requestAnimationFrame(raf);

                // Smooth scroll for internal anchor links
                document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                    anchor.addEventListener('click', function (e) {
                        const targetId = this.getAttribute('href');
                        if (targetId && targetId !== '#') {
                            const targetEl = document.querySelector(targetId);
                            if (targetEl) {
                                e.preventDefault();
                                lenis.scrollTo(targetEl, { offset: -80 });
                            }
                        }
                    });
                });
            }

            // Scroll-triggered Reveal Observer
            const observerOptions = {
                root: null,
                rootMargin: '0px 0px -80px 0px',
                threshold: 0.1
            };

            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('lenis-active');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.lenis-reveal, .lenis-reveal-fade').forEach(el => {
                revealObserver.observe(el);
            });
        });
    </script>

    @stack('scripts')
</body>

</html>