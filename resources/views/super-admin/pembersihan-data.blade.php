<x-layouts.admin :title="'Pembersihan Data Pendaftar'">
    <x-page-header title="Pembersihan Data" subtitle="Hapus seluruh data pendaftar beserta dokumen per kategori program beasiswa." />

    @if(session('success'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-xl bg-green-50 flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 mb-6 text-sm text-red-800 rounded-xl bg-red-50 flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-8 flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
            <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
        </div>
        <div>
            <h3 class="text-red-900 font-bold text-lg mb-1">Peringatan Zona Bahaya (Danger Zone)</h3>
            <p class="text-red-700 text-sm leading-relaxed">
                Tindakan menghapus data di halaman ini bersifat <strong>permanen dan tidak dapat dikembalikan (irreversible)</strong>.
                Sistem akan menghapus seluruh rekaman Pendaftaran, Audit Log, Nilai, Rekomendasi, hingga <strong>berkas fisik yang diunggah oleh pendaftar</strong> pada program terkait.
                Pastikan Anda benar-benar yakin sebelum mengeksekusi.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($programs as $program)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col hover:border-slate-300 transition-all">
                <div class="flex-1">
                    <div class="w-12 h-12 rounded-xl bg-primary-light flex items-center justify-center mb-4">
                        <i data-lucide="graduation-cap" class="w-6 h-6 text-primary"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 text-lg mb-1 line-clamp-2">{{ $program->nama }}</h3>
                    <p class="text-slate-500 text-sm mb-4">Tahun: {{ $program->tahun }} &bull; Kuota: {{ $program->kuota }}</p>
                    
                    <div class="bg-slate-50 rounded-xl p-4 flex items-center justify-between mb-6">
                        <span class="text-slate-600 font-medium text-sm">Total Pendaftar:</span>
                        <span class="font-extrabold text-xl text-slate-900">{{ number_format($program->pendaftarans_count) }}</span>
                    </div>
                </div>

                <a href="{{ route('super-admin.pembersihan-data.detail', $program->id) }}" class="btn btn-outline-primary w-full justify-center">
                    Lihat Daftar Pendaftar
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                </a>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-slate-500">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                </div>
                <p>Belum ada program beasiswa yang terdaftar.</p>
            </div>
        @endforelse
    </div>

</x-layouts.admin>
