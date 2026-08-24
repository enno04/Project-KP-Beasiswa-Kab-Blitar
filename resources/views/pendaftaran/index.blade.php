<x-layouts.public :title="'Pendaftaran Beasiswa'">
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

    <section class="py-12 min-h-[40vh]">
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
                                                    <a href="{{ route('pendaftaran.create', ['programSlug' => $prog->slug, 'jalurSlug' => $jalur->slug]) }}" class="btn btn-primary btn-sm w-full justify-center">
                                                        <i data-lucide="edit-3" class="w-4 h-4"></i> Daftar Jalur Ini
                                                    </a>
                                                @else
                                                    <button disabled class="btn btn-sm w-full justify-center bg-slate-200 text-slate-500 cursor-not-allowed">Belum Dibuka</button>
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
    </section>
</x-layouts.public>
