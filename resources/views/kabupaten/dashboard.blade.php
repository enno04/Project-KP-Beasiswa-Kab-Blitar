<x-layouts.admin :title="'Dashboard Kabupaten'">
    <div class="space-y-6">
        {{-- Welcome Banner --}}
        <div class="card overflow-hidden relative" style="background: linear-gradient(135deg, #2B5C92, #0C1446);">
            <div class="absolute inset-0">
                <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
            </div>
            <div class="relative card-body text-white">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center backdrop-blur-sm">
                        <i data-lucide="landmark" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-widest text-primary-light">Admin Kabupaten</span>
                </div>
                <h1 class="text-2xl font-extrabold">Selamat Datang, {{ auth()->user()->nama }}!</h1>
                <p class="text-primary-light text-sm mt-1">
                    Kelola dan pantau seluruh pendaftaran beasiswa.
                    @if($periodeAktif)
                        <span class="font-semibold">Periode: {{ $periodeAktif->nama }} ({{ $periodeAktif->tahun }})</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Total Masuk" :value="$stats['total']" icon="users" color="#2B5C92" />
            <x-stat-card label="Lolos Verifikasi" :value="$stats['lolos_verifikasi']" icon="check-square" color="#0284C7" />
            <x-stat-card label="Menunggu Penetapan" :value="$stats['menunggu_penetapan']" icon="clock" color="#D97706" />
            <x-stat-card label="SK Terbit" :value="$stats['lulus']" icon="award" color="#059669" />
        </div>

        {{-- Pendaftar Terbaru --}}
        <div class="card overflow-hidden">
            <div class="card-header flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-primary-light text-primary flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
                Pendaftar Terbaru (Sudah Diteruskan Kecamatan)
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama / NIK</th>
                            <th>Program & Jalur</th>
                            <th>Waktu Daftar</th>
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
                            <td class="text-xs text-slate-500">{{ $p->created_at->diffForHumans() }}</td>
                            <td class="text-center">
                                @php
                                    $statusType = match($p->status) {
                                        'menunggu_verifikasi' => 'warning',
                                        'lolos_verifikasi' => 'info',
                                        'menunggu_penetapan' => 'warning',
                                        'lulus' => 'success',
                                        'tidak_lulus', 'ditolak_verifikasi' => 'danger',
                                        default => 'muted',
                                    };
                                @endphp
                                <x-badge :type="$statusType">{{ str_replace('_', ' ', $p->status) }}</x-badge>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('kabupaten.show', $p->id) }}" class="btn btn-xs btn-outline">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <x-empty-state icon="inbox" title="Belum Ada Pendaftar" text="Belum ada pendaftar yang diteruskan dari kecamatan." />
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
