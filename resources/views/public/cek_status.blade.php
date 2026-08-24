<x-layouts.public :title="'Cek Status & Cetak Bukti Pendaftaran'">
    {{-- Page Hero --}}
    <section class="bg-cover bg-center bg-no-repeat border-b border-slate-200"
        style="background-image: url('{{ asset('images/header_cekstatus.png') }}');">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <p class="text-xs sm:text-sm font-extrabold text-amber-400 uppercase tracking-widest mb-3">Status Pendaftaran</p>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight drop-shadow-md">Cek Status & Cetak Bukti Pendaftaran</h1>
            <p class="text-slate-200 mt-3 max-w-2xl mx-auto leading-relaxed">Masukkan NIK dan Tahun Pendaftaran untuk melihat status dan mencetak tanda bukti pendaftaran Beasiswa Anda.</p>
        </div>
    </section>

    <section class="py-12 min-h-[50vh]">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Form Pencarian --}}
            <div class="card mb-8">
                <div class="card-body">
                    <form method="POST" action="{{ route('cek.status.proses') }}" class="space-y-5">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <x-form-input name="nik" label="Nomor Induk Kependudukan (NIK)" :required="true" :value="request('nik') ?? old('nik')" placeholder="Masukkan 16 digit NIK" maxlength="16" pattern="[0-9]{16}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 16);" />
                            </div>
                            <div>
                                <x-form-select name="tahun" label="Tahun Pendaftaran" :required="true" :options="$tahunOptions ?? []" :value="request('tahun') ?? old('tahun') ?? ($tahunAktif ?? date('Y'))" placeholder="Pilih Tahun" />
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-full justify-center">
                            <i data-lucide="search" class="w-5 h-5"></i> Cek Status & Cetak Bukti
                        </button>
                    </form>
                </div>
            </div>

            {{-- Hasil Pencarian --}}
            @if(isset($pendaftarans) || isset($historiPenerimas))
                @if((isset($pendaftarans) && $pendaftarans->count() > 0) || (isset($historiPenerimas) && $historiPenerimas->count() > 0))
                    <div class="space-y-6">
                        @foreach($pendaftarans as $p)
                        <div class="card overflow-hidden">
                            <div class="card-body py-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">No. Pendaftaran</p>
                                    <p class="font-extrabold text-xl text-primary-dark">{{ $p->nomor_pendaftaran }}</p>
                                </div>
                                <span class="badge {{ $p->status_color }}">{{ $p->status_label }}</span>
                            </div>

                            {{-- Alur Kemajuan Pendaftaran --}}
                            @php
                                $st = $p->status;
                                $prog = $p->program?->kode;
                                $stagesList = [];

                                // 1. PENGAJUAN (Selalu Pertama & Selalu Selesai)
                                $stagesList[] = ['title' => 'Pengajuan', 'desc' => 'Pendaftaran', 'state' => 'done', 'icon' => 'file-text'];
                                $currentStepIsDone = true;

                                // 2. VERIFIKASI OPD
                                $verifState = 'pending';
                                if (in_array($st, ['menunggu_verifikasi', 'sedang_diverifikasi'])) {
                                    $verifState = 'active';
                                    $currentStepIsDone = false;
                                } elseif ($st === 'tidak_lolos_verifikasi') {
                                    $verifState = 'failed';
                                    $currentStepIsDone = false;
                                } elseif ($st !== 'draft') {
                                    $verifState = 'done';
                                }
                                $stagesList[] = ['title' => 'Verifikasi OPD', 'desc' => 'Berkas & Syarat', 'state' => $verifState, 'icon' => 'shield-check'];


                                // TAHAP SELANJUTNYA TERGANTUNG PROGRAM
                                if ($prog === 'sdss') {
                                    // SDSS: Penilaian SPK (Desa) -> Rekomendasi (Desa&Kec) -> Penetapan

                                    // 3. Penilaian SPK (Skor Desa)
                                    $spkState = 'pending';
                                    if ($currentStepIsDone) {
                                        if (in_array($st, ['lolos_verifikasi', 'proses_penilaian', 'menunggu_penilaian'])) {
                                            $spkState = 'active';
                                            $currentStepIsDone = false;
                                        } else {
                                            $spkState = 'done';
                                        }
                                    }
                                    $stagesList[] = ['title' => 'Penilaian SPK', 'desc' => 'Skor (Desa)', 'state' => $spkState, 'icon' => 'award'];

                                    // 4. Rekomendasi
                                    $rekState = 'pending';
                                    if ($currentStepIsDone) {
                                        if (in_array($st, ['diteruskan_ke_kecamatan'])) {
                                            $rekState = 'active';
                                            $currentStepIsDone = false;
                                        } elseif (in_array($st, ['tidak_lolos_desa', 'ditolak_kecamatan', 'ditolak_dpmd'])) {
                                            $rekState = 'failed';
                                            $currentStepIsDone = false;
                                        } elseif (in_array($st, ['menunggu_penetapan', 'lulus', 'sk_terbit', 'tidak_lulus'])) {
                                            $rekState = 'done';
                                        } else {
                                            $rekState = 'active';
                                            $currentStepIsDone = false;
                                        }
                                    }
                                    $stagesList[] = ['title' => 'Rekomendasi', 'desc' => 'Desa, Kec & DPMD', 'state' => $rekState, 'icon' => 'map-pin'];

                                } else {
                                    // NON-SDSS: Penilaian SPK (Kabupaten) -> [Wawancara (BB)] -> Penetapan

                                    // 3. Penilaian SPK (Kabupaten)
                                    $spkState = 'pending';
                                    if ($currentStepIsDone) {
                                        if (in_array($st, ['lolos_verifikasi', 'proses_seleksi', 'proses_penilaian', 'menunggu_penilaian'])) {
                                            $spkState = 'active';
                                            $currentStepIsDone = false;
                                        } else {
                                            $spkState = 'done';
                                        }
                                    }
                                    $stagesList[] = ['title' => 'Penilaian SPK', 'desc' => 'Skor & Ranking', 'state' => $spkState, 'icon' => 'award'];

                                    // 4. Wawancara (Hanya BB)
                                    if ($prog === 'berdaya_berjaya') {
                                        $wawancaraState = 'pending';
                                        if ($currentStepIsDone) {
                                            if (in_array($st, ['menunggu_wawancara', 'proses_wawancara'])) {
                                                $wawancaraState = 'active';
                                                $currentStepIsDone = false;
                                            } elseif ($st === 'gugur_wawancara') {
                                                $wawancaraState = 'failed';
                                                $currentStepIsDone = false;
                                            } elseif (in_array($st, ['menunggu_penetapan', 'lulus', 'sk_terbit', 'tidak_lulus'])) {
                                                $wawancaraState = 'done';
                                            } else {
                                                $wawancaraState = 'active';
                                                $currentStepIsDone = false;
                                            }
                                        }
                                        $stagesList[] = ['title' => 'Wawancara', 'desc' => 'Seleksi Akhir', 'state' => $wawancaraState, 'icon' => 'users'];
                                    }
                                }

                                // 5. PENETAPAN (Untuk Semua Program)
                                $penetapanState = 'pending';
                                if ($currentStepIsDone) {
                                    if (in_array($st, ['menunggu_penetapan'])) {
                                        $penetapanState = 'active';
                                    } elseif (in_array($st, ['lulus', 'sk_terbit'])) {
                                        $penetapanState = 'done';
                                    } elseif ($st === 'tidak_lulus') {
                                        $penetapanState = 'failed';
                                    } else {
                                        $penetapanState = 'active';
                                    }
                                }
                                $stagesList[] = ['title' => 'Penetapan', 'desc' => 'Hasil Akhir', 'state' => $penetapanState, 'icon' => 'check-circle-2'];

                            @endphp

                            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/40">
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                                    <i data-lucide="git-commit" class="w-4 h-4 text-primary"></i> Alur Kemajuan Pendaftaran
                                </p>

                                {{-- Visual Stepper --}}
                                <div class="relative flex items-center justify-between mb-4">
                                    @foreach($stagesList as $idx => $stage)
                                        @php
                                            $isFirst = $idx === 0;
                                        @endphp

                                        <div class="flex-1 relative flex flex-col items-center group">
                                            @if(!$isFirst)
                                                <div class="absolute left-[-50%] top-4 w-full h-1 -z-10 {{ $stage['state'] === 'done' || $stagesList[$idx-1]['state'] === 'done' ? 'bg-emerald-500' : ($stage['state'] === 'failed' ? 'bg-red-400' : 'bg-slate-200') }}"></div>
                                            @endif

                                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold transition-all shadow-xs z-10
                                                {{ $stage['state'] === 'done' ? 'bg-emerald-500 text-white shadow-emerald-200' : '' }}
                                                {{ $stage['state'] === 'active' ? 'bg-primary text-white ring-4 ring-primary/20 shadow-primary-200 animate-pulse' : '' }}
                                                {{ $stage['state'] === 'failed' ? 'bg-red-500 text-white shadow-red-200' : '' }}
                                                {{ $stage['state'] === 'pending' ? 'bg-slate-100 text-slate-400 border border-slate-200' : '' }}
                                            ">
                                                @if($stage['state'] === 'done')
                                                    <i data-lucide="check" class="w-5 h-5"></i>
                                                @elseif($stage['state'] === 'failed')
                                                    <i data-lucide="x" class="w-5 h-5"></i>
                                                @else
                                                    <i data-lucide="{{ $stage['icon'] }}" class="w-4 h-4"></i>
                                                @endif
                                            </div>

                                            <div class="text-center mt-2 px-0.5">
                                                <p class="text-xs font-bold leading-tight {{ $stage['state'] === 'done' ? 'text-emerald-700' : ($stage['state'] === 'active' ? 'text-primary' : ($stage['state'] === 'failed' ? 'text-red-600' : 'text-slate-400')) }}">
                                                    {{ $stage['title'] }}
                                                </p>
                                                <p class="text-[10px] text-slate-400 hidden sm:block mt-0.5">{{ $stage['desc'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Status Banner --}}
                                <div class="p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ in_array($st, ['lulus', 'sk_terbit']) ? 'bg-emerald-100 text-emerald-600' : (in_array($st, ['tidak_lolos_desa', 'tidak_lolos_verifikasi', 'ditolak_kecamatan', 'gugur_wawancara', 'tidak_lulus']) ? 'bg-red-100 text-red-600' : 'bg-primary-light text-primary') }}">
                                        <i data-lucide="info" class="w-4.5 h-4.5"></i>
                                    </div>
                                    <div class="text-xs sm:text-sm">
                                        <span class="text-slate-500 font-medium">Status / Posisi Berkas:</span>
                                        <span class="font-bold text-slate-900 ml-1">{{ $p->detail_progres }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body space-y-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-slate-400 mb-1 text-xs font-medium">Nama Pendaftar</p>
                                        <p class="font-bold text-slate-900">{{ $p->identitas->nama_lengkap ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-slate-400 mb-1 text-xs font-medium">Program & Jalur</p>
                                        <p class="font-bold text-slate-900">{{ $p->program->nama ?? '-' }} — {{ $p->jalur->nama ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-slate-400 mb-1 text-xs font-medium">Periode</p>
                                        <p class="font-bold text-slate-900">{{ $p->periode->nama ?? '-' }} ({{ $p->periode->tahun ?? '-' }})</p>
                                    </div>
                                    <div>
                                        <p class="text-slate-400 mb-1 text-xs font-medium">Tanggal Daftar</p>
                                        <p class="font-bold text-slate-900">{{ $p->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>

                                {{-- Dokumen --}}
                                @if($p->uploadDokumens && $p->uploadDokumens->count() > 0)
                                <div class="border-t border-slate-100 pt-5">
                                    <p class="text-sm font-bold text-slate-800 mb-3 flex items-center gap-2">
                                        <i data-lucide="files" class="w-4 h-4 text-slate-500"></i> Status Dokumen Persyaratan
                                    </p>
                                    <div class="space-y-2">
                                        @foreach($p->uploadDokumens as $dok)
                                        @php
                                            $dokType = match($dok->status) {
                                                'valid' => 'success',
                                                'tidak_valid' => 'danger',
                                                'belum_diverifikasi' => 'warning',
                                                default => 'muted',
                                            };
                                        @endphp
                                        <div class="flex items-center justify-between text-sm p-3 bg-slate-50 rounded-lg border border-slate-100">
                                            <span class="font-medium text-slate-700">{{ $dok->dokumen->nama ?? 'Dokumen' }}</span>
                                            <x-badge :type="$dokType">{{ str_replace('_', ' ', $dok->status) }}</x-badge>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <div id="cetak" class="border-t border-slate-100 pt-4 flex justify-end">
                                    <a href="{{ route('pendaftaran.bukti', $p->id) }}" class="btn btn-outline" target="_blank">
                                        <i data-lucide="printer" class="w-4 h-4"></i> Cetak Tanda Bukti
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        {{-- Data Histori Penerima --}}
                        @if(isset($historiPenerimas))
                            @foreach($historiPenerimas as $h)
                            <div class="card overflow-hidden">
                                <div class="card-body py-4 border-b border-slate-100 bg-emerald-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1">No. Pendaftaran (Histori)</p>
                                        <p class="font-extrabold text-xl text-emerald-700">{{ $h->nomor_pendaftaran ?? '-' }}</p>
                                    </div>
                                    <span class="badge success">Penerima Beasiswa Ditetapkan</span>
                                </div>

                                <div class="bg-emerald-50/30 p-4 border-b border-slate-100">
                                    <div class="p-3.5 rounded-xl bg-white border border-slate-200/80 shadow-xs flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 bg-emerald-100 text-emerald-600">
                                            <i data-lucide="check-circle" class="w-4.5 h-4.5"></i>
                                        </div>
                                        <div class="text-xs sm:text-sm">
                                            <span class="text-slate-500 font-medium">Status / Posisi Berkas:</span>
                                            <span class="font-bold text-emerald-700 ml-1">Telah ditetapkan sebagai Penerima Beasiswa Tahun {{ $h->tahun }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body space-y-6">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-slate-400 mb-1 text-xs font-medium">Nama Pendaftar</p>
                                            <p class="font-bold text-slate-900">{{ $h->nama_lengkap ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-slate-400 mb-1 text-xs font-medium">Program & Jalur</p>
                                            <p class="font-bold text-slate-900">{{ $h->jenis_beasiswa ?? '-' }} — {{ $h->jalur_beasiswa ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-slate-400 mb-1 text-xs font-medium">Tahun Penetapan</p>
                                            <p class="font-bold text-slate-900">{{ $h->tahun ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-slate-400 mb-1 text-xs font-medium">Asal Perguruan Tinggi</p>
                                            <p class="font-bold text-slate-900">{{ $h->asal_perguruan_tinggi ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-slate-400 mb-1 text-xs font-medium">IPK / Nilai Akademik</p>
                                            <p class="font-bold text-slate-900">{{ $h->ipk_nilai ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-slate-400 mb-1 text-xs font-medium">Kecamatan</p>
                                            <p class="font-bold text-slate-900">{{ $h->kecamatan ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-slate-400 mb-1 text-xs font-medium">Desa</p>
                                            <p class="font-bold text-slate-900">{{ $h->desa ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-slate-400 mb-1 text-xs font-medium">Waktu Penetapan</p>
                                            <p class="font-bold text-slate-900">
                                                {{ $h->waktu_penetapan ?? '-' }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-slate-400 mb-1 text-xs font-medium">Sumber Data</p>
                                            <p class="font-bold text-slate-900">
                                                @if(strtolower($h->sumber_data) === 'sistem')
                                                    Sistem Pendaftaran Beasiswa Online (Tahun {{ $h->tahun ?? '-' }})
                                                @else
                                                    Arsip / Impor Data Dinas (Tahun {{ $h->tahun ?? '-' }})
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                @else
                    <x-empty-state icon="search-x" title="Data Tidak Ditemukan" text="Tidak ditemukan riwayat pendaftaran atau histori penerima beasiswa dengan NIK dan Tahun Pendaftaran tersebut." />
                @endif
            @endif
        </div>
    </section>
</x-layouts.public>
