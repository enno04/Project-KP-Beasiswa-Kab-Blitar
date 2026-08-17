<x-layouts.admin :title="'Daftar Verifikasi Dokumen'">
    <x-page-header title="Verifikasi Dokumen" subtitle="Daftar dokumen pendaftar yang menunggu verifikasi dari instansi OPD Anda." />

    {{-- Filter --}}
    <div class="card mb-6">
        <div class="card-body py-4">
            <form action="{{ route('opd.verifikasi.index') }}" method="GET" class="flex gap-3 items-end">
                <div class="w-full sm:w-64">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="belum_diverifikasi" {{ request('status') === 'belum_diverifikasi' ? 'selected' : '' }}>Belum Diverifikasi</option>
                        <option value="valid" {{ request('status') === 'valid' ? 'selected' : '' }}>Valid</option>
                        <option value="tidak_valid" {{ request('status') === 'tidak_valid' ? 'selected' : '' }}>Tidak Valid</option>
                    </select>
                </div>
                @if(request('status'))
                    <a href="{{ route('opd.verifikasi.index') }}" class="btn btn-sm btn-outline"><i data-lucide="x" class="w-3.5 h-3.5"></i> Reset</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No. Pendaftaran / Nama</th>
                        <th>Dokumen Wajib</th>
                        <th>Waktu Upload</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dokumen as $dok)
                        <tr>
                            <td>
                                <p class="font-bold text-primary-dark text-sm">{{ $dok->pendaftaran->nomor_pendaftaran ?? '-' }}</p>
                                <p class="font-medium text-sm text-slate-900">{{ $dok->pendaftaran->identitas->nama_lengkap ?? '-' }}</p>
                                <p class="text-xs text-slate-400">NIK: {{ $dok->pendaftaran->identitas->nik ?? '-' }}</p>
                            </td>
                            <td>
                                <p class="font-medium text-sm">{{ $dok->dokumen->nama ?? '-' }}</p>
                                <p class="text-xs text-slate-400">{{ $dok->dokumen->is_wajib ? 'Wajib' : 'Opsional' }}</p>
                            </td>
                            <td class="text-sm text-slate-500">{{ $dok->updated_at->format('d M Y H:i') }}</td>
                            <td>
                                @php
                                    $dokType = match($dok->status) {
                                        'valid' => 'success',
                                        'tidak_valid' => 'danger',
                                        'belum_diverifikasi' => 'warning',
                                        default => 'muted',
                                    };
                                @endphp
                                <x-badge :type="$dokType">{{ str_replace('_', ' ', $dok->status) }}</x-badge>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('opd.verifikasi.show', $dok->id) }}" class="btn btn-xs btn-primary">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i> Proses
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"><x-empty-state icon="inbox" title="Tidak Ada Dokumen" text="Tidak ada dokumen yang perlu diverifikasi saat ini." /></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($dokumen->hasPages())
            <div class="card-footer">{{ $dokumen->links() }}</div>
        @endif
    </div>
</x-layouts.admin>
