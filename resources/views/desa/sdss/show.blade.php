<x-admin-layout>
    <x-slot name="title">Proses Pendaftar SDSS</x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Proses SDSS</h1>
            <p style="color: var(--color-text-secondary);">Verifikasi dokumen dan input penilaian kriteria pendaftar SDSS.</p>
        </div>
        <a href="{{ route('desa.sdss.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border text-sm font-semibold hover:bg-gray-50 transition-all" style="border-color: var(--color-border); color: var(--color-text-secondary);">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri: Status & Form Verifikasi/Penilaian --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-2xl border p-6 bg-white" style="border-color: var(--color-border); box-shadow: 0 2px 8px rgba(0,0,0,.05);">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 rounded-full mx-auto flex items-center justify-center font-bold text-xl text-white mb-3" style="background-color: var(--color-primary);">
                        {{ strtoupper(substr($pendaftaran->nama_lengkap, 0, 2)) }}
                    </div>
                    <h2 class="font-bold text-lg leading-tight">{{ $pendaftaran->nama_lengkap }}</h2>
                    <p class="text-sm" style="color: var(--color-text-secondary);">{{ $pendaftaran->nomor_pendaftaran }}</p>
                </div>

                <div class="space-y-4 pt-4 border-t mb-6" style="border-color: var(--color-border);">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 mb-1">Status Saat Ini</p>
                        @php
                            $badgeColors = ['green' => ['#198754', 'rgba(25,135,84,.1)'], 'red' => ['#DC3545', 'rgba(220,53,69,.1)'], 'yellow' => ['#FD7E14', 'rgba(253,126,20,.1)'], 'blue' => ['#0DCAF0', 'rgba(13,202,240,.1)'], 'gray' => ['#6C757D', 'rgba(108,117,125,.1)']];
                            $bc = $badgeColors[$pendaftaran->status_color] ?? $badgeColors['gray'];
                        @endphp
                        <span class="inline-block px-3 py-1.5 rounded-full text-sm font-semibold" style="background-color: {{ $bc[1] }}; color: {{ $bc[0] }};">
                            {{ $pendaftaran->status_label }}
                        </span>
                    </div>

                    @if($pendaftaran->total_nilai)
                    <div>
                        <p class="text-xs font-semibold text-gray-500 mb-1">Total Nilai</p>
                        <p class="font-bold text-2xl" style="color: var(--color-primary);">{{ number_format($pendaftaran->total_nilai, 4) }}</p>
                    </div>
                    @endif
                </div>

                {{-- Form Verifikasi & Penilaian --}}
                @if(in_array($pendaftaran->status, ['menunggu_verifikasi', 'sudah_diverifikasi']))
                    <div class="pt-4 border-t" style="border-color: var(--color-border);" x-data="{ status: '' }">
                        <h3 class="font-bold text-lg mb-4">Proses Berkas</h3>
                        <form action="{{ route('desa.sdss.verifikasi.store', $pendaftaran->id) }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="block text-sm font-semibold mb-2">Keputusan Verifikasi Akhir</label>
                                <select name="status" x-model="status" class="w-full px-4 py-2.5 rounded-xl border text-sm focus:ring-2 outline-none" style="border-color: var(--color-border); focus:ring-color: var(--color-primary);" required>
                                    <option value="">-- Pilih Keputusan --</option>
                                    <option value="sudah_diverifikasi">Berkas Lengkap & Valid</option>
                                    <option value="perlu_perbaikan">Perlu Perbaikan Dokumen</option>
                                    <option value="ditolak">Tolak (Gugur)</option>
                                </select>
                            </div>

                            <div x-show="status === 'perlu_perbaikan' || status === 'ditolak'" x-transition class="mb-4">
                                <label class="block text-sm font-semibold mb-2">Catatan / Alasan</label>
                                <textarea name="catatan" rows="3" class="w-full px-4 py-2 rounded-xl border text-sm focus:ring-2 outline-none" style="border-color: var(--color-border); focus:ring-color: var(--color-primary);"></textarea>
                            </div>

                            {{-- Input Nilai Kriteria jika berkas valid --}}
                            <div x-show="status === 'sudah_diverifikasi'" x-transition class="mb-6 space-y-4">
                                <div class="p-3 bg-primary-light border border-primary-light rounded-xl text-xs text-primary-dark mb-2">
                                    <i data-lucide="info" class="w-4 h-4 inline mr-1"></i> Silakan masukkan nilai untuk kriteria berikut. Nilai ini akan digunakan dalam perhitungan skor (Weighted Score) untuk menentukan penerima dari desa Anda.
                                </div>
                                
                                @foreach($pendaftaran->kategori->kriteria as $krit)
                                    <div>
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">{{ $krit->nama_kriteria }} <span class="text-gray-500 font-normal">({{ $krit->bobot }}%)</span></label>
                                        @if($krit->tipe_nilai === 'pilihan')
                                            <select name="nilai_kriteria[{{ $krit->id }}]" class="w-full px-3 py-2 rounded-lg border text-sm focus:ring-1 outline-none" style="border-color: var(--color-border); focus:ring-color: var(--color-primary);" :required="status === 'sudah_diverifikasi'">
                                                <option value="">-- Pilih --</option>
                                                @foreach($krit->pilihan as $pil)
                                                    <option value="{{ $pil->nilai }}">{{ $pil->label_pilihan }} (Nilai: {{ $pil->nilai }})</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="number" step="0.01" name="nilai_kriteria[{{ $krit->id }}]" class="w-full px-3 py-2 rounded-lg border text-sm focus:ring-1 outline-none" placeholder="Masukkan angka" style="border-color: var(--color-border); focus:ring-color: var(--color-primary);" :required="status === 'sudah_diverifikasi'">
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-white font-semibold text-sm transition-all hover:opacity-90 mt-2" style="background-color: var(--color-primary);">
                                <i data-lucide="save" class="w-4 h-4"></i> Simpan & Proses
                            </button>
                        </form>
                    </div>
                @else
                    <div class="pt-4 border-t text-center" style="border-color: var(--color-border);">
                        <i data-lucide="check-circle" class="w-10 h-10 mx-auto text-green-500 mb-2 opacity-50"></i>
                        <p class="font-bold text-gray-700 mb-1">Telah Diproses</p>
                        <p class="text-sm text-gray-500">Pendaftar ini telah Anda proses dan nilai.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Data Lengkap dengan Tabs (Reusable structure) --}}
        <div class="lg:col-span-2">
            <div class="rounded-2xl border bg-white" style="border-color: var(--color-border); box-shadow: 0 2px 8px rgba(0,0,0,.05);" x-data="{ tab: 'biodata' }">
                {{-- Tab Nav --}}
                <div class="flex overflow-x-auto border-b" style="border-color: var(--color-border);">
                    <button @click="tab = 'biodata'" class="px-6 py-4 text-sm font-semibold whitespace-nowrap border-b-2 transition-colors" :class="tab === 'biodata' ? 'text-primary border-primary' : 'text-gray-500 border-transparent hover:text-gray-700'">Biodata</button>
                    <button @click="tab = 'ortu'" class="px-6 py-4 text-sm font-semibold whitespace-nowrap border-b-2 transition-colors" :class="tab === 'ortu' ? 'text-primary border-primary' : 'text-gray-500 border-transparent hover:text-gray-700'">Orang Tua</button>
                    <button @click="tab = 'akademik'" class="px-6 py-4 text-sm font-semibold whitespace-nowrap border-b-2 transition-colors" :class="tab === 'akademik' ? 'text-primary border-primary' : 'text-gray-500 border-transparent hover:text-gray-700'">Akademik & Ekonomi</button>
                    <button @click="tab = 'dokumen'" class="px-6 py-4 text-sm font-semibold whitespace-nowrap border-b-2 transition-colors" :class="tab === 'dokumen' ? 'text-primary border-primary' : 'text-gray-500 border-transparent hover:text-gray-700'">Dokumen</button>
                    @if($pendaftaran->penilaian->count() > 0)
                        <button @click="tab = 'penilaian'" class="px-6 py-4 text-sm font-semibold whitespace-nowrap border-b-2 transition-colors" :class="tab === 'penilaian' ? 'text-primary border-primary' : 'text-gray-500 border-transparent hover:text-gray-700'">Detail Penilaian</button>
                    @endif
                </div>

                <div class="p-6">
                    {{-- Tab 1: Biodata --}}
                    <div x-show="tab === 'biodata'" class="space-y-4">
                        <table class="w-full text-sm">
                            <tbody class="divide-y" style="divide-color: var(--color-border);">
                                <tr><td class="py-3 w-1/3 text-gray-500">NIK</td><td class="py-3 font-medium">{{ $pendaftaran->nik }}</td></tr>
                                <tr><td class="py-3 text-gray-500">Tempat, Tanggal Lahir</td><td class="py-3 font-medium">{{ $pendaftaran->tempat_lahir }}, {{ $pendaftaran->tanggal_lahir->format('d-m-Y') }}</td></tr>
                                <tr><td class="py-3 text-gray-500">Jenis Kelamin</td><td class="py-3 font-medium">{{ $pendaftaran->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                                <tr><td class="py-3 text-gray-500">Alamat Lengkap</td><td class="py-3 font-medium">{{ $pendaftaran->alamat }}</td></tr>
                                <tr><td class="py-3 text-gray-500">Wilayah</td><td class="py-3 font-medium">Desa {{ $pendaftaran->desa->nama_desa ?? '-' }}, Kec. {{ $pendaftaran->kecamatan->nama_kecamatan ?? '-' }}</td></tr>
                                <tr><td class="py-3 text-gray-500">Nomor HP/WA</td><td class="py-3 font-medium">{{ $pendaftaran->no_hp }}</td></tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Tab 2: Orang Tua --}}
                    <div x-show="tab === 'ortu'" style="display: none;" class="space-y-6">
                        <div>
                            <h3 class="font-bold mb-3 border-b pb-2">Data Ayah & Ibu</h3>
                            <table class="w-full text-sm">
                                <tbody class="divide-y" style="divide-color: var(--color-border);">
                                    <tr><td class="py-2 w-1/3 text-gray-500">Nama Ayah</td><td class="py-2 font-medium">{{ $pendaftaran->nama_ayah }}</td></tr>
                                    <tr><td class="py-2 text-gray-500">Pekerjaan Ayah</td><td class="py-2 font-medium">{{ $pendaftaran->pekerjaan_ayah ?? '-' }}</td></tr>
                                    <tr><td class="py-2 text-gray-500">Penghasilan Ayah</td><td class="py-2 font-medium">Rp {{ number_format($pendaftaran->penghasilan_ayah, 0, ',', '.') }}</td></tr>
                                    <tr><td class="py-2 text-gray-500">Nama Ibu</td><td class="py-2 font-medium">{{ $pendaftaran->nama_ibu }}</td></tr>
                                    <tr><td class="py-2 text-gray-500">Pekerjaan Ibu</td><td class="py-2 font-medium">{{ $pendaftaran->pekerjaan_ibu ?? '-' }}</td></tr>
                                    <tr><td class="py-2 text-gray-500">Penghasilan Ibu</td><td class="py-2 font-medium">Rp {{ number_format($pendaftaran->penghasilan_ibu, 0, ',', '.') }}</td></tr>
                                    <tr><td class="py-2 text-gray-500 font-bold">Total Tanggungan</td><td class="py-2 font-bold">{{ $pendaftaran->jumlah_tanggungan ?? 0 }} Anak</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Tab 3: Akademik --}}
                    <div x-show="tab === 'akademik'" style="display: none;" class="space-y-6">
                        <table class="w-full text-sm">
                            <tbody class="divide-y" style="divide-color: var(--color-border);">
                                @if($pendaftaran->kategori->kode === 'sdss')
                                    <tr><td class="py-3 w-1/3 text-gray-500">Nama Sekolah Asal</td><td class="py-3 font-medium">{{ $pendaftaran->nama_sekolah ?? '-' }}</td></tr>
                                @endif
                                <tr><td class="py-3 w-1/3 text-gray-500">Perguruan Tinggi</td><td class="py-3 font-medium">{{ $pendaftaran->asal_perguruan_tinggi ?? '-' }}</td></tr>
                                <tr><td class="py-3 text-gray-500">Program Studi</td><td class="py-3 font-medium">{{ $pendaftaran->program_studi ?? '-' }}</td></tr>
                            </tbody>
                        </table>
                        
                        @if(in_array($pendaftaran->kategori->kode, ['bbp_kurang_mampu', 'sdss']))
                        <table class="w-full text-sm mt-4 border-t">
                            <tbody>
                                <tr><td class="py-3 w-1/3 text-gray-500">Desil DTSEN</td><td class="py-3 font-medium">{{ $pendaftaran->dtsen_desil ?? 'Tidak Terdaftar' }}</td></tr>
                            </tbody>
                        </table>
                        @endif
                    </div>

                    {{-- Tab 4: Dokumen --}}
                    <div x-show="tab === 'dokumen'" style="display: none;" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($pendaftaran->dokumen as $dok)
                            <div class="border rounded-xl p-4 flex flex-col justify-between bg-gray-50">
                                <div>
                                    <h4 class="font-semibold text-sm mb-1">{{ $dok->jenisDokumen->nama_dokumen }}</h4>
                                    <p class="text-xs text-gray-500 mb-2">{{ $dok->nama_file }}</p>
                                </div>
                                <div class="flex items-center justify-between mt-2 pt-3 border-t border-gray-200">
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full border border-gray-200 bg-white">
                                        {{ ucwords(str_replace('_', ' ', $dok->status)) }}
                                    </span>
                                    <a href="{{ Storage::url($dok->file_path) }}" target="_blank" class="text-xs font-semibold text-primary hover:underline flex items-center gap-1">
                                        Lihat File <i data-lucide="external-link" class="w-3 h-3"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tab 5: Penilaian (Jika Ada) --}}
                    @if($pendaftaran->penilaian->count() > 0)
                    <div x-show="tab === 'penilaian'" style="display: none;" class="space-y-4">
                        <table class="w-full text-sm border">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-4 py-2 font-semibold text-left">Kriteria</th>
                                    <th class="px-4 py-2 font-semibold text-right">Nilai Asli</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($pendaftaran->penilaian as $nilai)
                                <tr>
                                    <td class="px-4 py-3">{{ $nilai->kriteria->nama_kriteria }}</td>
                                    <td class="px-4 py-3 text-right font-semibold">{{ $nilai->nilai_asli ?? $nilai->nilai_akhir }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @endpush
</x-admin-layout>
