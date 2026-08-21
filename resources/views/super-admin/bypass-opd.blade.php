<x-layouts.admin>
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Daftar Pendaftar (Bypass OPD)</h2>
            <p class="text-sm text-slate-500 mt-1">Pilih pendaftar (termasuk pendaftar dummy) yang ingin diloloskan tahap OPD secara otomatis.</p>
        </div>
        <div>
            <a href="{{ route('super-admin.data-dummy.index') }}" class="btn bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Kembali ke Generator
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6">
        <form action="{{ route('super-admin.data-dummy.bypass-opd') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4" id="filterForm">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Cari Nama/NIK/No.Reg</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-input !pl-10 w-full text-sm" placeholder="Ketik kata kunci...">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Program Beasiswa</label>
                <select name="program_id" class="form-select w-full text-sm">
                    <option value="">Semua Program</option>
                    @foreach($programs as $prog)
                        <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>
                            {{ $prog->nama }} ({{ $prog->tahun }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Kecamatan</label>
                <select name="kecamatan_id" id="kecamatan_id" class="form-select w-full text-sm" onchange="document.getElementById('filterForm').submit();">
                    <option value="">Semua Kecamatan</option>
                    @foreach($kecamatans as $kec)
                        <option value="{{ $kec->id }}" {{ request('kecamatan_id') == $kec->id ? 'selected' : '' }}>
                            {{ $kec->nama_kecamatan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Desa</label>
                <div class="flex gap-2">
                    <select name="desa_id" id="desa_id" class="form-select w-full text-sm">
                        <option value="">Semua Desa</option>
                        @foreach($desas as $desa)
                            <option value="{{ $desa->id }}" {{ request('desa_id') == $desa->id ? 'selected' : '' }}>
                                {{ $desa->nama_desa }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn bg-blue-600 text-white px-3 hover:bg-blue-700">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                    </button>
                    @if(request()->anyFilled(['search', 'program_id', 'kecamatan_id', 'desa_id']))
                        <a href="{{ route('super-admin.data-dummy.bypass-opd') }}" class="btn bg-slate-100 text-slate-600 px-3 hover:bg-slate-200" title="Reset Filter">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Data Table & Action Form -->
    <form action="{{ route('super-admin.data-dummy.auto-verify') }}" method="POST" id="bypassForm">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="p-4 w-12 text-center">
                                <input type="checkbox" id="selectAll" class="rounded border-slate-300 text-orange-600 focus:ring-orange-600">
                            </th>
                            <th class="p-4 font-semibold text-slate-600">Nama Pendaftar</th>
                            <th class="p-4 font-semibold text-slate-600">NIK / Nomor Reg</th>
                            <th class="p-4 font-semibold text-slate-600">Jalur Program</th>
                            <th class="p-4 font-semibold text-slate-600">Kecamatan/Desa</th>
                            <th class="p-4 font-semibold text-slate-600">Status OPD</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pendaftarans as $p)
                            <tr class="hover:bg-slate-50 transition-colors {{ str_starts_with($p->identitas->nama_lengkap ?? '', '[DUMMY]') ? 'bg-orange-50/50' : '' }}">
                                <td class="p-4 text-center">
                                    <input type="checkbox" name="pendaftaran_ids[]" value="{{ $p->id }}" class="item-checkbox rounded border-slate-300 text-orange-600 focus:ring-orange-600">
                                </td>
                                <td class="p-4">
                                    <div class="font-medium text-slate-800">{{ $p->identitas->nama_lengkap ?? '-' }}</div>
                                    @if(str_starts_with($p->identitas->nama_lengkap ?? '', '[DUMMY]'))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 mt-1">
                                            Data Dummy
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="text-slate-800">{{ $p->identitas->nik ?? '-' }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $p->nomor_pendaftaran }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="text-slate-800">{{ $p->program->nama ?? '-' }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $p->jalur->nama ?? '-' }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="text-slate-800">{{ $p->identitas->kecamatan->nama_kecamatan ?? '-' }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $p->identitas->desa->nama_desa ?? '-' }}</div>
                                </td>
                                <td class="p-4">
                                    @if($p->status === 'menunggu_verifikasi')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Menunggu Antrean
                                        </span>
                                    @elseif($p->status === 'sedang_diverifikasi')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Sedang Diproses OPD
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                            {{ ucwords(str_replace('_', ' ', $p->status)) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i data-lucide="inbox" class="w-10 h-10 text-slate-300 mb-3"></i>
                                        <p class="text-sm">Tidak ada pendaftar yang sedang menunggu verifikasi OPD.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($pendaftarans->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $pendaftarans->links() }}
                </div>
            @endif
        </div>

        <!-- Floating Action Bar -->
        <div id="actionBar" class="fixed bottom-0 left-0 right-0 md:left-64 bg-white border-t border-slate-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] transform translate-y-full transition-transform duration-300 z-40 p-4 flex justify-between items-center px-6">
            <div class="flex items-center gap-3">
                <div class="bg-orange-100 text-orange-700 w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm" id="selectedCount">
                    0
                </div>
                <span class="text-sm font-medium text-slate-700">Pendaftar Terpilih</span>
            </div>
            <div class="flex gap-3">
                <button type="button" id="btnCancel" class="btn bg-white border border-slate-200 text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
                <button type="button" onclick="confirmBypass()" class="btn bg-orange-600 text-white hover:bg-orange-700 shadow-md shadow-orange-500/20">
                    <i data-lucide="fast-forward" class="w-4 h-4 mr-2"></i> Eksekusi Auto-Verifikasi
                </button>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const actionBar = document.getElementById('actionBar');
        const selectedCount = document.getElementById('selectedCount');
        const btnCancel = document.getElementById('btnCancel');

        function updateActionBar() {
            const count = document.querySelectorAll('.item-checkbox:checked').length;
            selectedCount.textContent = count;
            
            if (count > 0) {
                actionBar.classList.remove('translate-y-full');
            } else {
                actionBar.classList.add('translate-y-full');
                selectAll.checked = false;
            }
        }

        selectAll.addEventListener('change', (e) => {
            checkboxes.forEach(cb => cb.checked = e.target.checked);
            updateActionBar();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                const allChecked = Array.from(checkboxes).every(c => c.checked);
                selectAll.checked = allChecked;
                updateActionBar();
            });
        });

        btnCancel.addEventListener('click', () => {
            checkboxes.forEach(cb => cb.checked = false);
            selectAll.checked = false;
            updateActionBar();
        });

        function confirmBypass() {
            const count = document.querySelectorAll('.item-checkbox:checked').length;
            if (confirm(`PERINGATAN: Anda akan memverifikasi ${count} pendaftar secara OTOMATIS tanpa melalui pengecekan dokumen OPD.\n\nMereka akan langsung diteruskan ke Desa. Anda yakin?`)) {
                document.getElementById('bypassForm').submit();
            }
        }
    </script>
    @endpush
</x-layouts.admin>
