<x-layouts.admin :title="'Dashboard Desa'">
    <div class="space-y-6">
        {{-- Alert Pintar --}}
        @if($butuhPenilaian > 0)
            <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl flex items-start gap-3 shadow-sm">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5 text-amber-600"></i>
                <div>
                    <h4 class="font-bold">Butuh Tindakan!</h4>
                    <p class="text-sm mt-0.5">Ada <strong>{{ $butuhPenilaian }}</strong> pendaftar yang Lolos Verifikasi OPD dan Menunggu Penilaian/Pemeringkatan dari Anda.</p>
                </div>
            </div>
        @endif
        
        @if($menungguPenetapan)
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-start gap-3 shadow-sm">
                <i data-lucide="award" class="w-5 h-5 shrink-0 mt-0.5 text-emerald-600"></i>
                <div>
                    <h4 class="font-bold">Selamat! Kandidat Peringkat 1 Tersedia</h4>
                    <p class="text-sm mt-0.5">Segera unggah <strong>Surat Rekomendasi & Berita Acara Musyawarah</strong> untuk menetapkan <strong>{{ $menungguPenetapan->identitas->nama_lengkap ?? 'Kandidat' }}</strong>.</p>
                    <a href="{{ route('desa.show', $menungguPenetapan->id) }}#form-rekomendasi" class="btn btn-xs btn-success mt-2">Tetapkan & Unggah Sekarang</a>
                </div>
            </div>
        @endif

        {{-- Welcome Banner & Countdown --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="card overflow-hidden relative lg:col-span-2" style="background: linear-gradient(135deg, #059669, #047857);">
                <div class="absolute inset-0">
                    <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
                </div>
                <div class="relative card-body text-white flex flex-col justify-between h-full">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center backdrop-blur-sm">
                                <i data-lucide="map-pin" class="w-5 h-5"></i>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-widest text-emerald-200">Admin Desa</span>
                        </div>
                        <h1 class="text-2xl font-extrabold">Selamat Datang, {{ auth()->user()->nama }}!</h1>
                        <p class="text-emerald-100 text-sm mt-1">Pantau pendaftaran beasiswa dari wilayah desa/kelurahan Anda.</p>
                    </div>
                </div>
            </div>
            
            {{-- Countdown Widget --}}
            <div class="card relative overflow-hidden shadow-lg border-0" style="background-color: #0f172a; color: white;">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/20 rounded-bl-full blur-2xl"></div>
                <div class="card-body relative flex flex-col items-center justify-center text-center h-full py-8">
                    <i data-lucide="timer" class="w-8 h-8 text-emerald-400 mb-3"></i>
                    <h3 class="font-bold text-slate-300 text-sm uppercase tracking-wider mb-1">Batas Pendaftaran</h3>
                    @if($periodeAktif && $periodeAktif->tanggal_selesai)
                        @php
                            $targetDate = \Carbon\Carbon::parse($periodeAktif->tanggal_selesai)->endOfDay();
                            $now = \Carbon\Carbon::now();
                            $isPast = $now->isAfter($targetDate);
                            $diffDays = (int) ceil($now->startOfDay()->diffInDays($targetDate->startOfDay(), true));
                        @endphp
                        @if(!$isPast && $diffDays > 0)
                            <div class="text-3xl font-extrabold text-white mb-1">
                                {{ $diffDays }} <span class="text-lg font-medium text-slate-400">Hari Lagi</span>
                            </div>
                        @elseif(!$isPast && $diffDays === 0)
                            <div class="text-2xl font-extrabold text-amber-400 mb-1">Hari Terakhir!</div>
                        @else
                            <div class="text-xl font-extrabold text-rose-400 mb-1">Telah Ditutup</div>
                        @endif
                        <p class="text-xs text-slate-400">Penutupan: {{ \Carbon\Carbon::parse($periodeAktif->tanggal_selesai)->format('d M Y') }}</p>
                    @else
                        <div class="text-lg font-bold text-slate-400">Belum Ada Periode Aktif</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Stats & Chart --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <x-stat-card label="Total Pendaftar" :value="$stats['total']" icon="users" color="#2B5C92" />
            <x-stat-card label="Menunggu Verifikasi" :value="$stats['menunggu_verifikasi']" icon="clock" color="#D97706" />
            <x-stat-card label="Siap Dinilai" :value="$stats['lolos_verifikasi']" icon="check-square" color="#0284C7" />
            <x-stat-card label="Telah Dinilai" :value="$stats['sudah_dinilai']" icon="award" color="#059669" />
        </div>

        <div class="card p-4 flex flex-col justify-center mb-6">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Proporsi Status Pendaftar</h3>
            @php
                $total = $stats['total'] ?: 1; // avoid division by zero
                $pctMenunggu = ($stats['menunggu_verifikasi'] / $total) * 100;
                $pctLolos = ($stats['lolos_verifikasi'] / $total) * 100;
                $pctDinilai = ($stats['sudah_dinilai'] / $total) * 100;
                $pctLulus = ($stats['lulus'] / $total) * 100;
                // Sisa adalah yang gagal/ditolak
                $pctSisa = max(0, 100 - ($pctMenunggu + $pctLolos + $pctDinilai + $pctLulus)); 
            @endphp
            <div class="w-full h-3 flex rounded-full overflow-hidden mb-3 bg-slate-100 shadow-inner">
                <div style="width: {{ $pctMenunggu }}%" class="bg-amber-400" title="Menunggu Verifikasi"></div>
                <div style="width: {{ $pctLolos }}%" class="bg-sky-400" title="Lolos Verifikasi"></div>
                <div style="width: {{ $pctDinilai }}%" class="bg-emerald-400" title="Sudah Dinilai"></div>
                <div style="width: {{ $pctLulus }}%" class="bg-indigo-500" title="Lulus/Ditetapkan"></div>
                <div style="width: {{ $pctSisa }}%" class="bg-rose-400" title="Gugur"></div>
            </div>
            <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-slate-500">
                <div class="flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-amber-400"></div> Menunggu</div>
                <div class="flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-sky-400"></div> Siap Nilai</div>
                <div class="flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-emerald-400"></div> Dinilai</div>
                <div class="flex items-center gap-1"><div class="w-2 h-2 rounded-full bg-rose-400"></div> Gugur</div>
            </div>
        </div>

        {{-- Pendaftar --}}
        <div x-data="{ tab: new URLSearchParams(location.search).get('tab') || 'baru' }" class="card overflow-hidden">
            <div class="card-header border-b border-slate-100 p-0">
                <div class="flex overflow-x-auto">
                    <button @click="tab = 'baru'; const url = new URL(location); url.searchParams.set('tab', 'baru'); history.pushState({}, '', url);" 
                            :class="{'border-b-2 border-primary text-primary font-bold bg-primary/5': tab === 'baru', 'text-slate-500 hover:text-slate-700': tab !== 'baru'}" 
                            class="px-5 py-4 text-sm font-medium whitespace-nowrap transition-colors flex-1 text-center">
                        Baru & Diproses <span class="ml-1 badge badge-primary text-xs">{{ $pendaftarBaru->total() }}</span>
                    </button>
                    <button @click="tab = 'proses'; const url = new URL(location); url.searchParams.set('tab', 'proses'); history.pushState({}, '', url);" 
                            :class="{'border-b-2 border-primary text-primary font-bold bg-primary/5': tab === 'proses', 'text-slate-500 hover:text-slate-700': tab !== 'proses'}" 
                            class="px-5 py-4 text-sm font-medium whitespace-nowrap transition-colors flex-1 text-center">
                        Siap Dinilai <span class="ml-1 badge badge-primary text-xs">{{ $pendaftarProses->total() }}</span>
                    </button>
                    <button @click="tab = 'selesai'; const url = new URL(location); url.searchParams.set('tab', 'selesai'); history.pushState({}, '', url);" 
                            :class="{'border-b-2 border-primary text-primary font-bold bg-primary/5': tab === 'selesai', 'text-slate-500 hover:text-slate-700': tab !== 'selesai'}" 
                            class="px-5 py-4 text-sm font-medium whitespace-nowrap transition-colors flex-1 text-center">
                        Selesai <span class="ml-1 badge badge-primary text-xs">{{ $pendaftarSelesai->total() }}</span>
                    </button>
                    <button @click="tab = 'gagal'; const url = new URL(location); url.searchParams.set('tab', 'gagal'); history.pushState({}, '', url);" 
                            :class="{'border-b-2 border-primary text-primary font-bold bg-primary/5': tab === 'gagal', 'text-slate-500 hover:text-slate-700': tab !== 'gagal'}" 
                            class="px-5 py-4 text-sm font-medium whitespace-nowrap transition-colors flex-1 text-center">
                        Gugur <span class="ml-1 badge badge-primary text-xs">{{ $pendaftarGagal->total() }}</span>
                    </button>
                </div>
            </div>

            <x-desa-dashboard-table :pendaftar="$pendaftarBaru" tabName="baru" />
            <x-desa-dashboard-table :pendaftar="$pendaftarProses" tabName="proses" />
            <x-desa-dashboard-table :pendaftar="$pendaftarSelesai" tabName="selesai" />
            <x-desa-dashboard-table :pendaftar="$pendaftarGagal" tabName="gagal" />
        </div>
    </div>
</x-layouts.admin>
