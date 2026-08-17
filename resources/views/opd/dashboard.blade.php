<x-layouts.admin :title="'Dashboard OPD'">
    <div class="space-y-6">
        {{-- Welcome Banner --}}
        <div class="card overflow-hidden relative" style="background: linear-gradient(135deg, #7C3AED, #6D28D9);">
            <div class="absolute inset-0">
                <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
            </div>
            <div class="relative card-body text-white">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center backdrop-blur-sm">
                        <i data-lucide="file-check" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-widest text-violet-200">Verifikator OPD</span>
                </div>
                <h1 class="text-2xl font-extrabold">Selamat Datang, {{ auth()->user()->nama }}!</h1>
                <p class="text-violet-100 text-sm mt-1">Pantau antrean dokumen yang membutuhkan verifikasi dari instansi Anda.</p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-stat-card label="Antrean Verifikasi" :value="$stats['belum_diverifikasi']" icon="clock" color="#D97706" />
            <x-stat-card label="Dokumen Valid" :value="$stats['valid']" icon="check-circle" color="#059669" />
            <x-stat-card label="Dokumen Tidak Valid" :value="$stats['tidak_valid']" icon="x-circle" color="#DC2626" />
        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="card">
                <div class="card-body flex items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-slate-900 mb-1">Mulai Verifikasi</h3>
                        <p class="text-sm text-slate-500">Periksa dokumen yang ada di antrean Anda.</p>
                    </div>
                    <a href="{{ route('opd.verifikasi.index', ['status' => 'belum_diverifikasi']) }}" class="btn btn-primary shrink-0">
                        <i data-lucide="play" class="w-4 h-4"></i> Mulai
                    </a>
                </div>
            </div>
            <div class="card">
                <div class="card-body flex items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-slate-900 mb-1">Riwayat Verifikasi</h3>
                        <p class="text-sm text-slate-500">Lihat log tindakan yang telah Anda lakukan.</p>
                    </div>
                    <a href="{{ route('opd.riwayat') }}" class="btn btn-outline shrink-0">
                        <i data-lucide="history" class="w-4 h-4"></i> Riwayat
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
