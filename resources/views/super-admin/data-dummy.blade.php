<x-layouts.admin :title="'Generator Data Dummy Pendaftar'">
    <x-page-header title="Data Dummy Generator" subtitle="Fitur testing untuk membuat data pendaftar fiktif secara instan." />

    @if(session('success'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-xl bg-green-50 flex items-center gap-3 border border-green-200">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 mb-6 text-sm text-red-800 rounded-xl bg-red-50 flex items-center gap-3 border border-red-200">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 mb-8 flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
            <i data-lucide="flask-conical" class="w-6 h-6 text-blue-600"></i>
        </div>
        <div>
            <h3 class="text-blue-900 font-bold text-lg mb-1">Mode Testing (Eksperimental)</h3>
            <p class="text-blue-700 text-sm leading-relaxed">
                Fitur ini akan men-generate data pendaftar palsu dengan status <strong>Diajukan (Menunggu Verifikasi)</strong>.
                Setiap nama pendaftar akan diberi label awalan <code>[DUMMY]</code> agar Anda mudah mengenalinya dan bisa dihapus kembali melalui fitur Pembersihan Data.
            </p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 max-w-2xl">
        <form action="{{ route('super-admin.data-dummy.generate') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menginjeksi data dummy ke dalam database?')">
            @csrf
            
            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Program Beasiswa Target</label>
                <select name="program_id" class="form-select w-full" required>
                    <option value="">-- Pilih Program --</option>
                    @foreach($programs as $prog)
                        <option value="{{ $prog->id }}">{{ $prog->nama }} ({{ $prog->tahun }})</option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-500 mt-2">Data akan diacak dan dimasukkan ke salah satu jalur yang tersedia pada program ini.</p>
            </div>

            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Filter Kecamatan (Opsional)</label>
                    <select id="kecamatan_select" class="form-select w-full">
                        <option value="">-- Acak Semua Kecamatan --</option>
                        @foreach($kecamatans as $kec)
                            <option value="{{ $kec->id }}" data-desas="{{ json_encode($kec->desa) }}">{{ $kec->nama_kecamatan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Filter Desa (Opsional)</label>
                    <select id="desa_select" name="desa_id" class="form-select w-full" disabled>
                        <option value="">-- Acak Semua Desa --</option>
                    </select>
                </div>
                <div class="col-span-full">
                    <p class="text-xs text-slate-500">Jika dikosongkan, data pendaftar akan disebar secara acak ke seluruh desa.</p>
                </div>
            </div>

            <div class="mb-8">
                <label class="block text-sm font-bold text-slate-700 mb-2">Jumlah Data Dummy</label>
                <div class="flex items-center gap-3">
                    <input type="number" name="jumlah" class="form-input w-32 text-center text-lg font-bold" min="1" max="100" value="5" required>
                    <span class="text-slate-500 text-sm font-medium">Orang / Pendaftar</span>
                </div>
                <p class="text-xs text-slate-500 mt-2">Maksimal 100 data dalam satu kali eksekusi agar server tidak kelebihan beban.</p>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button type="submit" class="btn bg-blue-600 text-white hover:bg-blue-700 shadow-md shadow-blue-500/20">
                    <i data-lucide="zap" class="w-4 h-4 mr-1"></i> Mulai Generate
                </button>
            </div>
        </form>
    </div>

    <!-- Mode Fast-Forward -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 max-w-2xl mt-6 border-l-4 border-l-orange-500">
        <h3 class="text-slate-800 font-bold text-lg mb-2">Bypass Verifikasi OPD (Auto-Verifikasi)</h3>
        <p class="text-sm text-slate-600 mb-6 leading-relaxed">
            Daripada harus *login* sebagai Admin OPD satu per satu untuk memvalidasi dokumen pendaftar *dummy*, Anda bisa mengeklik tombol di bawah ini. Sistem akan otomatis menandai <strong>semua dokumen yang berstatus "Belum Diverifikasi" menjadi "Valid"</strong>, sehingga pendaftar bisa langsung dilanjutkan ke tahap Seleksi Desa/Kabupaten.
        </p>
        <form action="{{ route('super-admin.data-dummy.bypass-opd') }}" method="GET">
            <button type="submit" class="btn bg-orange-600 text-white hover:bg-orange-700 shadow-md shadow-orange-500/20">
                <i data-lucide="fast-forward" class="w-4 h-4 mr-2"></i> Buka Daftar Bypass OPD
            </button>
        </form>
    </div>

    <!-- Mode Trigger SLA -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 max-w-2xl mt-6 border-l-4 border-l-purple-500">
        <h3 class="text-slate-800 font-bold text-lg mb-2">Trigger Auto-Verify SLA 3 Hari</h3>
        <p class="text-sm text-slate-600 mb-6 leading-relaxed">
            Tombol ini berfungsi untuk mengeksekusi sistem <strong>Cron Job SLA (Auto-Verifikasi)</strong> secara manual tanpa harus menunggu pukul 00:00 tengah malam. Sangat berguna ketika Anda menguji fitur dengan cara memundurkan waktu pengajuan secara manual lewat database.
        </p>
        <form action="{{ route('super-admin.trigger-auto-verify') }}" method="POST" onsubmit="return confirm('Jalankan Auto-Verify SLA sekarang?')">
            @csrf
            <button type="submit" class="btn bg-purple-600 text-white hover:bg-purple-700 shadow-md shadow-purple-500/20">
                <i data-lucide="clock" class="w-4 h-4 mr-2"></i> Jalankan SLA Sekarang
            </button>
        </form>
    </div>

    @push('scripts')
    <script>
        document.getElementById('kecamatan_select').addEventListener('change', function() {
            const desaSelect = document.getElementById('desa_select');
            desaSelect.innerHTML = '<option value="">-- Acak Semua Desa --</option>';
            
            if (this.value) {
                desaSelect.disabled = false;
                const selectedOption = this.options[this.selectedIndex];
                const desas = JSON.parse(selectedOption.getAttribute('data-desas'));
                
                desas.forEach(desa => {
                    const option = document.createElement('option');
                    option.value = desa.id;
                    option.textContent = desa.nama_desa;
                    desaSelect.appendChild(option);
                });
            } else {
                desaSelect.disabled = true;
            }
        });
    </script>
    @endpush
</x-layouts.admin>
