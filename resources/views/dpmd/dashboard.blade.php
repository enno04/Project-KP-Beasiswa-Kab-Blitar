<x-layouts.admin :title="'Dashboard DPMD'">
    <div class="space-y-6">
        {{-- Welcome Banner --}}
        <div class="card overflow-hidden relative" style="background: linear-gradient(135deg, #7C3AED, #4C1D95);">
            <div class="absolute inset-0">
                <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
            </div>
            <div class="relative card-body text-white">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center backdrop-blur-sm">
                        <i data-lucide="landmark" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-widest text-violet-200">Admin DPMD</span>
                </div>
                <h1 class="text-2xl font-extrabold">Selamat Datang, {{ auth()->user()->nama }}!</h1>
                <p class="text-violet-100 text-sm mt-1">Verifikasi rekomendasi Desa untuk program Satu Desa Satu Sarjana (SDSS) se-Kabupaten Blitar.</p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <x-stat-card label="Total Masuk" :value="$stats['total']" icon="users" color="#7C3AED" />
            <x-stat-card label="Perlu Diverifikasi" :value="$stats['menunggu_verifikasi_dpmd']" icon="inbox" color="#D97706" />
            <x-stat-card label="Disetujui DPMD" :value="$stats['disetujui_dpmd']" icon="check-circle" color="#059669" />
            <x-stat-card label="Ditolak DPMD" :value="$stats['ditolak_dpmd']" icon="x-circle" color="#DC2626" />
            <x-stat-card label="Lulus Final" :value="$stats['lulus']" icon="award" color="#0284C7" />
        </div>

        {{-- Monitoring Per Kecamatan --}}
        <div class="card overflow-hidden">
            <div class="card-header flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center">
                    <i data-lucide="map" class="w-4 h-4"></i>
                </div>
                <div>
                    <span class="font-bold">Monitoring Per Kecamatan</span>
                    <p class="text-xs text-slate-400 font-normal mt-0.5">Status pendaftar SDSS yang sudah diteruskan oleh Desa, per kecamatan.</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kecamatan</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Menunggu</th>
                            <th class="text-center">Disetujui</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monitoringKecamatan as $mk)
                        <tr>
                            <td class="font-semibold text-slate-900">{{ $mk->nama_kecamatan }}</td>
                            <td class="text-center">{{ $mk->total }}</td>
                            <td class="text-center">
                                <x-badge :type="$mk->menunggu > 0 ? 'warning' : 'muted'">{{ $mk->menunggu }}</x-badge>
                            </td>
                            <td class="text-center">
                                <x-badge type="success">{{ $mk->disetujui }}</x-badge>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4">
                                <x-empty-state icon="map" title="Tidak Ada Data" text="Belum ada pendaftar SDSS yang diteruskan." />
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pendaftar Terbaru --}}
        <div class="card overflow-hidden">
            <div class="card-header flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
                Pendaftar SDSS Terbaru
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama / NIK</th>
                            <th>Desa / Kec.</th>
                            <th>Skor & Rank</th>
                            <th class="text-center">Status Kec.</th>
                            <th class="text-center">Status DPMD</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aktivitasTerbaru as $p)
                        <tr>
                            <td>
                                <p class="font-semibold text-slate-900">{{ $p->identitas->nama_lengkap ?? '-' }}</p>
                                <p class="text-xs text-slate-400">NIK: {{ $p->identitas->nik ?? '-' }}</p>
                            </td>
                            <td>
                                <p class="font-medium text-sm">Desa {{ $p->identitas->desa->nama_desa ?? '-' }}</p>
                                <p class="text-xs text-slate-400">Kec. {{ $p->identitas->kecamatan->nama_kecamatan ?? '-' }}</p>
                            </td>
                            <td>
                                @if($p->total_nilai !== null)
                                    <span class="font-bold text-primary-dark">{{ number_format($p->total_nilai, 4) }}</span>
                                    <p class="text-xs text-slate-400">Rank: {{ $p->ranking ?? '-' }}</p>
                                @else
                                    <span class="text-xs italic text-slate-400">Belum dinilai</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php $skec = $p->rekomendasiDesa?->status_kecamatan ?? '-'; @endphp
                                <x-badge :type="$skec === 'disetujui' ? 'success' : ($skec === 'ditolak' ? 'danger' : 'warning')">
                                    {{ ucfirst($skec) }}
                                </x-badge>
                            </td>
                            <td class="text-center">
                                @php $sdpmd = $p->rekomendasiDesa?->status_dpmd ?? '-'; @endphp
                                <x-badge :type="$sdpmd === 'disetujui' ? 'success' : ($sdpmd === 'ditolak' ? 'danger' : 'warning')">
                                    {{ ucfirst($sdpmd) }}
                                </x-badge>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $p->status_color }}">{{ $p->status_label }}</span>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('dpmd.show', $p->id) }}" class="btn btn-xs btn-outline">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <x-empty-state icon="inbox" title="Belum Ada Pendaftar" text="Belum ada pendaftar SDSS yang diteruskan oleh Desa." />
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
