<x-layouts.admin :title="'Proses Verifikasi Dokumen'">
    <x-page-header title="Proses Verifikasi" subtitle="Verifikasi keabsahan dokumen persyaratan dari pendaftar.">
        <x-slot:actions>
            <a href="{{ route('opd.verifikasi.index') }}" class="btn btn-outline">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kolom Kiri --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Info Pendaftar --}}
            <div class="card">
                <div class="card-header font-bold">Informasi Pendaftar</div>
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
                </div>
            </div>

            {{-- Riwayat --}}
            @if($upload->verifikasis->count() > 0)
            <div class="card">
                <div class="card-header font-bold">Riwayat Pemeriksaan</div>
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
            @endif

            {{-- Form Verifikasi --}}
            @if(in_array($upload->status, ['belum_diverifikasi', 'upload_ulang']))
            <div class="card border-primary-light shadow-lg">
                <div class="card-header font-bold">Keputusan Verifikasi</div>
                <div class="card-body">
                    <form action="{{ route('opd.verifikasi.store', $upload->id) }}" method="POST" x-data="{ hasil: '' }">
                        @csrf
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
                            <textarea name="catatan" rows="3" class="form-input border-red-200 focus:border-red-500" placeholder="Jelaskan alasan penolakan..."></textarea>
                            <p class="text-xs text-slate-400 mt-1">Catatan ini akan dibaca oleh pendaftar.</p>
                        </div>

                        <button type="submit" class="btn btn-primary w-full justify-center">
                            <i data-lucide="save" class="w-4 h-4"></i> Simpan Keputusan
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="card text-center">
                <div class="card-body py-8">
                    <i data-lucide="check-circle-2" class="w-12 h-12 mx-auto mb-3 text-green-500 opacity-60"></i>
                    <p class="font-bold mb-1">Dokumen Telah Diproses</p>
                    <p class="text-sm text-slate-500">Status: <strong>{{ strtoupper(str_replace('_', ' ', $upload->status)) }}</strong></p>
                </div>
            </div>
            @endif
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
                        <iframe src="{{ Storage::url($upload->file_path) }}" class="w-full h-full border-0"></iframe>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
