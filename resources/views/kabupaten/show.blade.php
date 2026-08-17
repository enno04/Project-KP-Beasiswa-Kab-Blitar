<x-layouts.admin :title="'Detail Pendaftar: ' . ($pendaftaran->identitas->nama_lengkap ?? 'Unknown')">
    <div class="space-y-6 pb-20">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                    <a href="{{ route('kabupaten.program.index', [$pendaftaran->program->slug]) }}" class="hover:underline hover:text-primary">{{ $pendaftaran->program->nama }}</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span>{{ $pendaftaran->jalur->nama }}</span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ $pendaftaran->identitas->nama_lengkap ?? '-' }}</h1>
                <div class="flex items-center gap-3 mt-2 text-sm">
                    <code class="px-2 py-0.5 bg-slate-100 rounded text-slate-700 font-semibold border border-slate-200">{{ $pendaftaran->nomor_pendaftaran }}</code>
                    <span class="text-slate-400">NIK: {{ $pendaftaran->identitas->nik ?? '-' }}</span>
                </div>
            </div>
            <div class="flex flex-col items-end gap-2">
                <span class="badge {{ $pendaftaran->status_color }}"><i data-lucide="tag" class="w-3.5 h-3.5"></i> {{ $pendaftaran->status_label }}</span>
                @if($pendaftaran->total_nilai !== null)
                    <div class="font-extrabold text-lg text-primary-dark">Skor: {{ number_format($pendaftaran->total_nilai, 4) }}</div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                {{-- Biodata --}}
                <div class="card overflow-hidden">
                    <div class="card-header flex items-center gap-2"><i data-lucide="user" class="w-5 h-5 text-slate-400"></i><span class="font-bold text-lg">Biodata Pendaftar</span></div>
                    <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach([
                            ['Tempat, Tanggal Lahir', ($pendaftaran->identitas->tempat_lahir ?? '-') . ', ' . ($pendaftaran->identitas->tanggal_lahir ? \Carbon\Carbon::parse($pendaftaran->identitas->tanggal_lahir)->format('d M Y') : '-')],
                            ['Jenis Kelamin', $pendaftaran->identitas->jenis_kelamin === 'L' ? 'Laki-laki' : ($pendaftaran->identitas->jenis_kelamin === 'P' ? 'Perempuan' : '-')],
                            ['No. HP', $pendaftaran->identitas->no_hp ?? '-'],
                            ['Email', $pendaftaran->identitas->email ?? '-'],
                        ] as [$label, $value])
                        <div>
                            <span class="block text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-1">{{ $label }}</span>
                            <div class="font-medium text-sm text-slate-900">{{ $value }}</div>
                        </div>
                        @endforeach
                        <div class="md:col-span-2">
                            <span class="block text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-1">Alamat</span>
                            <div class="font-medium text-sm text-slate-900">{{ $pendaftaran->identitas->alamat_ktp ?? '-' }}<br><span class="text-slate-500">Desa {{ $pendaftaran->identitas->desa->nama_desa ?? '-' }}, Kec. {{ $pendaftaran->identitas->kecamatan->nama_kecamatan ?? '-' }}</span></div>
                        </div>
                    </div>
                </div>

                {{-- Akademik --}}
                <div class="card overflow-hidden">
                    <div class="card-header flex items-center gap-2"><i data-lucide="graduation-cap" class="w-5 h-5 text-slate-400"></i><span class="font-bold text-lg">Data Akademik</span></div>
                    <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2"><span class="block text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-1">Perguruan Tinggi</span><div class="font-medium text-sm">{{ $pendaftaran->identitas->asal_perguruan_tinggi ?? '-' }}</div></div>
                        <div><span class="block text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-1">Program Studi</span><div class="font-medium text-sm">{{ $pendaftaran->identitas->program_studi ?? '-' }}</div></div>
                        <div><span class="block text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-1">Semester</span><div class="font-medium text-sm">{{ $pendaftaran->identitas->semester ?? '-' }}</div></div>
                    </div>
                </div>

                {{-- Orang Tua --}}
                @if($pendaftaran->orangtua)
                <div class="card overflow-hidden">
                    <div class="card-header flex items-center gap-2"><i data-lucide="users" class="w-5 h-5 text-slate-400"></i><span class="font-bold text-lg">Data Orang Tua / Wali</span></div>
                    <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach([
                            ['Ayah', $pendaftaran->orangtua->nama_ayah, $pendaftaran->orangtua->nik_ayah, $pendaftaran->orangtua->tempat_lahir_ayah, $pendaftaran->orangtua->tanggal_lahir_ayah, $pendaftaran->orangtua->no_hp_ayah, $pendaftaran->orangtua->alamat_ayah],
                            ['Ibu', $pendaftaran->orangtua->nama_ibu, $pendaftaran->orangtua->nik_ibu, $pendaftaran->orangtua->tempat_lahir_ibu, $pendaftaran->orangtua->tanggal_lahir_ibu, $pendaftaran->orangtua->no_hp_ibu, $pendaftaran->orangtua->alamat_ibu],
                        ] as [$role, $nama, $nik, $tempat, $tgl, $hp, $alamat])
                        <div>
                            <span class="block text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-2">{{ $role }}</span>
                            <div class="font-medium text-sm">{{ $nama ?? '-' }}</div>
                            <div class="text-xs text-slate-500 mt-1">NIK: {{ $nik ?? '-' }}</div>
                            <div class="text-xs text-slate-500 mt-1">{{ $tempat ?? '-' }}, {{ $tgl ? \Carbon\Carbon::parse($tgl)->format('d M Y') : '-' }}</div>
                            <div class="text-xs text-slate-500 mt-1">HP: {{ $hp ?? '-' }} · {{ $alamat ?? '-' }}</div>
                        </div>
                        @endforeach
                        @if($pendaftaran->orangtua->nama_wali)
                        <div class="md:col-span-2 pt-4 border-t border-dashed border-slate-200">
                            <span class="block text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-2">Wali</span>
                            <div class="font-medium text-sm">{{ $pendaftaran->orangtua->nama_wali }}</div>
                            <div class="text-xs text-slate-500 mt-1">NIK: {{ $pendaftaran->orangtua->nik_wali ?? '-' }} · HP: {{ $pendaftaran->orangtua->no_hp_wali ?? '-' }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Kriteria Penilaian --}}
                <div class="card overflow-hidden">
                    <div class="card-header flex items-center gap-2"><i data-lucide="list-checks" class="w-5 h-5 text-slate-400"></i><span class="font-bold text-lg">Input Kriteria Penilaian</span></div>
                    <table class="data-table">
                        <tbody>
                            @foreach($pendaftaran->jawabanKriterias as $jawaban)
                            <tr>
                                <td class="w-1/2">
                                    <div class="font-semibold text-slate-700">{{ $jawaban->kriteria->nama }}</div>
                                    <div class="text-xs text-slate-400">{{ $jawaban->kriteria->kelompokKriteria->nama ?? '' }}</div>
                                </td>
                                <td>
                                    @if($jawaban->kriteria->tipe_input === 'pilihan')
                                        <span class="font-medium">{{ $jawaban->pilihanKriteria->label ?? '-' }}</span>
                                    @else
                                        <span class="font-medium font-mono text-primary">{{ $jawaban->nilai_angka }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Berkas Column --}}
            <div class="space-y-6">
                <div class="card overflow-hidden">
                    <div class="card-header flex items-center gap-2"><i data-lucide="files" class="w-5 h-5 text-slate-400"></i><span class="font-bold text-lg">Berkas Persyaratan</span></div>
                    <ul class="divide-y divide-slate-100">
                        @foreach($pendaftaran->uploadDokumens as $upload)
                        <li class="p-4 flex justify-between items-start gap-3">
                            <div>
                                <div class="font-semibold text-sm">{{ $upload->dokumen->nama ?? 'Dokumen' }}</div>
                                <div class="mt-1">
                                    @if($upload->status === 'valid')
                                        <x-badge type="success"><i data-lucide="check" class="w-3 h-3"></i> Valid</x-badge>
                                    @elseif($upload->status === 'tidak_valid')
                                        <x-badge type="danger"><i data-lucide="x" class="w-3 h-3"></i> Tidak Valid</x-badge>
                                        @if($upload->catatan)
                                            <p class="text-slate-400 italic mt-1 text-[11px]">{{ $upload->catatan }}</p>
                                        @endif
                                    @else
                                        <x-badge type="warning"><i data-lucide="clock" class="w-3 h-3"></i> Belum</x-badge>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ Storage::url($upload->file_path) }}" target="_blank" class="btn btn-xs btn-ghost text-primary hover:bg-primary-light shrink-0"><i data-lucide="eye" class="w-4 h-4"></i></a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
