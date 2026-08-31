<x-layouts.public title="Validasi Dokumen">
    <div class="max-w-2xl mx-auto pt-10 pb-20 px-4">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
            <div class="bg-emerald-600 p-8 text-white text-center">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                    <i data-lucide="check-circle" class="w-10 h-10 text-white"></i>
                </div>
                <h2 class="text-2xl font-bold mb-1">Dokumen Valid</h2>
                <p class="text-emerald-50 opacity-90">Dokumen resmi dikeluarkan oleh Pemerintah Kabupaten Blitar</p>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <p class="text-sm text-slate-500 mb-1">Nomor Registrasi</p>
                    <p class="font-bold text-slate-900 text-lg">{{ $pendaftaran->nomor_pendaftaran }}</p>
                </div>

                <div class="border-b border-slate-100 pb-4">
                    <p class="text-sm text-slate-500 mb-1">Nama Penerima</p>
                    <p class="font-bold text-slate-800">{{ $pendaftaran->nama_lengkap }}</p>
                </div>

                <div class="border-b border-slate-100 pb-4">
                    <p class="text-sm text-slate-500 mb-1">Program Beasiswa</p>
                    <p class="font-bold text-slate-800">{{ $pendaftaran->program->nama ?? $pendaftaran->jenis_beasiswa }}</p>
                </div>

                <div>
                    <p class="text-sm text-slate-500 mb-1">Tahun Anggaran</p>
                    <p class="font-bold text-slate-800">{{ $pendaftaran->periode->tahun ?? $pendaftaran->tahun }}</p>
                </div>
            </div>
            
            <div class="bg-slate-50 p-6 text-center border-t border-slate-100">
                <a href="{{ route('home') }}" class="btn btn-outline">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</x-layouts.public>
