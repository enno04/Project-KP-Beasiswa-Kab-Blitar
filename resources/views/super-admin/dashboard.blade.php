<x-layouts.admin :title="'Dashboard'">
    <div class="space-y-6">
        {{-- Welcome Banner --}}
        <div class="card overflow-hidden relative" style="background: linear-gradient(135deg, #2B5C92, #0C1446);">
            <div class="absolute inset-0">
                <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
                <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-white/5 rounded-full blur-2xl"></div>
            </div>
            <div class="relative card-body text-white">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center backdrop-blur-sm">
                        <i data-lucide="shield" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-widest text-primary-light">Super Admin</span>
                </div>
                <h1 class="text-2xl font-extrabold">Selamat Datang, {{ auth()->user()->nama }}!</h1>
                <p class="text-primary-light text-sm mt-1">Ringkasan kondisi sistem Beasiswa Blitar Mengabdi</p>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Total Pengguna" :value="$stats['total_user']" icon="users" color="#2B5C92" />
            <x-stat-card label="Total OPD" :value="$stats['total_opd']" icon="building-2" color="#059669" />
            <x-stat-card label="Total Program" :value="$stats['total_program']" icon="tag" color="#D97706" />
            <x-stat-card label="Periode Aktif" :value="$stats['periode_aktif']" icon="calendar" color="#0284C7" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Total Kecamatan" :value="$stats['total_kecamatan']" icon="map" color="#7C3AED" />
            <x-stat-card label="Total Desa" :value="$stats['total_desa']" icon="map-pin" color="#DB2777" />
            <x-stat-card label="Total Pendaftar" :value="$stats['total_pendaftar']" icon="file-text" color="#059669" />
            <x-stat-card label="Total Penerima" :value="$stats['total_penerima']" icon="award" color="#DC2626" />
        </div>

        {{-- Analytics Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Chart Program --}}
            <div class="card p-5">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="pie-chart" class="w-4 h-4 text-[#2B5C92]"></i> Pendaftar per Program
                    </h3>
                </div>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas id="programChart"></canvas>
                </div>
            </div>

            {{-- Chart Status --}}
            <div class="card p-5">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="bar-chart-3" class="w-4 h-4 text-[#059669]"></i> Distribusi Status
                    </h3>
                </div>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="card">
            <div class="card-header flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-primary-light text-primary flex items-center justify-center">
                    <i data-lucide="zap" class="w-4 h-4"></i>
                </div>
                Aksi Cepat
            </div>
            <div class="card-body">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @php
                        $quickActions = [
                            ['route' => 'super-admin.master.program.index', 'icon' => 'tag', 'label' => 'Program Beasiswa', 'color' => '#2B5C92'],
                            ['route' => 'super-admin.master.periode.index', 'icon' => 'calendar', 'label' => 'Periode', 'color' => '#D97706'],
                            ['route' => 'super-admin.master.users.index', 'icon' => 'users', 'label' => 'Pengguna', 'color' => '#059669'],
                            ['route' => 'super-admin.audit-log', 'icon' => 'scroll-text', 'label' => 'Audit Log', 'color' => '#7C3AED'],
                        ];
                    @endphp
                    @foreach($quickActions as $action)
                        <a href="{{ route($action['route']) }}" class="group flex flex-col items-center gap-3 p-5 rounded-xl border border-slate-100 hover:border-slate-200 hover:shadow-md transition-all duration-200">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform" style="background-color: {{ $action['color'] }}12; color: {{ $action['color'] }};">
                                <i data-lucide="{{ $action['icon'] }}" class="w-6 h-6"></i>
                            </div>
                            <span class="text-xs font-semibold text-center text-slate-600 group-hover:text-slate-900">{{ $action['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data from Controller
            const rawProgramData = @json($pendaftarPerProgram);
            const rawStatusData = @json($pendaftarPerStatus);

            // Chart Program (Doughnut)
            const programLabels = rawProgramData.map(item => item.nama);
            const programData = rawProgramData.map(item => item.total);
            const ctxProgram = document.getElementById('programChart').getContext('2d');
            new Chart(ctxProgram, {
                type: 'doughnut',
                data: {
                    labels: programLabels,
                    datasets: [{
                        data: programData,
                        backgroundColor: ['#2B5C92', '#059669', '#D97706', '#7C3AED', '#DB2777'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { family: 'Inter', size: 11 } } }
                    }
                }
            });

            // Chart Status (Bar)
            const statusMapping = {
                'draft': 'Draft',
                'menunggu_verifikasi': 'Menunggu Verif',
                'lolos_verifikasi': 'Lolos Verif',
                'tidak_lolos_verifikasi': 'Tdk Lolos Verif',
                'menunggu_penetapan': 'Menunggu Penetapan',
                'lulus': 'Ditetapkan (Lulus)',
                'tidak_lulus': 'Tdk Lulus'
            };
            
            const statusLabels = rawStatusData.map(item => {
                if (statusMapping[item.status]) {
                    return statusMapping[item.status];
                }
                // Fallback: ubah snake_case ke Title Case
                return item.status.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
            });
            const statusData = rawStatusData.map(item => item.total);
            const ctxStatus = document.getElementById('statusChart').getContext('2d');
            new Chart(ctxStatus, {
                type: 'bar',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        label: 'Total Pendaftar',
                        data: statusData,
                        backgroundColor: '#059669',
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } },
                        x: { grid: { display: false }, ticks: { font: { family: 'Inter', size: 10 } } }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        });
    </script>
    @endpush
</x-layouts.admin>
