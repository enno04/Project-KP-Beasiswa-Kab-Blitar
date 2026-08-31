<x-layouts.public :title="'Pendaftaran Beasiswa'">
    {{-- Emergency Banner (Dari Pengaturan Superadmin) --}}
    @if(isset($webSettings['emergency_banner_active']) && $webSettings['emergency_banner_active'] == '1' && !empty($webSettings['emergency_banner_text']))
        @php
            $bgColorClass = 'bg-amber-500';
            $textColorClass = 'text-amber-900';
            if (isset($webSettings['emergency_banner_color'])) {
                if ($webSettings['emergency_banner_color'] === 'red') {
                    $bgColorClass = 'bg-red-500';
                    $textColorClass = 'text-red-50';
                } elseif ($webSettings['emergency_banner_color'] === 'blue') {
                    $bgColorClass = 'bg-blue-600';
                    $textColorClass = 'text-blue-50';
                }
            }
        @endphp
        <div class="{{ $bgColorClass }} {{ $textColorClass }} font-semibold text-sm py-3 px-4 shadow-sm border-b border-black/10 relative z-40 w-full animate-fade-in-down">
            <div class="max-w-7xl mx-auto flex items-center justify-center gap-2 text-center">
                <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0 {{ $bgColorClass === 'bg-amber-500' ? 'text-amber-800' : 'text-white' }}"></i>
                <span>{{ $webSettings['emergency_banner_text'] }}</span>
            </div>
        </div>
    @endif

    {{-- Page Hero --}}
    <section class="bg-cover bg-center bg-no-repeat border-b border-slate-200"
        style="background-image: url('{{ asset('images/header_pendaftaran.png') }}');">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <p class="text-xs sm:text-sm font-extrabold text-amber-400 uppercase tracking-widest mb-3">Daftar Sekarang</p>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight drop-shadow-md">Pendaftaran Beasiswa</h1>
            @if($periodeAktif)
                <p class="text-slate-200 mt-3 font-medium">Periode: <strong class="text-amber-300 font-bold">{{ $periodeAktif->nama }}</strong></p>
                <p class="text-sm text-slate-300 mt-1">
                    Masa Periode: {{ $periodeAktif->tanggal_mulai?->translatedFormat('d M Y') ?? '-' }} 
                    — {{ $periodeAktif->tanggal_selesai?->translatedFormat('d M Y') ?? '-' }}
                </p>
            @else
                <div class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-red-500/20 backdrop-blur-md border border-red-300/40 text-red-200 font-semibold text-sm">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-red-300"></i> Saat ini belum ada periode pendaftaran yang dibuka.
                </div>
            @endif
        </div>
    </section>

    <section class="py-12 min-h-[40vh]" x-data="{
        modalOpen: false,
        jalurNama: '',
        progNama: '',
        daftarUrl: '',
        persyaratans: [],
        checks: {},
        openModal(jalurNama, progNama, daftarUrl, persyaratans) {
            this.jalurNama = jalurNama;
            this.progNama = progNama;
            this.daftarUrl = daftarUrl;
            this.persyaratans = persyaratans;
            this.checks = {};
            persyaratans.forEach((p, i) => { this.checks[i] = false; });
            this.modalOpen = true;
        },
        get checkedCount() {
            return Object.values(this.checks).filter(v => v).length;
        },
        get allChecked() {
            return this.persyaratans.length > 0 && this.checkedCount === this.persyaratans.length;
        },
        get progressPct() {
            if (this.persyaratans.length === 0) return 0;
            return Math.round((this.checkedCount / this.persyaratans.length) * 100);
        }
    }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($periodeAktif && $programs->count() > 0)
                <div class="space-y-8">
                    @foreach($programs as $prog)
                        <div class="card overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="card-body py-5 bg-slate-50/50 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 bg-primary-light text-primary-dark">
                                        <i data-lucide="{{ $prog->isSdss() ? 'graduation-cap' : 'award' }}" class="w-6 h-6"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h2 class="text-xl font-extrabold text-slate-900">{{ $prog->nama }}</h2>
                                        <p class="text-sm text-slate-500 mt-1 line-clamp-2 mb-3">{{ $prog->deskripsi ?? 'Program Beasiswa Pemerintah Kabupaten Blitar.' }}</p>
                                    </div>
                                </div>
                                @if($prog->status_pendaftaran === 'Sedang Dibuka')
                                    <x-badge type="success" dot>Buka</x-badge>
                                @else
                                    <x-badge type="muted">Tutup</x-badge>
                                @endif
                            </div>

                            <div class="card-body">
                                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4">Pilih Jalur Pendaftaran</h3>
                                @if($prog->jalurs->count() > 0)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        @foreach($prog->jalurs as $jalur)
                                            <div class="border border-slate-200 rounded-xl p-4 transition-all {{ $prog->status_pendaftaran === 'Sedang Dibuka' ? 'hover:border-primary hover:shadow-md bg-white' : 'bg-slate-50 opacity-75' }}">
                                                <h4 class="font-bold text-slate-900 mb-1">{{ $jalur->nama }}</h4>
                                                <p class="text-xs text-slate-500 mb-4">{{ $jalur->keterangan ?? 'Jalur pendaftaran beasiswa.' }}</p>
                                                <div class="bg-white rounded-lg p-3 flex flex-col gap-1 text-sm max-w-sm">
                                                    <div class="flex flex-row gap-4 items-center text-slate-600 border-b border-slate-50 border-dashed pb-1">
                                                        <span class="w-16">Dibuka</span><span class="font-semibold text-slate-800">: {{ $prog->tanggal_buka ? $prog->tanggal_buka->translatedFormat('d F Y') : '-' }}</span>
                                                    </div>
                                                    <div class="flex flex-row gap-4 items-center text-slate-600 pt-1">
                                                        <span class="w-16">Ditutup</span><span class="font-semibold text-slate-800">: {{ $prog->tanggal_tutup ? $prog->tanggal_tutup->translatedFormat('d F Y') : '-' }}</span>
                                                    </div>
                                                </div>
                                                @if($prog->status_pendaftaran === 'Sedang Dibuka')
                                                    @if($jalur->dokumens->count() > 0)
                                                        <button type="button" class="btn btn-primary btn-sm w-full justify-center mt-3"
                                                            @click="openModal(
                                                                '{{ addslashes($jalur->nama) }}',
                                                                '{{ addslashes($prog->nama) }}',
                                                                '{{ route('pendaftaran.create', ['programSlug' => $prog->slug, 'jalurSlug' => $jalur->slug]) }}',
                                                                {{ $jalur->dokumens->map(fn($d) => ['nama' => $d->nama])->toJson() }}
                                                            )">
                                                            <i data-lucide="clipboard-check" class="w-4 h-4"></i> Cek Syarat & Daftar
                                                        </button>
                                                    @else
                                                        <a href="{{ route('pendaftaran.create', ['programSlug' => $prog->slug, 'jalurSlug' => $jalur->slug]) }}" class="btn btn-primary btn-sm w-full justify-center mt-3">
                                                            <i data-lucide="edit-3" class="w-4 h-4"></i> Daftar Jalur Ini
                                                        </a>
                                                    @endif
                                                @else
                                                    <button disabled class="btn btn-sm w-full justify-center bg-slate-200 text-slate-500 cursor-not-allowed mt-3">Belum Dibuka</button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-slate-400 italic">Belum ada jalur yang dikonfigurasi.</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif($periodeAktif)
                <x-empty-state icon="folder-open" title="Belum Ada Program" text="Program beasiswa untuk periode ini belum dikonfigurasi oleh panitia." />
            @endif
        </div>
        {{-- ===== MODAL CHECKLIST PERSYARATAN ===== --}}
        <div x-show="modalOpen" x-cloak style="display:none;"
            class="fixed inset-0 z-50 flex items-center justify-center p-4">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modalOpen = false"></div>

            {{-- Modal Card --}}
            <div class="relative z-10 w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95">

                {{-- Header --}}
                <div class="p-6 border-b border-slate-100" style="background: linear-gradient(135deg, #2B5C92, #0C1446);">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold text-white/60 uppercase tracking-widest mb-1" x-text="progNama"></p>
                            <h3 class="text-lg font-extrabold text-white" x-text="jalurNama"></h3>
                            <p class="text-sm text-white/70 mt-1">Pastikan semua dokumen berikut sudah siap sebelum mengisi formulir.</p>
                        </div>
                        <button @click="modalOpen = false"
                            class="shrink-0 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="px-6 pt-5">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-semibold text-slate-500">Progress Kelengkapan</span>
                        <span class="text-xs font-bold text-primary" x-text="checkedCount + ' / ' + persyaratans.length + ' terpenuhi'"></span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500"
                            :class="allChecked ? 'bg-emerald-500' : 'bg-primary'"
                            :style="'width: ' + progressPct + '%'"></div>
                    </div>
                </div>

                {{-- Checklist Items --}}
                <div class="p-5 space-y-2 max-h-[45vh] overflow-y-auto overscroll-contain" @wheel.stop @touchmove.stop>
                    <template x-for="(item, index) in persyaratans" :key="index">
                        <label class="flex items-start gap-3 px-3 py-2.5 rounded-xl border cursor-pointer transition-all"
                            :class="checks[index] ? 'bg-emerald-50 border-emerald-300' : 'bg-slate-50 border-slate-200 hover:border-primary/50'">
                            <input type="checkbox" x-model="checks[index]"
                                class="mt-0.5 w-5 h-5 rounded accent-emerald-500 shrink-0 cursor-pointer">
                            <span class="text-sm font-medium leading-relaxed flex-1 transition-colors"
                                :class="checks[index] ? 'text-emerald-700 line-through opacity-70' : 'text-slate-700'"
                                x-text="item.nama"></span>
                            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5 transition-opacity"
                                :class="checks[index] ? 'opacity-100' : 'opacity-0'"></i>
                        </label>
                    </template>
                </div>

                {{-- Footer --}}
                <div class="px-6 pb-6 pt-4 border-t border-slate-100 flex flex-col sm:flex-row gap-3 items-center">
                    <div class="flex-1 text-xs rounded-lg px-3 py-2 flex items-center gap-2"
                        :class="allChecked
                            ? 'text-emerald-700 bg-emerald-50 border border-emerald-200'
                            : 'text-amber-600 bg-amber-50 border border-amber-200'">
                        <template x-if="!allChecked">
                            <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i>
                        </template>
                        <template x-if="allChecked">
                            <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                        </template>
                        <span x-text="allChecked ? 'Semua dokumen siap! Anda bisa melanjutkan.' : 'Centang semua persyaratan untuk melanjutkan.'"></span>
                    </div>
                    <a :href="allChecked ? daftarUrl : '#'"
                        :class="allChecked
                            ? 'btn btn-primary btn-sm shrink-0 justify-center'
                            : 'btn btn-sm shrink-0 justify-center bg-slate-200 text-slate-400 cursor-not-allowed pointer-events-none'">
                        <i data-lucide="edit-3" class="w-4 h-4"></i> Lanjut Isi Form
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
