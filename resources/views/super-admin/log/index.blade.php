<x-layouts.admin :title="'Audit Log'">
    <x-page-header title="Audit Log" subtitle="Pantau seluruh perubahan data dan konfigurasi sistem." />

    <!-- Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="card p-5 border-l-4 border-primary flex items-center gap-4 bg-white shadow-sm hover:shadow transition-shadow">
            <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0">
                <i data-lucide="activity" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500">Aktivitas Hari Ini</p>
                <h3 class="text-2xl font-bold">{{ number_format($totalHariIni) }} <span class="text-xs font-normal text-slate-400">log</span></h3>
            </div>
        </div>
        <div class="card p-5 border-l-4 border-red-500 flex items-center gap-4 bg-white shadow-sm hover:shadow transition-shadow">
            <div class="w-12 h-12 rounded-full bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-500">Aktivitas Kritis (Hapus/Error)</p>
                <h3 class="text-2xl font-bold">{{ number_format($totalKritis) }} <span class="text-xs font-normal text-slate-400">log</span></h3>
            </div>
        </div>
        <div class="card p-5 border-l-4 border-amber-500 flex items-center gap-4 bg-white shadow-sm hover:shadow transition-shadow">
            <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-slate-500">Top User Hari Ini</p>
                <h3 class="text-lg font-bold truncate" title="{{ $topUser }}">{{ $topUser }}</h3>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card p-4 mb-6">
        <form action="{{ route('super-admin.audit-log') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <!-- search -->
            <div class="lg:col-span-2">
                <x-form-input name="search" label="Cari..." value="{{ request('search') }}" placeholder="Cari aktivitas atau deskripsi" />
            </div>
            
            <!-- user -->
            <div>
                <x-form-select name="user_id" label="User" placeholder="Semua User">
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->nama }}</option>
                    @endforeach
                </x-form-select>
            </div>
            
            <!-- aktivitas -->
            <div>
                <x-form-select name="aktivitas" label="Aktivitas" placeholder="Semua Aktivitas">
                    @foreach($aktivitasList as $a)
                        <option value="{{ $a }}" @selected(request('aktivitas') == $a)>{{ $a }}</option>
                    @endforeach
                </x-form-select>
            </div>
            
            <!-- tanggal dari -->
            <div>
                <x-form-input type="date" name="tanggal_dari" label="Dari" value="{{ request('tanggal_dari') }}" />
            </div>
            <!-- tanggal sampai -->
            <div>
                <x-form-input type="date" name="tanggal_sampai" label="Sampai" value="{{ request('tanggal_sampai') }}" />
            </div>
            
            <div class="lg:col-span-6 flex flex-col sm:flex-row justify-between items-center mt-2 pt-4 border-t border-slate-100 gap-4">
                <button type="submit" name="export" value="csv" class="btn btn-secondary border-emerald-200 text-emerald-700 hover:bg-emerald-50 bg-white shadow-sm flex items-center gap-2">
                    <i data-lucide="download" class="w-4 h-4"></i> Export CSV
                </button>
                <div class="flex gap-2">
                    <a href="{{ route('super-admin.audit-log') }}" class="btn btn-secondary">Reset</a>
                    <button type="submit" class="btn btn-primary">Filter Data</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden shadow-sm border-slate-200" x-data="{ 
        detailOpen: false, 
        currentLog: null,
        tab: 'diff',
        getDiffs: function(lama, baru) {
            if(!lama && !baru) return [];
            let l = typeof lama === 'object' && lama !== null ? lama : {};
            let b = typeof baru === 'object' && baru !== null ? baru : {};
            let keys = new Set([...Object.keys(l), ...Object.keys(b)]);
            let diffs = [];
            keys.forEach(k => {
                let valL = l[k] !== undefined ? (typeof l[k] === 'object' ? JSON.stringify(l[k]) : l[k]) : 'null';
                let valB = b[k] !== undefined ? (typeof b[k] === 'object' ? JSON.stringify(b[k]) : b[k]) : 'null';
                
                if (valL !== valB) {
                    diffs.push({ key: k, old: valL, new: valB, type: (valL === 'null' ? 'added' : (valB === 'null' ? 'removed' : 'changed')) });
                } else {
                    diffs.push({ key: k, val: valL, type: 'unchanged' });
                }
            });
            return diffs;
        },
        formatJson: function(data) {
            if(!data) return '-';
            if(typeof data === 'string') return data;
            return JSON.stringify(data, null, 2);
        }
    }">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Aktivitas</th>
                        <th>Modul</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="text-xs whitespace-nowrap text-slate-500">{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                        <td class="font-medium text-sm">{{ $log->user?->nama ?? 'Sistem' }}</td>
                        <td class="text-xs"><span class="badge badge-secondary">{{ $log->user?->role?->nama ?? '-' }}</span></td>
                        <td>
                            @php
                                $a = strtolower($log->aktivitas);
                                $color = 'bg-blue-50 text-blue-700 border-blue-200';
                                if (str_contains($a, 'hapus') || str_contains($a, 'gagal') || str_contains($a, 'error')) $color = 'bg-red-50 text-red-700 border-red-200';
                                elseif (str_contains($a, 'tambah') || str_contains($a, 'buat')) $color = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                elseif (str_contains($a, 'ubah') || str_contains($a, 'edit') || str_contains($a, 'update')) $color = 'bg-amber-50 text-amber-700 border-amber-200';
                            @endphp
                            <span class="inline-block px-2 py-0.5 rounded-md text-xs font-semibold border {{ $color }} mb-1">{{ $log->aktivitas }}</span>
                            <div class="text-xs text-slate-500" title="{{ $log->deskripsi }}">{{ Str::limit($log->deskripsi, 60) }}</div>
                        </td>
                        <td class="text-xs">
                            <span class="font-mono bg-slate-100 px-1 rounded">{{ class_basename($log->model_type) }}</span> 
                            @if($log->model_id) <span class="text-slate-400">#{{ $log->model_id }}</span> @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-secondary" 
                                @click="currentLog = {{ json_encode([
                                    'aktivitas' => $log->aktivitas,
                                    'deskripsi' => $log->deskripsi,
                                    'data_lama' => $log->data_lama,
                                    'data_baru' => $log->data_baru,
                                    'ip' => $log->ip_address,
                                    'waktu' => $log->created_at?->format('d/m/Y H:i:s')
                                ]) }}; detailOpen = true;">
                                <i data-lucide="eye" class="w-4 h-4"></i> Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6"><x-empty-state icon="scroll-text" title="Belum Ada Log" text="Belum ada aktivitas yang tercatat." /></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $logs->links() }}</div>

        <!-- Detail Modal -->
        <div x-show="detailOpen" style="display: none" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
            <div @click.outside="detailOpen = false" class="bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                
                <div class="p-4 border-b flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-lg text-slate-800" x-text="currentLog?.aktivitas"></h3>
                    <button @click="detailOpen = false" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                
                <div class="p-6 overflow-y-auto flex-1">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-slate-50 p-3 rounded-lg border">
                            <span class="text-xs text-slate-500 uppercase font-semibold">Waktu</span>
                            <div class="font-medium mt-1" x-text="currentLog?.waktu"></div>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-lg border">
                            <span class="text-xs text-slate-500 uppercase font-semibold">IP Address</span>
                            <div class="font-medium mt-1 font-mono" x-text="currentLog?.ip"></div>
                        </div>
                        <div class="col-span-2 bg-slate-50 p-3 rounded-lg border">
                            <span class="text-xs text-slate-500 uppercase font-semibold">Deskripsi</span>
                            <div class="font-medium mt-1" x-text="currentLog?.deskripsi"></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mb-4 border-b">
                        <button @click="tab = 'diff'" class="px-4 py-2 text-sm font-semibold border-b-2 transition-colors outline-none" :class="tab === 'diff' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700'">Smart Diff</button>
                        <button @click="tab = 'raw'" class="px-4 py-2 text-sm font-semibold border-b-2 transition-colors outline-none" :class="tab === 'raw' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-700'">Raw JSON (Berdampingan)</button>
                    </div>

                    <!-- Smart Diff Tab -->
                    <div x-show="tab === 'diff'">
                        <div class="bg-slate-900 rounded-xl overflow-hidden border border-slate-700 shadow-inner">
                            <div class="flex items-center justify-between gap-2 px-4 py-3 bg-slate-800 border-b border-slate-700 text-slate-400 text-xs font-mono">
                                <span class="uppercase tracking-wider font-semibold">Perbandingan Data (Lama &rarr; Baru)</span>
                                <div class="flex gap-3 text-[10px] font-sans">
                                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Tambah</span>
                                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> Hapus</span>
                                </div>
                            </div>
                            <div class="p-4 text-sm font-mono overflow-y-auto max-h-[50vh]">
                                <template x-if="getDiffs(currentLog?.data_lama, currentLog?.data_baru).length === 0">
                                    <div class="text-slate-500 flex items-center justify-center py-8 flex-col gap-2">
                                        <i data-lucide="check-circle" class="w-8 h-8 text-slate-600"></i>
                                        Tidak ada perubahan data yang tercatat.
                                    </div>
                                </template>
                                <template x-for="d in getDiffs(currentLog?.data_lama, currentLog?.data_baru)">
                                    <div class="mb-2 flex flex-col sm:flex-row gap-2 border-b border-slate-800/50 pb-2 last:border-0 last:pb-0">
                                        <div class="w-48 text-slate-400 shrink-0 select-none font-bold" x-text="d.key + ':'"></div>
                                        
                                        <div class="flex-1 flex flex-col gap-1 min-w-0 break-words">
                                            <template x-if="d.type === 'unchanged'">
                                                <span class="text-slate-300" x-text="d.val"></span>
                                            </template>
                                            
                                            <template x-if="d.type === 'added'">
                                                <span class="text-emerald-400 bg-emerald-900/40 px-2 py-0.5 rounded block w-full">+ <span x-text="d.new"></span></span>
                                            </template>
                                            
                                            <template x-if="d.type === 'removed'">
                                                <span class="text-red-400 bg-red-900/40 px-2 py-0.5 rounded block w-full line-through opacity-80">- <span x-text="d.old"></span></span>
                                            </template>
                                            
                                            <template x-if="d.type === 'changed'">
                                                <div class="space-y-1">
                                                    <span class="text-red-400 bg-red-900/40 px-2 py-0.5 rounded block w-full line-through opacity-80">- <span x-text="d.old"></span></span>
                                                    <span class="text-emerald-400 bg-emerald-900/40 px-2 py-0.5 rounded block w-full">+ <span x-text="d.new"></span></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Raw JSON Tab -->
                    <div x-show="tab === 'raw'" style="display: none;" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Data Lama -->
                        <div>
                            <h4 class="font-semibold text-red-600 mb-2 flex items-center gap-2"><i data-lucide="minus-circle" class="w-4 h-4"></i> Data Lama</h4>
                            <div class="bg-red-50/50 rounded-xl border border-red-100 p-4 h-[50vh] overflow-y-auto shadow-inner">
                                <template x-if="!currentLog?.data_lama">
                                    <span class="text-slate-400 text-sm italic">Tidak ada data (NULL)</span>
                                </template>
                                <template x-if="currentLog?.data_lama">
                                    <pre class="text-xs font-mono whitespace-pre-wrap text-slate-700" x-text="formatJson(currentLog.data_lama)"></pre>
                                </template>
                            </div>
                        </div>
                        
                        <!-- Data Baru -->
                        <div>
                            <h4 class="font-semibold text-emerald-600 mb-2 flex items-center gap-2"><i data-lucide="plus-circle" class="w-4 h-4"></i> Data Baru</h4>
                            <div class="bg-emerald-50/50 rounded-xl border border-emerald-100 p-4 h-[50vh] overflow-y-auto shadow-inner">
                                <template x-if="!currentLog?.data_baru">
                                    <span class="text-slate-400 text-sm italic">Tidak ada data (NULL)</span>
                                </template>
                                <template x-if="currentLog?.data_baru">
                                    <pre class="text-xs font-mono whitespace-pre-wrap text-slate-700" x-text="formatJson(currentLog.data_baru)"></pre>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
