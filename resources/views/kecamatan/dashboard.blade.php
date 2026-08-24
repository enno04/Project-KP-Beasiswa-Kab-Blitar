<x-layouts.admin :title="'Dashboard Kecamatan'">
    <div class="space-y-6">
        {{-- Welcome Banner --}}
        <div class="card overflow-hidden relative" style="background: linear-gradient(135deg, #D97706, #B45309);">
            <div class="absolute inset-0">
                <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
            </div>
            <div class="relative card-body flex flex-col sm:flex-row justify-between sm:items-center text-white p-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center backdrop-blur-sm">
                            <i data-lucide="map" class="w-5 h-5"></i>
                        </div>
                        <span class="text-xs font-bold uppercase tracking-widest text-amber-200">Admin Kecamatan</span>
                    </div>
                    <h1 class="text-2xl font-extrabold">Selamat Datang, {{ auth()->user()->nama }}!</h1>
                    <p class="text-amber-100 text-sm mt-1">Pantau pendaftaran beasiswa dan verifikasi berkas dari seluruh desa di wilayah Anda.</p>
                </div>
                <div class="hidden sm:block text-right mt-4 sm:mt-0">
                    <div class="text-xs text-amber-200 font-semibold mb-1 uppercase tracking-wider">Progress Kinerja</div>
                    <div class="text-3xl font-black">{{ $persentase }}%</div>
                    <div class="text-xs text-amber-200 mt-1">{{ $disetujuiKecamatan }} dari {{ $stats['total'] }} Dokumen</div>
                </div>
            </div>
            
            <div class="relative px-6 pb-6 z-10">
                {{-- Progress Bar --}}
                <div class="mt-2">
                    <div class="h-2 w-full bg-white/20 rounded-full overflow-hidden shadow-inner">
                        <div class="h-full bg-white transition-all duration-1000 rounded-full" style="width: {{ $persentase }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SLA Warning --}}
        @if($slaWarning > 0)
        <div class="alert alert-danger shadow-sm border-0 bg-red-50 text-red-700">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i> 
            <div class="flex-1">
                <strong class="font-bold">Perhatian!</strong> Terdapat <span class="font-black underline">{{ $slaWarning }} dokumen</span> yang tertahan dan menunggu verifikasi Kecamatan selama lebih dari 3 hari. Mohon segera diproses.
            </div>
            <a href="{{ route('kecamatan.program.index', 'sdss') }}?status=diteruskan_ke_kecamatan&filter_kecamatan=belum" class="btn btn-xs btn-danger whitespace-nowrap">Lihat Berkas</a>
        </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Total Diteruskan" :value="$stats['total']" icon="users" color="#2B5C92" />
            <x-stat-card label="Menunggu Verif Kec." :value="$stats['diteruskan_ke_kecamatan']" icon="inbox" color="#D97706" />
            <x-stat-card label="Menunggu Penetapan" :value="$stats['menunggu_penetapan']" icon="check-square" color="#0284C7" />
            <x-stat-card label="Ditetapkan Lulus" :value="$stats['lulus']" icon="award" color="#059669" />
        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="card border-amber-500/20 bg-amber-50/30 hover:shadow-md transition-shadow">
                <div class="card-body flex items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-amber-800 mb-1">Mulai Verifikasi</h3>
                        <p class="text-sm text-slate-500">Periksa dokumen rekomendasi desa yang masuk ke kecamatan.</p>
                    </div>
                    <a href="{{ route('kecamatan.program.index', 'sdss') }}?status=diteruskan_ke_kecamatan&filter_kecamatan=belum" class="btn btn-primary bg-amber-600 border-amber-600 hover:bg-amber-700 shrink-0 shadow-md shadow-amber-500/20">
                        <i data-lucide="play" class="w-4 h-4"></i> Mulai
                    </a>
                </div>
            </div>
            <div class="card hover:shadow-md transition-shadow">
                <div class="card-body flex items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-slate-900 mb-1">Riwayat Verifikasi</h3>
                        <p class="text-sm text-slate-500">Lihat rekam jejak persetujuan yang telah dilakukan kecamatan.</p>
                    </div>
                    <a href="{{ route('kecamatan.riwayat') }}" class="btn btn-outline shrink-0">
                        <i data-lucide="history" class="w-4 h-4"></i> Riwayat
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Monitoring Per Desa (Chart) --}}
            <div class="lg:col-span-1">
                <div class="card h-full">
                    <div class="card-header flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                            <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
                        </div>
                        <span class="font-bold">Monitoring Desa</span>
                    </div>
                    <div class="card-body flex flex-col justify-center">
                        <div class="relative w-full h-[300px]">
                            <canvas id="desaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pendaftar Terbaru --}}
            <div class="lg:col-span-2">
                <div class="card h-full flex flex-col">
                    <div class="card-header flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                        </div>
                        <span class="font-bold">Pendaftar Terbaru di Wilayah Ini</span>
                    </div>
                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-slate-500 font-medium border-b border-slate-100">
                                <tr>
                                    <th class="px-4 py-3">Nama / NIK</th>
                                    <th class="px-4 py-3">Desa</th>
                                    <th class="px-4 py-3 text-center">Skor</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($aktivitasTerbaru as $p)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-800">{{ $p->identitas->nama_lengkap ?? '-' }}</div>
                                        <div class="text-xs text-slate-500 mt-0.5">{{ $p->identitas->nik ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-slate-700">Desa {{ $p->identitas->desa->nama_desa ?? '-' }}</div>
                                        <div class="text-xs text-slate-500 mt-0.5">{{ $p->program->nama }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($p->total_nilai !== null)
                                            <span class="font-bold text-amber-600">{{ number_format($p->total_nilai, 2) }}</span>
                                        @else
                                            <span class="text-xs italic text-slate-400">Belum Dinilai</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge {{ $p->status_color }}">{{ $p->status_label }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('kecamatan.show', $p->id) }}" class="btn btn-xs btn-outline">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center">
                                        <x-empty-state icon="inbox" title="Belum Ada Pendaftar" text="Belum ada pendaftar dari wilayah ini." />
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($stats['total'] > 5)
                    <div class="p-4 border-t border-slate-100 flex justify-center bg-slate-50/50">
                        <a href="{{ route('kecamatan.program.index', 'sdss') }}" class="group inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-white border border-slate-200 text-slate-600 font-medium text-sm hover:bg-primary hover:border-primary hover:text-white hover:shadow-lg hover:shadow-primary/30 transition-all duration-300 transform hover:-translate-y-0.5">
                            Lihat Selengkapnya ({{ $stats['total'] }} Data)
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data monitoringDesa dari controller diubah ke JSON
            const chartData = @json($monitoringDesa);
            
            const labels = chartData.map(item => item.desa.nama_desa);
            const totalData = chartData.map(item => item.total_pendaftar);
            const disetujuiData = chartData.map(item => item.disetujui);

            const ctx = document.getElementById('desaChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Disetujui',
                            data: disetujuiData,
                            backgroundColor: '#10B981', // Emerald 500
                            borderRadius: 4,
                        },
                        {
                            label: 'Total Pendaftar',
                            data: totalData,
                            backgroundColor: '#F59E0B', // Amber 500
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 11
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                }
            });
        });
    </script>
    @endpush
</x-layouts.admin>
