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
            @if(in_array(auth()->user()->getRoleKode(), ['admin_desa', 'admin_kecamatan', 'admin_dpmd', 'admin_opd', 'admin_kabupaten']))
                <button type="button" @click="$dispatch('open-faq')" class="flex items-center gap-1.5 bg-gradient-to-r from-amber-400 to-orange-400 hover:from-amber-300 hover:to-orange-300 text-orange-950 px-3 py-1.5 rounded-lg shadow-sm shadow-orange-500/20 transition-all text-xs font-bold border border-orange-300/50 transform hover:-translate-y-0.5">
                    <i data-lucide="book-open-check" class="w-3.5 h-3.5"></i> <span class="hidden sm:inline">Panduan</span>
                </button>
            @endif
            <span class="badge badge-primary text-xs">{{ auth()->user()->role_label }}</span>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-slate-50 transition-colors">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white shadow-sm" style="background: linear-gradient(135deg, #2B5C92, #0C1446);">
                        <i data-lucide="user" class="w-4 h-4"></i>
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

        {{-- Wrapper for Nav + Scroll Indicator --}}
        <div class="flex-1 relative overflow-hidden flex flex-col" x-data="{ 
            canScroll: false,
            checkScroll() {
                if(!this.$refs.nav) return;
                const nav = this.$refs.nav;
                const maxScroll = nav.scrollHeight - nav.clientHeight;
                this.canScroll = maxScroll > 0 && (nav.scrollHeight - nav.scrollTop - nav.clientHeight) > 30;
            }
        }" x-init="
            checkScroll();
            window.addEventListener('resize', () => checkScroll());
            setTimeout(() => checkScroll(), 300);
        ">
            <nav x-ref="nav" @scroll.passive="checkScroll()" class="p-4 pb-12 space-y-1 flex-1 overflow-y-auto overscroll-contain custom-scrollbar">
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
                {{-- (Menu Pembersihan Data dan Generator Dummy disembunyikan. Akses manual via URL) --}}
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

            {{-- Scroll Indicator Overlay --}}
            <div x-cloak x-show="canScroll" 
                 x-transition.opacity.duration.300ms
                 class="absolute bottom-2 left-0 right-0 pointer-events-none flex justify-center z-10">
                 <div class="bg-white/90 backdrop-blur-sm border border-slate-200 text-slate-500 rounded-full p-1.5 shadow-md animate-bounce">
                     <i data-lucide="chevron-down" class="w-4 h-4"></i>
                 </div>
            </div>
        </div>
  
        {{-- Sidebar Footer --}}
        <div class="p-4 border-t border-slate-100 mt-auto shrink-0 space-y-3">
            <a href="{{ route('home') }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-sm font-bold rounded-xl transition-colors border border-slate-200">
                <i data-lucide="globe" class="w-4 h-4 text-slate-500"></i> Ke Halaman Publik
            </a>
            <div class="bg-slate-50 rounded-xl p-4 text-center">
                <i data-lucide="info" class="w-5 h-5 text-primary mx-auto mb-2"></i>
                <p class="text-xs font-semibold text-slate-700 mb-1">Butuh bantuan?</p>
                <p class="text-[11px] text-slate-400">Hubungi Admin Dispora Blitar</p>
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

    @if(auth()->check() && auth()->user()->getRoleKode() === 'admin_desa')
    {{-- Alpine FAQ Component for Admin Desa --}}
    <div x-data="{ showFaq: false, showIntro: false }" 
         x-init="
            let currentSession = '{{ session()->getId() }}';
            if(localStorage.getItem('desa_intro_session') !== currentSession) { 
                setTimeout(() => showIntro = true, 500); 
            }
         "
         @open-faq.window="showFaq = true">
         
         {{-- Intro Modal --}}
         <div x-show="showIntro" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-transition.opacity>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all" @click.away="showIntro = false; localStorage.setItem('desa_intro_session', '{{ session()->getId() }}')">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="info" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Selamat Datang di Dashboard Desa!</h3>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Jika Anda belum mengetahui alur kerja atau tugas sebagai Admin Desa, silakan klik tombol <strong><i data-lucide="book-open-check" class="w-4 h-4 inline-block -mt-1 text-orange-600"></i> Panduan</strong> di bagian bilah menu (header) paling atas.
                    </p>
                    <button type="button" @click="showIntro = false; localStorage.setItem('desa_intro_session', '{{ session()->getId() }}')" class="btn btn-primary w-full py-2.5 font-bold text-base shadow-lg shadow-primary/30">
                        Mengerti
                    </button>
                </div>
            </div>
         </div>

         {{-- FAQ Modal --}}
         <div x-show="showFaq" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-transition.opacity>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden transform transition-all" @click.away="showFaq = false">
                <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="help-circle" class="w-5 h-5 text-emerald-600"></i> Panduan Penggunaan Admin Desa
                    </h3>
                    <button @click="showFaq = false" class="text-slate-400 hover:text-slate-600 bg-slate-100 hover:bg-slate-200 p-1.5 rounded-lg transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {{-- Kolom Kiri: Alur Utama --}}
                        <div class="lg:col-span-2 space-y-6">
                            <h4 class="font-extrabold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                                <span class="text-xl">🚀</span> Alur Kerja Utama Admin Desa
                            </h4>
                            
                            <div class="relative border-l-2 border-emerald-100 ml-3 space-y-8 pb-4">
                                {{-- Step 1 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-emerald-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="download" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">1. Menerima Data & Menunggu Pendaftaran Tutup</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">Anda akan menerima data pendaftar yang status dokumennya sudah diverifikasi oleh OPD. Pada tahap awal ini, Anda hanya perlu <strong>menunggu hingga masa pendaftaran program SDSS ini resmi ditutup</strong>.</p>
                                </div>
                                
                                {{-- Step 2 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-amber-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="edit-3" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">2. Melakukan Penilaian (Tombol Nilai Muncul)</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">Setelah masa pendaftaran ditutup, barulah <strong>tombol Nilai akan muncul</strong> di dashboard Anda. Silakan klik tombol tersebut untuk langsung melakukan penilaian kriteria pada masing-masing pendaftar.</p>
                                </div>

                                {{-- Step 3 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-blue-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="bar-chart-2" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">3. Mencari Kandidat Peringkat Satu</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">Sistem akan memproses seluruh nilai secara otomatis untuk mencari dan menentukan siapa kandidat penerima beasiswa yang berhak menduduki <strong>Peringkat 1 (Satu)</strong> dari desa Anda.</p>
                                </div>

                                {{-- Step 4 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-purple-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="send" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">4. Ekspor Data & Unggah Rekomendasi</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">Terakhir, silakan <strong>Ekspor Data Excel</strong> si penerima beasiswa tersebut, lalu unggah <strong>Surat Rekomendasi Kepala Desa & Berita Acara</strong> ke dalam sistem agar otomatis terkirim ke Kecamatan dan DPMD.</p>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Kolom Kanan: Fitur & Tips --}}
                        <div class="space-y-4">
                            <h4 class="font-extrabold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2 mb-2 lg:mt-0 mt-6">
                                <span class="text-xl">💡</span> Fitur Tambahan
                            </h4>

                            <div class="bg-indigo-50/70 p-4 rounded-xl border border-indigo-100">
                                <h5 class="font-bold text-indigo-900 mb-1.5 flex items-center gap-2">
                                    <i data-lucide="layout-dashboard" class="w-4 h-4 text-indigo-600"></i> Pantau Dashboard
                                </h5>
                                <p class="text-xs text-indigo-800 leading-relaxed">Gunakan halaman awal (Dashboard) untuk memantau ringkasan statistik warga yang sedang diproses. Terdapat juga kolom pencarian cepat (Search) untuk menemukan nama pendaftar tertentu.</p>
                            </div>

                            <div class="bg-amber-50/70 p-4 rounded-xl border border-amber-100">
                                <h5 class="font-bold text-amber-900 mb-1.5 flex items-center gap-2">
                                    <i data-lucide="file-spreadsheet" class="w-4 h-4 text-amber-600"></i> Ekspor Data (Excel)
                                </h5>
                                <p class="text-xs text-amber-800 leading-relaxed">Buka menu Data Pendaftar, klik tombol <strong>Export Data</strong> di pojok kanan atas. Sistem akan mengunduh seluruh rekap warga Anda ke format Excel untuk keperluan arsip cetak desa.</p>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <h5 class="font-bold text-slate-700 mb-1.5 flex items-center gap-2">
                                    <i data-lucide="monitor" class="w-4 h-4 text-slate-500"></i> Tip Perangkat
                                </h5>
                                <p class="text-xs text-slate-600 leading-relaxed">Sangat disarankan mengelola pendaftaran menggunakan <strong>Komputer/Laptop</strong>. Layar yang lebih besar memudahkan pengecekan berkas, sehingga meminimalisir risiko salah klik.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end">
                    <button @click="showFaq = false" class="btn btn-outline bg-white font-medium shadow-sm">Tutup Panduan</button>
                </div>
            </div>
         </div>
    </div>
    @elseif(auth()->check() && auth()->user()->getRoleKode() === 'admin_kecamatan')
    {{-- Alpine FAQ Component for Admin Kecamatan --}}
    <div x-data="{ showFaq: false, showIntro: false }" 
         x-init="
            let currentSession = '{{ session()->getId() }}';
            if(localStorage.getItem('kecamatan_intro_session') !== currentSession) { 
                setTimeout(() => showIntro = true, 500); 
            }
         "
         @open-faq.window="showFaq = true">
         
         {{-- Intro Modal --}}
         <div x-show="showIntro" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-transition.opacity>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all" @click.away="showIntro = false; localStorage.setItem('kecamatan_intro_session', '{{ session()->getId() }}')">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="info" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Selamat Datang di Dashboard Kecamatan!</h3>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Jika Anda belum mengetahui alur kerja atau tugas sebagai Admin Kecamatan, silakan klik tombol <strong><i data-lucide="book-open-check" class="w-4 h-4 inline-block -mt-1 text-orange-600"></i> Panduan</strong> di bagian bilah menu (header) paling atas.
                    </p>
                    <button type="button" @click="showIntro = false; localStorage.setItem('kecamatan_intro_session', '{{ session()->getId() }}')" class="btn btn-primary w-full py-2.5 font-bold text-base shadow-lg shadow-primary/30">
                        Mengerti
                    </button>
                </div>
            </div>
         </div>

         {{-- FAQ Modal --}}
         <div x-show="showFaq" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-transition.opacity>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden transform transition-all" @click.away="showFaq = false">
                <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="help-circle" class="w-5 h-5 text-blue-600"></i> Panduan Penggunaan Admin Kecamatan
                    </h3>
                    <button @click="showFaq = false" class="text-slate-400 hover:text-slate-600 bg-slate-100 hover:bg-slate-200 p-1.5 rounded-lg transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {{-- Kolom Kiri: Alur Utama --}}
                        <div class="lg:col-span-2 space-y-6">
                            <h4 class="font-extrabold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                                <span class="text-xl">🚀</span> Alur Kerja Utama Admin Kecamatan
                            </h4>
                            
                            <div class="relative border-l-2 border-blue-100 ml-3 space-y-8 pb-4">
                                {{-- Step 1 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-emerald-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="inbox" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">1. Memantau & Menerima Usulan Desa</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">Setelah pendaftar ditetapkan sebagai Peringkat 1 oleh Desa dan berkasnya diunggah, data tersebut akan otomatis masuk ke Admin Kecamatan. Buka menu <strong>Data Pendaftar</strong> untuk melihat seluruh usulan dari desa-desa di wilayah Anda.</p>
                                </div>
                                
                                {{-- Step 2 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-amber-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="search" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">2. Mengecek Detail Pendaftar</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">Klik tombol <strong>Detail</strong> pada tabel pendaftar. Anda bertugas untuk melihat ringkasan biodata kandidat dan meninjau dokumen yang telah diunggah oleh pihak desa (seperti Surat Rekomendasi dan Berita Acara).</p>
                                </div>

                                {{-- Step 3 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-blue-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="send" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">3. Meneruskan ke Kabupaten</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">Kecamatan <strong>tidak memiliki kewenangan untuk menolak</strong>. Tugas Anda hanyalah memastikan data bisa dilihat, lalu klik tombol <strong>Setujui / Teruskan</strong> agar berkas usulan pendaftar tersebut secara sistem terkirim ke tingkat Kabupaten.</p>
                                </div>

                                {{-- Step 4 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-purple-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="arrow-right-left" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">4. Persetujuan Paralel Bersama DPMD</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">Penerusan berkas dari Kecamatan berjalan paralel/bersamaan dengan pihak DPMD. Data usulan hanya akan masuk secara utuh ke Kabupaten apabila telah diteruskan oleh <em>Kecamatan DAN DPMD</em> sekaligus.</p>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Kolom Kanan: Fitur & Tips --}}
                        <div class="space-y-4">
                            <h4 class="font-extrabold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2 mb-2 lg:mt-0 mt-6">
                                <span class="text-xl">💡</span> Fitur Tambahan
                            </h4>

                            <div class="bg-indigo-50/70 p-4 rounded-xl border border-indigo-100">
                                <h5 class="font-bold text-indigo-900 mb-1.5 flex items-center gap-2">
                                    <i data-lucide="layout-dashboard" class="w-4 h-4 text-indigo-600"></i> Pantau Status Desa
                                </h5>
                                <p class="text-xs text-indigo-800 leading-relaxed">Melalui halaman Dashboard, Anda bisa melihat desa mana saja di wilayah Anda yang sudah mengusulkan kandidat, dan desa mana yang belum menyelesaikan penilaian/rekomendasi.</p>
                            </div>

                            <div class="bg-amber-50/70 p-4 rounded-xl border border-amber-100">
                                <h5 class="font-bold text-amber-900 mb-1.5 flex items-center gap-2">
                                    <i data-lucide="file-spreadsheet" class="w-4 h-4 text-amber-600"></i> Ekspor Data (Excel)
                                </h5>
                                <p class="text-xs text-amber-800 leading-relaxed">Anda bisa mengunduh rekap pendaftar dalam format Excel untuk pelaporan camat melalui menu Export Data. Tersedia juga opsi export berdasarkan status persetujuannya.</p>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <h5 class="font-bold text-slate-700 mb-1.5 flex items-center gap-2">
                                    <i data-lucide="monitor" class="w-4 h-4 text-slate-500"></i> Tip Perangkat
                                </h5>
                                <p class="text-xs text-slate-600 leading-relaxed">Sangat disarankan mengelola persetujuan menggunakan <strong>Komputer/Laptop</strong>. Layar yang lebih besar memudahkan pengecekan berkas surat dan meminimalisir risiko salah klik saat memberikan keputusan.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end">
                    <button @click="showFaq = false" class="btn btn-outline bg-white font-medium shadow-sm">Tutup Panduan</button>
                </div>
            </div>
         </div>
    </div>
    @elseif(auth()->check() && auth()->user()->getRoleKode() === 'admin_dpmd')
    {{-- Alpine FAQ Component for Admin DPMD --}}
    <div x-data="{ showFaq: false, showIntro: false }" 
         x-init="
            let currentSession = '{{ session()->getId() }}';
            if(localStorage.getItem('dpmd_intro_session') !== currentSession) { 
                setTimeout(() => showIntro = true, 500); 
            }
         "
         @open-faq.window="showFaq = true">
         
         {{-- Intro Modal --}}
         <div x-show="showIntro" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-transition.opacity>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all" @click.away="showIntro = false; localStorage.setItem('dpmd_intro_session', '{{ session()->getId() }}')">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="info" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Selamat Datang di Dashboard DPMD!</h3>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Jika Anda belum mengetahui alur kerja atau tugas sebagai Admin DPMD, silakan klik tombol <strong><i data-lucide="book-open-check" class="w-4 h-4 inline-block -mt-1 text-orange-600"></i> Panduan</strong> di bagian bilah menu (header) paling atas.
                    </p>
                    <button type="button" @click="showIntro = false; localStorage.setItem('dpmd_intro_session', '{{ session()->getId() }}')" class="btn btn-primary w-full py-2.5 font-bold text-base shadow-lg shadow-primary/30">
                        Mengerti
                    </button>
                </div>
            </div>
         </div>

         {{-- FAQ Modal --}}
         <div x-show="showFaq" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-transition.opacity>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden transform transition-all" @click.away="showFaq = false">
                <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="help-circle" class="w-5 h-5 text-blue-600"></i> Panduan Penggunaan Admin DPMD
                    </h3>
                    <button @click="showFaq = false" class="text-slate-400 hover:text-slate-600 bg-slate-100 hover:bg-slate-200 p-1.5 rounded-lg transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {{-- Kolom Kiri: Alur Utama --}}
                        <div class="lg:col-span-2 space-y-6">
                            <h4 class="font-extrabold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                                <span class="text-xl">🚀</span> Alur Kerja Utama Admin DPMD
                            </h4>
                            
                            <div class="relative border-l-2 border-blue-100 ml-3 space-y-8 pb-4">
                                {{-- Step 1 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-emerald-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="inbox" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">1. Memantau & Menerima Usulan Desa</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">Setelah pendaftar ditetapkan sebagai Peringkat 1 oleh Desa dan berkasnya diunggah, data tersebut akan otomatis masuk ke Admin DPMD. Buka menu <strong>Data Pendaftar</strong> untuk melihat seluruh usulan dari desa-desa di seluruh kabupaten.</p>
                                </div>
                                
                                {{-- Step 2 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-amber-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="search" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">2. Mengecek Detail Pendaftar</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">Klik tombol <strong>Detail</strong> pada tabel pendaftar. Anda bertugas untuk melihat ringkasan biodata kandidat dan meninjau dokumen yang telah diunggah oleh pihak desa (seperti Surat Rekomendasi dan Berita Acara).</p>
                                </div>

                                {{-- Step 3 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-blue-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="send" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">3. Meneruskan ke Kabupaten</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">DPMD <strong>tidak memiliki kewenangan untuk menolak</strong>. Tugas Anda hanyalah memastikan data bisa dilihat, lalu klik tombol <strong>Setujui / Teruskan</strong> agar berkas usulan pendaftar tersebut secara sistem terkirim ke tingkat Kabupaten.</p>
                                </div>

                                {{-- Step 4 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-purple-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="arrow-right-left" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">4. Persetujuan Paralel Bersama Kecamatan</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">Penerusan berkas dari DPMD berjalan paralel/bersamaan dengan pihak Kecamatan. Data usulan hanya akan masuk secara utuh ke Kabupaten apabila telah diteruskan oleh <em>Kecamatan DAN DPMD</em> sekaligus.</p>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Kolom Kanan: Fitur & Tips --}}
                        <div class="space-y-4">
                            <h4 class="font-extrabold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2 mb-2 lg:mt-0 mt-6">
                                <span class="text-xl">💡</span> Fitur Tambahan
                            </h4>

                            <div class="bg-indigo-50/70 p-4 rounded-xl border border-indigo-100">
                                <h5 class="font-bold text-indigo-900 mb-1.5 flex items-center gap-2">
                                    <i data-lucide="layout-dashboard" class="w-4 h-4 text-indigo-600"></i> Pantau Status Desa
                                </h5>
                                <p class="text-xs text-indigo-800 leading-relaxed">Melalui halaman Dashboard, Anda bisa melihat desa mana saja yang sudah mengusulkan kandidat, dan desa mana yang belum menyelesaikan penilaian/rekomendasi.</p>
                            </div>

                            <div class="bg-amber-50/70 p-4 rounded-xl border border-amber-100">
                                <h5 class="font-bold text-amber-900 mb-1.5 flex items-center gap-2">
                                    <i data-lucide="file-spreadsheet" class="w-4 h-4 text-amber-600"></i> Ekspor Data (Excel)
                                </h5>
                                <p class="text-xs text-amber-800 leading-relaxed">Anda bisa mengunduh rekap pendaftar dalam format Excel untuk keperluan pelaporan melalui menu Export Data. Tersedia juga opsi export berdasarkan status persetujuannya.</p>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <h5 class="font-bold text-slate-700 mb-1.5 flex items-center gap-2">
                                    <i data-lucide="monitor" class="w-4 h-4 text-slate-500"></i> Tip Perangkat
                                </h5>
                                <p class="text-xs text-slate-600 leading-relaxed">Sangat disarankan mengelola persetujuan menggunakan <strong>Komputer/Laptop</strong>. Layar yang lebih besar memudahkan pengecekan berkas surat dan meminimalisir risiko salah klik saat memberikan keputusan.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end">
                    <button @click="showFaq = false" class="btn btn-outline bg-white font-medium shadow-sm">Tutup Panduan</button>
                </div>
            </div>
         </div>
    </div>
    @elseif(auth()->check() && auth()->user()->getRoleKode() === 'admin_opd')
    {{-- Alpine FAQ Component for Admin OPD --}}
    <div x-data="{ showFaq: false, showIntro: false }" 
         x-init="
            let currentSession = '{{ session()->getId() }}';
            if(localStorage.getItem('opd_intro_session') !== currentSession) { 
                setTimeout(() => showIntro = true, 500); 
            }
         "
         @open-faq.window="showFaq = true">
         
         {{-- Intro Modal --}}
         <div x-show="showIntro" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-transition.opacity>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all" @click.away="showIntro = false; localStorage.setItem('opd_intro_session', '{{ session()->getId() }}')">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="info" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Selamat Datang di Dashboard Admin OPD!</h3>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Jika Anda belum mengetahui alur kerja atau tugas sebagai Admin OPD, silakan klik tombol <strong><i data-lucide="book-open-check" class="w-4 h-4 inline-block -mt-1 text-orange-600"></i> Panduan</strong> di bagian bilah menu (header) paling atas.
                    </p>
                    <button type="button" @click="showIntro = false; localStorage.setItem('opd_intro_session', '{{ session()->getId() }}')" class="btn btn-primary w-full py-2.5 font-bold text-base shadow-lg shadow-primary/30">
                        Mengerti
                    </button>
                </div>
            </div>
         </div>

         {{-- FAQ Modal --}}
         <div x-show="showFaq" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-transition.opacity>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden transform transition-all" @click.away="showFaq = false">
                <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="help-circle" class="w-5 h-5 text-blue-600"></i> Panduan Penggunaan Admin OPD
                    </h3>
                    <button @click="showFaq = false" class="text-slate-400 hover:text-slate-600 bg-slate-100 hover:bg-slate-200 p-1.5 rounded-lg transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {{-- Kolom Kiri: Alur Utama --}}
                        <div class="lg:col-span-2 space-y-6">
                            <h4 class="font-extrabold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                                <span class="text-xl">🚀</span> Alur Kerja Utama Admin OPD
                            </h4>
                            
                            <div class="relative border-l-2 border-blue-100 ml-3 space-y-8 pb-4">
                                {{-- Step 1 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-emerald-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="inbox" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">1. Menerima Berkas Masuk</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">Pendaftar yang telah menyelesaikan pendaftaran akan otomatis masuk ke sistem. Buka menu <strong>Verifikasi Dokumen</strong> untuk melihat daftar pendaftar yang dokumennya perlu Anda periksa (sistem hanya menampilkan dokumen yang menjadi kewenangan instansi Anda).</p>
                                </div>
                                
                                {{-- Step 2 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-amber-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="search" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">2. Melihat Detail Berkas</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">Klik tombol <strong>Detail</strong> pada baris pendaftar untuk melihat pratinjau dokumen. Anda bertugas mengecek dan memastikan keabsahan dokumen sesuai persyaratan.</p>
                                </div>

                                {{-- Step 3 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-blue-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="check-circle" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">3. Memberikan Keputusan Verifikasi</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">Setelah melihat berkas, <em>scroll</em> ke bawah untuk memberikan keputusan. Pilih status <strong>Valid</strong> jika sesuai, atau <strong>Tidak Valid</strong> beserta <strong>Catatan</strong> jika terdapat kekurangan/kesalahan.</p>
                                </div>

                                {{-- Step 4 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-purple-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="save" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">4. Menyimpan Keputusan & Otomatisasi</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">Klik tombol <strong>Simpan</strong>. Proses verifikasi antar OPD berjalan secara paralel. Pendaftar secara keseluruhan hanya akan dinyatakan "Lolos Verifikasi" oleh sistem apabila <em>seluruh</em> dokumen syarat wajib dari <em>berbagai</em> OPD telah dinyatakan Valid.</p>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Kolom Kanan: Fitur & Tips --}}
                        <div class="space-y-4">
                            <h4 class="font-extrabold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2 mb-2 lg:mt-0 mt-6">
                                <span class="text-xl">💡</span> Fitur Tambahan
                            </h4>

                            <div class="bg-indigo-50/70 p-4 rounded-xl border border-indigo-100">
                                <h5 class="font-bold text-indigo-900 mb-1.5 flex items-center gap-2">
                                    <i data-lucide="layout-dashboard" class="w-4 h-4 text-indigo-600"></i> Pantau Statistik
                                </h5>
                                <p class="text-xs text-indigo-800 leading-relaxed">Melalui halaman Dashboard, Anda bisa melihat jumlah berkas yang masuk, serta jumlah berkas yang belum dan sudah Anda verifikasi (Valid / Tidak Valid).</p>
                            </div>

                            <div class="bg-amber-50/70 p-4 rounded-xl border border-amber-100">
                                <h5 class="font-bold text-amber-900 mb-1.5 flex items-center gap-2">
                                    <i data-lucide="history" class="w-4 h-4 text-amber-600"></i> Riwayat Verifikasi
                                </h5>
                                <p class="text-xs text-amber-800 leading-relaxed">Anda dapat melihat kembali seluruh riwayat verifikasi yang pernah dilakukan oleh instansi Anda pada masa lalu melalui menu khusus Riwayat Verifikasi.</p>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <h5 class="font-bold text-slate-700 mb-1.5 flex items-center gap-2">
                                    <i data-lucide="monitor" class="w-4 h-4 text-slate-500"></i> Tip Perangkat
                                </h5>
                                <p class="text-xs text-slate-600 leading-relaxed">Sangat disarankan melakukan verifikasi dokumen menggunakan <strong>Komputer/Laptop</strong>. Layar yang lebih besar memudahkan Anda membaca teks kecil pada pratinjau dokumen (pdf/gambar) pendaftar.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end">
                    <button @click="showFaq = false" class="btn btn-outline bg-white font-medium shadow-sm">Tutup Panduan</button>
                </div>
            </div>
         </div>
    </div>
    @elseif(auth()->check() && auth()->user()->getRoleKode() === 'admin_kabupaten')
    {{-- Alpine FAQ Component for Admin Kabupaten --}}
    <div x-data="{ showFaq: false, showIntro: false }" 
         x-init="
            let currentSession = '{{ session()->getId() }}';
            if(localStorage.getItem('kab_intro_session') !== currentSession) { 
                setTimeout(() => showIntro = true, 500); 
            }
         "
         @open-faq.window="showFaq = true">
         
         {{-- Intro Modal --}}
         <div x-show="showIntro" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-transition.opacity>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all" @click.away="showIntro = false; localStorage.setItem('kab_intro_session', '{{ session()->getId() }}')">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="info" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Selamat Datang di Dashboard Admin Kabupaten!</h3>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Jika Anda belum mengetahui alur kerja atau tugas sebagai Admin Kabupaten, silakan klik tombol <strong><i data-lucide="book-open-check" class="w-4 h-4 inline-block -mt-1 text-orange-600"></i> Panduan</strong> di bagian bilah menu (header) paling atas.
                    </p>
                    <button type="button" @click="showIntro = false; localStorage.setItem('kab_intro_session', '{{ session()->getId() }}')" class="btn btn-primary w-full py-2.5 font-bold text-base shadow-lg shadow-primary/30">
                        Mengerti
                    </button>
                </div>
            </div>
         </div>

         {{-- FAQ Modal --}}
         <div x-show="showFaq" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4" x-transition.opacity>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden transform transition-all" @click.away="showFaq = false">
                <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="help-circle" class="w-5 h-5 text-blue-600"></i> Panduan Penggunaan Admin Kabupaten
                    </h3>
                    <button @click="showFaq = false" class="text-slate-400 hover:text-slate-600 bg-slate-100 hover:bg-slate-200 p-1.5 rounded-lg transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {{-- Kolom Kiri: Alur Utama --}}
                        <div class="lg:col-span-2 space-y-6">
                            <h4 class="font-extrabold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2">
                                <span class="text-xl">🚀</span> Alur Kerja Utama Admin Kabupaten
                            </h4>
                            
                            <div class="relative border-l-2 border-blue-100 ml-3 space-y-8 pb-4">
                                {{-- Step 1 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-emerald-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="inbox" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">1. Memantau & Menerima Usulan</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">Admin Kabupaten bertugas memantau seluruh proses pendaftaran beasiswa dari tingkat OPD, Desa, Kecamatan, hingga DPMD. Usulan akhir yang telah disetujui secara berjenjang akan masuk dan siap untuk Anda proses lebih lanjut.</p>
                                </div>
                                
                                {{-- Step 2 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-amber-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="search" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">2. Mengecek Detail & Validasi Akhir</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">Klik tombol <strong>Detail</strong> pada tabel pendaftar untuk melihat kelengkapan biodata, nilai dari instansi terkait, serta dokumen rekomendasi sebelum melakukan penetapan.</p>
                                </div>

                                {{-- Step 3 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-blue-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="check-circle" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">3. Menetapkan Status Penerima Beasiswa</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">Tahap ini merupakan finalisasi dari proses seleksi. Anda berhak <strong>Menetapkan Penerima</strong> untuk kandidat yang lolos seleksi dan memenuhi kuota dari Pemerintah Kabupaten Blitar.</p>
                                </div>

                                {{-- Step 4 --}}
                                <div class="relative pl-8">
                                    <div class="absolute -left-[11px] top-0 w-5 h-5 rounded-full bg-purple-500 ring-4 ring-white flex items-center justify-center">
                                        <i data-lucide="file-spreadsheet" class="w-3 h-3 text-white"></i>
                                    </div>
                                    <h5 class="font-bold text-slate-800 text-sm">4. Rekap Data & Pelaporan</h5>
                                    <p class="text-sm text-slate-600 mt-1.5 leading-relaxed">Untuk mengekspor data, Anda dapat memilih spesifik program beasiswa yang diinginkan pada menu <strong>Setiap Program Beasiswa</strong>. Jika Anda ingin mengekspor seluruh data sekaligus, Anda dapat masuk ke menu <strong>Riwayat Penetapan</strong> dan klik ekspor ke Excel.</p>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Kolom Kanan: Fitur & Tips --}}
                        <div class="space-y-4">
                            <h4 class="font-extrabold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2 mb-2 lg:mt-0 mt-6">
                                <span class="text-xl">💡</span> Fitur Tambahan
                            </h4>

                            <div class="bg-indigo-50/70 p-4 rounded-xl border border-indigo-100">
                                <h5 class="font-bold text-indigo-900 mb-1.5 flex items-center gap-2">
                                    <i data-lucide="layout-dashboard" class="w-4 h-4 text-indigo-600"></i> Manajemen Program
                                </h5>
                                <p class="text-xs text-indigo-800 leading-relaxed">Setiap program beasiswa dipisahkan agar Anda lebih mudah melacak progres dari masing-masing program. Silakan akses detail pendaftar di dalam menu spesifik program tersebut.</p>
                            </div>

                            <div class="bg-amber-50/70 p-4 rounded-xl border border-amber-100">
                                <h5 class="font-bold text-amber-900 mb-1.5 flex items-center gap-2">
                                    <i data-lucide="history" class="w-4 h-4 text-amber-600"></i> Riwayat Penetapan
                                </h5>
                                <p class="text-xs text-amber-800 leading-relaxed">Semua riwayat terkait penetapan yang pernah dilakukan akan terekam secara otomatis. Buka menu Riwayat Penetapan untuk melacak log perubahan yang terjadi.</p>
                            </div>

                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <h5 class="font-bold text-slate-700 mb-1.5 flex items-center gap-2">
                                    <i data-lucide="monitor" class="w-4 h-4 text-slate-500"></i> Tip Perangkat
                                </h5>
                                <p class="text-xs text-slate-600 leading-relaxed">Sangat disarankan melakukan penetapan dan validasi data menggunakan <strong>Komputer/Laptop</strong> karena cakupan data yang harus ditinjau sangat banyak dan detail.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end">
                    <button @click="showFaq = false" class="btn btn-outline bg-white font-medium shadow-sm">Tutup Panduan</button>
                </div>
            </div>
         </div>
    </div>
    @endif

    @stack('scripts')
</body>
</html>
