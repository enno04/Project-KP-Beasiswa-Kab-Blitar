<x-layouts.admin :title="'Audit Log'">
    <x-page-header title="Audit Log" subtitle="Pantau seluruh perubahan data dan konfigurasi sistem." />

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
            
            <div class="lg:col-span-6 flex justify-end gap-2 mt-2">
                <a href="{{ route('super-admin.audit-log') }}" class="btn btn-secondary">Reset</a>
                <button type="submit" class="btn btn-primary">Filter Data</button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden" x-data="{ 
        detailOpen: false, 
        currentLog: null,
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
                            <div class="font-medium text-slate-700">{{ $log->aktivitas }}</div>
                            <div class="text-xs text-slate-500 mt-1" title="{{ $log->deskripsi }}">{{ Str::limit($log->deskripsi, 60) }}</div>
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Data Lama -->
                        <div>
                            <h4 class="font-semibold text-red-600 mb-2 flex items-center gap-2"><i data-lucide="minus-circle" class="w-4 h-4"></i> Data Lama</h4>
                            <div class="bg-red-50/50 rounded border border-red-100 p-3 h-64 overflow-y-auto">
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
                            <div class="bg-emerald-50/50 rounded border border-emerald-100 p-3 h-64 overflow-y-auto">
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
