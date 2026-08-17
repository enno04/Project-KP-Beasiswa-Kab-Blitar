<x-admin-layout>
    <x-slot name="title">Laporan Rekapitulasi</x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">Laporan Rekapitulasi</h1>
            <p style="color: var(--color-text-secondary);">Filter dan Ekspor data pendaftar beasiswa sesuai hak akses Anda.</p>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="rounded-2xl border p-5 mb-6 bg-white" style="border-color: var(--color-border); box-shadow: 0 2px 8px rgba(0,0,0,.05);">
        <form action="{{ route('laporan.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end" id="filterForm">
            <div class="w-full md:w-1/4">
                <label class="block text-sm font-semibold mb-1 text-gray-700">Tahun Anggaran</label>
                <select name="tahun" class="w-full px-4 py-2.5 rounded-xl border text-sm focus:ring-2 outline-none" style="border-color: var(--color-border); focus:ring-color: var(--color-primary);">
                    <option value="">Semua Tahun</option>
                    @for($i = date('Y'); $i >= 2024; $i--)
                        <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="w-full md:w-1/4">
                <label class="block text-sm font-semibold mb-1 text-gray-700">Kategori Beasiswa</label>
                <select name="kategori_id" class="w-full px-4 py-2.5 rounded-xl border text-sm focus:ring-2 outline-none" style="border-color: var(--color-border); focus:ring-color: var(--color-primary);">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $k)
                        <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-1/4">
                <label class="block text-sm font-semibold mb-1 text-gray-700">Status</label>
                <select name="status" class="w-full px-4 py-2.5 rounded-xl border text-sm focus:ring-2 outline-none" style="border-color: var(--color-border); focus:ring-color: var(--color-primary);">
                    <option value="">Semua Status</option>
                    <option value="ditetapkan" {{ request('status') === 'ditetapkan' ? 'selected' : '' }}>Ditetapkan (Penerima)</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak / Gugur</option>
                    <option value="menunggu_penetapan" {{ request('status') === 'menunggu_penetapan' ? 'selected' : '' }}>Menunggu Penetapan</option>
                    <option value="menunggu_verifikasi" {{ request('status') === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                </select>
            </div>
            <div class="w-full md:w-auto flex flex-wrap gap-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl text-white font-semibold transition-all hover:opacity-90 flex items-center gap-2" style="background-color: var(--color-primary);">
                    <i data-lucide="search" class="w-4 h-4"></i> Tampilkan
                </button>
                <a href="{{ route('laporan.export', request()->all()) }}" class="px-5 py-2.5 rounded-xl text-white font-semibold transition-all hover:opacity-90 flex items-center gap-2 bg-green-600">
                    <i data-lucide="download" class="w-4 h-4"></i> Export CSV
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel Pendaftar --}}
    <div class="rounded-2xl border bg-white overflow-hidden" style="border-color: var(--color-border); box-shadow: 0 2px 8px rgba(0,0,0,.05);">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead style="background-color: var(--color-bg); border-bottom: 1px solid var(--color-border);">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-gray-700">No. Pendaftaran</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Nama Lengkap</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Kategori</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Wilayah</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Total Nilai</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Rank</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="divide-color: var(--color-border);">
                    @forelse($pendaftar as $p)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-gray-500 font-mono">{{ $p->nomor_pendaftaran }}</td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-800">{{ $p->nama_lengkap }}</p>
                                <p class="text-xs text-gray-500">{{ $p->nik }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold">{{ $p->kategori->nama ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ $p->tahun }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p>Desa {{ $p->desa->nama_desa ?? '-' }}</p>
                                <p class="text-xs text-gray-500">Kec. {{ $p->kecamatan->nama_kecamatan ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 font-semibold">{{ $p->total_nilai ? number_format($p->total_nilai, 2) : '-' }}</td>
                            <td class="px-6 py-4 font-bold text-primary">{{ $p->ranking ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $badgeColors = ['green' => ['#198754', 'rgba(25,135,84,.1)'], 'red' => ['#DC3545', 'rgba(220,53,69,.1)'], 'yellow' => ['#FD7E14', 'rgba(253,126,20,.1)'], 'blue' => ['#0DCAF0', 'rgba(13,202,240,.1)'], 'gray' => ['#6C757D', 'rgba(108,117,125,.1)']];
                                    $bc = $badgeColors[$p->status_color] ?? $badgeColors['gray'];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-semibold whitespace-nowrap" style="background-color: {{ $bc[1] }}; color: {{ $bc[0] }};">
                                    {{ $p->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="file-x" class="w-10 h-10 mx-auto mb-3 opacity-50"></i>
                                Tidak ada data pendaftar yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pendaftar->hasPages())
        <div class="p-4 border-t" style="border-color: var(--color-border);">
            {{ $pendaftar->links() }}
        </div>
        @endif
    </div>
</x-admin-layout>
