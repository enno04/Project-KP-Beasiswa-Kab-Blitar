<x-layouts.admin :title="'Dashboard Kabupaten'">
    <div class="space-y-6">
        {{-- Welcome Banner --}}
        <div class="card overflow-hidden relative bg-primary">
            <div class="absolute inset-0">
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 right-1/4 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-primary-dark/80 to-transparent"></div>
            </div>
            <div class="relative card-body text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center backdrop-blur-sm border border-white/20 shadow-inner">
                            <i data-lucide="landmark" class="w-5 h-5"></i>
                        </div>
                        <span class="text-xs font-bold uppercase tracking-widest text-primary-light">Admin Kabupaten</span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-extrabold mb-1">Selamat Datang, {{ auth()->user()->nama }}!</h1>
                    <p class="text-primary-100 text-sm md:text-base max-w-xl">
                        Pusat komando pendaftaran beasiswa se-Kabupaten Blitar.
                        @if($periodeAktif)
                            <span class="font-semibold block mt-1"><i data-lucide="calendar" class="w-4 h-4 inline-block mr-1"></i>Periode Aktif: {{ $periodeAktif->nama }}{{ str_contains($periodeAktif->nama, $periodeAktif->tahun) ? '' : ' (' . $periodeAktif->tahun . ')' }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- SLA Warning Banner --}}
        @if(isset($slaWarning) && $slaWarning > 0)
        <div class="bg-red-50 border-l-4 border-red-500 rounded-r-xl p-4 flex items-start gap-4 shadow-sm animate-pulse-slow">
            <div class="bg-red-100 rounded-full p-2 text-red-600 shrink-0">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-red-800 font-bold text-sm mb-1">Peringatan Keterlambatan Proses (SLA)</h3>
                <p class="text-red-600 text-sm">
                    Terdapat <strong>{{ $slaWarning }} data pendaftar</strong> yang tertahan di status <span class="font-semibold">Lolos Verifikasi</span> selama lebih dari 3 hari. Segera tindaklanjuti ke tahap penilaian atau penetapan agar tidak menghambat alur beasiswa.
                </p>
            </div>
        </div>
        @endif

        {{-- Modern Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Total Pendaftar (Level Kab)" :value="$stats['total']" icon="users" color="#0ea5e9" />
            <x-stat-card label="Siap Dinilai/Ditetapkan" :value="$stats['lolos_verifikasi']" icon="clipboard-check" color="#8b5cf6" />
            <x-stat-card label="Menunggu Penetapan (SK)" :value="$stats['menunggu_penetapan']" icon="clock" color="#f59e0b" />
            <x-stat-card label="SK Terbit (Lulus)" :value="$stats['lulus']" icon="award" color="#10b981" />
        </div>

        {{-- Grafik --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Bar Chart: Sebaran Kecamatan --}}
            <div class="lg:col-span-2">
                <div class="card h-full flex flex-col hover:shadow-xl hover:shadow-slate-200/50 transition-shadow duration-300">
                    <div class="card-header border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
                            </div>
                            <span class="font-bold text-slate-800">Sebaran Pendaftar per Kecamatan</span>
                        </div>
                    </div>
                    <div class="card-body flex-1 relative min-h-[300px]">
                        <canvas id="kecamatanChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Doughnut Chart: Komposisi Status --}}
            <div class="lg:col-span-1">
                <div class="card h-full flex flex-col hover:shadow-xl hover:shadow-slate-200/50 transition-shadow duration-300">
                    <div class="card-header border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <i data-lucide="pie-chart" class="w-4 h-4"></i>
                            </div>
                            <span class="font-bold text-slate-800">Komposisi Status</span>
                        </div>
                    </div>
                    <div class="card-body flex-1 relative min-h-[300px] flex items-center justify-center">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pendaftar Terbaru --}}
        <div class="card overflow-hidden border-0 shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="card-header border-b border-slate-100 bg-slate-50/80 backdrop-blur flex items-center justify-between py-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                    </div>
                    <span class="font-bold text-slate-800">Pendaftar Terbaru (Sudah Diteruskan ke Kabupaten)</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/50 text-slate-500 font-medium border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">Pendaftar</th>
                            <th class="px-6 py-4">Program & Jalur</th>
                            <th class="px-6 py-4">Waktu Daftar</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($aktivitasTerbaru as $p)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 group-hover:text-primary transition-colors">{{ $p->identitas->nama_lengkap ?? '-' }}</div>
                                <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                    <i data-lucide="credit-card" class="w-3 h-3"></i> {{ $p->identitas->nik ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 font-medium text-xs mb-1">
                                    {{ $p->program->nama }}
                                </div>
                                <div class="text-xs text-slate-500 flex items-center gap-1">
                                    <i data-lucide="git-branch" class="w-3 h-3"></i> {{ $p->jalur->nama }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                                    {{ $p->created_at->format('d M Y') }}
                                </div>
                                <div class="text-xs text-slate-400 mt-0.5 ml-6">{{ $p->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="badge {{ $p->status_color }}">{{ $p->status_label }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('kabupaten.show', $p->id) }}" class="btn btn-xs btn-outline hover:bg-slate-800 hover:text-white transition-colors">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <x-empty-state icon="inbox" title="Belum Ada Pendaftar" text="Belum ada pendaftar yang diteruskan ke tingkat Kabupaten." />
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($stats['total'] > 5)
            <div class="p-4 border-t border-slate-100 flex justify-center bg-slate-50/50">
                <a href="{{ route('kabupaten.rekap-data') }}" class="group inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-white border border-slate-200 text-slate-600 font-medium text-sm hover:bg-primary hover:border-primary hover:text-white hover:shadow-lg hover:shadow-primary/30 transition-all duration-300 transform hover:-translate-y-0.5">
                    Lihat Selengkapnya ({{ $stats['total'] }} Data)
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data Monitoring Kecamatan
            const kecData = @json($monitoringKecamatan ?? []);
            
            if (kecData.length > 0 && document.getElementById('kecamatanChart')) {
                const ctxKec = document.getElementById('kecamatanChart').getContext('2d');
                
                const primaryColor = '#2563eb';
                const primaryLight = '#60a5fa';
                
                new Chart(ctxKec, {
                    type: 'bar',
                    data: {
                        labels: kecData.map(d => d.nama_kecamatan),
                        datasets: [
                            {
                                label: 'Total Pendaftar',
                                data: kecData.map(d => d.total),
                                backgroundColor: primaryLight,
                                borderColor: primaryColor,
                                borderWidth: 1,
                                borderRadius: 4,
                                hoverBackgroundColor: primaryColor
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            // Data Komposisi Status
            const statusData = @json($komposisiStatus ?? []);
            
            if (statusData.length > 0 && document.getElementById('statusChart')) {
                const ctxStatus = document.getElementById('statusChart').getContext('2d');
                
                const getStatusStyle = (status) => {
                    const styles = {
                        'lolos_verifikasi': { color: '#8b5cf6', label: 'Lolos Verifikasi' },
                        'proses_penilaian': { color: '#3b82f6', label: 'Proses Penilaian' },
                        'menunggu_penetapan': { color: '#f59e0b', label: 'Menunggu SK' },
                        'lulus': { color: '#10b981', label: 'Lulus' },
                        'tidak_lulus': { color: '#ef4444', label: 'Tidak Lulus' },
                        'gugur_wawancara': { color: '#f43f5e', label: 'Gugur Wawancara' }
                    };
                    return styles[status] || { color: '#94a3b8', label: status };
                };

                const labels = statusData.map(d => getStatusStyle(d.status).label);
                const dataValues = statusData.map(d => d.total);
                const bgColors = statusData.map(d => getStatusStyle(d.status).color);

                new Chart(ctxStatus, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: dataValues,
                            backgroundColor: bgColors,
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    boxWidth: 8
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }
        });
    </script>
    @endpush
</x-layouts.admin>
