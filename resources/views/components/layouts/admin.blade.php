@props(['title' => 'Dashboard'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — Beasiswa Blitar Mengabdi</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-kab-blitar.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-kab-blitar.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen" style="background-color: var(--color-background);" x-data="{ sidebarOpen: false }">

    {{-- ═══ TOPBAR ═══ --}}
    <header class="fixed top-0 left-0 right-0 z-40 h-16 bg-white/95 backdrop-blur-md border-b border-slate-200/80 flex items-center justify-between px-4 lg:px-6" style="box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 text-slate-500 transition-colors">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
            @php
                $roleKode = auth()->user()->getRoleKode();
                $dashboardRoute = match($roleKode) {
                    'super_admin' => 'super-admin.dashboard',
                    'admin_kabupaten' => 'kabupaten.dashboard',
                    'admin_opd' => 'opd.dashboard',
                    'admin_kecamatan' => 'kecamatan.dashboard',
                    'admin_desa' => 'desa.dashboard',
                    'admin_dpmd' => 'dpmd.dashboard',
                    default => 'login'
                };
            @endphp
            <a href="{{ route($dashboardRoute) }}" class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-extrabold text-xs shadow-sm" style="background: linear-gradient(135deg, #2B5C92, #0C1446);">BM</div>
                <span class="font-extrabold text-sm hidden sm:inline tracking-tight" style="color: #2B5C92;">Beasiswa Blitar</span>
            </a>
        </div>
        <div class="flex items-center gap-3">
            <span class="badge badge-primary text-xs">{{ auth()->user()->role_label }}</span>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-slate-50 transition-colors">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background: linear-gradient(135deg, #2B5C92, #0C1446);">
                        {{ strtoupper(substr(auth()->user()->nama, 0, 2)) }}
                    </div>
                    <span class="text-sm font-medium text-slate-700 hidden sm:inline">{{ auth()->user()->nama }}</span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400" :class="open ? 'rotate-180' : ''" style="transition: transform 200ms;"></i>
                </button>
                <div x-show="open" @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl border border-slate-100 py-1.5 z-50" style="box-shadow: 0 10px 40px rgba(0,0,0,0.1); display: none;">
                    <div class="px-4 py-2.5 border-b border-slate-100">
                        <p class="text-xs font-bold text-slate-900">{{ auth()->user()->nama }}</p>
                        <p class="text-[11px] text-slate-400">{{ auth()->user()->role_label }}</p>
                    </div>
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                        <i data-lucide="globe" class="w-4 h-4 text-slate-500"></i> Halaman Publik
                    </a>
                    <a href="{{ route('password.change') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                        <i data-lucide="key-round" class="w-4 h-4 text-slate-500"></i> Ubah Password
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- ═══ SIDEBAR OVERLAY ═══ --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/30 backdrop-blur-sm z-40 lg:hidden" x-transition.opacity style="display: none;"></div>

    {{-- ═══ SIDEBAR ═══ --}}
    <aside class="fixed top-0 left-0 bottom-0 z-50 w-[264px] bg-white border-r border-slate-200/80 flex flex-col transition-transform duration-300 lg:top-16 -translate-x-full lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full lg:translate-x-0 lg:shadow-none'">
        
        {{-- Mobile Sidebar Header --}}
        <div class="h-16 flex items-center justify-between px-4 border-b border-slate-100 lg:hidden shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-extrabold text-[10px] shadow-sm" style="background: linear-gradient(135deg, #2B5C92, #0C1446);">BM</div>
                <span class="font-extrabold text-sm tracking-tight" style="color: #2B5C92;">Menu Admin</span>
            </div>
            <button @click="sidebarOpen = false" class="p-1.5 rounded-lg bg-slate-50 hover:bg-red-50 text-slate-500 hover:text-red-500 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <nav class="p-4 space-y-1 flex-1 overflow-y-auto">
            @php $roleKode = auth()->user()->getRoleKode(); @endphp

            {{-- Dashboard --}}
            <a href="{{ route($dashboardRoute) }}" class="sidebar-link {{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
            </a>

            {{-- ── SUPER ADMIN ── --}}
            @if($roleKode === 'super_admin')
                <div class="sidebar-section">Master Referensi</div>
                <a href="{{ route('super-admin.master.users.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.master.users.*') ? 'active' : '' }}">
                    <i data-lucide="users" class="w-5 h-5"></i> Pengguna
                </a>
                <a href="{{ route('super-admin.master.opd.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.master.opd.*') ? 'active' : '' }}">
                    <i data-lucide="building-2" class="w-5 h-5"></i> OPD
                </a>
                <a href="{{ route('super-admin.master.kecamatan.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.master.kecamatan.*') ? 'active' : '' }}">
                    <i data-lucide="map" class="w-5 h-5"></i> Kecamatan
                </a>
                <a href="{{ route('super-admin.master.desa.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.master.desa.*') ? 'active' : '' }}">
                    <i data-lucide="map-pin" class="w-5 h-5"></i> Desa
                </a>

                <div class="sidebar-section">Konfigurasi</div>
                <a href="{{ route('super-admin.master.periode.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.master.periode.*') ? 'active' : '' }}">
                    <i data-lucide="calendar" class="w-5 h-5"></i> Periode
                </a>
                <a href="{{ route('super-admin.master.program.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.master.program.*') ? 'active' : '' }}">
                    <i data-lucide="tag" class="w-5 h-5"></i> Program Beasiswa
                </a>
                <a href="{{ route('super-admin.master.dokumen-publik.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.master.dokumen-publik.*') ? 'active' : '' }}">
                    <i data-lucide="file-text" class="w-5 h-5"></i> Dokumen Publik
                </a>
                <a href="{{ route('super-admin.master.faq.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.master.faq.*') ? 'active' : '' }}">
                    <i data-lucide="message-square" class="w-5 h-5"></i> FAQ (Tanya Jawab)
                </a>

                <div class="sidebar-section">Monitoring</div>
                <a href="{{ route('super-admin.monitoring.pendaftaran') }}" class="sidebar-link {{ request()->routeIs('super-admin.monitoring.pendaftaran') ? 'active' : '' }}">
                    <i data-lucide="activity" class="w-5 h-5"></i> Monitoring Pendaftaran
                </a>
                <a href="{{ route('super-admin.histori') }}" class="sidebar-link {{ request()->routeIs('super-admin.histori') ? 'active' : '' }}">
                    <i data-lucide="history" class="w-5 h-5"></i> Histori Penerima
                </a>
                <a href="{{ route('super-admin.audit-log') }}" class="sidebar-link {{ request()->routeIs('super-admin.audit-log') ? 'active' : '' }}">
                    <i data-lucide="scroll-text" class="w-5 h-5"></i> Audit Log
                </a>

                <div class="sidebar-section">Manajemen Sistem</div>
                <a href="{{ route('super-admin.pembersihan-data.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.pembersihan-data.*') ? 'active' : '' }}">
                    <i data-lucide="trash-2" class="w-5 h-5"></i> Pembersihan Data
                </a>
                <a href="{{ route('super-admin.data-dummy.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.data-dummy.*') ? 'active' : '' }}">
                    <i data-lucide="flask-conical" class="w-5 h-5"></i> Generator Dummy
                </a>
                <a href="{{ route('super-admin.pengaturan.index') }}" class="sidebar-link {{ request()->routeIs('super-admin.pengaturan.*') ? 'active' : '' }}">
                    <i data-lucide="settings" class="w-5 h-5"></i> Pengaturan Web
                </a>
            @endif

            {{-- ── ADMIN KABUPATEN ── --}}
            @if($roleKode === 'admin_kabupaten')
                @php $programs = \App\Models\Program::with('jalurs')->aktif()->orderBy('urutan')->get(); @endphp
                <div class="sidebar-section">Program Beasiswa</div>
                @foreach($programs as $prog)
                    @if($prog->jalurs->count() <= 1)
                        <a href="{{ route('kabupaten.program.index', $prog->slug) }}" class="sidebar-link {{ request()->is('kabupaten/program/' . $prog->slug . '*') ? 'active' : '' }}">
                            <i data-lucide="graduation-cap" class="w-5 h-5 shrink-0"></i> <span class="leading-tight">{{ $prog->nama }}</span>
                        </a>
                    @else
                        @foreach($prog->jalurs as $jalur)
                            <a href="{{ route('kabupaten.program.index', [$prog->slug, $jalur->slug]) }}" class="sidebar-link {{ request()->is('kabupaten/program/' . $prog->slug . '/' . $jalur->slug . '*') ? 'active' : '' }}">
                                <i data-lucide="graduation-cap" class="w-5 h-5 shrink-0"></i> <span class="leading-tight">{{ $prog->nama }} — {{ $jalur->nama }}</span>
                            </a>
                        @endforeach
                    @endif
                @endforeach

                <div class="sidebar-section">Lainnya</div>
                <a href="{{ route('kabupaten.rekap-data') }}" class="sidebar-link {{ request()->routeIs('kabupaten.rekap-data') ? 'active' : '' }}">
                    <i data-lucide="filter" class="w-5 h-5 shrink-0"></i> Rekap & Filter Lanjutan
                </a>
                <a href="{{ route('kabupaten.riwayat-penetapan') }}" class="sidebar-link {{ request()->routeIs('kabupaten.riwayat-penetapan') ? 'active' : '' }}">
                    <i data-lucide="history" class="w-5 h-5 shrink-0"></i> Riwayat Penetapan
                </a>
            @endif

            {{-- ── ADMIN OPD ── --}}
            @if($roleKode === 'admin_opd')
                <div class="sidebar-section">Verifikasi</div>
                <a href="{{ route('opd.verifikasi.index') }}" class="sidebar-link {{ request()->routeIs('opd.verifikasi.*') ? 'active' : '' }}">
                    <i data-lucide="file-check" class="w-5 h-5"></i> Verifikasi Dokumen
                </a>
                <a href="{{ route('opd.riwayat') }}" class="sidebar-link {{ request()->routeIs('opd.riwayat') ? 'active' : '' }}">
                    <i data-lucide="history" class="w-5 h-5"></i> Riwayat Verifikasi
                </a>
            @endif

            {{-- ── ADMIN KECAMATAN ── --}}
            @if($roleKode === 'admin_kecamatan')
                @php $programs = \App\Models\Program::with('jalurs')->aktif()->where('kode', 'sdss')->orderBy('urutan')->get(); @endphp
                <div class="sidebar-section">Program Beasiswa</div>
                @foreach($programs as $prog)
                    <a href="{{ route('kecamatan.program.index', $prog->slug) }}" class="sidebar-link {{ request()->is('kecamatan/program/' . $prog->slug . '*') ? 'active' : '' }}">
                        <i data-lucide="graduation-cap" class="w-5 h-5"></i> {{ $prog->nama }}
                    </a>
                @endforeach
                
                <div class="sidebar-section mt-4">Lainnya</div>
                <a href="{{ route('kecamatan.riwayat') }}" class="sidebar-link {{ request()->routeIs('kecamatan.riwayat') ? 'active' : '' }}">
                    <i data-lucide="history" class="w-5 h-5"></i> Riwayat Keputusan
                </a>
            @endif

            {{-- ── ADMIN DESA ── --}}
            @if($roleKode === 'admin_desa')
                @php $programs = \App\Models\Program::with('jalurs')->aktif()->where('kode', 'sdss')->orderBy('urutan')->get(); @endphp
                <div class="sidebar-section">Program Beasiswa</div>
                @foreach($programs as $prog)
                    @if($prog->jalurs->count() <= 1)
                        <a href="{{ route('desa.program.index', $prog->slug) }}" class="sidebar-link {{ request()->is('desa/program/' . $prog->slug . '*') ? 'active' : '' }}">
                            <i data-lucide="graduation-cap" class="w-5 h-5"></i> {{ $prog->nama }}
                        </a>
                    @else
                        @foreach($prog->jalurs as $jalur)
                            <a href="{{ route('desa.program.index', [$prog->slug, $jalur->slug]) }}" class="sidebar-link {{ request()->is('desa/program/' . $prog->slug . '/' . $jalur->slug . '*') ? 'active' : '' }}">
                                <i data-lucide="graduation-cap" class="w-5 h-5"></i> {{ $prog->nama }} — {{ $jalur->nama }}
                            </a>
                        @endforeach
                    @endif
                @endforeach

                <div class="sidebar-section mt-4">Lainnya</div>
                <a href="{{ route('desa.riwayat') }}" class="sidebar-link {{ request()->routeIs('desa.riwayat') ? 'active' : '' }}">
                    <i data-lucide="history" class="w-5 h-5"></i> Riwayat Keputusan
                </a>
            @endif

            {{-- ── ADMIN DPMD ── --}}
            @if($roleKode === 'admin_dpmd')
                @php $programs = \App\Models\Program::with('jalurs')->aktif()->where('kode', 'sdss')->orderBy('urutan')->get(); @endphp
                <div class="sidebar-section">Program SDSS</div>
                @foreach($programs as $prog)
                    @if($prog->jalurs->count() <= 1)
                        <a href="{{ route('dpmd.program.index', $prog->slug) }}" class="sidebar-link {{ request()->is('dpmd/program/' . $prog->slug . '*') ? 'active' : '' }}">
                            <i data-lucide="graduation-cap" class="w-5 h-5"></i> {{ $prog->nama }}
                        </a>
                    @else
                        @foreach($prog->jalurs as $jalur)
                            <a href="{{ route('dpmd.program.index', [$prog->slug, $jalur->slug]) }}" class="sidebar-link {{ request()->is('dpmd/program/' . $prog->slug . '/' . $jalur->slug . '*') ? 'active' : '' }}">
                                <i data-lucide="graduation-cap" class="w-5 h-5"></i> {{ $prog->nama }} — {{ $jalur->nama }}
                            </a>
                        @endforeach
                    @endif
                @endforeach

                <div class="sidebar-section mt-4">Lainnya</div>
                <a href="{{ route('dpmd.riwayat') }}" class="sidebar-link {{ request()->routeIs('dpmd.riwayat') ? 'active' : '' }}">
                    <i data-lucide="history" class="w-5 h-5"></i> Riwayat Keputusan
                </a>
            @endif
        </nav>
  
        {{-- Sidebar Footer --}}
        <div class="p-4 border-t border-slate-100 mt-auto shrink-0 space-y-3">
            <a href="{{ route('home') }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-sm font-bold rounded-xl transition-colors border border-slate-200">
                <i data-lucide="globe" class="w-4 h-4 text-slate-500"></i> Ke Halaman Publik
            </a>
            <div class="bg-slate-50 rounded-xl p-4 text-center">
                <i data-lucide="info" class="w-5 h-5 text-primary mx-auto mb-2"></i>
                <p class="text-xs font-semibold text-slate-700 mb-1">Butuh bantuan?</p>
                <p class="text-[11px] text-slate-400">Hubungi admin Dispora Blitar</p>
            </div>
        </div>
    </aside>

    {{-- ═══ MAIN CONTENT ═══ --}}
    <main class="pt-16 lg:pl-[264px] min-h-screen">
        {{-- Flash Messages (SweetAlert2) --}}
        @if(session('success') || session('error') || session('info') || session('warning'))
            @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    @if(session('success'))
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: `{!! session('success') !!}`,
                            confirmButtonColor: '#10B981',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    @endif
                    @if(session('error'))
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops! Terjadi Kesalahan',
                            html: `{!! session('error') !!}`,
                            confirmButtonColor: '#EF4444',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    @endif
                    @if(session('info'))
                        Swal.fire({
                            icon: 'info',
                            title: 'Informasi',
                            html: `{!! session('info') !!}`,
                            confirmButtonColor: '#3B82F6',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    @endif
                    @if(session('warning'))
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            html: `{!! session('warning') !!}`,
                            confirmButtonColor: '#F59E0B',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    @endif
                });
            </script>
            @endpush
        @endif

        @if($errors->any())
            @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    let errorMessages = '';
                    @foreach($errors->all() as $error)
                        errorMessages += '&bull; {{ addslashes($error) }}<br>';
                    @endforeach
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        html: `<div class="text-left text-sm text-slate-600">${errorMessages}</div>`,
                        confirmButtonColor: '#EF4444',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                });
            </script>
            @endpush
        @endif

        <div class="p-6">
            {{ $slot }}
        </div>
    </main>

    {{-- ═══ SESSION TIMEOUT WARNING ═══ --}}
    <div x-data="sessionTimeoutWarning({{ config('session.lifetime') }})" x-cloak>
        <div x-show="showWarning" 
             x-transition.opacity.duration.300ms
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4"
             style="display: none;">
            <div @click.away="extendSession" class="bg-white rounded-2xl p-6 shadow-2xl max-w-sm w-full text-center border border-slate-100"
                 x-transition:enter="transition ease-out duration-300" 
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                <div class="w-16 h-16 bg-yellow-100 text-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-extrabold text-slate-900 mb-2">Sesi Hampir Habis</h3>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed">
                    Sesi login Anda akan otomatis berakhir dalam <strong class="text-red-500 font-bold"><span x-text="timeLeft"></span> detik</strong> karena tidak ada aktivitas.<br>Apakah Anda masih di sini?
                </p>
                <div class="flex gap-3 justify-center">
                    <button @click="logout" class="flex-1 px-4 py-2.5 text-sm font-bold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">Keluar</button>
                    <button @click="extendSession" class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-[#2B5C92] hover:bg-[#1a3d63] rounded-xl transition-colors shadow-md">Tetap Login</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
        document.addEventListener('alpine:init', () => { 
            setTimeout(() => lucide.createIcons(), 100); 

            Alpine.data('sessionTimeoutWarning', (lifetimeMinutes) => ({
                lifetimeMs: lifetimeMinutes * 60 * 1000,
                warningThresholdMs: 5 * 60 * 1000, // Tampil 5 menit sebelum habis
                timer: null,
                countdown: null,
                showWarning: false,
                timeLeft: 0,
                
                init() {
                    // Untuk testing: Jika lifetime sangat singkat (misal 1 menit), munculkan di pertengahan waktu
                    if (this.lifetimeMs <= 5 * 60 * 1000) {
                        this.warningThresholdMs = Math.floor(this.lifetimeMs / 2);
                    }
                    this.startTimer();
                },
                startTimer() {
                    this.showWarning = false;
                    let timeUntilWarning = this.lifetimeMs - this.warningThresholdMs;
                    this.timer = setTimeout(() => {
                        this.showWarningModal();
                    }, timeUntilWarning);
                },
                showWarningModal() {
                    this.showWarning = true;
                    this.timeLeft = Math.floor(this.warningThresholdMs / 1000);
                    setTimeout(() => lucide.createIcons(), 50);
                    
                    this.countdown = setInterval(() => {
                        this.timeLeft--;
                        if (this.timeLeft <= 0) {
                            clearInterval(this.countdown);
                            this.logout();
                        }
                    }, 1000);
                },
                extendSession() {
                    // Kirim HEAD request ping untuk memperpanjang sesi tanpa me-reload halaman
                    fetch(window.location.href, { method: 'HEAD' })
                        .then(() => {
                            clearInterval(this.countdown);
                            clearTimeout(this.timer);
                            this.startTimer();
                        });
                },
                logout() {
                    window.location.reload(); 
                }
            }));
        });
    </script>
    @stack('scripts')
</body>
</html>
