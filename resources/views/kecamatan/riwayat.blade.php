<x-layouts.admin :title="'Riwayat Verifikasi Kecamatan'">
    <x-page-header title="Riwayat Verifikasi" subtitle="Catatan seluruh keputusan rekomendasi yang telah dilakukan oleh instansi Anda." />

    {{-- Filter & Search --}}
    <div class="card p-4 mb-6">
        <form action="{{ route('kecamatan.riwayat') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="form-label">Cari Pendaftar</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input" style="padding-left: 2.5rem;" placeholder="Nama atau No. Pendaftaran...">
                </div>
            </div>
            
            <div class="w-full sm:w-48">
                <label class="form-label">Filter Desa</label>
                <select name="desa_id" class="form-select">
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

            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary"><i data-lucide="filter" class="w-4 h-4"></i> Filter</button>
                @if(request()->hasAny(['search', 'desa_id', 'status']))
                    <a href="{{ route('kecamatan.riwayat') }}" class="btn btn-outline"><i data-lucide="x" class="w-4 h-4"></i> Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Waktu Verifikasi</th>
                        <th>No. Pendaftaran / Nama</th>
                        <th>Asal Desa</th>
                        <th>Keputusan & Catatan</th>
                        <th>Verifikator</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $r)
                        <tr>
                            <td><span class="text-slate-500 font-medium">{{ $riwayat->firstItem() + $loop->index }}</span></td>
                            <td class="whitespace-nowrap text-sm text-slate-500">{{ $r->verified_at ? $r->verified_at->format('d M Y H:i') : '-' }}</td>
                            <td>
                                <p class="font-bold text-primary-dark text-sm">{{ $r->pendaftaran->nomor_pendaftaran ?? '-' }}</p>
                                <p class="text-sm text-slate-600">{{ $r->pendaftaran->identitas->nama_lengkap ?? '-' }}</p>
                            </td>
                            <td class="font-medium text-sm">{{ $r->desa->nama_desa ?? '-' }}</td>
                            <td>
                                @if($r->status_kecamatan === 'disetujui')
                                    <x-badge type="success"><i data-lucide="check" class="w-3 h-3"></i> Disetujui</x-badge>
                                @elseif($r->status_kecamatan === 'ditolak')
                                    <x-badge type="danger"><i data-lucide="x" class="w-3 h-3"></i> Ditolak</x-badge>
                                @else
                                    <x-badge type="warning">{{ ucwords(str_replace('_', ' ', $r->status_kecamatan)) }}</x-badge>
                                @endif

                                @if($r->catatan_kecamatan)
                                    <p class="text-xs text-slate-500 border-l-2 border-indigo-300 pl-2 mt-1.5">{{ $r->catatan_kecamatan }}</p>
                                @endif
                            </td>
                            <td class="text-sm text-slate-600">
                                @if($r->kecamatanVerifier)
                                    {{ $r->kecamatanVerifier->nama }}
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-1 rounded-md border border-amber-200">
                                        <i data-lucide="bot" class="w-3.5 h-3.5"></i> Sistem Otomatis
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"><x-empty-state icon="history" title="Belum Ada Riwayat" text="Belum ada riwayat rekomendasi." /></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
            <div class="card-footer">{{ $riwayat->links() }}</div>
        @endif
    </div>
</x-layouts.admin>
