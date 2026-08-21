<x-layouts.admin :title="'Riwayat Verifikasi OPD'">
    <x-page-header title="Riwayat Verifikasi" subtitle="Catatan seluruh keputusan verifikasi dokumen yang telah dilakukan oleh instansi Anda." />

    {{-- Filter Form --}}
    <div class="card mb-6">
        <div class="card-header border-b border-slate-100">
            <div class="flex items-center gap-2 font-bold text-slate-700">
                <i data-lucide="filter" class="w-5 h-5"></i> Filter & Pencarian
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('opd.riwayat') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                    {{-- Pencarian --}}
                    <div>
                        <label class="form-label">Cari Pendaftar</label>
                        <div class="relative flex items-center">
                            <i data-lucide="search" class="w-4 h-4 absolute left-3 text-slate-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full" style="padding-left: 2.25rem;" placeholder="Nama, NIK, No. Daftar...">
                        </div>
                    </div>
                    
                    {{-- Program --}}
                    <div>
                        <label class="form-label">Program Beasiswa</label>
                        <select name="program_id" class="form-input">
                            <option value="">Semua Program</option>
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}" {{ request('program_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Keputusan --}}
                    <div>
                        <label class="form-label">Keputusan</label>
                        <select name="status" class="form-input">
                            <option value="">Semua Keputusan</option>
                            <option value="valid" {{ request('status') === 'valid' ? 'selected' : '' }}>Valid</option>
                            <option value="tidak_valid" {{ request('status') === 'tidak_valid' ? 'selected' : '' }}>Tidak Valid</option>
                        </select>
                    </div>

                    {{-- Rentang Tanggal --}}
                    <div class="md:col-span-2 lg:col-span-2">
                        <label class="form-label">Rentang Waktu</label>
                        <div class="flex items-center gap-3">
                            <input type="date" name="tanggal_awal" value="{{ request('tanggal_awal') }}" class="form-input text-sm w-full" title="Tanggal Awal">
                            <span class="text-slate-400 font-medium hidden sm:block">s/d</span>
                            <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="form-input text-sm w-full" title="Tanggal Akhir">
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('opd.riwayat') }}" class="btn btn-outline">Reset</a>
                    <button type="submit" class="btn btn-primary"><i data-lucide="search" class="w-4 h-4"></i> Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Waktu Verifikasi</th>
                        <th>No. Pendaftaran / Nama</th>
                        <th>Jenis Dokumen</th>
                        <th>Keputusan & Catatan</th>
                        <th>Verifikator</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $r)
                        <tr>
                            <td class="whitespace-nowrap text-sm text-slate-500">{{ $r->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <p class="font-bold text-primary-dark text-sm">{{ $r->uploadDokumen->pendaftaran->nomor_pendaftaran ?? '-' }}</p>
                                <p class="text-sm text-slate-600">{{ $r->uploadDokumen->pendaftaran->identitas->nama_lengkap ?? '-' }}</p>
                            </td>
                            <td class="font-medium text-sm">{{ $r->uploadDokumen->dokumen->nama ?? '-' }}</td>
                            <td>
                                @if($r->hasil === 'valid')
                                    <x-badge type="success"><i data-lucide="check" class="w-3 h-3"></i> Valid</x-badge>
                                @elseif($r->hasil === 'gugur_desa')
                                    <x-badge type="warning" class="bg-amber-100 text-amber-800"><i data-lucide="info" class="w-3 h-3"></i> Gugur (Kuota Desa)</x-badge>
                                    @if($r->catatan)
                                        <p class="text-xs text-slate-500 border-l-2 border-amber-300 pl-2 mt-1.5">{{ $r->catatan }}</p>
                                    @endif
                                @else
                                    <x-badge type="danger"><i data-lucide="x" class="w-3 h-3"></i> Tidak Valid</x-badge>
                                    @if($r->catatan)
                                        <p class="text-xs text-slate-500 border-l-2 border-red-300 pl-2 mt-1.5">{{ $r->catatan }}</p>
                                    @endif
                                @endif
                            </td>
                            <td class="text-sm text-slate-600">{{ $r->user->nama ?? 'Sistem' }}</td>
                            <td class="text-right">
                                <a href="{{ route('opd.verifikasi.show', $r->uploadDokumen->id) }}" class="btn btn-xs btn-outline" title="Lihat Detail">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"><x-empty-state icon="history" title="Belum Ada Riwayat" text="Belum ada riwayat verifikasi." /></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-slate-50/50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 p-4">
            <div class="text-sm text-slate-500 font-medium">
                Menampilkan <span class="text-slate-800 font-bold">{{ $riwayat->firstItem() ?? 0 }}</span> - <span class="text-slate-800 font-bold">{{ $riwayat->lastItem() ?? 0 }}</span> dari <span class="text-primary-dark font-bold">{{ $riwayat->total() }}</span> data
            </div>
            <div>
                {{ $riwayat->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
</x-layouts.admin>
