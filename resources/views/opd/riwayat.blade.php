<x-layouts.admin :title="'Riwayat Verifikasi OPD'">
    <x-page-header title="Riwayat Verifikasi" subtitle="Catatan seluruh keputusan verifikasi dokumen yang telah dilakukan oleh instansi Anda." />

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Waktu Verifikasi</th>
                        <th>No. Pendaftaran / Nama</th>
                        <th>Jenis Dokumen</th>
                        <th>Keputusan & Catatan</th>
                        <th>Verifikator</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $r)
                        <tr>
                            <td class="whitespace-nowrap text-sm text-slate-500">{{ $r->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <p class="font-bold text-primary-dark text-sm">{{ $r->uploadDokumen->pendaftaran->nomor_pendaftaran ?? '-' }}</p>
                                <p class="text-sm text-slate-600">{{ $r->uploadDokumen->pendaftaran->identitas->nama_lengkap ?? '-' }}</p>
                            </td>
                            <td class="font-medium text-sm">{{ $r->uploadDokumen->dokumen->nama ?? '-' }}</td>
                            <td>
                                @if($r->hasil === 'valid')
                                    <x-badge type="success"><i data-lucide="check" class="w-3 h-3"></i> Valid</x-badge>
                                @else
                                    <x-badge type="danger"><i data-lucide="x" class="w-3 h-3"></i> Tidak Valid</x-badge>
                                    @if($r->catatan)
                                        <p class="text-xs text-slate-500 border-l-2 border-red-300 pl-2 mt-1.5">{{ $r->catatan }}</p>
                                    @endif
                                @endif
                            </td>
                            <td class="text-sm text-slate-600">{{ $r->user->nama ?? 'Sistem' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"><x-empty-state icon="history" title="Belum Ada Riwayat" text="Belum ada riwayat verifikasi." /></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
            <div class="card-footer">{{ $riwayat->links() }}</div>
        @endif
    </div>
</x-layouts.admin>
