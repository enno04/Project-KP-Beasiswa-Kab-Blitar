<x-layouts.admin :title="'Data Pendaftar SDSS — ' . $program->nama">
    <x-page-header :title="'Data Pendaftar SDSS: ' . $program->nama" :subtitle="$jalur ? 'Jalur: ' . $jalur->nama : null">
        <x-slot:actions>
            <div class="flex items-center gap-2.5 px-4 py-2 rounded-xl border shadow-sm"
                 style="background: linear-gradient(135deg, #f0f7ff 0%, #e8f4fd 100%); border-color: #bfdbfe;">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg"
                     style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                    <i data-lucide="users" class="w-4 h-4 text-white"></i>
                </div>
                <div class="leading-tight">
                    <p class="text-xs font-medium text-blue-500 uppercase tracking-wide">Total Pendaftar</p>
                    <p class="text-lg font-bold text-blue-900">{{ number_format($pendaftar->total(), 0, ',', '.') }}</p>
                </div>
            </div>
        </x-slot:actions>
    </x-page-header>

    {{-- Filter --}}
    <div class="card mb-6">
        <div class="card-body py-4">
            <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 items-end">
                <div class="flex-1 w-full min-w-[200px]">
                    <label class="form-label">Cari Pendaftar</label>
                    <div class="relative">
                        <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-input" style="padding-left: 2.5rem;" placeholder="Nama atau No. Pendaftaran...">
                    </div>
                </div>

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
                    <label class="form-label">Filter Kecamatan</label>
                    <select name="kecamatan_id" id="kecamatan_id" class="form-select" onchange="filterDesa()">
                        <option value="">Semua Kecamatan</option>
                        @foreach($kecamatanList as $kec)
                            <option value="{{ $kec->id }}" {{ request('kecamatan_id') == $kec->id ? 'selected' : '' }}>{{ $kec->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:w-48">
                    <label class="form-label">Filter Desa</label>
                    <select name="desa_id" id="desa_id" class="form-select">
                        <option value="">Semua Desa</option>
                        @foreach($desaList as $desa)
                            <option value="{{ $desa->id }}" {{ request('desa_id') == $desa->id ? 'selected' : '' }}>{{ $desa->nama_desa }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:w-48">
                    <label class="form-label">Status DPMD</label>
                    <select name="filter_dpmd" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        <option value="belum" {{ request('filter_dpmd') === 'belum' ? 'selected' : '' }}>Belum Diverifikasi</option>
                        <option value="disetujui" {{ request('filter_dpmd') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    </select>
                </div>
                
                <div class="w-full sm:w-48">
                    <label class="form-label">Status Pendaftaran</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="diteruskan_ke_kecamatan" {{ request('status') === 'diteruskan_ke_kecamatan' ? 'selected' : '' }}>Diteruskan ke Kecamatan / DPMD</option>
                        <option value="ditolak_kecamatan" {{ request('status') === 'ditolak_kecamatan' ? 'selected' : '' }}>Ditolak Kecamatan</option>
                        <option value="proses_seleksi" {{ request('status') === 'proses_seleksi' ? 'selected' : '' }}>Proses Seleksi</option>
                        <option value="menunggu_penetapan" {{ request('status') === 'menunggu_penetapan' ? 'selected' : '' }}>Menunggu Penetapan</option>
                        <option value="lulus" {{ request('status') === 'lulus' ? 'selected' : '' }}>Lulus Final</option>
                        <option value="tidak_lulus" {{ request('status') === 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                    </select>
                </div>
                
                <div class="flex gap-2 w-full sm:w-auto">
                    <button type="submit" class="btn btn-primary"><i data-lucide="filter" class="w-4 h-4"></i> Filter</button>
                    @if(request()->hasAny(['search', 'tahun', 'kecamatan_id', 'desa_id', 'status', 'filter_dpmd']))
                        <a href="{{ url()->current() }}" class="btn btn-outline"><i data-lucide="x" class="w-4 h-4"></i> Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No.</th>
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
                            <td><span class="text-slate-500 font-medium">{{ $pendaftar->firstItem() + $loop->index }}</span></td>
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
                        <tr><td colspan="9"><x-empty-state icon="folder-open" title="Tidak Ada Data" text="Tidak ada data pendaftar SDSS pada filter ini." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pendaftar->hasPages())
            <div class="card-footer">{{ $pendaftar->links() }}</div>
        @endif
    </div>

    @push('scripts')
    <script>
        const allDesa = @json($desaList->map(fn($d) => ['id' => $d->id, 'nama' => $d->nama_desa, 'kecamatan_id' => $d->kecamatan_id]));
        const reqDesaId = "{{ request('desa_id') }}";
        
        function filterDesa() {
            const kecId = document.getElementById('kecamatan_id').value;
            const desaSelect = document.getElementById('desa_id');
            const currentValue = desaSelect.value || reqDesaId;
            
            desaSelect.innerHTML = '<option value="">Semua Desa</option>';
            
            let valueFound = false;
            
            allDesa.forEach(desa => {
                if (!kecId || desa.kecamatan_id == kecId) {
                    const opt = document.createElement('option');
                    opt.value = desa.id;
                    opt.textContent = desa.nama;
                    if (desa.id == currentValue) {
                        opt.selected = true;
                        valueFound = true;
                    }
                    desaSelect.appendChild(opt);
                }
            });
            
            if (!valueFound && currentValue !== '') {
                desaSelect.value = ''; 
            }
        }
        
        document.addEventListener('DOMContentLoaded', filterDesa);
    </script>
    @endpush
</x-layouts.admin>
