<x-layouts.admin :title="'Detail Pendaftar: ' . ($pendaftaran->identitas->nama_lengkap ?? 'Unknown')">
    <div class="space-y-6 pb-20">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                    <a href="{{ route('kecamatan.program.index', [$pendaftaran->program->slug]) }}" class="hover:underline hover:text-primary">{{ $pendaftaran->program->nama }}</a>
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
                        <div>
                            <span class="block text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-1">Fakultas / Program Studi</span>
                            <div class="font-medium text-sm text-slate-900">{{ $pendaftaran->identitas->program_studi ?? '-' }}</div>
                        </div>
                        <div>
                            <span class="block text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-1">Semester Saat Ini</span>
                            <div class="font-medium text-sm text-slate-900">{{ $pendaftaran->identitas->semester ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Data Orang Tua --}}
                @if($pendaftaran->orangtua)
                <div class="card overflow-hidden">
                    <div class="card-header flex items-center gap-2">
                        <i data-lucide="users" class="w-5 h-5 text-slate-400"></i>
                        <span class="font-bold text-lg">Data Orang Tua / Wali</span>
                    </div>
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
                            <div class="text-xs text-slate-500 mt-1">HP: {{ $hp ?? '-' }}</div>
                            <div class="text-xs text-slate-500 mt-1">Alamat: {{ $alamat ?? '-' }}</div>
                        </div>
                        @endforeach
                        @if($pendaftaran->orangtua->nama_wali)
                        <div class="md:col-span-2 pt-4 border-t border-dashed border-slate-200">
                            <span class="block text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-2">Wali</span>
                            <div class="font-medium text-sm">{{ $pendaftaran->orangtua->nama_wali }}</div>
                            <div class="text-xs text-slate-500 mt-1">NIK: {{ $pendaftaran->orangtua->nik_wali ?? '-' }}</div>
                            <div class="text-xs text-slate-500 mt-1">HP: {{ $pendaftaran->orangtua->no_hp_wali ?? '-' }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Verifikasi Berkas Desa --}}
                @if($pendaftaran->rekomendasiDesa)
                <div class="card overflow-hidden">
                    <div class="card-header flex items-center gap-2 bg-amber-50/30">
                        <i data-lucide="shield-check" class="w-5 h-5 text-amber-500"></i>
                        <span class="font-bold text-lg">Verifikasi Berkas Desa</span>
                    </div>
                    <div class="card-body space-y-4">
                        <div class="text-[11px] font-bold uppercase text-slate-400 tracking-wider">Dokumen dari Desa {{ $pendaftaran->identitas->desa->nama_desa ?? '' }}</div>

                        @foreach([
                            ['Surat Rekomendasi Kepala Desa', $pendaftaran->rekomendasiDesa->surat_rekomendasi_path, 'text-red-600', 'bg-red-50'],
                            ['Berita Acara Musyawarah Desa', $pendaftaran->rekomendasiDesa->berita_acara_path, 'text-primary', 'bg-primary-light'],
                        ] as [$docName, $docPath, $iconColor, $bgColor])
                            @if($docPath)
                            <div class="flex items-center justify-between bg-white border border-slate-100 px-4 py-3 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg {{ $bgColor }} flex items-center justify-center">
                                        <i data-lucide="file-text" class="w-5 h-5 {{ $iconColor }}"></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm">{{ $docName }}</div>
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

                        {{-- Status Verifikasi --}}
                        @if($pendaftaran->rekomendasiDesa->status_kecamatan === 'disetujui')
                            <div class="alert alert-success"><i data-lucide="check-circle" class="w-5 h-5"></i> <div><strong>DISETUJUI</strong><p class="text-xs mt-1">Diverifikasi: {{ $pendaftaran->rekomendasiDesa->verified_at?->format('d M Y H:i') }}</p>@if($pendaftaran->rekomendasiDesa->catatan_kecamatan)<p class="text-sm mt-1">{{ $pendaftaran->rekomendasiDesa->catatan_kecamatan }}</p>@endif</div></div>
                        @elseif($pendaftaran->rekomendasiDesa->status_kecamatan === 'ditolak')
                            <div class="alert alert-danger"><i data-lucide="x-circle" class="w-5 h-5"></i> <div><strong>DITOLAK</strong><p class="text-xs mt-1">Ditolak: {{ $pendaftaran->rekomendasiDesa->verified_at?->format('d M Y H:i') }}</p>@if($pendaftaran->rekomendasiDesa->catatan_kecamatan)<p class="text-sm mt-1">{{ $pendaftaran->rekomendasiDesa->catatan_kecamatan }}</p>@endif</div></div>
                        @else
                            <form action="{{ route('kecamatan.verifikasi.rekomendasi', $pendaftaran->id) }}" method="POST" class="space-y-4 pt-4 border-t border-slate-100">
                                @csrf
                                <div class="text-sm font-bold text-slate-700 flex items-center gap-2">
                                    <i data-lucide="clipboard-check" class="w-4 h-4"></i> Berikan Keputusan Verifikasi
                                </div>
                                <div>
                                    <label class="form-label">Catatan Verifikasi (Opsional)</label>
                                    <textarea name="catatan_kecamatan" rows="3" class="form-input" placeholder="Tuliskan catatan verifikasi..."></textarea>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button type="submit" name="keputusan" value="disetujui" class="btn btn-success flex-1 justify-center"><i data-lucide="check" class="w-4 h-4"></i> Setujui & Teruskan</button>
                                    <button type="submit" name="keputusan" value="ditolak" class="btn btn-sm" style="background:#DC2626;color:white;" onclick="return confirm('Yakin tolak berkas ini?')"><i data-lucide="x" class="w-4 h-4"></i> Tolak</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
                @elseif($pendaftaran->status === 'diteruskan_ke_kecamatan')
                <div class="card text-center">
                    <div class="card-body py-10">
                        <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 text-amber-400"></i>
                        <p class="font-bold text-amber-700">Berkas dari Desa belum tersedia</p>
                        <p class="text-sm text-slate-500 mt-1">Menunggu Desa mengunggah Surat Rekomendasi dan Berita Acara.</p>
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
            </div>
        </div>
    </div>
</x-layouts.admin>
