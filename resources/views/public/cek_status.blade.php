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
            @if(isset($pendaftarans))
                @if($pendaftarans->count() > 0)
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

                                // Stage 1: Pengajuan (Selalu selesai jika data ada)
                                $stage1 = 'done';

                                // Stage 2: Verifikasi OPD
                                if (in_array($st, ['menunggu_verifikasi', 'sedang_diverifikasi'])) {
                                    $stage2 = 'active';
                                    $stage3 = 'pending';
                                    $stage4 = 'pending';
                                    $stage5 = 'pending';
                                } elseif ($st === 'tidak_lolos_verifikasi') {
                                    $stage2 = 'failed';
                                    $stage3 = 'pending';
                                    $stage4 = 'pending';
                                    $stage5 = 'pending';
                                } else {
                                    $stage2 = 'done';
                                }

                                // Stage 3: Rekomendasi Desa & Kecamatan
                                if ($stage2 === 'done') {
                                    if ($st === 'tidak_lolos_desa' || $st === 'ditolak_kecamatan') {
                                        $stage3 = 'failed';
                                        $stage4 = 'pending';
                                        $stage5 = 'pending';
                                    } elseif (in_array($st, ['lolos_verifikasi', 'diteruskan_ke_kecamatan']) && ($p->program?->kode === 'sdss')) {
                                        $stage3 = 'active';
                                        $stage4 = 'pending';
                                        $stage5 = 'pending';
                                    } else {
                                        $stage3 = 'done';
                                    }
                                }

                                // Stage 4: Penilaian SPK
                                if ($stage3 === 'done') {
                                    if (in_array($st, ['proses_seleksi', 'proses_penilaian', 'menunggu_penilaian', 'proses_wawancara', 'menunggu_wawancara', 'menunggu_penetapan'])) {
                                        $stage4 = 'active';
                                        $stage5 = 'pending';
                                    } elseif ($st === 'gugur_wawancara') {
                                        $stage4 = 'failed';
                                        $stage5 = 'pending';
                                    } else {
                                        $stage4 = 'done';
                                    }
                                }

                                // Stage 5: Hasil Penetapan
                                if ($stage4 === 'done') {
                                    if (in_array($st, ['lulus', 'sk_terbit'])) {
                                        $stage5 = 'done';
                                    } elseif ($st === 'tidak_lulus') {
                                        $stage5 = 'failed';
                                    } else {
                                        $stage5 = 'active';
                                    }
                                }

                                $stagesList = [
                                    ['title' => 'Pengajuan', 'desc' => 'Pendaftaran', 'state' => $stage1, 'icon' => 'file-text'],
                                    ['title' => 'Verifikasi OPD', 'desc' => 'Berkas & Syarat', 'state' => $stage2, 'icon' => 'shield-check'],
                                    ['title' => 'Desa & Kec.', 'desc' => 'Rekomendasi', 'state' => $stage3, 'icon' => 'map-pin'],
                                    ['title' => 'Penilaian SPK', 'desc' => 'Skor & Ranking', 'state' => $stage4, 'icon' => 'award'],
                                    ['title' => 'Penetapan', 'desc' => 'Hasil Pengumuman', 'state' => $stage5, 'icon' => 'check-circle-2'],
                                ];
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
                    </div>
                @else
                    <x-empty-state icon="search-x" title="Data Tidak Ditemukan" text="Tidak ditemukan riwayat pendaftaran beasiswa dengan NIK dan Tahun Pendaftaran tersebut." />
                @endif
            @endif
        </div>
    </section>
</x-layouts.public>
