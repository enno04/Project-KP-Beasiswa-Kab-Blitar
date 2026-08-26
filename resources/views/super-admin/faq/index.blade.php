<x-layouts.admin title="Master FAQ">
    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">FAQ (Tanya Jawab)</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola daftar pertanyaan yang sering diajukan beserta jawabannya.</p>
            </div>
            <button type="button" onclick="openCreateModal()" class="btn btn-primary shadow-md flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah FAQ
            </button>
        </div>

        {{-- Filter & Search Card --}}
        <div class="card p-4">
            <form method="GET" action="{{ route('super-admin.master.faq.index') }}" class="flex flex-col sm:flex-row gap-3 items-center justify-between">
                <div class="relative w-full sm:w-80">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pertanyaan atau jawaban..."
                        class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all">
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select name="status" onchange="this.form.submit()" class="px-3 py-2 rounded-xl border border-slate-200 text-sm text-slate-700 focus:ring-2 focus:ring-primary outline-none">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @if(request()->anyFilled(['search', 'status']))
                        <a href="{{ route('super-admin.master.faq.index') }}" class="btn btn-outline btn-sm text-slate-500">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table Card --}}
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="text-center w-16">Urutan</th>
                            <th class="w-1/3">Pertanyaan</th>
                            <th>Jawaban</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faqs as $faq)
                            <tr class="{{ !$faq->is_aktif ? 'bg-slate-50/70 opacity-75' : '' }}">
                                <td class="text-center font-bold text-slate-500">
                                    {{ $faq->urutan }}
                                </td>
                                <td>
                                    <p class="font-bold text-slate-900">{{ $faq->pertanyaan }}</p>
                                </td>
                                <td>
                                    <p class="text-sm text-slate-600 line-clamp-2" title="{{ $faq->jawaban }}">{{ $faq->jawaban }}</p>
                                </td>
                                <td class="text-center">
                                    <button type="button" onclick="toggleStatus({{ $faq->id }})" id="btn-status-{{ $faq->id }}" 
                                        class="px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ $faq->is_aktif ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-slate-200 text-slate-600 hover:bg-slate-300' }}">
                                        {{ $faq->is_aktif ? '✓ Aktif' : '✗ Nonaktif' }}
                                    </button>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" onclick="openEditModal({{ json_encode($faq) }})" class="btn btn-xs btn-outline" title="Edit">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Edit
                                        </button>
                                        <form action="{{ route('super-admin.master.faq.destroy', $faq->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus FAQ ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs text-red-600 hover:bg-red-50 border-red-200" title="Hapus">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="flex flex-col items-center justify-center py-10 text-slate-400">
                                        <i data-lucide="message-square" class="w-12 h-12 mb-3 text-slate-200"></i>
                                        <p class="font-medium text-slate-500">Belum ada data FAQ</p>
                                        <p class="text-sm mt-1">Silakan tambahkan pertanyaan dan jawaban baru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($faqs->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $faqs->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Form FAQ --}}
    <div id="faqModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeModal()"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form id="faqForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary-light text-primary sm:mx-0 sm:h-10 sm:w-10">
                                <i data-lucide="message-square" class="w-5 h-5"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-extrabold text-slate-900" id="modalTitle">Tambah FAQ</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label class="form-label">Pertanyaan <span class="text-red-500">*</span></label>
                                        <textarea name="pertanyaan" id="pertanyaan" rows="2" class="form-input w-full border border-slate-300 rounded-lg p-2.5 focus:border-primary focus:ring focus:ring-primary/20" required></textarea>
                                    </div>
                                    <div>
                                        <label class="form-label">Jawaban <span class="text-red-500">*</span></label>
                                        <textarea name="jawaban" id="jawaban" rows="4" class="form-input w-full border border-slate-300 rounded-lg p-2.5 focus:border-primary focus:ring focus:ring-primary/20" required></textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="form-label">Urutan</label>
                                            <input type="number" name="urutan" id="urutan" class="form-input w-full border border-slate-300 rounded-lg p-2.5 focus:border-primary focus:ring focus:ring-primary/20" value="0" min="0" required>
                                            <p class="text-[11px] text-slate-500 mt-1">Angka kecil tampil lebih dulu</p>
                                        </div>
                                        <div>
                                            <label class="form-label">Status</label>
                                            <div class="mt-2">
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox" name="is_aktif" id="is_aktif" value="1" class="rounded border-slate-300 text-primary focus:ring-primary" checked>
                                                    <span class="ml-2 text-sm text-slate-700">Aktif Tampil</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Simpan Data
                        </button>
                        <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const modal = document.getElementById('faqModal');
        const form = document.getElementById('faqForm');
        const title = document.getElementById('modalTitle');
        const method = document.getElementById('formMethod');
        
        function openCreateModal() {
            title.textContent = 'Tambah FAQ';
            form.action = '{{ route("super-admin.master.faq.store") }}';
            method.value = 'POST';
            
            document.getElementById('pertanyaan').value = '';
            document.getElementById('jawaban').value = '';
            document.getElementById('urutan').value = '{{ $maxUrutan + 1 }}';
            document.getElementById('is_aktif').checked = true;
            
            modal.classList.remove('hidden');
        }

        function openEditModal(faq) {
            title.textContent = 'Edit FAQ';
            form.action = `/super-admin/master/faq/${faq.id}`;
            method.value = 'PUT';
            
            document.getElementById('pertanyaan').value = faq.pertanyaan;
            document.getElementById('jawaban').value = faq.jawaban;
            document.getElementById('urutan').value = faq.urutan;
            document.getElementById('is_aktif').checked = faq.is_aktif;
            
            modal.classList.remove('hidden');
        }

        function closeModal() {
            modal.classList.add('hidden');
        }

        // Close on ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });

        // Toggle Status via AJAX
        async function toggleStatus(id) {
            try {
                const response = await fetch(`/super-admin/master/faq/${id}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    const btn = document.getElementById(`btn-status-${id}`);
                    const tr = btn.closest('tr');
                    
                    if (data.new_status) {
                        btn.className = 'px-2.5 py-1 rounded-full text-xs font-bold transition-all bg-green-100 text-green-700 hover:bg-green-200';
                        btn.innerHTML = '✓ Aktif';
                        tr.classList.remove('bg-slate-50/70', 'opacity-75');
                    } else {
                        btn.className = 'px-2.5 py-1 rounded-full text-xs font-bold transition-all bg-slate-200 text-slate-600 hover:bg-slate-300';
                        btn.innerHTML = '✗ Nonaktif';
                        tr.classList.add('bg-slate-50/70', 'opacity-75');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengubah status');
            }
        }
    </script>
    @endpush
</x-layouts.admin>
