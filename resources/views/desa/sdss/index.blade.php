<x-admin-layout>
    <x-slot name="title">Daftar Pendaftar - SDSS</x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Data Beasiswa SDSS</h1>
            <p style="color: var(--color-text-secondary);">Verifikasi akhir dan penilaian mandiri oleh pihak Desa.</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="rounded-2xl border p-4 mb-6 flex flex-col sm:flex-row gap-4 bg-white" style="border-color: var(--color-border);">
        <form action="{{ route('desa.sdss.index') }}" method="GET" class="flex flex-1 gap-4">
            <div class="w-full sm:w-48">
                <select name="tahun" class="w-full px-4 py-2.5 rounded-xl border text-sm focus:ring-2 outline-none" style="border-color: var(--color-border); focus:ring-color: var(--color-primary);" onchange="this.form.submit()">
                    <option value="">Semua Tahun</option>
                    @for($i = date('Y'); $i >= 2024; $i--)
                        <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="w-full sm:w-64">
                <select name="status" class="w-full px-4 py-2.5 rounded-xl border text-sm focus:ring-2 outline-none" style="border-color: var(--color-border); focus:ring-color: var(--color-primary);" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="menunggu_verifikasi" {{ request('status') === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi (Baru)</option>
                    <option value="sudah_diverifikasi" {{ request('status') === 'sudah_diverifikasi' ? 'selected' : '' }}>Sudah Diverifikasi (Siap Nilai)</option>
                    <option value="sudah_dinilai" {{ request('status') === 'sudah_dinilai' ? 'selected' : '' }}>Sudah Dinilai (Siap Ranking)</option>
                </select>
            </div>
            @if(request()->hasAny(['tahun', 'status']))
                <a href="{{ route('desa.sdss.index') }}" class="px-4 py-2.5 rounded-xl border text-sm font-semibold hover:bg-gray-50 flex items-center" style="border-color: var(--color-border); color: var(--color-text-secondary);">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl border bg-white overflow-hidden" style="border-color: var(--color-border); box-shadow: 0 2px 8px rgba(0,0,0,.05);">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead style="background-color: var(--color-bg); border-bottom: 1px solid var(--color-border);">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-gray-700">No. Pendaftaran</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Nama Lengkap</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Asal Sekolah</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Total Nilai</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 font-semibold text-gray-700 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="divide-color: var(--color-border);">
                    @forelse($pendaftar as $p)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-primary-dark">{{ $p->nomor_pendaftaran }}</td>
                            <td class="px-6 py-4">
                                <p class="font-medium">{{ $p->nama_lengkap }}</p>
                                <p class="text-xs text-gray-500">NIK: {{ $p->nik }}</p>
                            </td>
                            <td class="px-6 py-4">{{ $p->nama_sekolah ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if($p->total_nilai)
                                    <span class="font-bold text-primary-dark">{{ number_format($p->total_nilai, 2) }}</span>
                                @else
                                    <span class="text-gray-400 italic">Belum dinilai</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $badgeColors = ['green' => ['#198754', 'rgba(25,135,84,.1)'], 'red' => ['#DC3545', 'rgba(220,53,69,.1)'], 'yellow' => ['#FD7E14', 'rgba(253,126,20,.1)'], 'blue' => ['#0DCAF0', 'rgba(13,202,240,.1)'], 'gray' => ['#6C757D', 'rgba(108,117,125,.1)']];
                                    $bc = $badgeColors[$p->status_color] ?? $badgeColors['gray'];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap" style="background-color: {{ $bc[1] }}; color: {{ $bc[0] }};">
                                    {{ $p->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('desa.sdss.show', $p->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold {{ in_array($p->status, ['menunggu_verifikasi', 'sudah_diverifikasi']) ? 'bg-primary text-white hover:bg-primary-dark' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 border' }} transition-all">
                                    @if(in_array($p->status, ['menunggu_verifikasi', 'sudah_diverifikasi']))
                                        <i data-lucide="file-edit" class="w-3.5 h-3.5"></i> Proses
                                    @else
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                                    @endif
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="folder-open" class="w-10 h-10 mx-auto mb-3 opacity-50"></i>
                                Tidak ada data pendaftar SDSS.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($pendaftar->hasPages())
            <div class="px-6 py-4 border-t" style="border-color: var(--color-border);">
                {{ $pendaftar->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
