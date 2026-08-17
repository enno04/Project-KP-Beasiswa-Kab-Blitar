<x-layouts.public :title="'Bukti Pendaftaran — ' . ($pendaftaran->identitas->nama_lengkap ?? 'Beasiswa')">
    {{-- Page Hero --}}
    <section class="bg-gradient-to-br from-slate-50 via-primary-light/30 to-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
            <p class="text-sm font-bold text-green-600 uppercase tracking-widest mb-2">✓ Berhasil</p>
            <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Tanda Bukti Pendaftaran</h1>
        </div>
    </section>

    <section class="py-12 min-h-[50vh]">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card" id="print-area">
                <div class="card-body space-y-8">
                    {{-- Header --}}
                    <div class="flex items-center gap-4 border-b border-slate-100 pb-6">
                        <div class="w-16 h-16 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-sm" style="background: linear-gradient(135deg, #2B5C92, #0C1446);">BM</div>
                        <div>
                            <h2 class="text-2xl font-extrabold text-primary-dark">Tanda Bukti Pendaftaran</h2>
                            <p class="font-semibold text-slate-700">Beasiswa Kabupaten Blitar Tahun {{ $pendaftaran->tahun }}</p>
                        </div>
                    </div>

                    {{-- Nomor & Waktu --}}
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 p-5 rounded-xl bg-primary-light border border-primary-light border-dashed">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest text-primary-dark mb-1">Nomor Registrasi</p>
                            <p class="text-2xl font-extrabold font-mono text-primary-dark">{{ $pendaftaran->nomor_pendaftaran }}</p>
                        </div>
                        <div class="sm:text-right">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-primary-dark mb-1">Waktu Pendaftaran</p>
                            <p class="font-semibold text-slate-900">{{ $pendaftaran->created_at->format('d F Y H:i:s') }}</p>
                        </div>
                    </div>

                    {{-- Info Beasiswa --}}
                    <div>
                        <h3 class="font-bold text-lg mb-4 flex items-center gap-2 border-b border-slate-100 pb-2 text-slate-800">
                            <i data-lucide="award" class="w-5 h-5 text-primary"></i> Informasi Beasiswa
                        </h3>
                        <table class="w-full text-sm">
                            <tr><td class="py-2.5 w-1/3 text-slate-400 font-medium">Program</td><td class="py-2.5 font-bold text-slate-900">{{ $pendaftaran->program->nama ?? '-' }}</td></tr>
                            <tr><td class="py-2.5 w-1/3 text-slate-400 font-medium">Jalur</td><td class="py-2.5 font-semibold text-slate-800">{{ $pendaftaran->jalur->nama ?? '-' }}</td></tr>
                            <tr><td class="py-2.5 w-1/3 text-slate-400 font-medium">Status</td><td class="py-2.5"><span class="badge {{ $pendaftaran->status_color }}">{{ $pendaftaran->status_label }}</span></td></tr>
                        </table>
                    </div>

                    {{-- Data Diri --}}
                    <div>
                        <h3 class="font-bold text-lg mb-4 flex items-center gap-2 border-b border-slate-100 pb-2 text-slate-800">
                            <i data-lucide="user" class="w-5 h-5 text-primary"></i> Data Diri
                        </h3>
                        <table class="w-full text-sm">
                            <tr><td class="py-2 w-1/3 text-slate-400 font-medium">NIK</td><td class="py-2 font-bold font-mono">{{ $pendaftaran->identitas->nik ?? '-' }}</td></tr>
                            <tr><td class="py-2 w-1/3 text-slate-400 font-medium">Nama Lengkap</td><td class="py-2 font-bold text-slate-900">{{ $pendaftaran->identitas->nama_lengkap ?? '-' }}</td></tr>
                            <tr><td class="py-2 w-1/3 text-slate-400 font-medium">TTL</td><td class="py-2 font-medium">{{ $pendaftaran->identitas->tempat_lahir ?? '-' }}, {{ $pendaftaran->identitas->tanggal_lahir ? $pendaftaran->identitas->tanggal_lahir->format('d-m-Y') : '-' }}</td></tr>
                            <tr><td class="py-2 w-1/3 text-slate-400 font-medium">Alamat</td><td class="py-2 font-medium">{{ $pendaftaran->identitas->alamat ?? '-' }}</td></tr>
                            <tr><td class="py-2 w-1/3 text-slate-400 font-medium">Wilayah</td><td class="py-2 font-medium">Desa {{ $pendaftaran->identitas->desa->nama_desa ?? '-' }}, Kec. {{ $pendaftaran->identitas->kecamatan->nama_kecamatan ?? '-' }}</td></tr>
                            <tr><td class="py-2 w-1/3 text-slate-400 font-medium">No. HP</td><td class="py-2 font-medium">{{ $pendaftaran->identitas->no_hp ?? '-' }}</td></tr>
                            @if($pendaftaran->identitas->asal_perguruan_tinggi)
                            <tr><td class="py-2 w-1/3 text-slate-400 font-medium">Perguruan Tinggi</td><td class="py-2 font-medium">{{ $pendaftaran->identitas->asal_perguruan_tinggi }}</td></tr>
                            @endif
                            @if($pendaftaran->identitas->program_studi)
                            <tr><td class="py-2 w-1/3 text-slate-400 font-medium">Program Studi</td><td class="py-2 font-medium">{{ $pendaftaran->identitas->program_studi }}</td></tr>
                            @endif
                        </table>
                    </div>

                    {{-- Dokumen --}}
                    <div>
                        <h3 class="font-bold text-lg mb-4 flex items-center gap-2 border-b border-slate-100 pb-2 text-slate-800">
                            <i data-lucide="files" class="w-5 h-5 text-primary"></i> Dokumen Terlampir
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($pendaftaran->uploadDokumens as $upload)
                                <div class="p-3 bg-slate-50 border border-slate-100 rounded-lg flex items-center gap-3">
                                    <i data-lucide="check-circle-2" class="w-4 h-4 text-green-500 shrink-0"></i>
                                    <span class="text-sm font-medium text-slate-700 truncate">{{ $upload->dokumen->nama ?? '-' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Warning --}}
                    <div class="alert alert-warning">
                        <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0"></i>
                        <div>
                            <p class="font-semibold">Simpan Tanda Bukti Ini!</p>
                            <p class="text-sm mt-0.5">Simpan atau cetak sebagai referensi. Cek status melalui menu <strong>Cek Status</strong> di halaman utama.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4 no-print">
                <button onclick="window.print()" class="btn btn-primary btn-lg justify-center">
                    <i data-lucide="printer" class="w-5 h-5"></i> Cetak / Simpan PDF
                </button>
                <a href="{{ route('home') }}" class="btn btn-outline btn-lg justify-center">
                    <i data-lucide="home" class="w-5 h-5"></i> Ke Beranda
                </a>
            </div>
        </div>
    </section>

    @push('scripts')
    <style>
        @media print {
            body { background-color: white !important; }
            nav, footer, .no-print { display: none !important; }
            #print-area { box-shadow: none !important; border: none !important; padding: 0 !important; }
        }
    </style>
    @endpush
</x-layouts.public>
< s c r i p t > l o c a l S t o r a g e . r e m o v e I t e m ( ' d r a f t P e n d a f t a r a n _ S D S S ' ) ; < / s c r i p t >  
 