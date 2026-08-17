<x-layouts.public :title="'Kriteria & Persyaratan — Beasiswa Blitar Mengabdi'">
    {{-- Page Hero --}}
    <section class="bg-cover bg-center bg-no-repeat border-b border-slate-200" style="background-image: url('{{ asset('images/kriteria.png') }}');">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <p class="text-sm font-bold text-white uppercase tracking-widest mb-3">Berkas Wajib & Skema SPK</p>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">Persyaratan Berkas & Bobot Penilaian SPK</h1>
            <p class="text-white mt-3 max-w-2xl mx-auto">Cek daftar dokumen kelengkapan berkas yang wajib diunggah serta skema bobot penilaian Sistem Pendukung Keputusan (SPK) untuk setiap jalur beasiswa.</p>
        </div>
    </section>

    <div class="bg-cover bg-center bg-fixed" style="background-image: url('{{ asset('images/background.jpeg') }}')">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
    @if($programs->count() > 0)
        @php
            $firstJalur = null;
            foreach($programs as $prog) {
                if($prog->jalurs->count() > 0) {
                    $firstJalur = $prog->jalurs->first()->slug;
                    break;
                }
            }
        @endphp

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16" x-data="{ tab: '{{ $firstJalur ?? 'none' }}' }">
            {{-- Tabs --}}
            <div class="flex flex-wrap justify-center gap-2 mb-12">
                @foreach($programs as $prog)
                    @foreach($prog->jalurs as $jalur)
                        <button @click="tab = '{{ $jalur->slug }}'"
                                :class="tab === '{{ $jalur->slug }}' ? 'bg-primary text-white shadow-lg' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'"
                                class="px-6 py-3 rounded-xl font-bold text-sm transition-all focus:outline-none">
                            {{ $jalur->nama }}
                        </button>
                    @endforeach
                @endforeach
            </div>

            {{-- Tab Contents --}}
            <div>
                @foreach($programs as $prog)
                    @foreach($prog->jalurs as $jalur)
                        <div x-show="tab === '{{ $jalur->slug }}'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="card" style="display: none;">
                            <div class="card-body space-y-10">
                                <div class="border-b border-slate-200 pb-6 flex items-center justify-between">
                                    <div>
                                        <h2 class="text-2xl font-extrabold text-slate-900">Persyaratan Jalur {{ $jalur->nama }}</h2>
                                        <p class="text-sm text-slate-500 mt-1">Program: <span class="font-semibold text-slate-700">{{ $prog->nama }}</span></p>
                                    </div>
                                    <div class="w-12 h-12 bg-primary-light text-primary rounded-xl flex items-center justify-center shrink-0">
                                        <i data-lucide="{{ $prog->isSdss() ? 'graduation-cap' : 'award' }}" class="w-6 h-6"></i>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                    {{-- Dokumen & Syarat --}}
                                    <div class="space-y-8">
                                        <div>
                                            <h3 class="font-bold text-slate-900 text-lg mb-4 flex items-center gap-2">
                                                <i data-lucide="file-check" class="w-5 h-5 text-green-500"></i> Dokumen Wajib
                                            </h3>
                                            <ul class="space-y-3 text-sm text-slate-600">
                                                @forelse($jalur->dokumens as $dok)
                                                    <li class="flex items-start gap-3 p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                                                        <span class="font-bold text-slate-400 mt-0.5">{{ $loop->iteration }}.</span>
                                                        <div>
                                                            <span class="font-bold text-slate-800">{{ $dok->nama }}</span>
                                                            @if($dok->wajib) <span class="text-red-500 ml-1">*</span> @endif
                                                            <p class="text-xs mt-1 text-slate-400">{{ $dok->deskripsi ?? 'Format: ' . strtoupper($dok->format_file) }}</p>
                                                        </div>
                                                    </li>
                                                @empty
                                                    <li class="text-slate-400 italic">Belum ada dokumen persyaratan yang dikonfigurasi.</li>
                                                @endforelse
                                            </ul>
                                        </div>

                                        @if($jalur->persyaratans && $jalur->persyaratans->count() > 0)
                                        <div>
                                            <h3 class="font-bold text-slate-900 text-lg mb-4 flex items-center gap-2">
                                                <i data-lucide="list-checks" class="w-5 h-5 text-primary"></i> Syarat Khusus
                                            </h3>
                                            <ul class="space-y-3 text-sm text-slate-600">
                                                @foreach($jalur->persyaratans as $syarat)
                                                    <li class="flex items-start gap-2">
                                                        <span class="text-primary mt-0.5">•</span>
                                                        <span>{{ $syarat->deskripsi }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif
                                    </div>

                                    {{-- Kriteria Penilaian --}}
                                    <div class="space-y-6 bg-primary-light/50 p-6 rounded-2xl border border-primary-light">
                                        <h3 class="font-bold text-slate-900 text-lg mb-4 flex items-center gap-2">
                                            <i data-lucide="target" class="w-5 h-5 text-primary"></i> Kriteria Penilaian (SPK)
                                        </h3>
                                        <p class="text-xs text-slate-600 mb-6 leading-relaxed">Kelulusan jalur ini ditentukan melalui Sistem Pendukung Keputusan dengan mempertimbangkan bobot kriteria berikut:</p>

                                        <div class="space-y-6">
                                            @forelse($jalur->kelompokKriterias as $kelompok)
                                                <div>
                                                    <h4 class="font-bold text-sm text-slate-800 mb-3 border-b border-primary-light pb-2 flex justify-between items-center">
                                                        <span>{{ $kelompok->nama }}</span>
                                                        @if($kelompok->bobot > 0)
                                                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-primary/10 text-primary font-bold">Bobot: {{ number_format($kelompok->bobot, 0) }}%</span>
                                                        @endif
                                                    </h4>
                                                    <ul class="space-y-2 text-sm text-slate-600 pl-2">
                                                        @foreach($kelompok->kriterias as $kriteria)
                                                            <li class="flex items-center justify-between">
                                                                <span class="flex items-center gap-2">
                                                                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                                                    {{ $kriteria->nama }}
                                                                </span>
                                                                @if($kriteria->bobot > 0)
                                                                    <span class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">Bobot: {{ number_format($kriteria->bobot, 0) }}%</span>
                                                                @elseif(!empty($kriteria->sifat))
                                                                    <x-badge type="muted">{{ $kriteria->sifat }}</x-badge>
                                                                @else
                                                                    <x-badge type="muted">{{ ucfirst($kriteria->tipe_input) }}</x-badge>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @empty
                                                <p class="text-slate-400 italic text-sm">Kriteria penilaian belum dikonfigurasi.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
    @else
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <x-empty-state icon="folder-open" title="Belum Ada Informasi" text="Kriteria dan persyaratan beasiswa belum dikonfigurasi untuk periode ini." />
        </div>
    @endif
</x-layouts.public>
