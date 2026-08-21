<x-layouts.admin :title="'Rekap & Filter Lanjutan'">
    <x-page-header title="Rekap & Filter Lanjutan" subtitle="Cari dan lacak data pendaftar beserta rekam jejak verifikasinya.">
        <x-slot:actions>
            <span class="badge badge-primary text-xs">{{ $pendaftar->total() }} data ditemukan</span>
        </x-slot:actions>
    </x-page-header>

    {{-- Filter --}}
    <div class="card mb-6">
        <div class="card-body py-4">
            <form method="GET" class="flex flex-col gap-4">
                <div class="flex flex-col sm:flex-row flex-wrap gap-4 items-end">
                    <div class="flex-1 w-full min-w-[200px]">
                        <label class="form-label">Pencarian</label>
                        <div class="relative">
                            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-input" style="padding-left: 2.5rem;" placeholder="Nama / No. Pendaftaran...">
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
                        <label class="form-label">Program</label>
                        <select name="program_id" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Program</option>
                            @foreach($programs as $prog)
                                <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>{{ $prog->nama }}</option>
                            @endforeach
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
                        <label class="form-label">Status Pendaftaran</label>
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="diteruskan_ke_kecamatan" {{ request('status') === 'diteruskan_ke_kecamatan' ? 'selected' : '' }}>Menunggu Verif Kec/DPMD</option>
                            <option value="lolos_verifikasi" {{ request('status') === 'lolos_verifikasi' ? 'selected' : '' }}>Lolos Verifikasi</option>
                            <option value="menunggu_penetapan" {{ request('status') === 'menunggu_penetapan' ? 'selected' : '' }}>Menunggu Penetapan</option>
                            <option value="lulus" {{ request('status') === 'lulus' ? 'selected' : '' }}>Lulus</option>
                            <option value="tidak_lulus" {{ request('status') === 'tidak_lulus' ? 'selected' : '' }}>Tidak Lulus</option>
                        </select>
                    </div>

                    <div class="w-full sm:w-48">
                        <label class="form-label">Tipe Persetujuan (SDSS)</label>
                        <select name="verifikasi_tipe" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Tipe</option>
                            <option value="sistem" {{ request('verifikasi_tipe') === 'sistem' ? 'selected' : '' }}>Otomatis (SLA)</option>
                            <option value="manual" {{ request('verifikasi_tipe') === 'manual' ? 'selected' : '' }}>Manual</option>
                        </select>
                    </div>

                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="submit" class="btn btn-primary"><i data-lucide="filter" class="w-4 h-4"></i> Terapkan</button>
                        @if(request()->hasAny(['search', 'tahun', 'program_id', 'kecamatan_id', 'desa_id', 'status', 'verifikasi_tipe']))
                            <a href="{{ route('kabupaten.rekap-data') }}" class="btn btn-outline"><i data-lucide="x" class="w-4 h-4"></i> Reset</a>
                        @endif
                    </div>
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
                        <th>Identitas Pendaftar</th>
                        <th>Program & Jalur</th>
                        <th>Asal Wilayah</th>
                        <th>Status</th>
                        <th>Tipe Persetujuan (SDSS)</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftar as $p)
                        <tr>
                            <td>
                                <p class="font-bold text-primary-dark text-sm">{{ $p->nomor_pendaftaran }}</p>
                                <p class="text-sm font-medium">{{ $p->identitas->nama_lengkap ?? '-' }}</p>
                            </td>
                            <td>
                                <p class="font-medium text-sm">{{ $p->program->nama ?? '-' }}</p>
                                <p class="text-xs text-slate-500">{{ $p->jalur->nama ?? '-' }}</p>
                            </td>
                            <td>
                                <p class="font-medium text-sm">{{ $p->identitas->desa->nama_desa ?? '-' }}</p>
                                <p class="text-xs text-slate-500">{{ $p->identitas->desa->kecamatan->nama_kecamatan ?? '-' }}</p>
                            </td>
                            <td>
                                @if(in_array($p->status, ['lulus', 'lolos_verifikasi']))
                                    <x-badge type="success">{{ $p->status_label }}</x-badge>
                                @elseif(in_array($p->status, ['tidak_lulus', 'ditolak_verifikasi']))
                                    <x-badge type="danger">{{ $p->status_label }}</x-badge>
                                @else
                                    <x-badge type="warning">{{ $p->status_label }}</x-badge>
                                @endif
                            </td>
                            <td class="text-sm">
                                @if($p->program && $p->program->isSdss() && $p->rekomendasiDesa)
                                    @php
                                        $isAuto = is_null($p->rekomendasiDesa->kecamatan_verified_by) && is_null($p->rekomendasiDesa->dpmd_verified_by);
                                    @endphp
                                    
                                    @if($p->rekomendasiDesa->status_kecamatan == 'disetujui' && $p->rekomendasiDesa->status_dpmd == 'disetujui')
                                        @if($isAuto)
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-1 rounded-md border border-amber-200">
                                                <i data-lucide="bot" class="w-3.5 h-3.5"></i> Verifikasi Sistem (SLA)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-200">
                                                <i data-lucide="user-check" class="w-3.5 h-3.5"></i> Verifikasi Manual
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-slate-400 italic text-xs">Belum Disetujui Penuh</span>
                                    @endif
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('kabupaten.show', $p->id) }}" class="btn btn-xs btn-outline" title="Detail">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><x-empty-state icon="folder-open" title="Tidak Ada Data" text="Tidak ada data pendaftar pada filter ini." /></td></tr>
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
