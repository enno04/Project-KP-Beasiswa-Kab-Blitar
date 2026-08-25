<x-layouts.public :title="'404 Halaman Tidak Ditemukan — Beasiswa Blitar Mengabdi'">
    <style>
        @keyframes slow-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }
        .animate-float {
            animation: slow-float 4s ease-in-out infinite;
        }
    </style>
    <div class="min-h-[70vh] flex items-center justify-center bg-cover bg-center md:bg-fixed" style="background-image: url('{{ asset('images/background.jpeg') }}');">
        <div class="max-w-3xl mx-auto px-4 py-16 text-center animate-fade-in flex flex-col items-center">
            
            <div class="relative w-full max-w-sm mx-auto mb-10 flex items-center justify-center">
                <!-- Teks 404 Latar Belakang -->
                <h1 class="text-[8rem] sm:text-[12rem] font-extrabold text-transparent bg-clip-text bg-gradient-to-br from-primary-light to-primary opacity-10 leading-none tracking-tighter select-none" style="line-height: 1;">404</h1>
                
                <!-- Floating Icon di Tengah -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="animate-float">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 bg-white rounded-full flex items-center justify-center" style="box-shadow: 0 10px 40px -10px rgba(43, 92, 146, 0.4);">
                            <!-- Menggunakan style statis agar icon Lucide pasti proporsional -->
                            <i data-lucide="map-pin-off" class="text-primary" style="width: 36px; height: 36px;"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-4">Ups! Tersesat?</h2>
            <p class="text-slate-600 text-lg mb-10 max-w-lg mx-auto leading-relaxed">
                Halaman yang Anda cari tidak dapat ditemukan. Mungkin URL salah, atau halaman telah dipindahkan.
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ url('/') }}" class="btn btn-primary px-8 py-3.5 rounded-xl font-bold text-sm shadow-xl shadow-primary/20 hover:shadow-primary/40 hover:-translate-y-1 transition-all flex items-center gap-2">
                    <i data-lucide="home" class="w-5 h-5"></i> Ke Beranda
                </a>
                <button onclick="window.history.back()" class="btn btn-outline px-8 py-3.5 rounded-xl font-bold text-sm text-slate-700 border-2 border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition-all flex items-center gap-2">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i> Kembali
                </button>
            </div>
        </div>
    </div>
</x-layouts.public>
