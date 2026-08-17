<x-layouts.admin :title="'Data Pendaftar — ' . $program->nama">
    <x-page-header :title="'Data Pendaftar: ' . $program->nama" :subtitle="$jalur ? 'Jalur: ' . $jalur->nama : null">
        <x-slot:actions>
            <span class="badge badge-primary text-xs">{{ $pendaftar->total() }} data</span>
        </x-slot:actions>
    </x-page-header>

    {{-- Filter --}}
    <div class="card mb-6">
        <div class="card-body py-4">
            <form method="GET" class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="w-full sm:w-48">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Tahun</option>
                        @for($i = date('Y'); $i >= 2024; $i--)
                            <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="w-full sm:w-64">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="menunggu_verifikasi" {{ request('status') === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="lolos_verifikasi" {{ request('status') === 'lolos_verifikasi' ? 'selected' : '' }}>Lolos Verifikasi</option>
                        <option value="ditolak_verifikasi" {{ request('status') === 'ditolak_verifikasi' ? 'selected' : '' }}>Ditolak Verifikasi</option>
                        <option value="menunggu_penetapan" {{ request('status') === 'menunggu_penetapan' ? 'selected' : '' }}>Menunggu Penetapan</option>
                        <option value="lulus" {{ request('status') === 'lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="tidak_lulus" {{ request('status') === 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                    </select>
                </div>
                @if(request()->hasAny(['tahun', 'status']))
                    <a href="{{ url()->current() }}" class="btn btn-sm btn-outline"><i data-lucide="x" class="w-3.5 h-3.5"></i> Reset</a>
                @endif
            </form>
        </div>
    </div>

    {{-- SDSS Penilaian Panel --}}
    @if($program->isSdss() && $jalur && $periodeAktif)
    <div class="card mb-6">
        <div class="card-body flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="font-bold text-slate-900">Penilaian & Pemeringkatan ({{ $periodeAktif->nama }})</h3>
                <p class="text-xs text-slate-400 mt-1">Kalkulasi nilai dan ranking untuk pendaftar yang lolos verifikasi OPD.</p>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('desa.penilaian.hitung') }}" method="POST" onsubmit="return confirm('Proses penilaian & perangkingan pendaftar?');">
                    @csrf
                    <input type="hidden" name="jalur_id" value="{{ $jalur->id }}">
                    <input type="hidden" name="periode_id" value="{{ $periodeAktif->id }}">
                    <button type="submit" class="btn btn-sm btn-primary"><i data-lucide="calculator" class="w-4 h-4"></i> Hitung Penilaian & Ranking</button>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No. Pendaftaran</th>
                        <th>Nama Lengkap</th>
                        <th>Skor & Rank</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftar as $p)
                        <tr>
                            <td><span class="font-bold text-primary-dark">{{ $p->nomor_pendaftaran }}</span></td>
                            <td>
                                <p class="font-semibold text-slate-900">{{ $p->identitas->nama_lengkap ?? '-' }}</p>
                                <p class="text-xs text-slate-400">NIK: {{ $p->identitas->nik ?? '-' }}</p>
                            </td>
                            <td>
                                @if($p->total_nilai !== null)
                                    <span class="font-bold text-primary-dark">{{ number_format($p->total_nilai, 4) }}</span>
                                    <p class="text-xs text-slate-400">Rank: {{ $p->ranking ?? '-' }}</p>
                                @else
                                    <span class="text-xs italic text-slate-400">Belum dinilai</span>
                                @endif
                            </td>
                            <td><span class="badge {{ $p->status_color }}">{{ $p->status_label }}</span></td>
                            <td class="text-right">
                                <div class="flex justify-end gap-2">
                                    @if($program->isSdss() && $p->ranking && in_array($p->status, ['lolos_verifikasi', 'menunggu_penetapan']))
                                        <form action="{{ route('desa.tetapkan', $p->id) }}" method="POST" class="inline" onsubmit="return confirm('Tetapkan sebagai perwakilan Desa?');">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-success"><i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Tetapkan</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('desa.show', $p->id) }}" class="btn btn-xs btn-outline"><i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-empty-state icon="folder-open" title="Tidak Ada Data" text="Tidak ada data pendaftar pada filter ini." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pendaftar->hasPages())
            <div class="card-footer">{{ $pendaftar->links() }}</div>
        @endif
    </div>
</x-layouts.admin>
