<x-layouts.admin :title="'Dashboard Desa'">
    <div class="space-y-6">
        {{-- Welcome Banner --}}
        <div class="card overflow-hidden relative" style="background: linear-gradient(135deg, #059669, #047857);">
            <div class="absolute inset-0">
                <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/5 rounded-full blur-2xl"></div>
            </div>
            <div class="relative card-body text-white">
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

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card label="Total Pendaftar" :value="$stats['total']" icon="users" color="#2B5C92" />
            <x-stat-card label="Menunggu Verifikasi" :value="$stats['menunggu_verifikasi']" icon="clock" color="#D97706" />
            <x-stat-card label="Lolos Verifikasi" :value="$stats['lolos_verifikasi']" icon="check-square" color="#0284C7" />
            <x-stat-card label="Ditetapkan Lulus" :value="$stats['lulus']" icon="award" color="#059669" />
        </div>

        {{-- Pendaftar Terbaru --}}
        <div class="card overflow-hidden">
            <div class="card-header flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
                Pendaftar Terbaru dari Desa Ini
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama / NIK</th>
                            <th>Program & Jalur</th>
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
                                <div class="flex justify-end gap-2">
                                    @if($p->program->isSdss() && $p->status === 'menunggu_penetapan' && $p->ranking)
                                        <form action="{{ route('desa.tetapkan', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Tetapkan pendaftar ini sebagai perwakilan Desa?');">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-success">
                                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Tetapkan
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('desa.show', $p->id) }}" class="btn btn-xs btn-outline">Detail</a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
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
