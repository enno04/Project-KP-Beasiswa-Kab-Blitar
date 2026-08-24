@props(['pendaftar', 'tabName'])

<div x-show="tab === '{{ $tabName }}'" {!! $tabName !== 'baru' ? 'x-cloak' : '' !!}>
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
                @forelse($pendaftar as $p)
                <tr class="{{ $p->ranking === 1 ? 'bg-amber-50' : '' }}">
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
                            @if($p->ranking === 1)
                                <p class="text-xs font-bold text-amber-600">Rank: {{ $p->ranking ?? '-' }} (Peringkat 1)</p>
                            @else
                                <p class="text-xs text-slate-400">Rank: {{ $p->ranking ?? '-' }}</p>
                            @endif
                        @else
                            <span class="text-xs italic text-slate-400">Belum dinilai</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $p->status_color }}">{{ $p->status_label }}</span>
                    </td>
                    <td class="text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('desa.show', $p->id) }}" class="btn btn-xs btn-outline">Detail</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state icon="inbox" title="Tidak Ada Data" text="Belum ada pendaftar pada kelompok status ini." />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pendaftar->total() > 5)
        @php
            $targetUrl = match($tabName) {
                'selesai' => route('desa.riwayat'),
                default => route('desa.program.index', 'sdss')
            };
        @endphp
        <div class="p-4 border-t border-slate-100 flex justify-center bg-slate-50/50">
            <a href="{{ $targetUrl }}" class="group inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-white border border-slate-200 text-slate-600 font-medium text-sm hover:bg-primary hover:border-primary hover:text-white hover:shadow-lg hover:shadow-primary/30 transition-all duration-300 transform hover:-translate-y-0.5">
                Lihat Selengkapnya ({{ $pendaftar->total() }} Data)
                <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    @endif
</div>
