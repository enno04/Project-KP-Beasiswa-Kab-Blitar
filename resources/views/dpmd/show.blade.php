<x-layouts.admin :title="'Detail Pendaftar: ' . ($pendaftaran->identitas->nama_lengkap ?? 'Unknown')">
    <div class="space-y-6 pb-20">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                    <a href="{{ route('dpmd.program.index', [$pendaftaran->program->slug]) }}" class="hover:underline hover:text-primary">{{ $pendaftaran->program->nama }}</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span>{{ $pendaftaran->jalur->nama }}</span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ $pendaftaran->identitas->nama_lengkap ?? '-' }}</h1>
                <div class="flex items-center gap-3 mt-2 text-sm">
                    <code class="px-2 py-0.5 bg-slate-100 rounded text-slate-700 font-semibold border border-slate-200">{{ $pendaftaran->nomor_pendaftaran }}</code>
                    <span class="text-slate-400">NIK: {{ $pendaftaran->identitas->nik ?? '-' }}</span>
                </div>
            </div>
            <span class="badge {{ $pendaftaran->status_color }}">
                <i data-lucide="tag" class="w-3.5 h-3.5"></i> {{ $pendaftaran->status_label }}
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Biodata Column --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Data Diri --}}
                <div class="card overflow-hidden">
                    <div class="card-header flex items-center gap-2">
                        <i data-lucide="user" class="w-5 h-5 text-slate-400"></i>
                        <span class="font-bold text-lg">Biodata Pendaftar</span>
                    </div>
                    <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach([
                            ['Tempat, Tanggal Lahir', ($pendaftaran->identitas->tempat_lahir ?? '-') . ', ' . ($pendaftaran->identitas->tanggal_lahir ? \Carbon\Carbon::parse($pendaftaran->identitas->tanggal_lahir)->format('d M Y') : '-')],
                            ['Jenis Kelamin', $pendaftaran->identitas->jenis_kelamin === 'L' ? 'Laki-laki' : ($pendaftaran->identitas->jenis_kelamin === 'P' ? 'Perempuan' : '-')],
                            ['No. HP / WhatsApp', $pendaftaran->identitas->no_hp ?? '-'],
                            ['Email', $pendaftaran->identitas->email ?? '-'],
                        ] as [$label, $value])
                        <div>
                            <span class="block text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-1">{{ $label }}</span>
                            <div class="font-medium text-sm text-slate-900">{{ $value }}</div>
                        </div>
                        @endforeach
                        <div class="md:col-span-2">
                            <span class="block text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-1">Alamat Lengkap</span>
                            <div class="font-medium text-sm text-slate-900">
                                {{ $pendaftaran->identitas->alamat_ktp ?? '-' }}<br>
                                <span class="text-slate-500">Desa {{ $pendaftaran->identitas->desa->nama_desa ?? '-' }}, Kec. {{ $pendaftaran->identitas->kecamatan->nama_kecamatan ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Data Akademik --}}
                <div class="card overflow-hidden">
                    <div class="card-header flex items-center gap-2">
                        <i data-lucide="graduation-cap" class="w-5 h-5 text-slate-400"></i>
                        <span class="font-bold text-lg">Data Akademik</span>
                    </div>
                    <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <span class="block text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-1">Asal Perguruan Tinggi</span>
                            <div class="font-medium text-sm text-slate-900">{{ $pendaftaran->identitas->asal_perguruan_tinggi ?? '-' }}</div>
                        </div>
                        @foreach([
                            ['Program Studi', $pendaftaran->identitas->program_studi ?? '-'],
                            ['Semester', $pendaftaran->identitas->semester ?? '-'],
                        ] as [$label, $value])
                        <div>
                            <span class="block text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-1">{{ $label }}</span>
                            <div class="font-medium text-sm text-slate-900">{{ $value }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Penilaian --}}
                @if($pendaftaran->penilaians->isNotEmpty())
                <div class="card overflow-hidden">
                    <div class="card-header flex items-center gap-2">
                        <i data-lucide="calculator" class="w-5 h-5 text-slate-400"></i>
                        <span class="font-bold text-lg">Penilaian & Ranking</span>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center p-4 bg-primary-light rounded-xl">
                                <div class="text-xs font-bold text-slate-500 uppercase">Total Nilai</div>
                                <div class="text-2xl font-extrabold text-primary-dark">{{ number_format($pendaftaran->total_nilai, 4) }}</div>
                            </div>
                            <div class="text-center p-4 bg-amber-50 rounded-xl">
                                <div class="text-xs font-bold text-slate-500 uppercase">Ranking Desa</div>
                                <div class="text-2xl font-extrabold text-amber-700">#{{ $pendaftaran->ranking ?? '-' }}</div>
                            </div>
                        </div>
                        <table class="data-table">
                            <thead><tr><th>Kriteria</th><th class="text-right">Skor</th><th class="text-right">Nilai Terbobot</th></tr></thead>
                            <tbody>
                                @foreach($pendaftaran->penilaians as $penilaian)
                                <tr>
                                    <td class="font-medium">{{ $penilaian->kriteria->nama ?? '-' }}</td>
                                    <td class="text-right">{{ number_format($penilaian->skor, 2) }}</td>
                                    <td class="text-right font-semibold text-primary-dark">{{ number_format($penilaian->nilai_terbobot, 4) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Rekomendasi Desa & Verifikasi DPMD --}}
                @if($pendaftaran->rekomendasiDesa)
                <div class="card overflow-hidden">
                    <div class="card-header flex items-center gap-2" style="background: linear-gradient(135deg, #7C3AED11, #4C1D9511);">
                        <i data-lucide="landmark" class="w-5 h-5 text-violet-600"></i>
                        <span class="font-bold text-lg">Rekomendasi Desa & Verifikasi</span>
                    </div>
                    <div class="card-body space-y-4">
                        {{-- Dokumen Rekomendasi --}}
                        @foreach([
                            ['Surat Rekomendasi Desa', $pendaftaran->rekomendasiDesa->surat_rekomendasi_path],
                            ['Berita Acara Musyawarah Desa', $pendaftaran->rekomendasiDesa->berita_acara_path],
                        ] as [$docLabel, $docPath])
                            @if($docPath)
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-violet-100 flex items-center justify-center"><i data-lucide="file-text" class="w-5 h-5 text-violet-600"></i></div>
                                    <div>
                                        <div class="font-semibold text-sm">{{ $docLabel }}</div>
                                        <div class="text-xs text-slate-400">Diunggah: {{ $pendaftaran->rekomendasiDesa->tanggal_rekomendasi?->format('d M Y H:i') }}</div>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($docPath) }}" target="_blank" class="btn btn-xs btn-outline-primary"><i data-lucide="eye" class="w-3.5 h-3.5"></i> Lihat</a>
                            </div>
                            @endif
                        @endforeach

                        @if($pendaftaran->rekomendasiDesa->catatan)
                        <div class="text-sm">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Catatan dari Desa:</span>
                            <p class="mt-1 italic border-l-4 border-slate-300 pl-3 text-slate-700">{{ $pendaftaran->rekomendasiDesa->catatan }}</p>
                        </div>
                        @endif

                        {{-- Status Verifikasi Kecamatan --}}
                        <div class="pt-3 border-t border-slate-100">
                            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Status Verifikasi Kecamatan</div>
                            @if($pendaftaran->rekomendasiDesa->status_kecamatan === 'disetujui')
                                <div class="alert alert-success"><i data-lucide="check-circle" class="w-5 h-5"></i> <div><strong>DISETUJUI oleh Kecamatan</strong><p class="text-xs mt-1">Diverifikasi: {{ $pendaftaran->rekomendasiDesa->verified_at?->format('d M Y H:i') }}</p>@if($pendaftaran->rekomendasiDesa->catatan_kecamatan)<p class="text-sm mt-1">{{ $pendaftaran->rekomendasiDesa->catatan_kecamatan }}</p>@endif</div></div>
                            @elseif($pendaftaran->rekomendasiDesa->status_kecamatan === 'ditolak')
                                <div class="alert alert-danger"><i data-lucide="x-circle" class="w-5 h-5"></i> <div><strong>DITOLAK oleh Kecamatan</strong><p class="text-xs mt-1">{{ $pendaftaran->rekomendasiDesa->verified_at?->format('d M Y H:i') }}</p>@if($pendaftaran->rekomendasiDesa->catatan_kecamatan)<p class="text-sm mt-1">{{ $pendaftaran->rekomendasiDesa->catatan_kecamatan }}</p>@endif</div></div>
                            @else
                                <div class="alert alert-warning"><i data-lucide="clock" class="w-5 h-5"></i> <div><strong>Menunggu verifikasi Kecamatan</strong></div></div>
                            @endif
                        </div>

                        {{-- Status Verifikasi DPMD --}}
                        <div class="pt-3 border-t border-slate-100">
                            <div class="text-xs font-bold text-violet-600 uppercase tracking-wider mb-2">Status Verifikasi DPMD</div>
                            @if($pendaftaran->rekomendasiDesa->status_dpmd === 'disetujui')
                                <div class="alert alert-success"><i data-lucide="check-circle" class="w-5 h-5"></i> <div><strong>DISETUJUI oleh DPMD</strong><p class="text-xs mt-1">Diverifikasi: {{ $pendaftaran->rekomendasiDesa->dpmd_verified_at?->format('d M Y H:i') }} oleh {{ $pendaftaran->rekomendasiDesa->dpmdVerifier?->nama ?? '-' }}</p>@if($pendaftaran->rekomendasiDesa->catatan_dpmd)<p class="text-sm mt-1">{{ $pendaftaran->rekomendasiDesa->catatan_dpmd }}</p>@endif</div></div>
                            @elseif($pendaftaran->rekomendasiDesa->status_dpmd === 'ditolak')
                                <div class="alert alert-danger"><i data-lucide="x-circle" class="w-5 h-5"></i> <div><strong>DITOLAK oleh DPMD</strong><p class="text-xs mt-1">{{ $pendaftaran->rekomendasiDesa->dpmd_verified_at?->format('d M Y H:i') }} oleh {{ $pendaftaran->rekomendasiDesa->dpmdVerifier?->nama ?? '-' }}</p>@if($pendaftaran->rekomendasiDesa->catatan_dpmd)<p class="text-sm mt-1">{{ $pendaftaran->rekomendasiDesa->catatan_dpmd }}</p>@endif</div></div>
                            @else
                                {{-- Form Verifikasi DPMD --}}
                                <form action="{{ route('dpmd.verifikasi.rekomendasi', $pendaftaran->id) }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div class="text-sm font-bold text-violet-700 flex items-center gap-2">
                                        <i data-lucide="clipboard-check" class="w-4 h-4"></i> Berikan Keputusan Verifikasi DPMD
                                    </div>
                                    <div>
                                        <label class="form-label">Catatan Verifikasi (Opsional)</label>
                                        <textarea name="catatan_dpmd" rows="3" class="form-input" placeholder="Tuliskan catatan verifikasi DPMD..."></textarea>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button type="submit" name="keputusan" value="disetujui" class="btn btn-success flex-1 justify-center"><i data-lucide="check" class="w-4 h-4"></i> Setujui & Teruskan</button>
                                        <button type="submit" name="keputusan" value="ditolak" class="btn btn-sm" style="background:#DC2626;color:white;" onclick="return confirm('Yakin tolak rekomendasi ini?')"><i data-lucide="x" class="w-4 h-4"></i> Tolak</button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Dokumen Column --}}
            <div class="space-y-6">
                <div class="card overflow-hidden">
                    <div class="card-header flex items-center gap-2">
                        <i data-lucide="files" class="w-5 h-5 text-slate-400"></i>
                        <span class="font-bold text-lg">Berkas Persyaratan</span>
                    </div>
                    <ul class="divide-y divide-slate-100">
                        @foreach($pendaftaran->uploadDokumens as $upload)
                        <li class="p-4 flex justify-between items-start gap-3">
                            <div>
                                <div class="font-semibold text-sm">{{ $upload->dokumen->nama ?? 'Dokumen' }}</div>
                                <div class="text-xs mt-1">
                                    @if($upload->status === 'valid')
                                        <x-badge type="success"><i data-lucide="check" class="w-3 h-3"></i> Valid</x-badge>
                                    @elseif($upload->status === 'tidak_valid')
                                        <x-badge type="danger"><i data-lucide="x" class="w-3 h-3"></i> Tidak Valid</x-badge>
                                    @else
                                        <x-badge type="warning"><i data-lucide="clock" class="w-3 h-3"></i> Belum Verifikasi</x-badge>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ Storage::url($upload->file_path) }}" target="_blank" class="btn btn-xs btn-ghost text-primary hover:bg-primary-light shrink-0" title="Lihat Berkas">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Status Persetujuan Summary --}}
                <div class="card overflow-hidden">
                    <div class="card-header flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-5 h-5 text-violet-500"></i>
                        <span class="font-bold">Status Persetujuan Paralel</span>
                    </div>
                    <div class="card-body space-y-3">
                        @php
                            $rek = $pendaftaran->rekomendasiDesa;
                            $skec = $rek?->status_kecamatan ?? 'belum_diverifikasi';
                            $sdpmd = $rek?->status_dpmd ?? 'belum_diverifikasi';
                        @endphp
                        <div class="flex items-center justify-between p-3 rounded-lg {{ $skec === 'disetujui' ? 'bg-green-50' : ($skec === 'ditolak' ? 'bg-red-50' : 'bg-amber-50') }}">
                            <span class="text-sm font-semibold">Kecamatan</span>
                            <x-badge :type="$skec === 'disetujui' ? 'success' : ($skec === 'ditolak' ? 'danger' : 'warning')">
                                {{ $skec === 'disetujui' ? '✓ Disetujui' : ($skec === 'ditolak' ? '✗ Ditolak' : '⏳ Menunggu') }}
                            </x-badge>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg {{ $sdpmd === 'disetujui' ? 'bg-green-50' : ($sdpmd === 'ditolak' ? 'bg-red-50' : 'bg-amber-50') }}">
                            <span class="text-sm font-semibold">DPMD</span>
                            <x-badge :type="$sdpmd === 'disetujui' ? 'success' : ($sdpmd === 'ditolak' ? 'danger' : 'warning')">
                                {{ $sdpmd === 'disetujui' ? '✓ Disetujui' : ($sdpmd === 'ditolak' ? '✗ Ditolak' : '⏳ Menunggu') }}
                            </x-badge>
                        </div>
                        @if($rek && $rek->isFullyApproved())
                            <div class="text-center p-3 bg-green-50 rounded-lg border border-green-200">
                                <i data-lucide="check-circle-2" class="w-6 h-6 text-green-600 mx-auto mb-1"></i>
                                <p class="text-sm font-bold text-green-700">Kedua pihak sudah menyetujui</p>
                                <p class="text-xs text-green-600">Pendaftaran diteruskan ke Kabupaten</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
