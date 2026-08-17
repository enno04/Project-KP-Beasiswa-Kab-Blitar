<x-layouts.admin :title="'Data Pendaftar SDSS — ' . $program->nama">
    <x-page-header :title="'Data Pendaftar SDSS: ' . $program->nama" :subtitle="$jalur ? 'Jalur: ' . $jalur->nama : null">
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
                <div class="w-full sm:w-48">
                    <label class="form-label">Status DPMD</label>
                    <select name="filter_dpmd" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        <option value="belum" {{ request('filter_dpmd') === 'belum' ? 'selected' : '' }}>Belum Diverifikasi</option>
                        <option value="disetujui" {{ request('filter_dpmd') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('filter_dpmd') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="w-full sm:w-48">
                    <label class="form-label">Status Pendaftaran</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="diteruskan_ke_kecamatan" {{ request('status') === 'diteruskan_ke_kecamatan' ? 'selected' : '' }}>Menunggu Verif</option>
                        <option value="menunggu_penetapan" {{ request('status') === 'menunggu_penetapan' ? 'selected' : '' }}>Menunggu Penetapan</option>
                        <option value="lulus" {{ request('status') === 'lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="tidak_lulus" {{ request('status') === 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                    </select>
                </div>
                @if(request()->hasAny(['tahun', 'status', 'filter_dpmd']))
                    <a href="{{ url()->current() }}" class="btn btn-sm btn-outline"><i data-lucide="x" class="w-3.5 h-3.5"></i> Reset</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No. Pendaftaran</th>
                        <th>Nama Lengkap</th>
                        <th>Desa / Kec.</th>
                        <th>Skor & Rank</th>
                        <th class="text-center">Kecamatan</th>
                        <th class="text-center">DPMD</th>
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
                                <p class="text-sm font-medium">Desa {{ $p->identitas->desa->nama_desa ?? '-' }}</p>
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
                                @php $skec = $p->rekomendasiDesa?->status_kecamatan ?? 'belum_diverifikasi'; @endphp
                                <x-badge :type="$skec === 'disetujui' ? 'success' : ($skec === 'ditolak' ? 'danger' : 'warning')">
                                    {{ $skec === 'belum_diverifikasi' ? '⏳' : ($skec === 'disetujui' ? '✓' : '✗') }}
                                </x-badge>
                            </td>
                            <td class="text-center">
                                @php $sdpmd = $p->rekomendasiDesa?->status_dpmd ?? 'belum_diverifikasi'; @endphp
                                <x-badge :type="$sdpmd === 'disetujui' ? 'success' : ($sdpmd === 'ditolak' ? 'danger' : 'warning')">
                                    {{ $sdpmd === 'belum_diverifikasi' ? '⏳' : ($sdpmd === 'disetujui' ? '✓' : '✗') }}
                                </x-badge>
                            </td>
                            <td><span class="badge {{ $p->status_color }}">{{ $p->status_label }}</span></td>
                            <td class="text-right">
                                <a href="{{ route('dpmd.show', $p->id) }}" class="btn btn-xs btn-outline"><i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><x-empty-state icon="folder-open" title="Tidak Ada Data" text="Tidak ada data pendaftar SDSS pada filter ini." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pendaftar->hasPages())
            <div class="card-footer">{{ $pendaftar->links() }}</div>
        @endif
    </div>
</x-layouts.admin>
