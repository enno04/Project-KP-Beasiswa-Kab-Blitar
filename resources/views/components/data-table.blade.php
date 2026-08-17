@props([
    'items', // paginator object
    'search' => true,
    'action' => url()->current(),
    'emptyIcon' => 'database',
    'emptyTitle' => 'Tidak Ada Data',
    'emptyText' => 'Belum ada data yang tersedia.',
])

<div class="card overflow-hidden" x-data="{ loading: false, confirmSort(url) { this.loading = true; window.location.href = url } }">
    <div class="relative">
        {{-- Loading Overlay --}}
        <div x-show="loading" class="absolute inset-0 z-50 bg-white/60 backdrop-blur-sm flex items-center justify-center transition-opacity" style="display: none;">
            <div class="bg-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-3 border border-primary-light">
                <i data-lucide="loader-2" class="w-5 h-5 text-primary animate-spin"></i>
                <span class="text-sm font-semibold text-primary-dark">Memuat data...</span>
            </div>
        </div>

        {{-- Toolbar: Search & Filters --}}
        @if($search || isset($filter))
            <div class="p-4 border-b border-slate-200 bg-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                @if($search)
                    <form action="{{ $action }}" method="GET" class="w-full md:w-80 relative" @submit="loading = true">
                        {{-- Keep filter and sorting parameters if searching --}}
                        @if(request('tahun')) <input type="hidden" name="tahun" value="{{ request('tahun') }}"> @endif
                        @if(request('program_id')) <input type="hidden" name="program_id" value="{{ request('program_id') }}"> @endif
                        @if(request('kecamatan_id')) <input type="hidden" name="kecamatan_id" value="{{ request('kecamatan_id') }}"> @endif
                        @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                        @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                        @if(request('dir')) <input type="hidden" name="dir" value="{{ request('dir') }}"> @endif
                        
                        <div class="relative">
                            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                placeholder="Cari data..." 
                                class="w-full pl-9 pr-4 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-primary outline-none select-all transition-colors bg-white hover:bg-slate-50 focus:bg-white"
                            >
                            @if(request('search'))
                                <a href="{{ $action }}" @click="loading = true" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500 transition-colors" title="Batal Cari">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                @endif
                
                @if(isset($filter))
                    <form action="{{ $action }}" method="GET" class="flex items-center gap-2" @submit="loading = true">
                        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                        @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
                        @if(request('dir')) <input type="hidden" name="dir" value="{{ request('dir') }}"> @endif
                        {{ $filter }}
                    </form>
                @endif
            </div>
        @endif

        {{-- Table Container --}}
        <div class="overflow-x-auto w-full relative">
            <table class="data-table w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase text-slate-600">
                    <tr>
                        {{ $header }}
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    {{ $slot }}
                    @if($items->isEmpty())
                        <tr>
                            <td colspan="100%" class="py-12">
                                <x-empty-state :icon="$emptyIcon" :title="$emptyTitle" :text="request('search') ? 'Tidak ditemukan hasil pencarian untuk &quot;' . request('search') . '&quot;' : $emptyText" />
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($items->hasPages())
            <div class="card-footer border-t border-slate-200 p-4 bg-slate-50">
                {{ $items->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
</div>
