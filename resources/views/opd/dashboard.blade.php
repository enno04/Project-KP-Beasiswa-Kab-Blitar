<x-layouts.admin :title="'Dashboard OPD'">
    <div class="space-y-6">
        {{-- Welcome Banner --}}
        <div class="card overflow-hidden relative" style="background: linear-gradient(135deg, #7C3AED, #6D28D9);">
            <div class="absolute inset-0">
                <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
            </div>
            <div class="relative card-body flex flex-col sm:flex-row justify-between sm:items-center text-white p-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center backdrop-blur-sm">
                            <i data-lucide="file-check" class="w-5 h-5"></i>
                        </div>
                        <span class="text-xs font-bold uppercase tracking-widest text-violet-200">Verifikator OPD</span>
                    </div>
                    <h1 class="text-2xl font-extrabold">Selamat Datang, {{ auth()->user()->nama }}!</h1>
                    <p class="text-violet-100 text-sm mt-1">Pantau antrean dokumen yang membutuhkan verifikasi dari instansi Anda.</p>
                </div>
                <div class="hidden sm:block text-right mt-4 sm:mt-0">
                    <div class="text-xs text-violet-200 font-semibold mb-1 uppercase tracking-wider">Progress Kinerja</div>
                    <div class="text-3xl font-black">{{ $persentase }}%</div>
                    <div class="text-xs text-violet-200 mt-1">{{ $stats['valid'] + $stats['tidak_valid'] }} dari {{ $totalDokumen }} Dokumen</div>
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
                <strong class="font-bold">Perhatian!</strong> Terdapat <span class="font-black underline">{{ $slaWarning }} dokumen</span> yang sudah berada di antrean lebih dari 3 hari. Mohon segera diproses.
            </div>
            <a href="{{ route('opd.verifikasi.index', ['status' => 'belum_diverifikasi']) }}" class="btn btn-xs btn-danger whitespace-nowrap">Lihat Berkas</a>
        </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-stat-card label="Antrean Verifikasi" :value="$stats['belum_diverifikasi']" icon="clock" color="#D97706" />
            <x-stat-card label="Dokumen Valid" :value="$stats['valid']" icon="check-circle" color="#059669" />
            <x-stat-card label="Dokumen Tidak Valid" :value="$stats['tidak_valid']" icon="x-circle" color="#DC2626" />
        </div>

        {{-- Quick Actions --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="card border-primary/20 bg-primary-50/30 hover:shadow-md transition-shadow">
                <div class="card-body flex items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-primary-dark mb-1">Mulai Verifikasi</h3>
                        <p class="text-sm text-slate-500">Periksa dokumen yang ada di antrean Anda.</p>
                    </div>
                    <a href="{{ route('opd.verifikasi.index', ['status' => 'belum_diverifikasi']) }}" class="btn btn-primary shrink-0 shadow-md shadow-primary/20">
                        <i data-lucide="play" class="w-4 h-4"></i> Mulai
                    </a>
                </div>
            </div>
            <div class="card hover:shadow-md transition-shadow">
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

        {{-- Antrean Terbaru --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-800">Antrean Terbaru</h2>
            </div>
            <div class="card overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 font-medium border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3">Pendaftar</th>
                            <th class="px-4 py-3">Dokumen</th>
                            <th class="px-4 py-3">Waktu Upload</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($aktivitasTerbaru as $upload)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-800">{{ $upload->pendaftaran->identitas->nama_lengkap ?? 'Tanpa Nama' }}</div>
                                <div class="text-xs text-slate-500">{{ $upload->pendaftaran->nomor_pendaftaran }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $upload->dokumen->nama ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-xs text-slate-500">{{ $upload->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('opd.verifikasi.show', $upload->id) }}" class="btn btn-xs btn-primary"><i data-lucide="check-square" class="w-3.5 h-3.5"></i> Proses</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-8">
                                <i data-lucide="check-circle-2" class="w-12 h-12 text-green-200 mx-auto mb-3"></i>
                                <p class="text-slate-500 font-medium">Luar biasa! Tidak ada antrean baru saat ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                
                @if(count($aktivitasTerbaru) > 0)
                <div class="p-3 border-t border-slate-100 bg-slate-50/50">
                    <a href="{{ route('opd.verifikasi.index', ['status' => 'belum_diverifikasi']) }}" class="btn w-full justify-center bg-white border border-slate-200 text-slate-600 hover:text-primary hover:border-primary-light hover:bg-primary-50 transition-all">
                        Lihat Semua Antrean <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>

    </div>
</x-layouts.admin>
