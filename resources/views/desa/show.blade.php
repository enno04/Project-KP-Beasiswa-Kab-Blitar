<x-layouts.admin :title="'Detail Pendaftar: ' . ($pendaftaran->identitas->nama_lengkap ?? 'Unknown')">
    <div class="space-y-6 pb-20">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                    <a href="{{ route('desa.program.index', [$pendaftaran->program->slug]) }}" class="hover:underline hover:text-primary">{{ $pendaftaran->program->nama }}</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span>{{ $pendaftaran->jalur->nama }}</span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900">{{ $pendaftaran->identitas->nama_lengkap ?? '-' }}</h1>
                <div class="flex items-center gap-3 mt-2 text-sm">
                    <code class="px-2 py-0.5 bg-slate-100 rounded text-slate-700 font-semibold border border-slate-200">{{ $pendaftaran->nomor_pendaftaran }}</code>
                    <span class="text-slate-400">NIK: {{ $pendaftaran->identitas->nik ?? '-' }}</span>
                </div>
            </div>
            <div class="flex flex-col items-end gap-3">
                <span class="badge {{ $pendaftaran->status_color }}"><i data-lucide="tag" class="w-3.5 h-3.5"></i> {{ $pendaftaran->status_label }}</span>
                <a href="{{ route('desa.program.index', [$pendaftaran->program->slug, $pendaftaran->jalur->slug]) }}" class="btn btn-sm btn-outline border-slate-300 text-slate-600 hover:bg-slate-50"><i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Pendaftar</a>
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
                            ['Ayah', $pendaftaran->orangtua->nama_ayah, $pendaftaran->orangtua->nik_ayah, $pendaftaran->orangtua->no_hp_ayah, $pendaftaran->orangtua->alamat_ayah],
                            ['Ibu', $pendaftaran->orangtua->nama_ibu, $pendaftaran->orangtua->nik_ibu, $pendaftaran->orangtua->no_hp_ibu, $pendaftaran->orangtua->alamat_ibu],
                        ] as [$role, $nama, $nik, $hp, $alamat])
                        <div>
                            <span class="block text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-2">{{ $role }}</span>
                            <div class="font-medium text-sm">{{ $nama ?? '-' }}</div>
                            <div class="text-xs text-slate-500 mt-1">NIK: {{ $nik ?? '-' }} · HP: {{ $hp ?? '-' }}</div>
                            <div class="text-xs text-slate-500 mt-1">{{ $alamat ?? '-' }}</div>
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

                {{-- Kriteria Penilaian & Ranking --}}
                @if($pendaftaran->penilaians->isNotEmpty())
                <div class="card overflow-hidden mt-6">
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
                @else
                <div class="card overflow-hidden mt-6">
                    <div class="card-header flex items-center gap-2"><i data-lucide="list-checks" class="w-5 h-5 text-slate-400"></i><span class="font-bold text-lg">Input Jawaban Kriteria</span></div>
                    <table class="data-table">
                        <tbody>
                            @forelse($pendaftaran->jawabanKriterias as $jawaban)
                            <tr>
                                <td class="w-1/2">
                                    <div class="font-semibold text-slate-700">{{ $jawaban->kriteria->nama }}</div>
                                    <div class="text-xs text-slate-400">{{ $jawaban->kriteria->kelompokKriteria->nama ?? '' }}</div>
                                </td>
                                <td>
                                    @if($jawaban->kriteria->tipe_input === 'pilihan')
                                        <span class="font-medium">{{ $jawaban->pilihanKriteria->label ?? '-' }}</span>
                                    @else
                                        <span class="font-medium font-mono text-primary">{{ $jawaban->nilai_input ?? '-' }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-slate-400 py-4 text-sm italic">Belum ada data nilai kriteria.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif

                {{-- SDSS Upload Rekomendasi --}}
                @if($pendaftaran->program->isSdss())
                @php
                    $totalDokumen = $pendaftaran->uploadDokumens->count();
                    $dokumenValid = $pendaftaran->uploadDokumens->where('status', 'valid')->count();
                    $dokumenTidakValid = $pendaftaran->uploadDokumens->where('status', 'tidak_valid')->count();
                    $semuaDokumenValid = $totalDokumen > 0 && $dokumenValid === $totalDokumen;
                    $adaDokumenDitolak = $dokumenTidakValid > 0;
                @endphp

                @if((!$semuaDokumenValid || !$pendaftaran->ranking) && !$pendaftaran->rekomendasiDesa)
                <div class="card">
                    <div class="card-body flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                            <i data-lucide="lock" class="w-6 h-6 text-amber-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-amber-800">Upload Rekomendasi Dikunci</h3>
                            @if(!$semuaDokumenValid)
                                <p class="text-sm text-slate-600 mt-1">Upload hanya dapat dilakukan setelah <strong>seluruh dokumen diverifikasi oleh OPD</strong>.</p>
                                <div class="mt-3 flex items-center gap-2">
                                    <x-badge type="info">{{ $dokumenValid }}/{{ $totalDokumen }} Valid</x-badge>
                                    @if($adaDokumenDitolak)
                                        <x-badge type="danger">{{ $dokumenTidakValid }} Ditolak</x-badge>
                                    @endif
                                </div>
                            @else
                                <p class="text-sm text-slate-600 mt-1">Upload hanya dapat dilakukan setelah Anda melakukan kalkulasi <strong>Hitung Penilaian & Ranking</strong> dan menekan tombol <strong>Tetapkan</strong> pada pendaftar ini di halaman utama.</p>
                            @endif
                        </div>
                    </div>
                </div>
                @else
                <div id="form-rekomendasi" class="card overflow-hidden">
                    <div class="card-header flex items-center justify-between gap-2 bg-amber-50/30">
                        <div class="flex items-center gap-2">
                            <i data-lucide="upload-cloud" class="w-5 h-5 text-amber-500"></i>
                            <span class="font-bold text-lg text-amber-800">Surat Rekomendasi & Berita Acara</span>
                        </div>
                        <a href="{{ route('desa.program.index', [$pendaftaran->program->slug, $pendaftaran->jalur->slug]) }}" class="btn btn-xs btn-outline border-amber-200 text-amber-700 hover:bg-amber-100"><i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali</a>
                    </div>
                    <div class="card-body space-y-4">
                        {{-- Status --}}
                        @if($pendaftaran->rekomendasiDesa)
                            @if($pendaftaran->rekomendasiDesa->status_dpmd === 'disetujui')
                                <div class="alert alert-success"><i data-lucide="check-circle" class="w-5 h-5"></i><div><strong>Disetujui oleh Kecamatan & DPMD</strong><p class="text-xs mt-1">Diunggah: {{ $pendaftaran->rekomendasiDesa->tanggal_rekomendasi?->format('d M Y H:i') }}</p></div></div>
                            @elseif($pendaftaran->rekomendasiDesa->status_dpmd === 'ditolak')
                                <div class="alert alert-danger"><i data-lucide="x-circle" class="w-5 h-5"></i><div><strong>Ditolak oleh DPMD</strong>@if($pendaftaran->rekomendasiDesa->catatan_dpmd)<p class="text-sm mt-1">{{ $pendaftaran->rekomendasiDesa->catatan_dpmd }}</p>@endif</div></div>
                            @elseif($pendaftaran->rekomendasiDesa->status_kecamatan === 'disetujui')
                                <div class="alert alert-warning"><i data-lucide="clock" class="w-5 h-5"></i><div><strong>Menunggu Verifikasi DPMD</strong><p class="text-xs mt-1">Telah disetujui Kecamatan. Diunggah: {{ $pendaftaran->rekomendasiDesa->tanggal_rekomendasi?->format('d M Y H:i') }}</p></div></div>
                            @elseif($pendaftaran->rekomendasiDesa->status_kecamatan === 'ditolak')
                                <div class="alert alert-danger"><i data-lucide="x-circle" class="w-5 h-5"></i><div><strong>Ditolak oleh Kecamatan</strong>@if($pendaftaran->rekomendasiDesa->catatan_kecamatan)<p class="text-sm mt-1">{{ $pendaftaran->rekomendasiDesa->catatan_kecamatan }}</p>@endif</div></div>
                            @else
                                <div class="alert alert-warning"><i data-lucide="clock" class="w-5 h-5"></i><div><strong>Menunggu Verifikasi Kecamatan & DPMD</strong><p class="text-xs mt-1">Diunggah: {{ $pendaftaran->rekomendasiDesa->tanggal_rekomendasi?->format('d M Y H:i') }}</p></div></div>
                            @endif
                            <div class="flex flex-wrap gap-3 text-xs">
                                @if($pendaftaran->rekomendasiDesa->surat_rekomendasi_path)
                                    <a href="{{ Storage::url($pendaftaran->rekomendasiDesa->surat_rekomendasi_path) }}" target="_blank" class="btn btn-xs btn-outline-primary"><i data-lucide="file-text" class="w-3 h-3"></i> Surat Rekomendasi</a>
                                @endif
                                @if($pendaftaran->rekomendasiDesa->berita_acara_path)
                                    <a href="{{ Storage::url($pendaftaran->rekomendasiDesa->berita_acara_path) }}" target="_blank" class="btn btn-xs btn-outline-primary"><i data-lucide="file-text" class="w-3 h-3"></i> Berita Acara</a>
                                @endif
                            </div>
                        @endif

                        {{-- Upload Form --}}
                        @if($pendaftaran->ranking === 1 && $pendaftaran->rekomendasiDesa && (!$pendaftaran->rekomendasiDesa->surat_rekomendasi_path || $pendaftaran->rekomendasiDesa->status_kecamatan === 'ditolak'))
                        <form action="{{ route('desa.rekomendasi', $pendaftaran->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-4 border-t border-slate-100">
                            @csrf
                            <div>
                                <label class="form-label">File Surat Rekomendasi (PDF) <span class="required">*</span></label>
                                <input type="file" name="surat_rekomendasi" accept=".pdf" class="form-input file:mr-4 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary-light file:text-primary-dark" required>
                                <p class="text-xs text-slate-400 mt-1">Maks 2MB.</p>
                                @error('surat_rekomendasi') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="form-label">File Berita Acara Musyawarah (PDF) <span class="required">*</span></label>
                                <input type="file" name="berita_acara" accept=".pdf" class="form-input file:mr-4 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary-light file:text-primary-dark" required>
                                <p class="text-xs text-slate-400 mt-1">Maks 2MB.</p>
                                @error('berita_acara') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="form-label">Catatan Tambahan (Opsional)</label>
                                <textarea name="catatan" rows="3" class="form-input" placeholder="Catatan terkait rekomendasi..."></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="btn btn-primary"><i data-lucide="send" class="w-4 h-4"></i> Kirim ke Kecamatan & DPMD</button>
                            </div>
                        </form>
                        @elseif($pendaftaran->ranking === 1 && $pendaftaran->total_nilai > 0 && !$pendaftaran->rekomendasiDesa)
                        <div class="mt-2">
                            <div class="alert alert-warning">
                                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                                <div>
                                    <strong>Menunggu Penetapan</strong>
                                    <p class="text-sm mt-1">Pendaftar ini menduduki Peringkat 1. Klik tombol di bawah ini untuk mengunci perwakilan Desa sebelum dapat mengunggah Surat Rekomendasi.</p>
                                    <form id="form-tetapkan-perwakilan" action="{{ route('desa.tetapkan', $pendaftaran->id) }}" method="POST" class="mt-3">
                                        @csrf
                                        <button type="button" onclick="konfirmasiTetapkan()" class="btn btn-sm btn-primary shadow-sm hover:shadow-md transition-all">
                                            <i data-lucide="award" class="w-4 h-4"></i> Tetapkan Sebagai Perwakilan
                                        </button>
                                    </form>
                                    
                                    <script>
                                        function konfirmasiTetapkan() {
                                            Swal.fire({
                                                title: 'Tetapkan Perwakilan?',
                                                text: "Pendaftar ini akan ditetapkan sebagai perwakilan tunggal dari Desa Anda. Pendaftar lain akan otomatis digugurkan!",
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#059669',
                                                cancelButtonColor: '#ef4444',
                                                confirmButtonText: 'Ya, Tetapkan!',
                                                cancelButtonText: 'Batal'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    document.getElementById('form-tetapkan-perwakilan').submit();
                                                }
                                            });
                                        }
                                    </script>
                                </div>
                            </div>
                        </div>
                        @elseif($pendaftaran->ranking !== 1)
                        <div class="mt-2">
                            <div class="alert alert-secondary bg-slate-50 border border-slate-200 text-slate-600">
                                <i data-lucide="info" class="w-5 h-5"></i>
                                <div>
                                    <strong>Bukan Perwakilan Desa</strong>
                                    <p class="text-sm mt-1">Pendaftar ini tidak menduduki Peringkat 1 (atau pendaftar lain sudah ditetapkan). Unggah dokumen rekomendasi tidak tersedia.</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                @endif
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
