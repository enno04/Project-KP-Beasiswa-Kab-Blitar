<x-layouts.admin :title="'Riwayat Penetapan Beasiswa'">
    <x-page-header title="Riwayat Penetapan Beasiswa" subtitle="Daftar pendaftar yang telah ditetapkan (LULUS) menerima beasiswa.">
        <x-slot:actions>
            <div class="flex items-center gap-2.5 px-4 py-2 rounded-xl border shadow-sm"
                 style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-color: #bbf7d0;">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg"
                     style="background: linear-gradient(135deg, #22c55e, #15803d);">
                    <i data-lucide="trophy" class="w-4 h-4 text-white"></i>
                </div>
                <div class="leading-tight">
                    <p class="text-xs font-medium text-green-600 uppercase tracking-wide">Total Penerima</p>
                    <p class="text-lg font-bold text-green-900">{{ number_format($riwayatPenetapan->total(), 0, ',', '.') }}</p>
                </div>
            </div>
            <a href="{{ route('kabupaten.export-riwayat', request()->query()) }}"
               class="flex items-center gap-2 self-stretch px-4 rounded-xl text-white text-sm font-semibold shadow-sm transition-all hover:opacity-90 active:scale-95"
               style="background: linear-gradient(135deg, #22c55e, #15803d);">
                <i data-lucide="download" class="w-4 h-4 shrink-0"></i>
                <span>Export ke Excel</span>
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Filter Form --}}
    <div class="card mb-6">
        <div class="card-body">
            <form action="{{ route('kabupaten.riwayat-penetapan') }}" method="GET">
                <div class="flex flex-wrap items-end gap-4">
                    <div class="w-full sm:w-64">
                        <label class="form-label">Cari Pendaftar</label>
                        <div class="relative">
                            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-input" style="padding-left: 2.5rem;" placeholder="Nama, NIK, atau No. Daftar...">
                        </div>
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
                        <select name="kecamatan_id" id="kecamatan_id" class="form-select" onchange="this.form.submit()">
                            <option value="">Semua Kecamatan</option>
                            @foreach($kecamatans as $kec)
                                <option value="{{ $kec->id }}" {{ request('kecamatan_id') == $kec->id ? 'selected' : '' }}>{{ $kec->nama_kecamatan }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Desa Filter Note: For dynamic desa, usually we need JS or just text search. I'll keep it simple or implement JS. --}}
                    
                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="submit" class="btn btn-primary"><i data-lucide="filter" class="w-4 h-4"></i> Terapkan</button>
                        @if(request()->hasAny(['search', 'program_id', 'kecamatan_id', 'desa_id']))
                            <a href="{{ route('kabupaten.riwayat-penetapan') }}" class="btn btn-outline"><i data-lucide="x" class="w-4 h-4"></i> Reset</a>
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
                        <th>Nama / NIK</th>
                        <th>Program & Jalur</th>
                        <th>Skor & Rank</th>
                        <th>Desa & Kecamatan</th>
                        <th>Waktu Ditetapkan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatPenetapan as $p)
                        <tr>
                            <td>
                                <p class="font-semibold text-slate-900">{{ $p->identitas->nama_lengkap ?? '-' }}</p>
                                <p class="text-xs text-slate-400">NIK: {{ $p->identitas->nik ?? '-' }}</p>
                            </td>
                            <td>
                                <p class="font-medium text-primary-dark text-sm">{{ $p->program->nama ?? '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $p->jalur->nama ?? '-' }}</p>
                            </td>
                            <td>
                                @if($p->total_nilai !== null)
                                    <span class="font-bold text-primary-dark">{{ number_format($p->total_nilai, 4) }}</span>
                                    <p class="text-xs text-slate-400">Rank: {{ $p->ranking ?? '-' }}</p>
                                @else
                                    <span class="text-xs italic text-slate-400">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-sm font-semibold text-slate-700">{{ $p->identitas->desa->nama_desa ?? '-' }}</span>
                                <p class="text-xs text-slate-500">{{ $p->identitas->desa->kecamatan->nama_kecamatan ?? '-' }}</p>
                            </td>
                            <td class="text-xs text-slate-500">
                                {{ $p->updated_at ? $p->updated_at->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="text-right">
                                <a href="{{ route('kabupaten.show', $p->id) }}" class="btn btn-xs btn-outline" title="Detail">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-empty-state icon="archive" title="Belum Ada Riwayat" text="Belum ada pendaftar yang ditetapkan lulus sesuai filter pencarian Anda." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayatPenetapan->hasPages())
            <div class="card-footer">{{ $riwayatPenetapan->links() }}</div>
        @endif
    </div>
</x-layouts.admin>
