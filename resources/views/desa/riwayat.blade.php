<x-layouts.admin :title="'Riwayat Penetapan Desa'">
    <x-page-header title="Riwayat Penetapan Desa" subtitle="Catatan seluruh pendaftar yang telah ditetapkan dan diajukan sebagai perwakilan dari desa Anda." />

    {{-- Filter & Search --}}
    <div class="card p-4 mb-6">
        <form action="{{ route('desa.riwayat') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end flex-wrap">
            <div class="flex-1 w-full min-w-[200px]">
                <label class="form-label">Cari Pendaftar</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full" style="padding-left: 2.5rem !important;" placeholder="Nama atau No. Pendaftaran...">
                </div>
            </div>

            <div class="w-full sm:w-40">
                <label class="form-label">Status Kecamatan</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="belum_diverifikasi" {{ request('status') === 'belum_diverifikasi' ? 'selected' : '' }}>Menunggu</option>
                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="w-full sm:w-40">
                <label class="form-label">Status DPMD</label>
                <select name="status_dpmd" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="belum_diverifikasi" {{ request('status_dpmd') === 'belum_diverifikasi' ? 'selected' : '' }}>Menunggu</option>
                    <option value="disetujui" {{ request('status_dpmd') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="ditolak" {{ request('status_dpmd') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <div class="w-full sm:w-40">
                <label class="form-label">Urutkan</label>
                <select name="sort" class="form-select">
                    <option value="terbaru" {{ request('sort') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ request('sort') === 'terlama' ? 'selected' : '' }}>Terlama</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary"><i data-lucide="filter" class="w-4 h-4"></i> Filter</button>
                @if(request()->hasAny(['search', 'status', 'status_dpmd', 'sort']))
                    <a href="{{ route('desa.riwayat') }}" class="btn btn-outline" title="Reset"><i data-lucide="x" class="w-4 h-4"></i></a>
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
                        <th>Waktu Ditetapkan</th>
                        <th>No. Pendaftaran / Nama</th>
                        <th>Status Kecamatan</th>
                        <th>Status DPMD</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $r)
                        <tr>
                            <td><span class="text-slate-500 font-medium">{{ $riwayat->firstItem() + $loop->index }}</span></td>
                            <td class="whitespace-nowrap text-sm text-slate-500">{{ $r->created_at ? $r->created_at->format('d M Y H:i') : '-' }}</td>
                            <td>
                                <p class="font-bold text-primary-dark text-sm">{{ $r->pendaftaran->nomor_pendaftaran ?? '-' }}</p>
                                <p class="text-sm text-slate-600">{{ $r->pendaftaran->identitas->nama_lengkap ?? '-' }}</p>
                            </td>
                            <td>
                                @if($r->status_kecamatan === 'disetujui')
                                    <x-badge type="success"><i data-lucide="check" class="w-3 h-3"></i> Disetujui</x-badge>
                                @elseif($r->status_kecamatan === 'ditolak')
                                    <x-badge type="danger"><i data-lucide="x" class="w-3 h-3"></i> Ditolak</x-badge>
                                @else
                                    <x-badge type="warning"><i data-lucide="clock" class="w-3 h-3"></i> Menunggu</x-badge>
                                @endif
                                
                                @if($r->catatan_kecamatan)
                                    <p class="text-xs text-slate-500 border-l-2 border-slate-300 pl-2 mt-1.5">{{ $r->catatan_kecamatan }}</p>
                                @endif
                            </td>
                            <td>
                                @if($r->status_dpmd === 'disetujui')
                                    <x-badge type="success"><i data-lucide="check" class="w-3 h-3"></i> Disetujui</x-badge>
                                @elseif($r->status_dpmd === 'ditolak')
                                    <x-badge type="danger"><i data-lucide="x" class="w-3 h-3"></i> Ditolak</x-badge>
                                @else
                                    <x-badge type="warning"><i data-lucide="clock" class="w-3 h-3"></i> Menunggu</x-badge>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('desa.show', $r->pendaftaran_id) }}" class="btn btn-xs btn-outline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"><x-empty-state icon="history" title="Belum Ada Riwayat" text="Anda belum menetapkan atau mengajukan perwakilan desa apa pun." /></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $riwayat->links() }}</div>
    </div>
</x-layouts.admin>
