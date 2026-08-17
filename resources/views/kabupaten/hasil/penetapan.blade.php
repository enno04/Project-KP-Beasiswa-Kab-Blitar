<x-layouts.admin :title="'Penetapan Penerima Beasiswa'">
    <div class="space-y-6 pb-20">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">Penetapan Penerima Beasiswa</h1>
                <p style="color: var(--color-text-secondary);">Pilih kandidat dari daftar peringkat untuk ditetapkan sebagai penerima resmi.</p>
            </div>
        </div>

        {{-- Filter Panel --}}
        <div class="rounded-2xl border p-5 bg-white" style="border-color: var(--color-border);" x-data="filterRanking()">
            <form method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="w-full md:w-1/3">
                    <label class="block text-sm font-semibold mb-1 text-gray-700">Periode Pendaftaran</label>
                    <select name="periode_id" class="w-full px-4 py-2.5 rounded-xl border text-sm" required>
                        <option value="">-- Pilih Periode --</option>
                        @foreach($periodeList as $p)
                            <option value="{{ $p->id }}" {{ request('periode_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }} ({{ $p->tahun }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-1/3">
                    <label class="block text-sm font-semibold mb-1 text-gray-700">Program</label>
                    <select id="program_select" class="w-full px-4 py-2.5 rounded-xl border text-sm" required @change="updateJalur()">
                        <option value="">-- Pilih Program --</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog->id }}" data-jalurs="{{ json_encode($prog->jalurs) }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>{{ $prog->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-1/3">
                    <label class="block text-sm font-semibold mb-1 text-gray-700">Jalur</label>
                    <select name="jalur_id" id="jalur_select" class="w-full px-4 py-2.5 rounded-xl border text-sm" required>
                        <option value="">-- Pilih Jalur --</option>
                    </select>
                </div>
                <div class="w-full md:w-auto flex gap-2">
                    <button type="submit" class="px-5 py-2.5 rounded-xl text-white font-semibold transition-all hover:opacity-90 flex items-center gap-2" style="background-color: var(--color-primary);">
                        <i data-lucide="search" class="w-4 h-4"></i> Tampilkan
                    </button>
                    @if(request('jalur_id') && request('periode_id'))
                    <a href="{{ route('kabupaten.hasil.ranking', ['jalur_id' => request('jalur_id'), 'periode_id' => request('periode_id')]) }}" class="px-5 py-2.5 rounded-xl border bg-white font-semibold transition-all hover:bg-gray-50 flex items-center gap-2 text-gray-700" style="border-color: var(--color-border);">
                        <i data-lucide="list-ordered" class="w-4 h-4"></i> Lihat Peringkat
                    </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Form Penetapan --}}
        @if(request('jalur_id') && request('periode_id'))
            <form action="{{ route('kabupaten.hasil.penetapan.store') }}" method="POST" x-data="penetapanForm()">
                @csrf
                
                <div class="mb-6 flex flex-col md:flex-row items-center gap-4 p-4 rounded-xl border bg-white shadow-sm" style="border-color: var(--color-border);">
                    <div class="flex-1">
                        <p class="text-sm text-gray-600 mb-1">Aksi Massal:</p>
                        <div class="flex items-center gap-3">
                            <select name="keputusan" class="w-64 px-4 py-2 rounded-xl border text-sm focus:ring-2 outline-none bg-gray-50" style="border-color: var(--color-border);" required>
                                <option value="">-- Pilih Keputusan --</option>
                                <option value="lulus">Tetapkan LULUS</option>
                                <option value="tidak_lulus">Tolak / TIDAK LULUS</option>
                            </select>
                            <input type="text" name="catatan" placeholder="Catatan opsional..." class="w-64 px-4 py-2 rounded-xl border text-sm focus:ring-2 outline-none bg-gray-50" style="border-color: var(--color-border);">
                            <button type="submit" class="px-5 py-2 rounded-xl text-white font-semibold text-sm transition-all hover:opacity-90" style="background-color: var(--color-primary);" :disabled="selectedCount === 0" :class="selectedCount === 0 ? 'opacity-50 cursor-not-allowed' : ''" onclick="return confirm('Apakah Anda yakin dengan keputusan ini?')">
                                Proses (<span x-text="selectedCount">0</span> Terpilih)
                            </button>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border bg-white overflow-hidden" style="border-color: var(--color-border);">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead style="background-color: var(--color-bg); border-bottom: 1px solid var(--color-border);">
                                <tr>
                                    <th class="px-6 py-4 w-12 text-center">
                                        <input type="checkbox" @change="toggleAll($event.target.checked)" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary">
                                    </th>
                                    <th class="px-6 py-4 font-semibold text-gray-700 text-center">Rank</th>
                                    <th class="px-6 py-4 font-semibold text-gray-700">Pendaftar</th>
                                    <th class="px-6 py-4 font-semibold text-gray-700">Wilayah</th>
                                    <th class="px-6 py-4 font-semibold text-gray-700">Status</th>
                                    <th class="px-6 py-4 font-bold text-primary-dark text-right">Total Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" style="border-color: var(--color-border);">
                                @forelse($pendaftars as $p)
                                    <tr class="hover:bg-gray-50 transition-colors {{ $p->status === 'lulus' ? 'bg-green-50/20' : '' }}">
                                        <td class="px-6 py-4 text-center">
                                            <input type="checkbox" name="pendaftaran_ids[]" value="{{ $p->id }}" x-model="selected" class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary">
                                        </td>
                                        <td class="px-6 py-4 text-center font-bold text-gray-700">
                                            {{ $p->ranking ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="font-bold">{{ $p->identitas->nama_lengkap ?? '-' }}</p>
                                            <p class="text-xs text-gray-500">{{ $p->nomor_pendaftaran }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p>Desa {{ $p->identitas->desa->nama_desa ?? '-' }}</p>
                                            <p class="text-xs text-gray-500">Kec. {{ $p->identitas->kecamatan->nama_kecamatan ?? '-' }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $p->status_color }}">
    {{ strtoupper($p->status_label) }}
</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="font-bold text-lg" style="color: var(--color-primary);">{{ number_format($p->total_nilai, 4) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            <i data-lucide="check-square" class="w-10 h-10 mx-auto mb-3 opacity-50"></i>
                                            Tidak ada data yang siap ditetapkan. Pastikan Anda telah melakukan Generate Ranking terlebih dahulu.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('filterRanking', () => ({
                init() {
                    this.updateJalur('{{ request('jalur_id') }}');
                },
                updateJalur(selectedJalur = '') {
                    const progSelect = document.getElementById('program_select');
                    const jalurSelect = document.getElementById('jalur_select');
                    
                    if(!progSelect.value) {
                        jalurSelect.innerHTML = '<option value="">-- Pilih Jalur --</option>';
                        return;
                    }

                    const selectedOption = progSelect.options[progSelect.selectedIndex];
                    const jalurs = JSON.parse(selectedOption.getAttribute('data-jalurs'));
                    
                    jalurSelect.innerHTML = '<option value="">-- Pilih Jalur --</option>';
                    jalurs.forEach(j => {
                        const isSelected = selectedJalur == j.id ? 'selected' : '';
                        jalurSelect.innerHTML += `<option value="${j.id}" ${isSelected}>${j.nama}</option>`;
                    });
                }
            }));
            
            Alpine.data('penetapanForm', () => ({
                selected: [],
                get selectedCount() {
                    return this.selected.length;
                },
                toggleAll(checked) {
                    if (checked) {
                        this.selected = Array.from(document.querySelectorAll('input[name="pendaftaran_ids[]"]')).map(el => el.value);
                    } else {
                        this.selected = [];
                    }
                }
            }));
        });
    </script>
    @endpush
</x-layouts.admin>
