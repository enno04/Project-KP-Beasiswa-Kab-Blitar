<x-layouts.admin :title="'Monitoring Pendaftaran'">
    <x-page-header title="Monitoring Pendaftar" subtitle="Pantau progres dan status seluruh pendaftar Beasiswa Blitar Mengabdi." />

    <x-data-table :items="$pendaftaran" empty-icon="activity" empty-title="Tidak Ada Data" empty-text="Tidak ada data pendaftar yang sesuai filter.">
        <x-slot:filter>
            <div class="flex flex-col sm:flex-row flex-wrap gap-3 items-end">
                <div class="w-full sm:w-32">
                    <select name="tahun" class="form-select text-sm py-2" onchange="this.form.submit()">
                        <option value="">Semua Tahun</option>
                        @foreach($tahunList as $i)
                            <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-48">
                    <select name="program_id" class="form-select text-sm py-2" onchange="this.form.submit()">
                        <option value="">Semua Program</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>{{ $prog->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-48">
                    <select name="status" class="form-select text-sm py-2" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="menunggu_verifikasi" {{ request('status') === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="sudah_diverifikasi" {{ request('status') === 'sudah_diverifikasi' ? 'selected' : '' }}>Sudah Diverifikasi</option>
                        <option value="sudah_dinilai" {{ request('status') === 'sudah_dinilai' ? 'selected' : '' }}>Sudah Dinilai</option>
                        <option value="ditetapkan" {{ request('status') === 'ditetapkan' ? 'selected' : '' }}>Ditetapkan / Lolos</option>
                        <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Gugur / Ditolak</option>
                    </select>
                </div>
                <div class="w-full sm:w-40">
                    <select name="kecamatan_id" class="form-select text-sm py-2" onchange="this.form.submit()">
                        <option value="">Semua Kecamatan</option>
                        @foreach($kecamatanList as $kec)
                            <option value="{{ $kec->id }}" {{ request('kecamatan_id') == $kec->id ? 'selected' : '' }}>{{ $kec->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-40">
                    <select name="urutan_waktu" class="form-select text-sm py-2" onchange="this.form.submit()">
                        <option value="">Urutkan (Terbaru)</option>
                        <option value="terbaru" {{ request('urutan_waktu') === 'terbaru' ? 'selected' : '' }}>Terbaru Mendaftar</option>
                        <option value="terlama" {{ request('urutan_waktu') === 'terlama' ? 'selected' : '' }}>Terlama Mendaftar</option>
                    </select>
                </div>
                <div class="w-full sm:w-32">
                    <select name="per_page" class="form-select text-sm py-2" onchange="this.form.submit()">
                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15 Baris</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Baris</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Baris</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Baris</option>
                    </select>
                </div>
                @if(request()->hasAny(['tahun', 'program_id', 'status', 'kecamatan_id', 'urutan_waktu', 'per_page']))
                    <a href="{{ route('super-admin.monitoring.pendaftaran') }}" class="btn btn-outline h-[38px] px-3" title="Reset Filter">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </x-slot:filter>

        <x-slot:header>
            <x-table-column label="Pendaftar" sortable="nomor_pendaftaran" />
            <x-table-column label="Program Beasiswa" sortable="program_id" />
            <x-table-column label="Wilayah" />
            <x-table-column label="Status Akhir" sortable="status" />
            <x-table-column label="Detail Progres" />
        </x-slot:header>

        @foreach($pendaftaran as $p)
            <tr class="hover:bg-slate-50 transition-colors group">
                <td class="px-4 py-3 border-b border-slate-100">
                    <p class="font-bold text-primary-dark text-sm">{{ $p->nomor_pendaftaran }}</p>
                    <p class="font-medium text-sm text-slate-900">{{ $p->nama_lengkap }}</p>
                    <p class="text-xs text-slate-400">Tahun: {{ $p->tahun }}</p>
                </td>
                <td class="px-4 py-3 border-b border-slate-100 font-medium text-sm">{{ $p->program->nama }}</td>
                <td class="px-4 py-3 border-b border-slate-100">
                    <p class="text-sm">Desa {{ $p->desa->nama_desa ?? '-' }}</p>
                    <p class="text-xs text-slate-400">Kec. {{ $p->kecamatan->nama_kecamatan ?? '-' }}</p>
                </td>
                <td class="px-4 py-3 border-b border-slate-100">
                    @php
                        $statusType = match($p->status_color ?? 'gray') {
                            'green' => 'success', 'red' => 'danger', 'yellow' => 'warning', 'blue' => 'info', default => 'muted',
                        };
                    @endphp
                    <x-badge :type="$statusType">{{ $p->status_label }}</x-badge>
                </td>
                <td class="px-4 py-3 border-b border-slate-100">
                    @php
                        $detail = $p->detail_progres;
                        $isPending = str_contains($detail, 'Terkendala') || str_contains($detail, 'Menunggu') || str_contains($detail, 'Di tangan');
                        $isStop = str_contains($detail, 'Berhenti');
                        $isDone = str_contains($detail, 'Selesai');
                    @endphp
                    <p class="text-sm font-medium {{ $isPending ? 'text-amber-600' : ($isStop ? 'text-red-600' : ($isDone ? 'text-green-600' : 'text-slate-600')) }}">
                        {{ $detail }}
                    </p>
                </td>
            </tr>
        @endforeach
    </x-data-table>
</x-layouts.admin>
