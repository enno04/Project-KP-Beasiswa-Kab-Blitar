<x-layouts.admin :title="'Daftar Verifikasi Dokumen'">
    <x-page-header title="Verifikasi Dokumen" subtitle="Daftar dokumen pendaftar yang menunggu verifikasi dari instansi OPD Anda." />

    {{-- Filter --}}
    <div class="card mb-6">
        <div class="card-body py-4">
            <form action="{{ route('opd.verifikasi.index') }}" method="GET" class="flex gap-3 items-end flex-wrap">
                <div class="w-full sm:w-64">
                    <label class="form-label">Cari No. Pendaftaran</label>
                    <div class="flex gap-2">
                        <input type="text" name="search" class="form-input" placeholder="Ketik nomor..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary"><i data-lucide="search" class="w-4 h-4"></i></button>
                    </div>
                </div>
                <div class="w-full sm:w-64">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="belum_diverifikasi" {{ request('status', 'belum_diverifikasi') === 'belum_diverifikasi' ? 'selected' : '' }}>Belum Diverifikasi</option>
                        <option value="semua" {{ request('status') === 'semua' ? 'selected' : '' }}>Semua Status</option>
                        <option value="valid" {{ request('status') === 'valid' ? 'selected' : '' }}>Valid</option>
                        <option value="tidak_valid" {{ request('status') === 'tidak_valid' ? 'selected' : '' }}>Tidak Valid</option>
                    </select>
                </div>
                <div class="w-full sm:w-64">
                    <label class="form-label">Kategori Beasiswa</label>
                    <select name="program_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Program</option>
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}" {{ request('program_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-48">
                    <label class="form-label">Kecamatan</label>
                    <select name="kecamatan_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Kecamatan</option>
                        @foreach($kecamatans as $kec)
                            <option value="{{ $kec->id }}" {{ request('kecamatan_id') == $kec->id ? 'selected' : '' }}>{{ $kec->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                </div>
                @if(request('kecamatan_id'))
                <div class="w-full sm:w-48">
                    <label class="form-label">Desa / Kelurahan</label>
                    <select name="desa_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Desa</option>
                        @foreach($desas as $desa)
                            <option value="{{ $desa->id }}" {{ request('desa_id') == $desa->id ? 'selected' : '' }}>{{ $desa->nama_desa }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                @if(request('status') || request('program_id') || request('kecamatan_id') || request('desa_id') || request('search'))
                    <a href="{{ route('opd.verifikasi.index') }}" class="btn btn-sm btn-outline"><i data-lucide="x" class="w-3.5 h-3.5"></i> Reset</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-12">No.</th>
                        <th>No. Pendaftaran / Nama</th>
                        <th>Dokumen Wajib</th>
                        <th>Waktu Upload</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dokumen as $dok)
                        <tr>
                            <td class="text-sm font-medium text-slate-500">{{ $dokumen->firstItem() + $loop->index }}</td>
                            <td>
                                <p class="font-bold text-primary-dark text-sm">{{ $dok->pendaftaran->nomor_pendaftaran ?? '-' }}</p>
                                <p class="font-medium text-sm text-slate-900">{{ $dok->pendaftaran->identitas->nama_lengkap ?? '-' }}</p>
                                <p class="text-xs text-slate-400">NIK: {{ $dok->pendaftaran->identitas->nik ?? '-' }}</p>
                            </td>
                            <td>
                                <p class="font-medium text-sm">{{ $dok->dokumen->nama ?? '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $dok->dokumen->is_wajib ? 'Wajib' : 'Opsional' }}</p>
                            </td>
                            <td class="text-sm text-slate-500">{{ $dok->updated_at->format('d M Y H:i') }}</td>
                            <td>
                                @php
                                    $dokType = match($dok->status) {
                                        'valid' => 'success',
                                        'tidak_valid' => 'danger',
                                        'belum_diverifikasi' => 'warning',
                                        default => 'muted',
                                    };
                                @endphp
                                <x-badge :type="$dokType">{{ str_replace('_', ' ', $dok->status) }}</x-badge>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('opd.verifikasi.show', ['id' => $dok->id, 'redirect_to' => request()->fullUrl()]) }}" class="btn btn-xs btn-primary">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i> Proses
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"><x-empty-state icon="inbox" title="Tidak Ada Dokumen" text="Tidak ada dokumen yang perlu diverifikasi saat ini." /></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-slate-50/50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 p-4">
            <div class="text-sm text-slate-500 font-medium">
                Menampilkan <span class="text-slate-800 font-bold">{{ $dokumen->firstItem() ?? 0 }}</span> - <span class="text-slate-800 font-bold">{{ $dokumen->lastItem() ?? 0 }}</span> dari <span class="text-primary-dark font-bold">{{ $dokumen->total() }}</span> data
            </div>
            <div>
                {{ $dokumen->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
</x-layouts.admin>
