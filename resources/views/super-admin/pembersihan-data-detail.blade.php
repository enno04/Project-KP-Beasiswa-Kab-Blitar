<x-layouts.admin :title="'Pilih Data Pendaftar - ' . $program->nama">
    <x-page-header title="Pilih Pendaftar" subtitle="Program {{ $program->nama }} (Tahun {{ $program->tahun }})" />

    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('super-admin.pembersihan-data.index') }}" class="btn btn-outline">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
        </a>
    </div>

    <!-- Filter Form -->
    <div class="card p-4 mb-6">
        <form action="{{ route('super-admin.pembersihan-data.detail', $program->id) }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Pencarian -->
            <div class="lg:col-span-2">
                <label class="form-label text-sm">Cari Pendaftar</label>
                <div class="relative flex items-center">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full" style="padding-left: 2.25rem;" placeholder="Nama, NIK, No. Daftar...">
                </div>
            </div>
            
            <!-- Kecamatan -->
            <div>
                <label class="form-label text-sm">Kecamatan</label>
                <select name="kecamatan_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kecamatan</option>
                    @foreach($kecamatans as $kec)
                        <option value="{{ $kec->id }}" {{ request('kecamatan_id') == $kec->id ? 'selected' : '' }}>{{ $kec->nama_kecamatan }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Desa (jika kecamatan dipilih) -->
            @if(request('kecamatan_id'))
            <div>
                <label class="form-label text-sm">Desa / Kelurahan</label>
                <select name="desa_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Desa</option>
                    @foreach($desas as $desa)
                        <option value="{{ $desa->id }}" {{ request('desa_id') == $desa->id ? 'selected' : '' }}>{{ $desa->nama_desa }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Status -->
            <div>
                <label class="form-label text-sm">Status</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="menunggu_verifikasi" {{ request('status') === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="lulus" {{ request('status') === 'lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            
            <div class="lg:col-span-5 flex justify-end gap-2 mt-2 pt-4 border-t border-slate-100">
                <a href="{{ route('super-admin.pembersihan-data.detail', $program->id) }}" class="btn btn-secondary">Reset</a>
                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-xl bg-green-50 flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 mb-6 text-sm text-red-800 rounded-xl bg-red-50 flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-8 flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
            <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
        </div>
        <div>
            <h3 class="text-red-900 font-bold text-lg mb-1">Hati-Hati Dalam Memilih Data</h3>
            <p class="text-red-700 text-sm leading-relaxed">
                Tindakan menghapus data pendaftar bersifat <strong>permanen dan tidak dapat dikembalikan</strong>. 
                File lampiran beserta nilai dan seluruh rekam jejaknya juga akan dihapus.
            </p>
        </div>
    </div>

    <div x-data="{ 
        showModal: false, 
        confirmText: '',
        singleDelete(id) {
            document.querySelectorAll('.check-item').forEach(el => el.checked = false);
            const checkbox = document.querySelector('.check-item[value=\''+id+'\']');
            if(checkbox) checkbox.checked = true;
            if(typeof updateDeleteButton === 'function') updateDeleteButton();
            this.showModal = true;
        }
    }">
        <form action="{{ route('super-admin.pembersihan-data.destroy') }}" method="POST" id="bulk-delete-form">
            @csrf
            @method('DELETE')
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 text-xs uppercase tracking-wider">
                                <th class="px-4 py-3 w-12 text-center">
                                    <input type="checkbox" id="check-all" class="form-checkbox text-red-600 rounded">
                                </th>
                                <th class="px-4 py-3">Nama Lengkap & NIK</th>
                                <th class="px-4 py-3">Asal Desa/Kecamatan</th>
                                <th class="px-4 py-3">Status Saat Ini</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($pendaftarans as $p)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" name="pendaftaran_ids[]" value="{{ $p->id }}" class="form-checkbox check-item text-red-600 rounded">
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="font-bold text-slate-800 text-sm">{{ $p->nama_lengkap }}</p>
                                        <p class="text-xs text-slate-500 font-medium font-mono">{{ $p->nik }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-sm">Desa {{ $p->desa->nama_desa ?? '-' }}</p>
                                        <p class="text-xs text-slate-400">Kec. {{ $p->kecamatan->nama_kecamatan ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        @php
                                            $statusType = match($p->status_color ?? 'gray') {
                                                'green' => 'success', 'red' => 'danger', 'yellow' => 'warning', 'blue' => 'info', default => 'muted',
                                            };
                                        @endphp
                                        <x-badge :type="$statusType">{{ $p->status_label }}</x-badge>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <button type="button" @click="singleDelete({{ $p->id }})" class="btn btn-xs btn-outline-danger" title="Hapus Data Ini">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5 mr-1"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-12 text-center text-slate-500">
                                        Tidak ada data pendaftar di program ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer bg-slate-50/50 border-t border-slate-100 p-4">
                    {{ $pendaftarans->onEachSide(1)->links() }}
                </div>
            </div>

            @if($pendaftarans->count() > 0)
            <div class="flex items-center justify-end">
                <button type="button" @click="showModal = true" class="btn bg-red-600 text-white hover:bg-red-700" id="btn-delete" disabled>
                    <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus <span id="count-selected" class="mx-1">0</span> Data Terpilih
                </button>
            </div>
            @endif

            <!-- Modal Konfirmasi -->
            <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm" x-transition.opacity style="display: none;">
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6" @click.away="showModal = false" x-show="showModal" x-transition.scale.origin.bottom>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                            <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900">Konfirmasi Penghapusan</h3>
                            <p class="text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan!</p>
                        </div>
                    </div>
                    
                    <p class="text-slate-700 mb-4 text-sm leading-relaxed">
                        Anda akan menghapus permanen data pendaftar terpilih beserta seluruh berkas unggahannya. Untuk melanjutkan, silakan ketik <strong class="text-red-600 select-none">HAPUS</strong> di bawah ini.
                    </p>
                    
                    <input type="text" x-model="confirmText" class="form-input w-full mb-6 font-mono text-center tracking-widest text-lg uppercase" placeholder="Ketik HAPUS">
                    
                    <div class="flex gap-3 justify-end">
                        <button type="button" @click="showModal = false; confirmText = ''" class="btn btn-outline">Batal</button>
                        <button type="submit" class="btn bg-red-600 text-white hover:bg-red-700" x-bind:disabled="confirmText.toUpperCase() !== 'HAPUS'">
                            Ya, Hapus Permanen
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkAll = document.getElementById('check-all');
            const checkItems = document.querySelectorAll('.check-item');
            const btnDelete = document.getElementById('btn-delete');
            const countSelectedSpan = document.getElementById('count-selected');

            function updateDeleteButton() {
                const selectedCount = document.querySelectorAll('.check-item:checked').length;
                if(countSelectedSpan) countSelectedSpan.textContent = selectedCount;
                if(btnDelete) {
                    btnDelete.disabled = selectedCount === 0;
                }
            }

            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    checkItems.forEach(item => {
                        item.checked = checkAll.checked;
                    });
                    updateDeleteButton();
                });
            }

            checkItems.forEach(item => {
                item.addEventListener('change', function() {
                    const allChecked = document.querySelectorAll('.check-item:checked').length === checkItems.length;
                    checkAll.checked = allChecked;
                    updateDeleteButton();
                });
            });
        });
    </script>
    @endpush
</x-layouts.admin>
