<x-layouts.admin :title="'Proses Verifikasi Dokumen'">
    <x-page-header title="Proses Verifikasi" subtitle="Verifikasi keabsahan dokumen persyaratan dari pendaftar.">
        <x-slot:actions>
            <a href="{{ request('redirect_to', route('opd.verifikasi.index')) }}" class="btn btn-outline">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Info Pendaftar --}}
            <div class="card" x-data="{ open: true }">
                <div @click="open = !open" class="card-header font-bold cursor-pointer hover:bg-slate-50 transition-colors flex justify-between items-center">
                    <span class="flex items-center gap-2"><i data-lucide="user" class="w-4 h-4 text-slate-400"></i> Informasi Pendaftar</span>
                    <span class="transition-transform duration-300 inline-flex" :class="open ? 'rotate-180' : ''"><i data-lucide="chevron-down" class="w-4 h-4"></i></span>
                </div>
                <div x-show="open" x-transition.duration.300ms>
                <div class="card-body space-y-3 text-sm">
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nomor Pendaftaran</p>
                        <p class="font-bold text-primary-dark">{{ $upload->pendaftaran->nomor_pendaftaran }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Program Beasiswa</p>
                        <p class="font-medium">{{ $upload->pendaftaran->program->nama ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">NIK</p>
                        <p class="font-medium font-mono">{{ $upload->pendaftaran->identitas->nik ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nama Lengkap</p>
                        <p class="font-medium">{{ $upload->pendaftaran->identitas->nama_lengkap ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Alamat Lengkap</p>
                        <p class="font-medium">{{ $upload->pendaftaran->identitas->alamat_ktp ?? '-' }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Desa {{ $upload->pendaftaran->identitas->desa->nama_desa ?? '-' }}, 
                            Kec. {{ $upload->pendaftaran->identitas->kecamatan->nama_kecamatan ?? '-' }}
                        </p>
                    </div>
                </div>
                </div>
            </div>

                        {{-- Data Akademik --}}
            <div class="card" x-data="{ open: false }">
                <div @click="open = !open" class="card-header font-bold cursor-pointer hover:bg-slate-50 transition-colors flex justify-between items-center">
                    <span class="flex items-center gap-2"><i data-lucide="graduation-cap" class="w-4 h-4 text-slate-400"></i> Data Akademik</span>
                    <span class="transition-transform duration-300 inline-flex" :class="open ? 'rotate-180' : ''"><i data-lucide="chevron-down" class="w-4 h-4"></i></span>
                </div>
                <div x-show="open" x-transition.duration.300ms style="display: none;">
                <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="md:col-span-2">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Perguruan Tinggi</p>
                        <p class="font-medium">{{ $upload->pendaftaran->identitas->asal_perguruan_tinggi ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Program Studi</p>
                        <p class="font-medium">{{ $upload->pendaftaran->identitas->program_studi ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Semester</p>
                        <p class="font-medium">{{ $upload->pendaftaran->identitas->semester ?? '-' }}</p>
                    </div>
                </div>
                </div>
            </div>

            {{-- Data Orang Tua / Wali --}}
            @if($upload->pendaftaran->orangtua)
            <div class="card" x-data="{ open: false }">
                <div @click="open = !open" class="card-header font-bold cursor-pointer hover:bg-slate-50 transition-colors flex justify-between items-center">
                    <span class="flex items-center gap-2"><i data-lucide="users" class="w-4 h-4 text-slate-400"></i> Data Orang Tua / Wali</span>
                    <span class="transition-transform duration-300 inline-flex" :class="open ? 'rotate-180' : ''"><i data-lucide="chevron-down" class="w-4 h-4"></i></span>
                </div>
                <div x-show="open" x-transition.duration.300ms style="display: none;">
                <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    @foreach([
                        ['Ayah', $upload->pendaftaran->orangtua->nama_ayah, $upload->pendaftaran->orangtua->nik_ayah, $upload->pendaftaran->orangtua->no_hp_ayah, $upload->pendaftaran->orangtua->alamat_ayah],
                        ['Ibu', $upload->pendaftaran->orangtua->nama_ibu, $upload->pendaftaran->orangtua->nik_ibu, $upload->pendaftaran->orangtua->no_hp_ibu, $upload->pendaftaran->orangtua->alamat_ibu],
                    ] as [$role, $nama, $nik, $hp, $alamat])
                    <div>
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">{{ $role }}</span>
                        <div class="font-medium">{{ $nama ?? '-' }}</div>
                        <div class="text-xs text-slate-500 mt-1">NIK: {{ $nik ?? '-' }} &middot; HP: {{ $hp ?? '-' }}</div>
                        <div class="text-xs text-slate-500 mt-1">{{ $alamat ?? '-' }}</div>
                    </div>
                    @endforeach
                    @if($upload->pendaftaran->orangtua->nama_wali)
                    <div class="md:col-span-2 pt-4 border-t border-dashed border-slate-200">
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Wali</span>
                        <div class="font-medium">{{ $upload->pendaftaran->orangtua->nama_wali }}</div>
                        <div class="text-xs text-slate-500 mt-1">NIK: {{ $upload->pendaftaran->orangtua->nik_wali ?? '-' }} &middot; HP: {{ $upload->pendaftaran->orangtua->no_hp_wali ?? '-' }}</div>
                    </div>
                    @endif
                </div>
                </div>
            </div>
            @endif

                        {{-- Kriteria Penilaian & Ranking --}}
            @if($upload->pendaftaran->penilaians->isNotEmpty())
            <div class="card overflow-hidden" x-data="{ open: false }">
                <div @click="open = !open" class="card-header font-bold text-slate-700 bg-slate-50 cursor-pointer hover:bg-slate-100 transition-colors flex justify-between items-center">
                    <span class="flex items-center gap-2"><i data-lucide="calculator" class="w-4 h-4 text-slate-400"></i> Penilaian & Ranking</span>
                    <span class="transition-transform duration-300 inline-flex" :class="open ? 'rotate-180' : ''"><i data-lucide="chevron-down" class="w-4 h-4"></i></span>
                </div>
                <div x-show="open" x-transition.duration.300ms style="display: none;">
                <div class="card-body">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center p-4 bg-primary-light rounded-xl">
                            <div class="text-xs font-bold text-slate-500 uppercase">Total Nilai</div>
                            <div class="text-xl font-extrabold text-primary-dark">{{ number_format($upload->pendaftaran->total_nilai, 4) }}</div>
                        </div>
                        <div class="text-center p-4 bg-amber-50 rounded-xl">
                            <div class="text-xs font-bold text-slate-500 uppercase">Ranking Desa</div>
                            <div class="text-xl font-extrabold text-amber-700">#{{ $upload->pendaftaran->ranking ?? '-' }}</div>
                        </div>
                    </div>
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="py-2 text-slate-500 font-bold">Kriteria</th>
                                <th class="py-2 text-right text-slate-500 font-bold">Skor</th>
                                <th class="py-2 text-right text-slate-500 font-bold">Nilai Terbobot</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($upload->pendaftaran->penilaians as $penilaian)
                            <tr>
                                <td class="py-2 font-medium">{{ $penilaian->kriteria->nama ?? '-' }}</td>
                                <td class="py-2 text-right">{{ number_format($penilaian->skor, 2) }}</td>
                                <td class="py-2 text-right font-semibold text-primary-dark">{{ number_format($penilaian->nilai_terbobot, 4) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
            @else
            <div class="card overflow-hidden" x-data="{ open: false }">
                <div @click="open = !open" class="card-header font-bold text-slate-700 bg-slate-50 cursor-pointer hover:bg-slate-100 transition-colors flex justify-between items-center">
                    <span class="flex items-center gap-2"><i data-lucide="list-checks" class="w-4 h-4 text-slate-400"></i> Input Jawaban Kriteria</span>
                    <span class="transition-transform duration-300 inline-flex" :class="open ? 'rotate-180' : ''"><i data-lucide="chevron-down" class="w-4 h-4"></i></span>
                </div>
                <div x-show="open" x-transition.duration.300ms style="display: none;">
                <table class="w-full text-left text-sm border-collapse">
                    <tbody class="divide-y divide-slate-100">
                        @forelse($upload->pendaftaran->jawabanKriterias as $jawaban)
                        <tr>
                            <td class="py-3 px-4 w-1/2">
                                <div class="font-semibold text-slate-700">{{ $jawaban->kriteria->nama }}</div>
                                <div class="text-xs text-slate-400">{{ $jawaban->kriteria->kelompokKriteria->nama ?? '' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                @if($jawaban->kriteria->tipe_input === 'pilihan')
                                    <span class="font-medium">{{ $jawaban->pilihanKriteria->label ?? '-' }}</span>
                                @else
                                    <span class="font-medium font-mono text-primary">{{ $jawaban->nilai_input ?? '-' }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-slate-400 py-4 text-sm italic">Belum ada data nilai kriteria.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
            @endif

            {{-- Informasi Tambahan (Custom Fields) --}}
            @php
                $validCustomFields = $upload->pendaftaran->customFieldAnswers->filter(fn($answer) => $answer->customField !== null);
            @endphp
            @if($validCustomFields->count() > 0)
            <div class="card" x-data="{ open: false }">
                <div @click="open = !open" class="card-header font-bold text-slate-700 bg-slate-50 cursor-pointer hover:bg-slate-100 transition-colors flex justify-between items-center">
                    <span class="flex items-center gap-2"><i data-lucide="file-text" class="w-4 h-4 text-slate-400"></i> Informasi Tambahan</span>
                    <span class="transition-transform duration-300 inline-flex" :class="open ? 'rotate-180' : ''"><i data-lucide="chevron-down" class="w-4 h-4"></i></span>
                </div>
                <div x-show="open" x-transition.duration.300ms style="display: none;">
                <div class="card-body space-y-3 text-sm">
                    @foreach($validCustomFields as $answer)
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">{{ $answer->customField->nama_field }} <span class="lowercase capitalize text-slate-300">({{ str_replace('_', ' ', $answer->customField->penempatan) }})</span></p>
                        <p class="font-medium">{{ $answer->jawaban ?? '-' }}</p>
                    </div>
                    @endforeach
                </div>
                </div>
            </div>
            @endif

            {{-- Riwayat --}}
            @if($upload->verifikasis->count() > 0)
            <div class="card" x-data="{ open: false }">
                <div @click="open = !open" class="card-header font-bold cursor-pointer hover:bg-slate-50 transition-colors flex justify-between items-center">
                    <span class="flex items-center gap-2"><i data-lucide="history" class="w-4 h-4 text-slate-400"></i> Riwayat Pemeriksaan</span>
                    <span class="transition-transform duration-300 inline-flex" :class="open ? 'rotate-180' : ''"><i data-lucide="chevron-down" class="w-4 h-4"></i></span>
                </div>
                <div x-show="open" x-transition.duration.300ms style="display: none;">
                <div class="card-body space-y-4">
                    @foreach($upload->verifikasis as $v)
                    <div class="border-l-2 pl-3 ml-1" style="border-color: {{ $v->hasil === 'valid' ? 'var(--color-success)' : 'var(--color-danger)' }};">
                        <p class="text-xs text-slate-400">{{ $v->created_at->format('d M Y H:i') }} oleh {{ $v->user->nama ?? 'Sistem' }}</p>
                        <p class="font-bold text-sm" style="color: {{ $v->hasil === 'valid' ? 'var(--color-success)' : 'var(--color-danger)' }};">
                            {{ strtoupper(str_replace('_', ' ', $v->hasil)) }}
                        </p>
                        @if($v->catatan)
                            <p class="text-sm bg-slate-50 p-2 rounded-lg border border-slate-100 mt-1 text-slate-600">{{ $v->catatan }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
                </div>
            </div>
            @endif

            {{-- Form Verifikasi --}}
            @php
                $isPendaftaranActive = $upload->pendaftaran->isOpdActive();
                $isPending = $isPendaftaranActive && in_array($upload->status, ['belum_diverifikasi', 'upload_ulang']);
            @endphp
            <div class="card border-primary-light shadow-lg" x-data="{ openForm: {{ $isPending ? 'true' : 'false' }} }">
                <div class="card-header font-bold flex justify-between items-center">
                    <span class="flex items-center gap-2"><i data-lucide="check-square" class="w-4 h-4 text-slate-400"></i> Keputusan Verifikasi</span>
                    <button type="button" x-show="!openForm" @click="openForm = true" class="btn btn-xs btn-outline" x-bind:disabled="!{{ $isPendaftaranActive ? 'true' : 'false' }}">
                        Ubah Keputusan
                    </button>
                </div>
                
                <div class="card-body py-8 text-center" x-show="!openForm" {!! $isPending ? 'style="display: none;"' : '' !!}>
                    @if(!$isPendaftaranActive)
                        <i data-lucide="lock" class="w-12 h-12 mx-auto mb-3 text-slate-400 opacity-60"></i>
                        <p class="font-bold mb-1">Verifikasi Terkunci</p>
                        <p class="text-sm text-slate-500">Pendaftar ini sudah berada di tahap selanjutnya atau telah ditetapkan. Tidak dapat menerima aksi verifikasi lagi.</p>
                    @else
                        <i data-lucide="{{ $upload->status === 'valid' ? 'check-circle-2' : 'x-circle' }}" class="w-12 h-12 mx-auto mb-3 {{ $upload->status === 'valid' ? 'text-green-500' : 'text-red-500' }} opacity-60"></i>
                        <p class="font-bold mb-1">Dokumen Telah Diproses</p>
                        <p class="text-sm text-slate-500">Status Saat Ini: <strong class="{{ $upload->status === 'valid' ? 'text-green-600' : 'text-red-600' }}">{{ strtoupper(str_replace('_', ' ', $upload->status)) }}</strong></p>
                    @endif
                </div>

                @if($isPendaftaranActive)
                <div class="card-body" x-show="openForm" {!! !$isPending ? 'style="display: none;"' : '' !!}>
                    <form action="{{ route('opd.verifikasi.store', $upload->id) }}" method="POST" x-data="{ hasil: '{{ !$isPending ? $upload->status : '' }}' }">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
                        <div class="space-y-3 mb-4">
                            <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-colors" :class="hasil === 'valid' ? 'bg-green-50 border-green-500' : 'hover:bg-slate-50 border-slate-200'">
                                <input type="radio" name="hasil" value="valid" x-model="hasil" class="w-4 h-4 text-green-600" required>
                                <div class="flex items-center gap-2">
                                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                                    <span class="font-medium text-green-700">Dokumen Valid</span>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-colors" :class="hasil === 'tidak_valid' ? 'bg-red-50 border-red-500' : 'hover:bg-slate-50 border-slate-200'">
                                <input type="radio" name="hasil" value="tidak_valid" x-model="hasil" class="w-4 h-4 text-red-600" required>
                                <div class="flex items-center gap-2">
                                    <i data-lucide="x-circle" class="w-5 h-5 text-red-600"></i>
                                    <span class="font-medium text-red-700">Dokumen Tidak Valid</span>
                                </div>
                            </label>
                        </div>

                        <div x-show="hasil === 'tidak_valid'" x-transition class="mb-4">
                            <label class="form-label text-red-700">Alasan Penolakan <span class="required">*</span></label>
                            <textarea name="catatan" rows="3" class="form-input border-red-200 focus:border-red-500 @error('catatan') border-red-500 @enderror" placeholder="Jelaskan alasan penolakan..." x-bind:required="hasil === 'tidak_valid'">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                            @else
                                <p class="text-xs text-slate-400 mt-1">Catatan ini akan dibaca oleh pendaftar.</p>
                            @enderror
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="btn btn-primary flex-1 justify-center">
                                <i data-lucide="save" class="w-4 h-4"></i> Simpan Keputusan
                            </button>
                            <button type="button" @click="openForm = false" x-show="!{{ $isPending ? 'true' : 'false' }}" class="btn btn-outline">Batal</button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Preview --}}
        <div class="lg:col-span-2">
            <div class="card flex flex-col" style="min-height: 600px;">
                <div class="card-header flex items-center justify-between">
                    <span class="font-bold">{{ $upload->dokumen->nama ?? '-' }}</span>
                    <a href="{{ Storage::url($upload->file_path) }}" target="_blank" class="btn btn-xs btn-outline">
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i> Tab Baru
                    </a>
                </div>
                <div class="flex-1 bg-slate-100 overflow-hidden relative">
                    @php
                        $ext = pathinfo($upload->file_path, PATHINFO_EXTENSION);
                        $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png']);
                    @endphp

                    @if($isImage)
                        <div class="absolute inset-0 overflow-auto flex items-center justify-center p-4">
                            <img src="{{ Storage::url($upload->file_path) }}" alt="Preview" class="max-w-full max-h-full object-contain shadow-sm border border-slate-200 rounded-lg">
                        </div>
                    @else
                        <iframe src="{{ Storage::url($upload->file_path) }}" class="absolute inset-0 w-full h-full border-0"></iframe>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
