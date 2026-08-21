<x-layouts.admin :title="'Dashboard Kecamatan'">
    <div class="space-y-6">
        {{-- Welcome Banner --}}
        <div class="card overflow-hidden relative" style="background: linear-gradient(135deg, #D97706, #B45309);">
            <div class="absolute inset-0">
                <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
            </div>
            <div class="relative card-body text-white">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center backdrop-blur-sm">
                        <i data-lucide="map" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-widest text-amber-200">Admin Kecamatan</span>
                </div>
                <h1 class="text-2xl font-extrabold">Selamat Datang, {{ auth()->user()->nama }}!</h1>
                <p class="text-amber-100 text-sm mt-1">Pantau pendaftaran beasiswa dan verifikasi berkas dari seluruh desa di wilayah Anda.</p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Total Diteruskan" :value="$stats['total']" icon="users" color="#2B5C92" />
            <x-stat-card label="Menunggu Verif Kec." :value="$stats['diteruskan_ke_kecamatan']" icon="inbox" color="#D97706" />
            <x-stat-card label="Menunggu Penetapan" :value="$stats['menunggu_penetapan']" icon="check-square" color="#0284C7" />
            <x-stat-card label="Ditetapkan Lulus" :value="$stats['lulus']" icon="award" color="#059669" />
        </div>

        {{-- Monitoring Desa --}}
        <div class="card overflow-hidden">
            <div class="card-header flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-primary-light text-primary flex items-center justify-center">
                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                </div>
                <div>
                    <span class="font-bold">Monitoring Status Musyawarah Desa</span>
                    <p class="text-xs text-slate-400 font-normal mt-0.5">Pantau desa mana yang sudah mengirim berkas Berita Acara & Surat Rekomendasi.</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Desa</th>
                            <th class="text-center">Total Pendaftar</th>
                            <th class="text-center">Sudah Kirim</th>
                            <th class="text-center">Belum Kirim</th>
                            <th class="text-center">Disetujui</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monitoringDesa as $md)
                        <tr>
                            <td class="font-semibold text-slate-900">{{ $md['desa']->nama_desa }}</td>
                            <td class="text-center">{{ $md['total_pendaftar'] }}</td>
                            <td class="text-center">
                                <x-badge :type="$md['sudah_kirim'] > 0 ? 'info' : 'muted'">{{ $md['sudah_kirim'] }}</x-badge>
                            </td>
                            <td class="text-center">
                                <x-badge :type="$md['belum_kirim'] > 0 ? 'warning' : 'success'">{{ $md['belum_kirim'] }}</x-badge>
                            </td>
                            <td class="text-center">
                                <x-badge type="success">{{ $md['disetujui'] }}</x-badge>
                            </td>
                            <td class="text-center">
                                @if($md['total_pendaftar'] === 0)
                                    <span class="text-xs text-slate-400">Belum ada</span>
                                @elseif($md['belum_kirim'] > 0)
                                    <x-badge type="warning" dot>BELUM LENGKAP</x-badge>
                                @elseif($md['disetujui'] === $md['total_pendaftar'])
                                    <x-badge type="success" dot>SELESAI</x-badge>
                                @else
                                    <x-badge type="info" dot>PROSES</x-badge>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state icon="map" title="Tidak Ada Data" text="Tidak ada data desa di wilayah ini." />
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
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
                Pendaftar Terbaru di Wilayah Ini
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama / NIK</th>
                            <th>Program & Jalur</th>
                            <th>Desa</th>
                            <th>Skor & Rank</th>
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
                                <p class="font-medium text-primary-dark text-sm">{{ $p->program->nama }}</p>
                                <p class="text-xs text-slate-400">{{ $p->jalur->nama }}</p>
                            </td>
                            <td class="font-medium text-sm">Desa {{ $p->identitas->desa->nama_desa ?? '-' }}</td>
                            <td>
                                @if($p->total_nilai !== null)
                                    <span class="font-bold text-primary-dark">{{ number_format($p->total_nilai, 4) }}</span>
                                    <p class="text-xs text-slate-400">Rank: {{ $p->ranking ?? '-' }}</p>
                                @else
                                    <span class="text-xs italic text-slate-400">Belum dinilai</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $p->status_color }}">{{ $p->status_label }}</span>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('kecamatan.show', $p->id) }}" class="btn btn-xs btn-outline">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state icon="inbox" title="Belum Ada Pendaftar" text="Belum ada pendaftar dari wilayah ini." />
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
