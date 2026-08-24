<x-layouts.admin :title="'Riwayat Verifikasi DPMD'">
    <x-page-header title="Riwayat Verifikasi" subtitle="Catatan seluruh keputusan persetujuan yang telah dilakukan oleh instansi Anda.">
        <x-slot:actions>
            <div class="flex items-center gap-2.5 px-4 py-2 rounded-xl border shadow-sm"
                 style="background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%); border-color: #ddd6fe;">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg"
                     style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                    <i data-lucide="history" class="w-4 h-4 text-white"></i>
                </div>
                <div class="leading-tight">
                    <p class="text-xs font-medium text-violet-500 uppercase tracking-wide">Total Riwayat</p>
                    <p class="text-lg font-bold text-violet-900">{{ number_format($riwayat->total(), 0, ',', '.') }}</p>
                </div>
            </div>
        </x-slot:actions>
    </x-page-header>

    {{-- Filter & Search --}}
    <div class="card p-4 mb-6">
        <form action="{{ route('dpmd.riwayat') }}" method="GET" class="flex flex-col sm:flex-row flex-wrap gap-4 items-end">
            <div class="flex-1 w-full min-w-[200px]">
                <label class="form-label">Cari Pendaftar</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input" style="padding-left: 2.5rem;" placeholder="Nama atau No. Pendaftaran...">
                </div>
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
                <label class="form-label">Status Verifikasi</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui Manual</option>
                    <option value="otomatis" {{ request('status') === 'otomatis' ? 'selected' : '' }}>Disetujui Otomatis</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="btn btn-primary"><i data-lucide="filter" class="w-4 h-4"></i> Filter</button>
                @if(request()->hasAny(['search', 'kecamatan_id', 'desa_id', 'status']))
                    <a href="{{ route('dpmd.riwayat') }}" class="btn btn-outline"><i data-lucide="x" class="w-4 h-4"></i> Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Waktu Verifikasi</th>
                        <th>No. Pendaftaran / Nama</th>
                        <th>Asal Desa & Kecamatan</th>
                        <th>Keputusan & Catatan</th>
                        <th>Verifikator</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $r)
                        <tr>
                            <td class="whitespace-nowrap text-sm text-slate-500">{{ $r->dpmd_verified_at ? $r->dpmd_verified_at->format('d M Y H:i') : '-' }}</td>
                            <td>
                                <p class="font-bold text-primary-dark text-sm">{{ $r->pendaftaran->nomor_pendaftaran ?? '-' }}</p>
                                <p class="text-sm text-slate-600">{{ $r->pendaftaran->identitas->nama_lengkap ?? '-' }}</p>
                            </td>
                            <td>
                                <p class="font-medium text-sm">{{ $r->desa->nama_desa ?? '-' }}</p>
                                <p class="text-xs text-slate-500">{{ $r->desa->kecamatan->nama_kecamatan ?? '-' }}</p>
                            </td>
                            <td>
                                @if($r->status_dpmd === 'disetujui')
                                    <x-badge type="success"><i data-lucide="check" class="w-3 h-3"></i> Disetujui</x-badge>
                                @elseif($r->status_dpmd === 'ditolak')
                                    <x-badge type="danger"><i data-lucide="x" class="w-3 h-3"></i> Ditolak</x-badge>
                                @else
                                    <x-badge type="warning">{{ ucwords(str_replace('_', ' ', $r->status_dpmd)) }}</x-badge>
                                @endif

                                @if($r->catatan_dpmd)
                                    <p class="text-xs text-slate-500 border-l-2 border-indigo-300 pl-2 mt-1.5">{{ $r->catatan_dpmd }}</p>
                                @endif
                            </td>
                            <td class="text-sm text-slate-600">
                                @if($r->dpmdVerifier)
                                    {{ $r->dpmdVerifier->nama }}
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-1 rounded-md border border-amber-200">
                                        <i data-lucide="bot" class="w-3.5 h-3.5"></i> Sistem Otomatis
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"><x-empty-state icon="history" title="Belum Ada Riwayat" text="Belum ada riwayat persetujuan." /></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
            <div class="card-footer">{{ $riwayat->links() }}</div>
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
